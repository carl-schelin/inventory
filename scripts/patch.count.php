#!/usr/local/bin/php
<?php
# Script: patch.count.php
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

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

  print "Server Name,OS,Target OS,Date Reached,Product\n";

  $q_string  = "select inv_id,inv_name,inv_kernel,prod_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_products on inv_products.prod_id = inv_inventory.inv_product ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $manager . " and inv_ssh = 1 ";
  $q_string .= "order by inv_name ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n");
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

    $target = 'Unknown';

    $q_string  = "select sw_software ";
    $q_string .= "from inv_software ";
    $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = inv_software.sw_id ";
    $q_string .= "left join inv_sw_types     on inv_sw_types.typ_id             = inv_software.sw_type ";
    $q_string .= "where svr_companyid = " . $a_inv_inventory['inv_id'] . " and typ_name = 'Operating System' ";
    $q_inv_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n");
    $a_inv_software = mysqli_fetch_array($q_inv_software);

    if (strpos($a_inv_software['sw_software'], 'Red Hat') !== false) {
      $target = 'RHEL 6.9';
      $version = 'RHEL 5.11';
      if (strpos($a_inv_software['sw_software'], ' 5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.1 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.2 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.3 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.8 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.9 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.10 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.11 ') !== false) {
        $target = $version;
      }
      $version = 'RHEL 6.10';
      if (strpos($a_inv_software['sw_software'], ' 6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.1 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.2 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.3 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.8 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.9 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.10 ') !== false) {
        $target = $version;
      }

      $version = 'RHEL 7.6';
      if (strpos($a_inv_software['sw_software'], ' 7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.1 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.2 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.3 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.6 ') !== false) {
        $target = $version;
      }
    }
    if (strpos($a_inv_software['sw_software'], 'CentOS') !== false) {
      $target = 'CentOS 6.9';
      $version = 'CentOS 5.11';
      if (strpos($a_inv_software['sw_software'], ' 5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.1 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.2 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.3 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.8 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.9 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.10 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 5.11 ') !== false) {
        $target = $version;
      }

      $version = 'CentOS 6.10';
      if (strpos($a_inv_software['sw_software'], ' 6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.1 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.2 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.3 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.8 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.6 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.8 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.9 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 6.10 ') !== false) {
        $target = $version;
      }
      $version = 'CentOS 7.6';
      if (strpos($a_inv_software['sw_software'], ' 7 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.1 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.2 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.3 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.4 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.5 ') !== false) {
        $target = $version;
      }
      if (strpos($a_inv_software['sw_software'], ' 7.6 ') !== false) {
        $target = $version;
      }
    }
    if (strpos($a_inv_software['sw_software'], 'HP-UX') !== false) {
      $target = 'HP-UX B.11.31';
    }
    if (strpos($a_inv_software['sw_software'], 'Solaris') !== false) {
      $target = 'Solaris 11';
    }

    print "\"" . $a_inv_inventory['inv_name'] . "\",\"" . $a_inv_software['sw_software'] . "\",\"" . $target . "\",\"" . $a_inv_inventory['inv_kernel'] . "\",\"" . $a_inv_inventory['prod_name'] . "\"\n";
  }

  mysqli_close($db);

?>
