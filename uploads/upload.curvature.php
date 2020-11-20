<?php
# Script: upload.curvature.php
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

  $file = 'curvature.current.csv';

# legend
  print "Usage:\n";
  print "php upload.support.php\n";
  print "f - Found the hardware\n";
  print ". - Found the Serial Number but did not find the hardware.\n";

# this isn't an import but more identifying servers that are in the spreadsheet, updating the end data (12/31/2020) and support contract info.
# check against the serial number, if found, update the server
# 0-Line                   # no
# 1-Status                 # no
# 2-Start Date             # no
# 3-End Date               # no
# 4-SLA                    # no
# 5-Asset Type Date        # no
# 6-Manufacturer           # no
# 7-Model                  # no
# 8-Serial Number          # yes
# 9-Asset Name             # no
# 10-Address               # no
# 11-Rate                  # no
#

# First, set the contract field to 0 if the contract is 177 and it's the unix team server
# then run through the 

print "Clearing Support ID and Verification Flag.\n";
$q_string  = "update ";
$q_string .= "hardware ";
$q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
$q_string .= "set hw_supportid = 0,hw_supid_verified = 0 ";
$q_string .= "where inv_manager = 1 and hw_supportid = 177 ";
if ($debug == 'no') {
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
} else {
  print "Nnumber that could be changed: " . mysqli_num_rows($q_hardware) . "\n";
}

$unix = 0;
$windows = 0;
$lab = 0;
$networking = 0;
$virtualization = 0;
$retired = 0;
$found = 0;
$total = 0;
$header = 0;
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    $data[2] = clean($data[2], 20);
    $data[8] = clean($data[8], 50);

    $total++;
# we want to match the serials or service tags with _all_ systems in case retired systems are being paid for.
    $q_string  = "select hw_id,inv_name,inv_manager ";
    $q_string .= "from hardware ";
    $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
    $q_string .= "where hw_serial = \"" . $data[8] . "\" ";
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
        $output .= "hw_supportid = " . "181" . ",";
        $output .= "hw_supportstart = '" . $data[2] . "',";
        $output .= "hw_supportend = '" . "2020-12-31" . "',";
        $output .= "hw_supid_verified = 1 where hw_id = " . $a_hardware['hw_id'] . " ";

        if ($debug == 'yes') {
          print $output . "\n";
        } else {
          print "f";
          $found++;
          $result = mysqli_query($db, $output) or die($output . ": " . mysqli_error($db));
        }
      }
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
