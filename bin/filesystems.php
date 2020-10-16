#!/usr/local/bin/php
<?php
# Script: filesystems.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  if ($argv[$argc - 1] == 'debug') {
    $debug = 'yes';
  }

# use the group name. The email will identify the correct list of users (maybe the changelog name?)
  $q_string  = "select inv_name,fs_mount,fs_group,grp_name ";
  $q_string .= "from filesystem ";
  $q_string .= "left join inventory on inventory.inv_id = filesystem.fs_companyid ";
  $q_string .= "left join groups on groups.grp_id = filesystem.fs_group ";
  $q_string .= "where inv_manager = " . $GRP_Unix . " and inv_status = 0 and fs_mount != '' and grp_email != '' and fs_group != " . $GRP_Unix . " and fs_group != 0 ";
  $q_string .= "order by inv_name,fs_mount ";
  $q_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_filesystem = mysql_fetch_array($q_filesystem)) {

    print $a_filesystem['inv_name'] . ":" . $a_filesystem['fs_mount'] . ":" . $a_filesystem['grp_name'] . "\n";

  }

?>
