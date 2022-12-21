#!/usr/local/bin/php
<?php
# Script: vmimport.php
# By: Carl Schelin
# See: https://incowk01/makers/index.php/Coding_Standards
# This script reads in a comma delimited file created by the chksys script. The chksys script has various keywords 
# which are parsed by this script and then imported into the inventory database.

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  function mask2cidr($mask) {
    $long = ip2long($mask);
    $base = ip2long('255.255.255.255');
    return 32-log(($long ^ $base)+1,2);
  }

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the import file name.\n";
    exit(1);
  } else {
    $email = $argv[1];
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server names from the inventory table to identify the server id

  $date = date('Y-m-d');

  $error = 'ERROR: ';
  $output = '';

  if ($debug == 'yes') {
    print "input: " . $argv[1] . "\n";
  }

  $file = fopen($argv[1], "r") or die;
  while(!feof($file)) {
    $process = trim(fgets($file));

    if ($debug == 'yes') {
      print $process . "\n";
    }

    $value = explode(":", $process);
    $value[0] = trim($value[0]);
    $value[1] = trim($value[1]);

    if ($value[0] == "fqdn") {
      $fqdn = explode('.', $value[1], 2);
    }
    if ($value[0] == 'Function') {
      $function = $value[1];
    }
    if ($value[0] == 'Application') {
      $product = $value[1];
    }
    if ($value[0] == 'Location') {
      $location = $value[1];
    }
    if ($value[0] == 'Environment') {
      $environment = $value[1];
    }
    if ($value[0] == 'Project') {
      $project = $value[1];
    }
    if ($value[0] == 'AppSupport') {
      $appadmin = $value[1];
    }
    if ($value[0] == 'ipaddr') {
      $ipaddr = $value[1];
    }
    if ($value[0] == 'netm') {
      $mask = mask2cidr($value[1]);
      if ($mask > 32) {
        $mask = 0;
      }
    }
    if ($value[0] == 'gw') {
      $gateway = $value[1];
    }
  }

  if ($debug == 'yes') {
    print "FQDN: " . $fqdn[0] . "." . $fqdn[1] . "\n";
    print "Function: " . $function . "\n";
    print "Application: " . $product . "\n";
    print "Location: " . $location . "\n";
    print "Environment: " . $environment . "\n";
    print "Project: " . $project . "\n";
    print "AppSupport: " . $appadmin . "\n";
    print "IP Address: " . $ipaddr . "\n";
    print "Netmask: " . $mask . "\n";
    print "Gateway: " . $gateway . "\n";
  }

  if ($fqdn[0] != '') {
    $q_string  = "select inv_id ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = \"" . $fqdn[0] . "\" ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) > 0) {
      print "Error: Server already exists in the Inventory\n";
    } else {
# get the appadmin group id
      $inv_appadmin = 0;
      $q_string  = "select grp_id ";
      $q_string .= "from inv_groups ";
      $q_string .= "where grp_name = \"" . $appadmin . "\" and grp_disabled = 0 ";
      $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_groups) == 0) {
        $grp_test = explode(" ", $appadmin);
        if ($debug == 'yes') {
           print "Unable to locate this group: " . $appadmin . ", trying " . $grp_test[0] . "\n";
        }

        $q_string  = "select grp_id,grp_name ";
        $q_string .= "from inv_groups ";
        $q_string .= "where grp_name like \"%" . $grp_test[0] . "%\" and grp_disabled = 0 ";
        $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inv_groups) == 0) {
          $error .= "Unable to locate AppSupport group: " . $appadmin . " ";
        } else {
          if (mysqli_num_rows($q_inv_groups) == 1) {
            $a_inv_groups = mysqli_fetch_array($q_inv_groups);
            $inv_appadmin = $a_inv_groups['grp_id'];
            $appadmin = $a_inv_groups['grp_name'];
          } else {
            $error .= "Unable to locate AppSupport group " . $appadmin . " but found these possibilities:";
            while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
              $error .= " " . $a_inv_groups['grp_name'];
            }
            $error .= " ";
          }
        }
      } else {
        $a_inv_groups = mysqli_fetch_array($q_inv_groups);
        $inv_appadmin = $a_inv_groups['grp_id'];
        if ($debug == 'yes') {
           print "Found this group: " . $appadmin . ", ID " . $inv_appadmin . "\n";
        }
      }

# get the environment. need to get this before we get the location
      $inv_env = 0;
      $q_string  = "select env_id ";
      $q_string .= "from inv_environment ";
      $q_string .= "where env_name = \"" . $environment . "\" ";
      $q_inv_environment = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_environment) == 0) {
        $error .= "Unable to locate environment: " . $environment . " ";
      } else {
        $a_inv_environment = mysqli_fetch_array($q_inv_environment);

        $inv_env = $a_inv_environment['env_id'];
      }

# get the location. need to compare to the environment and just get the first instance since most earlier servers were L&S data center.
      $inv_location = 0;
      $q_string  = "select loc_id ";
      $q_string .= "from inv_locations ";
      $q_string .= "where loc_identity = \"" . $location . "\" and loc_environment = " . $inv_env . " and loc_type = 1 ";
      $q_string .= "order by loc_id ";
      $q_string .= "limit 1 ";
      $q_inv_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_locations) == 0) {
        $error .= "Unable to identify location: " . $location . " ";
      } else {
        $a_inv_locations = mysqli_fetch_array($q_inv_locations);

        $inv_location = $a_inv_locations['loc_id'];
      }

