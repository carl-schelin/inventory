<?php
# Script: upload.support.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($argv[1])) {
    $debug = 'yes';
  } else {
    $debug = 'no';
  }

  $file = 'support.current.csv';

# legend
  print "Usage:\n";
  print "php upload.support.php\n";
  print "f - Found the hardware\n";
  print ". - Found the Serial Number but did not find the hardware.\n";

# 0-Vendor            # yes
# 1-Company           # no - this is Intrado or Positron
# 2-PO                # yes
# 3-Comment           # no
# 4-Description       # yes
# 5-Start Date        # yes
# 6-End Date          # yes
# 7-Custodian         # yes
# 8-BUC               # yes
# 9-BU                # yes
# 10-Dept             # yes
# 11-Exp Acct         # yes
# 12-Project #        # no
# 13-Location #       # no
# 14-Customer #       # yes
# 15-Quantity         # no
# 16-Serial Number    # yes
# 17-System Name      # yes
# 18-Coverage         # yes
#
# I only care about the serial number (16) for the first run through

# First, set the contract field to 0, as in, not under contract.
# don't want to touch the other fields in case there is support but maybe under a different contract

print "Clearing Support ID Verification Flag.\n";
$q_string  = "update ";
$q_string .= "hardware ";
$q_string .= "set hw_supid_verified = 0 ";
$q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

$unix = 0;
$windows = 0;
$lab = 0;
$networking = 0;
$virtualization = 0;
$found = 0;
$total = 0;
$header = 0;
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    $data[0] = clean($data[0], 50);  # support vendor
    $data[2] = clean($data[2], 40);  # po number
    $data[4] = clean($data[4], 100);  # description
    $data[5] = clean($data[5], 100);  # start date
    $data[6] = clean($data[6], 100);  # end date
    $data[7] = clean($data[7], 100);  # custodian; first and last
    $data[8] = clean($data[8], 100);  # buc; just last but check for spaces
    $data[9] = clean($data[9], 100);  # business unit
    $data[10] = clean($data[10], 100);  # department
    $data[11] = clean($data[11], 100);  # expence account
    $data[14] = clean($data[14], 100);  # customer
    $data[16] = clean($data[16], 100);  # serial number
    $data[17] = clean($data[17], 100);  # system name
    $data[18] = clean($data[18], 100);  # coverage

# add the po number to the system.
    $q_string  = "select po_id ";
    $q_string .= "from purchaseorder ";
    $q_string .= "where po_number = '" . $data[2] . "' ";
    $q_purchaseorder = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_purchaseorder) == 0) {
      $q_string = "insert into purchaseorder set po_id = null,po_number = '" . $data[2] . "' ";
      $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    } else {
      $a_purchaseorder = mysqli_fetch_array($q_purchaseorder);

      $q_string = "update purchaseorder set po_number = '" . $data[2] . "' where po_id = " . $a_purchaseorder['po_id'] . " ";
      $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    }

    if (isset($data[16]) && strlen($data[16]) > 0) {

      if ($data[0] == 'Akibia') {
        $data[0] = 'Zensar';
      }

      if ($header > 0) {
# we want to match the serials or service tags with _all_ systems in case retired systems are being paid for.
        $q_string  = "select hw_id,inv_name,inv_manager ";
        $q_string .= "from hardware ";
        $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
        $q_string .= "where hw_serial = \"" . $data[16] . "\" ";
        $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        if (mysqli_num_rows($q_hardware) > 0) {
          while ($a_hardware = mysqli_fetch_array($q_hardware)) {

            if ($a_hardware['inv_manager'] == $GRP_Unix) {
              $unix++;
            }
            if ($a_hardware['inv_manager'] == $GRP_Windows) {
              $windows++;
            }
            if ($a_hardware['inv_manager'] == $GRP_ICLAdmins) {
              $lab++;
            }
            if ($a_hardware['inv_manager'] == $GRP_Networking) {
              $networking++;
            }
            if ($a_hardware['inv_manager'] == $GRP_Virtualization) {
              $virtualization++;
            }

            $output  = "update ";
            $output .= "hardware ";
            $output .= "set ";

            $q_string  = "select po_id ";
            $q_string .= "from purchaseorder ";
            $q_string .= "where po_number = '" . $data[2] . "' ";
            $q_purchaseorder = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_purchaseorder = mysqli_fetch_array($q_purchaseorder);

            $output .= "hw_poid = " . $a_purchaseorder['po_id'] . ",";

            $q_string  = "select sup_id,sup_company ";
            $q_string .= "from support ";
            $q_string .= "where sup_company = \"" . $data[0] . "\" ";
            $q_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_support ) > 0) {
              $a_support = mysqli_fetch_array($q_support);
              $output .= "hw_supportid = " . $a_support['sup_id'] . ",";
            } else {
              $output .= "hw_supportid = 0,";
              print " Vendor Not Found: " . $data[0];
            }

            $q_string  = "select slv_id,slv_value ";
            $q_string .= "from supportlevel ";
            $q_string .= "where slv_translate like \"%" . $data[18] . "%\" ";
            $q_supportlevel = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_supportlevel) > 0) {
              $a_supportlevel = mysqli_fetch_array($q_supportlevel);
              $output .= "hw_response = " . $a_supportlevel['slv_id'] . ",";
            } else {
              $output .= "hw_response = 0,";
              print " Coverage Not Found: " . $data[18] . "\n";
            }

            $start = date('Y-m-d', strtotime($data[5]));
            $end = date('Y-m-d', strtotime($data[6]));

            $output .= "hw_supportstart = '" . $start . "',";
            $output .= "hw_supportend = '" . $end . "',";

