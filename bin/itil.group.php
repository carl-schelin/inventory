#!/usr/local/bin/php
<?php
# Script: itil.group.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve the 'support group' information from the group table
# for the conversion to Remedy.
# Requires:
# Support Organization
# Support Group Name
# Support Group Role
# Vendor Group
# Status

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company,Support Organization,Support Group Name,Support Group Role,Description,Group Email,Deletion Flag,Status\n";

  $q_string  = "select grp_id,grp_disabled,grp_name,grp_email,org_name,role_name ";
  $q_string .= "from groups ";
  $q_string .= "left join organizations on organizations.org_id = groups.grp_organization ";
  $q_string .= "left join roles on roles.role_id = groups.grp_role ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {

    if ($a_groups['org_name'] == 'Vendor') {
      $vendor = '0';
    } else {
      $vendor = '1';
    }

    if ($a_groups['grp_disabled'] || $a_groups['grp_role'] == 0) {
      $disabled = 'No'; # was '2'
    } else {
      $disabled = 'Yes'; # was '1'
    }
    print "\"Intrado, Inc.\",\"" . $a_groups['org_name'] . "\",\"" . $a_groups['grp_name'] . "\",\"" . $a_groups['role_name'] . "\",\"\",\"" . $a_groups['grp_email'] . "\",\"" . $disabled . "\",\"0\"\n";
  }

  mysqli_free_request($db);

?>
