#!/usr/local/bin/php
<?php
# Script: ddurgee.hwage.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

# root.cron: # Denise Durgee requesting hardware build dates
# root.cron: 30 5 * * * /usr/local/bin/php /usr/local/httpd/bin/ddurgee.hwage.php > /usr/local/httpd/htsecure/reports/ddurgee.hwage.prod.csv 2>/dev/null
# root.cron: 45 5 * * * /usr/local/bin/php /usr/local/httpd/bin/ddurgee.hwage.php 26 > /usr/local/httpd/htsecure/reports/ddurgee.hwage.lab.csv 2>/dev/null

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $debug = 'no';
  $debug = 'yes';

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $manager = "(inv_manager = 1 or inv_manager = 5 or inv_manager = 4 or inv_manager = 9) ";

  if ($argc > 1) {
    $manager = "inv_manager = 26 ";
  }

  print "\"Server Name\",\"Function\",\"Operating System\",\"Hardware\",\"Serial Number\",\"Project\",\"Build Date\"\n";

  $q_string  = "select inv_id,inv_name,inv_function,sw_software,prod_name,hw_built,hw_serial,mod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "where inv_status = 0 and sw_type = 'OS' and mod_virtual = 0 and hw_primary = 1 and " . $manager;
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . $a_inventory['sw_software'] . "\",";
    print "\"" . $a_inventory['mod_name'] . "\",";
    print "\"" . $a_inventory['hw_serial'] . "\",";
    print "\"" . $a_inventory['prod_name'] . "\",";
    print "\"" . $a_inventory['hw_built'] . "\"\n";

  }

?>
