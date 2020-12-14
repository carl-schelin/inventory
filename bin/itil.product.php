#!/usr/local/bin/php
<?php
# Script: itil.product.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve the 'product catalog' listing
# for the conversion to Remedy.
# Requires:
# Product Type
# Product Name
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company,Product Type,CI Type,Product Categorization Tier 1,Product Categorization Tier 2,Product Categorization Tier 3,Product Name,Manufacturer,Deletion Flag,Status\n";

  $q_string  = "select prod_id,prod_name,prod_type,prod_citype,prod_tier1,prod_tier2,prod_tier3 ";
  $q_string .= "from products ";
  $q_string .= "where prod_type = 'Service' ";
  $q_string .= "order by prod_name ";
  $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {

    print "\"Intrado, Inc.\",\"" . $a_products['prod_type'] . "\",\"" . $a_products['prod_citype'] . "\",\"" . $a_products['prod_tier1'] . "\",\"" . $a_products['prod_tier2'] . "\",\"" . $a_products['prod_tier3'] . "\",\"" . $a_products['prod_name'] . "\",\"Intrado\",\"No\",\"1\"\n";

  }

  mysqli_free_request($db);

?>
