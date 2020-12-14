#!/usr/local/bin/php
<?php
# Script: filesystems.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
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
  $q_string .= "left join groups    on groups.grp_id    = filesystem.fs_group ";
  $q_string .= "where inv_manager = " . $GRP_Unix . " and inv_status = 0 and fs_mount != '' and fs_group != " . $GRP_Unix . " and fs_group != 0 ";
  $q_string .= "order by inv_name,fs_mount ";
  $q_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_filesystem = mysqli_fetch_array($q_filesystem)) {

    print $a_filesystem['inv_name'] . ":" . $a_filesystem['fs_mount'] . ":" . $a_filesystem['grp_name'] . "\n";

  }

  mysqli_free_result($db);

?>