# get the product id
      $inv_product = 0;
      $q_string  = "select prod_id ";
      $q_string .= "from inv_products ";
      $q_string .= "where prod_name = \"" . $product . "\" ";
      $q_inv_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_products) == 0) {
         $error .= "Unable to locate product: " . $product . " ";
      } else {
        $a_inv_products = mysqli_fetch_array($q_inv_products);

        $inv_product = $a_inv_products['prod_id'];
      }

# get the project id
      $inv_project = 0;
      $q_string  = "select prj_id ";
      $q_string .= "from inv_projects ";
      $q_string .= "where prj_name = \"" . $project . "\" and prj_product = " . $inv_product . " ";
      $q_inv_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_projects) == 0) {
        $error .= "Unable to locate project: " . $project . " in product: " . $product . " ";

        $q_string  = "select prj_name ";
        $q_string .= "from inv_projects ";
        $q_string .= "where prj_product = " . $inv_product . " ";
        $q_inv_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inv_projects) > 0) {
          $error .= "\nAvailable Projects:";
          while ($a_inv_projects = mysqli_fetch_array($q_inv_projects)) {
            $error .= "\n" . $a_inv_projects['prj_name'];
          }
        }
      } else {
        $a_inv_projects = mysqli_fetch_array($q_inv_projects);

        $inv_project = $a_inv_projects['prj_id'];
      }

      if ($error != "ERROR: ") {
        print $error . "\n";
      } else {

        print "Hostname: " . $fqdn[0] . "\n";
        print "Domain: " . $fqdn[1] . "\n";
        print "Function: " . $function . "\n";
        print "Location: " . $location . " ID: " . $inv_location . "\n";
        print "Environment: " . $environment . " ID: " . $inv_env . "\n";
        print "Application: " . $product . " ID: " . $inv_product . "\n";
        print "Project: " . $project . " ID: " . $inv_project . "\n";
        print "AppAdmin: " . $appadmin . " ID: " . $inv_appadmin . "\n";
        print "IP Address: " . $ipaddr . "/" . $mask . "\n";
        print "Gateway: " . $gateway . "\n\n";

        if ($debug == 'yes') {
          echo "Does this look good to you?  Type 'yes' to continue: ";
          $handle = fopen ("php://stdin","r");
          $line = fgets($handle);
          if(trim($line) != 'yes'){
              echo "Exiting!\n";
              exit;
          }
          fclose($handle);
          echo "\n"; 
        }

        print "Adding Server...\n";

        $q_string  = "insert into inventory set inv_id = null,inv_manager = 1,";
        $q_string .= "inv_name           = \"" . $fqdn[0]          . "\",";
        $q_string .= "inv_function       = \"" . $function         . "\",";
        $q_string .= "inv_product        =   " . $inv_product      . ",";
        $q_string .= "inv_project        =   " . $inv_project      . ",";
        $q_string .= "inv_location       =   " . $inv_location     . ",";
        $q_string .= "inv_env            =   " . $inv_env          . ",";
        $q_string .= "inv_appadmin       =   " . $inv_appadmin     . ",";
        $q_string .= "inv_ansible        =   " . "1"               . ",";
        $q_string .= "inv_ssh            =   " . "1"               . ",";
        $q_string .= "inv_status         =   " . "0";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }

        $q_string  = "select inv_id ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_name = \"" . $fqdn[0] . "\" ";
        $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_inventory) == 0) {
          print "Error adding server " . $fqdn[0] . "\n";
        } else {
          $a_inventory = mysqli_fetch_array($q_inventory);

          $q_string  = "insert into inv_interface set int_id = null,";
          $q_string .= "int_server      = \"" . $fqdn[0]               . "\",";
          $q_string .= "int_domain      = \"" . $fqdn[1]               . "\",";
          $q_string .= "int_companyid   =   " . $a_inventory['inv_id'] . ",";
          $q_string .= "int_face        = \"" . "ens192"               . "\",";
          $q_string .= "int_addr        = \"" . $ipaddr                . "\",";
          $q_string .= "int_mask        =   " . $mask                  . ",";
          $q_string .= "int_eth         = \"" . "00:00:00:00:00:00"    . "\",";
          $q_string .= "int_gate        = \"" . $gateway               . "\",";
          $q_string .= "int_management  =   " . "1"                    . ",";
          $q_string .= "int_nagios      =   " . "1"                    . ",";
          $q_string .= "int_ping        =   " . "1"                    . ",";
          $q_string .= "int_ssh         =   " . "1";
 
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

          $q_string  = "insert into hardware set hw_id = null,";
          $q_string .= "hw_companyid = " . $a_inventory['inv_id'] . ",";
          $q_string .= "hw_type = " . "45" . ",";
          $q_string .= "hw_vendorid = " . "45" . ",";
          $q_string .= "hw_primary = " . "1" . ",";
          $q_string .= "hw_group = " . "1";

          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }
    }
  }

  mysqli_close($db);

?>
