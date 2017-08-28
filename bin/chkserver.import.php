#!/usr/local/bin/php
<?php
# Script: chkserver.import.php
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

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server id from the inventory table
  $q_string  = "select inv_id ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_name = \"" . $server . "\" and inv_status = 0 ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
  while ($a_inventory = mysql_fetch_array($q_inventory)) {
    $inv_id = $a_inventory['inv_id'];
  }

  if ($inv_id == '') {
    print "Error: No server found (" . $server . ")\n";
    exit(1);
  }

  print $server . ":";

# In order to automatically close errors, set import to 1. As errors are checked, update the flag
  $q_string  = "update chkserver ";
  $q_string .= "set chk_import = 1 ";
  $q_string .= "where chk_companyid = " . $inv_id . " ";
  $results = mysql_query($q_string) or die($q_string . ": " . mysql_error());


  $servername = '/usr/local/admin/servers/' . $server . "/chkserver.output";

  $file = fopen($servername, "r") or die;
  while(!feof($file)) {

    $process = trim(fgets($file));

    $process = bin2hex($process);
    $check = 0;

# red
    if (strpos($process, "1b5b33316d") !== false) {
      $check = 1;
      $priority = 2;
      $process = preg_replace("/1b5b33316d/", "", preg_replace("/1b5b306d/", "", $process));
    }
# green
    if (strpos($process, "1b5b33326d") !== false) {
      $check = 0;
      $priority = 0;
      $process = preg_replace("/1b5b33326d/", "", preg_replace("/1b5b306d/", "", $process));
    }
# yellow
    if (strpos($process, "1b5b33336d") !== false) {
      $check = 1;
      $priority = 5;
      $process = preg_replace("/1b5b33336d/", "", preg_replace("/1b5b306d/", "", $process));
    }

    $process = hex2bin($process);

# filter out the header
    if (strpos($process, "Passed - Test or value was successful") !== false) {
      $check = 0;
    }
    if (strpos($process, "Warning - Setting should be reviewed and corrected if appropriate") !== false) {
      $check = 0;
    }
    if (strpos($process, "Error - Must be corrected due to vulnerability or system stability issue") !== false) {
      $check = 0;
    }

    if ($check) {
      $q_string  = "select ce_id ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_error = \"" . $process . "\" ";
      $q_chkerrors = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
      if (mysql_num_rows($q_chkerrors) == 0) {
# add the error if it's not there
        $q_string  = "insert ";
        $q_string .= "into chkerrors ";
        $q_string .= "set ";
        $q_string .= "ce_id = null,";
        $q_string .= "ce_error    = \"" . $process  . "\",";
        $q_string .= "ce_priority =   " . $priority;

        if ($debug == 'yes') {
          print "Missing error: " . $process . "\n";
        } else {
          print "e";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
        }
      }

# okay, now actually get the id for the update
      $q_string  = "select ce_id ";
      $q_string .= "from chkerrors ";
      $q_string .= "where ce_error = \"" . $process . "\" ";
      $q_chkerrors = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_chkerrors) == 0) {
        print "Unable to locate: " . $process . "\n";
      } else {
        $a_chkerrors = mysql_fetch_array($q_chkerrors);

# the idea is to see if the server and id exists and hasn't been closed yet.
# and got the id, now see if the id already exists in the chkserver table
# if it was in place, update the date.
# add a record only when the server was found with the same error but has been closed
        $q_string  = "select chk_id ";
        $q_string .= "from chkserver ";
        $q_string .= "where chk_companyid = " . $inv_id . " and chk_errorid = " . $a_chkerrors['ce_id'] . " and chk_closed = '0000-00-00 00:00:00' ";
        $q_chkserver = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
        if (mysql_num_rows($q_chkserver) == 0) {
# add the message flag
          $q_string  = "insert ";
          $q_string .= "into chkserver ";
          $q_string .= "set ";
          $q_string .= "chk_id = null,";
          $q_string .= "chk_companyid  =   " . $inv_id  . ",";
          $q_string .= "chk_errorid    =   " . $a_chkerrors['ce_id'];
  
          print "s";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
        } else {
          $a_chkserver = mysql_fetch_array($q_chkserver);

          $q_string  = "update chkserver ";
          $q_string .= "set ";
          $q_string .= "chk_import = 0 ";
          $q_string .= "where chk_id = " . $a_chkserver['chk_id'] . " and chk_closed = '0000-00-00 00:00:00' ";

          print "i";
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");
        }
      }
    }
  }

# Auto-close: need to now update any entry that is set to 1 to the current date and time if it's not already set.
  $q_string  = "update chkserver ";
  $q_string .= "set ";
  $q_string .= "chk_closed = '" . date('Y-m-d H:i:s') . "' ";
  $q_string .= "where chk_import = 1 and chk_companyid = " . $inv_id . " and chk_closed = '0000-00-00 00:00:00' ";

  print "c";
  $result = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n");


  print "\n";

?>
