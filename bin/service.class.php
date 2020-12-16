#!/usr/local/bin/php
<?php
# Script: service.class.php
# By: Carl Schelin
# This script reads the output of the chkserver.output file and stores the information in a table

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# look through the product table
# if products.prod_service > 0, then set inventory.inv_class == class.

  $q_string  = "select prod_id,prod_service ";
  $q_string .= "from products ";
  $q_string .= "where prod_service > 0 ";
  $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {

    $q_string  = "update inventory ";
    $q_string .= "set ";
    $q_string .= "inv_class = " . $a_products['prod_service'] . " ";
    $q_string .= "where inv_manager = 1 and inv_product = " . $a_products['prod_id'] . " ";
    $results = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# labs
    $q_string  = "update inventory ";
    $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
    $q_string .= "set ";
    $q_string .= "inv_class = 6 ";
    $q_string .= "where inv_manager = 1 and loc_instance = 0 and inv_product = " . $a_products['prod_id'] . " ";
    $results = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  }

  mysqli_close($db);

?>
