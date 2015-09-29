#!/usr/local/bin/php
<?php
# Script: itil.product.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve the 'product catalog' listing
# for the conversion to Remedy.
# Requires:
# Product Type
# Product Name
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company,Product Type,CI Type,Product Categorization Tier 1,Product Categorization Tier 2,Product Categorization Tier 3,Product Name,Manufacturer,Deletion Flag,Status\n";

  $q_string  = "select prod_id,prod_name,prod_type,prod_citype,prod_tier1,prod_tier2,prod_tier3 ";
  $q_string .= "from products ";
  $q_string .= "where prod_type = 'Service' ";
  $q_string .= "order by prod_name ";
  $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_products = mysql_fetch_array($q_products)) {

    print "Intrado, Inc.," . $a_products['prod_type'] . "," . $a_products['prod_citype'] . "," . $a_products['prod_tier1'] . "," . $a_products['prod_tier2'] . "," . $a_products['prod_tier3'] . ",\"" . $a_products['prod_name'] . "\",Intrado,0,1\n";

  }

?>
