<?php
# Script: upload.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  $debug = 'yes';

# read in the uploaded file

  $file = "/opt/intrado/etc/intrado.email";

  $q_string  = "update email set mail_disabled = 1";
  if ($debug == 'yes') {
    print "Marked all email as disabled.\n";
  } else {
    $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  }

  if (($handle = fopen($file, "r")) !== FALSE) {
    while (($input = fgets($handle)) !== FALSE) {

      $data = rtrim($input);

      if ($data != '') {
        $q_string  = "select mail_id ";
        $q_string .= "from email ";
        $q_string .= "where mail_address = \"" . $data . "\" ";
        $q_email = mysql_query($q_string) or die($q_string . ": " . mysql_error());
# if it doesn't exist, add it.
        if (mysql_num_rows($q_email) == 0) {
          $q_string  = 
            "mail_address      = \"" . $data         . "\"," . 
            "mail_disabled     = "   . 0             . "," . 
            "mail_date         = \"" . date('Y-m-d') . "\"";

          $query = "insert into email set mail_id = null," . $q_string;

          if ($debug == 'yes') {
            print $query . "\n";
          } else {
            $result = mysql_query($query) or die($query . ": " . mysql_error());
          }
        } else {
          $q_string  = 
            "mail_address      = \"" . $data         . "\"," . 
            "mail_disabled     = "   . 0             . "," . 
            "mail_date         = \"" . date('Y-m-d') . "\"";

          $query = "update email set " . $q_string . " where mail_id = " . $a_email['mail_id'] . " ";

          if ($debug == 'yes') {
            print $query . "\n";
          } else {
            $result = mysql_query($query) or die($query . ": " . mysql_error());
          }
        }
      }
    }
    fclose($handle);
  }

?>
