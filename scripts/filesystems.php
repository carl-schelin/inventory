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

# use the group name. The email will identify the correct list of users
  $q_string  = "select inv_name,fs_mount,fs_group,grp_name ";
  $q_string .= "from inv_filesystem ";
  $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_filesystem.fs_companyid ";
  $q_string .= "left join inv_groups    on inv_groups.grp_id    = inv_filesystem.fs_group ";
  $q_string .= "where inv_manager = " . $GRP_Unix . " and inv_status = 0 and fs_mount != '' and fs_group != " . $GRP_Unix . " and fs_group != 0 ";
  $q_string .= "order by inv_name,fs_mount ";
  $q_inv_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_filesystem = mysqli_fetch_array($q_inv_filesystem)) {

    print $a_inv_filesystem['inv_name'] . ":" . $a_inv_filesystem['fs_mount'] . ":" . $a_inv_filesystem['grp_name'] . "\n";

  }

  mysqli_close($db);

?>
