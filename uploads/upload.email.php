<?php
# Script: upload.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

# read in the uploaded file

  $file = "/opt/intrado/etc/intrado.email";

  $q_string  = "update email set mail_disabled = 1";
  if ($debug == 'yes') {
    print "Marked all email as disabled.\n";
  } else {
    $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  }

  if (($handle = fopen($file, "r")) !== FALSE) {
    while (($input = fgets($handle)) !== FALSE) {

      $data = rtrim($input);

      if ($data != '') {
        $q_string  = "select mail_id ";
        $q_string .= "from email ";
        $q_string .= "where mail_address = \"" . $data . "\" ";
        $q_email = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
# if it doesn't exist, add it.
        if (mysqli_num_rows($q_email) == 0) {
          $q_string  = 
            "mail_address      = \"" . $data         . "\"," . 
            "mail_disabled     = "   . 0             . "," . 
            "mail_date         = \"" . date('Y-m-d') . "\"";

          $query = "insert into email set mail_id = null," . $q_string;

          if ($debug == 'yes') {
            print $query . "\n";
          } else {
            $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          }
        } else {
          $a_email = mysqli_fetch_array($q_email);
          $q_string  = 
            "mail_address      = \"" . $data         . "\"," . 
            "mail_disabled     = "   . 0             . "," . 
            "mail_date         = \"" . date('Y-m-d') . "\"";

          $query = "update email set " . $q_string . " where mail_id = " . $a_email['mail_id'] . " ";

          if ($debug == 'yes') {
            print $query . "\n";
          } else {
            $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
          }
        }
      }
    }
    fclose($handle);
  }

?>
