#!/bin/php
<?php
# Script: migrate.php
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

# Plan:
# need to migrate the sw_group, sw_verified, sw_cert, sw_facing, sw_primary, sw_locked, sw_user, sw_update to inv_svr_software
#
#sw_software -> svr_softwareid
#sw_group    -> svr_groupid
#sw_cert     -> svr_certid
#sw_facing   -> svr_facing
#sw_primary  -> svr_primary
#sw_locked   -> svr_locked
#sw_user     -> svr_userid
#sw_verified -> svr_verified
#sw_update   -> svr_update
#
#
#Need unique software name, product, and group in order to properly 
#
#select sw_id,sw_software,sw_product,sw_group,sw_cert,sw_facing,sw_primary,sw_locked,sw_user,sw_verified,sw_update from software group by sw_software,sw_product,sw_group;
#
#select sw_companyid from software where sw_software = [sw_software] and sw_product = [sw_product] and sw_group = [sw_group]
#
#List of servers results.
#
#Add an entry in the inv_svr_software table.
#
#insert into inv_svr_software set svr_id = null,svr_softwareid = [sw_id], svr_groupid = [sw_group], svr_certid = [sw_cert], sw_facing = [sw_facing] ...
#
#finally, delete all others.
#
#delete from software where sw_software = [] and sw_product = [] and sw_group = [] and sw_id != [sw_id]
#

  $q_string  = "update inv_software set sw_software = \"'\" where sw_software = '\"'";
  mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  $q_string  = "select sw_id,sw_software,sw_product,sw_group,sw_cert,sw_facing,sw_primary,";
  $q_string .= "sw_locked,sw_user,sw_verified,sw_update ";
  $q_string .= "from inv_software ";
  $q_string .= "group by sw_software,sw_product,sw_group ";
  $q_inv_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_software) > 0) {
    while ($a_inv_software = mysqli_fetch_array($q_inv_software)) {
      $q_string  = "select sw_companyid ";
      $q_string .= "from inv_software ";
      $q_string .= "where sw_software = \"" . $a_inv_software['sw_software'] . "\" and sw_product = " . $a_inv_software['sw_product'] . " and sw_group = " . $a_inv_software['sw_group'] . " ";
      $q_companyid = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_companyid) > 0) {
        while ($a_companyid = mysqli_fetch_array($q_companyid)) {
          $q_string = 
            "svr_companyid   =   " . $a_companyid['sw_companyid']  . "," . 
            "svr_softwareid  =   " . $a_inv_software['sw_id']          . "," . 
            "svr_groupid     =   " . $a_inv_software['sw_group']       . "," . 
            "svr_certid      =   " . $a_inv_software['sw_cert']        . "," . 
            "svr_facing      =   " . $a_inv_software['sw_facing']      . "," . 
            "svr_primary     =   " . $a_inv_software['sw_primary']     . "," . 
            "svr_locked      =   " . $a_inv_software['sw_locked']      . "," . 
            "svr_userid      =   " . $a_inv_software['sw_user']        . "," . 
            "svr_verified    =   " . $a_inv_software['sw_verified']    . "," . 
            "svr_update      = \"" . $a_inv_software['sw_update']      . "\"";

          $q_string = "insert into inv_svr_software set svr_id = null," . $q_string;
          mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }
      }
      $q_string  = "delete ";
      $q_string .= "from inv_software ";
      $q_string .= "where sw_software = \"" . $a_inv_software['sw_software'] . "\" and sw_product = " . $a_inv_software['sw_product'] . " and sw_group = " . $a_inv_software['sw_group'] . " and sw_id != " . $a_inv_software['sw_id'] . " ";
      mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    }
  }

  mysqli_close($db);

?>