# get last name if custodian has a first name; assuming first and last
            $username = explode(" ", $data[7]);
            if (count($username) == 1) {
              $checkuser = $username[0];
            } else {
              $checkuser = $username[1];
            }

            if ($debug == 'yes') {
              print "data:7 " . $data[7] . "\n";
            }

            $q_string  = "select usr_id ";
            $q_string .= "from users ";
            $q_string .= "where usr_last = \"" . $checkuser . "\" and usr_disabled = 0 ";
            $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_users) > 1) {
# more than one; check for first name?
            } else {
              if (mysqli_num_rows($q_users) == 1) {
                $a_users = mysqli_fetch_array($q_users);

                $output .= "hw_custodian = " . $a_users['usr_id'] . ",";
              } else {
# reset to no owner if buc is gone
                if ($debug == 'yes') {
                  print "not found: data:7 " . $data[7] . "\n";;
                }
                $output .= "hw_custodian = " . "0" . ",";
              }
            }

# get last name if buc has a first name; assuming first and last
            $username = explode(" ", $data[8]);
            if (count($username) == 1) {
              $checkuser = $username[0];
            } else {
              $checkuser = $username[1];
            }

            if ($debug == 'yes') {
              print "data:8 " . $data[8] . "\n";;
            }

            $q_string  = "select usr_id ";
            $q_string .= "from users ";
            $q_string .= "where usr_last = \"" . $checkuser . "\" and usr_disabled = 0 ";
            $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            if (mysqli_num_rows($q_users) > 1) {
# more than one; check for first name?
            } else {
              if (mysqli_num_rows($q_users) == 1) {
                $a_users = mysqli_fetch_array($q_users);

                $output .= "hw_buc = " . $a_users['usr_id'] . ",";
              } else {
# reset to no owner if buc is gone
                $output .= "hw_buc = " . "0" . ",";
                if ($debug == 'yes') {
                  print "not found: data:8 " . $data[8] . "\n";;
                }
              }
            }


            if ($data[9] != '') {
              $output .= "hw_business = " . $data[9] . ",";
            }

            if ($data[10] != '') {
              $output .= "hw_dept = " . $data[10] . ",";
            }

            if ($data[11] != '') {
              $output .= "hw_expense = " . $data[11] . ",";
            }

            if ($data[14] != '') {
              $output .= "hw_customer = " . $data[14] . ",";
            }

# last line
            $output .= "hw_supid_verified = 1 where hw_id = " . $a_hardware['hw_id'] . " ";

            if ($debug == 'yes') {
              print $output . "\n";
            } else {
              print "f";
              $found++;
              $total++;
              $result = mysqli_query($db, $output) or die($output . ": " . mysqli_error($db));
            }
          }
        } else {
          if ($debug == 'yes') {
            print "Vendor: [" . $data[0] . "] Description: [" . $data[4] . "] Serial: [" . $data[16] . "] Servername: [" . $data[17] . "]\n";
          } else {
            print ".";
            $total++;
          }
        }
      }
      $header++;
    }
  }

  print "\n\n";
  print "Unix: " . $unix . "\n";
  print "Windows: " . $windows . "\n";
  print "Virtualization: " . $virtualization . "\n";
  print "Networking: " . $networking . "\n";
  print "ICL/Engineering: " . $lab . "\n";
  print "=======================\n";
  print "Found: " . $found . "\n";
  print "=======================\n";
  print "Total: " . $total . "\n";
  fclose($handle);
}

?>
