#!/usr/local/bin/php
<?php
# Script: import.policy.php
# By: Carl Schelin
# Coding Standard 3.0 Applied
# This script reads in a colon delimited file which imports the Openview policy info in and associates it with the server

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
    print "ERROR: invalid command line parameters. Need to pass the server name.\n";
    exit(1);
  } else {
    $server = $argv[1];
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server id from the inventory table
  $q_string  = "select inv_id ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_name = \"" . $server . "\" and inv_status = 0 ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) == 0) {
    print "import.policy.ph: Unable to locate " . $server . " in the inventory.\n";
    print $q_string . "\n";
    exit(1);
  } else {
    $a_inventory = mysqli_fetch_array($q_inventory);
  }

  $serverid = $a_inventory['inv_id'];

  $date = date('Y-m-d');

  $filename = "/usr/local/admin/servers/" . $server . "/chkpolicy.input";

# in the file there are 4 fields
# 0 - Type
# 1 - Description
# 2 - Status (enabled or disabled)
# 3 - Installed policy version

# it will only get here if there's data in the file
  $file = fopen($filename, "r") or die;
  while(!feof($file)) {
    $process = trim(fgets($file));

    $value = split(":", $process);

# so something is in the type field. check the policy_type pt_type field
    if ($value[0] != '') {
      $q_string  = "select pt_id ";
      $q_string .= "from policy_type ";
      $q_string .= "where pt_type = \"" . $value[0] . "\" ";
      $q_policy_type = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_policy_type) > 0) {
        $a_policy_type = mysqli_fetch_array($q_policy_type);
      } else {
        $q_string  = "insert ";
        $q_string .= "into policy_type ";
        $q_string .= "set pt_id = null,pt_type = \"" . $value[0] . "\" ";

        if ($debug == 'yes') {
          print "T";
        } else {
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          print "t";
        }
# if it's been inserted, get the id
        $q_string  = "select pt_id ";
        $q_string .= "from policy_type ";
        $q_string .= "where pt_type = \"" . $value[0] . "\" ";
        $q_policy_type = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_policy_type) > 0) {
          $a_policy_type = mysqli_fetch_array($q_policy_type);
        } else {
          $a_policy_type['pt_id'] = 0;
        }
      }
    }

# if something is in the description field, check the policy_description pd_description field
    if ($value[1] != '') {
      $q_string  = "select pd_id ";
      $q_string .= "from policy_description ";
      $q_string .= "where pd_description = \"" . $value[1] . "\" ";
      $q_policy_description = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_policy_description) > 0) {
        $a_policy_description = mysqli_fetch_array($q_policy_description);
      } else {
        $q_string  = "insert ";
        $q_string .= "into policy_description ";
        $q_string .= "set pd_id = null,pd_description = \"" . $value[1] . "\" ";

        if ($debug == 'yes') {
          print "D";
        } else {
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          print "d";
        }
# if it's been inserted, get the id
        $q_string  = "select pd_id ";
        $q_string .= "from policy_description ";
        $q_string .= "where pd_description = \"" . $value[1] . "\" ";
        $q_policy_description = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_policy_description) > 0) {
          $a_policy_description = mysqli_fetch_array($q_policy_description);
        } else {
          $a_policy_description['pd_id'] = 0;
        }
      }
    }

# if something is in the status field, 
    $status = -1;
    if ($value[2] != '') {
      if ($value[2] == 'enabled') {
        $status = 1;
      }
      if ($value[2] == 'disabled') {
        $status = 0;
      }
    }

# now check and add it if it passed
    if ($a_policy_type['pt_id'] != 0 && $a_policy_description['pd_id'] != 0 && $status != -1) {
      $q_string  = "select pol_id ";
      $q_string .= "from policy ";
      $q_string .= "where pol_companyid = " . $a_inventory['inv_id'] . " and pol_type = " . $a_policy_type['pt_id'] . " and pol_description = " . $a_policy_description['pd_id'] . " ";
      $q_policy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_policy) == 0) {
        $q_string  = "insert ";
        $q_string .= "into policy ";
        $q_string .= "set ";
        $q_string .=   "pol_id          =   " . "null"                         . ", ";
        $q_string .=   "pol_companyid   =   " . $a_inventory['inv_id']         . ",";
        $q_string .=   "pol_type        =   " . $a_policy_type['pt_id']        . ",";
        $q_string .=   "pol_description =   " . $a_policy_description['pd_id'] . ",";
        $q_string .=   "pol_status      =   " . $status                        . ",";
        $q_string .=   "pol_version     = \"" . $value[3]                      . "\",";
        $q_string .=   "pol_date        = \"" . $date                          . "\" ";

        if ($debug == 'yes') {
          print "I";
        } else {
          $result = mysqli_query($db, $q_string);
          print "i";
        }
      } else {
        $a_policy = mysqli_fetch_array($q_policy);

        $q_string  = "update "; 
        $q_string .= "policy ";
        $q_string .= "set ";
        $q_string .=   "pol_status  =   " . $status   . ",";
        $q_string .=   "pol_version = \"" . $value[3] . "\",";
        $q_string .=   "pol_date    = \"" . $date     . "\" ";
        $q_string .= "where pol_id = " . $a_policy['pol_id'] . " ";

        if ($debug == 'yes') {
          print "P";
        } else {
          $result = mysqli_query($db, $q_string);
          print "p";
        }
      }
    } else {
      print "x";
    }
  }

  mysqli_close($db);

?>
