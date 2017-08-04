#!/usr/local/bin/php
<?php
# Script: changelog.userlist.php
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

  $path = "/export/home/";

  $q_string  = "select grp_id,grp_changelog ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 and grp_changelog != '' ";
  $q_string .= "order by grp_name ";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {

# open file for writing; but not write plus unless the group has already been written to
# like changelog which has product support and unix.
    if (isset($changelog[$a_groups['grp_changelog']])) {
      $handle = fopen($path . $a_groups['grp_changelog'] . "/admins.email", "a");
      print "Updating: " . $a_groups['grp_changelog'] . "\n";
    } else {
      $handle = fopen($path . $a_groups['grp_changelog'] . "/admins.email", "w");
      $changelog[$a_groups['grp_changelog']] = "1";
      print "Creating: " . $a_groups['grp_changelog'] . "\n";
    }

    $q_string  = "select usr_email,usr_altemail ";
    $q_string .= "from users ";
    $q_string .= "left join grouplist on grouplist.gpl_user = users.usr_id ";
    $q_string .= "where usr_disabled = 0 and gpl_group = " . $a_groups['grp_id'] . " ";
    $q_string .= "order by usr_last ";
    $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_users = mysql_fetch_array($q_users)) {

      fwrite($handle, $a_users['usr_email'] . "\n");
      if (strlen($a_users['usr_altemail']) > 0) {
        $emails = preg_split("/[\s,]+/", $a_users['usr_altemail']);

        for ($i = 0; $i < count($emails); $i++) {
          fwrite($handle, $emails[$i] . "\n");
        }

      }

    }
# add unixsvc and root to the email listing. Won't matter to anyone other than the changelog but better than adding logic
    fwrite($handle, "unixsvc\n");
    fwrite($handle, "root\n");

    fclose($handle);

  }

?>
