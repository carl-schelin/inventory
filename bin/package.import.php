#!/usr/local/bin/php
<?php
# Script: package.import.php
# By: Carl Schelin
# This script reads the output of the chkserver.output file and stores the information in a table

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# import doesn't change the entry, just adds if it's not there already.

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the import file name.\n";
    exit(1);
  } else {
    $server = $argv[1];
  }

  $date = date('Y-m-d');

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server id from the inventory table
  $q_string  = "select inv_id,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where inv_name = \"" . $server . "\" and inv_status = 0 and sw_type = 'OS' ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
  while ($a_inventory = mysql_fetch_array($q_inventory)) {
    $inv_id = $a_inventory['inv_id'];
    $sw_software = $a_inventory['sw_software'];
  }

  if ($inv_id == '') {
    print "Error: No server found (" . $server . ")\n";
    exit(1);
  }

  print $server . ":";

  $servername = '/usr/local/admin/servers/' . $server . "/chkpackages.output";

  $file = fopen($servername, "r") or die;
  while(!feof($file)) {

    $process = trim(fgets($file));

# get and update if found or add if not
    $q_string  = "select pkg_id ";
    $q_string .= "from packages ";
    $q_string .= "where pkg_name = '" . $process . "' and pkg_inv_id = " . $inv_id . " ";
    $q_packages = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_packages = mysql_fetch_array($q_packages);

# already exists
    if (mysql_num_rows($q_packages) > 0) {
      $q_string  = "update ";
      $q_string .= "packages ";
      $q_string .= "set ";
      $q_string .= "pkg_update = '" . $date . "',";
      $q_string .= "pkg_os     = '" . $sw_software . "' ";
      $q_string .= "where pkg_id = " . $a_packages['pkg_id'] . " ";

      if ($debug == 'yes') {
        print $q_string . "\n";
      } else {
        print "i";
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      }

# add new
    } else {
      $q_string  = "insert into packages set ";
      $q_string .= "pkg_inv_id   =   " . $inv_id       . ",";
      $q_string .= "pkg_name     = \"" . $process      . "\",";
      $q_string .= "pkg_update   = \"" . $date         . "\",";
      $q_string .= "pkg_usr_id   =   " . "1"           . ",";
      $q_string .= "pkg_grp_id   =   " . "1"           . ",";
      $q_string .= "pkg_os       = \"" . $sw_software  . "\"";

      if ($debug == 'yes') {
        print $q_string . "\n";
      }else {
        print "a";
        $q_result = mysql_query($q_string) or die($q_string . ":  " . mysql_error());
      }
    }
  }

  print "\n";

?>
