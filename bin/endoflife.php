<?php
# Script: endoflife.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $where = "and inv_manager = 1 ";
  if ($argc > 1) {
    $where = "and inv_manager = " . $argv[1] . " ";
  }

  $product = '';
  $q_string  = "select inv_id,inv_name,inv_function,prod_name,hw_group,mod_vendor,mod_name,mod_virtual,mod_eol,";
  $q_string .= "hw_serial,hw_purchased,grp_name,inv_appadmin,sup_company,sup_contract,hw_eolticket ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join groups   on groups.grp_id    = hardware.hw_group ";
  $q_string .= "left join models   on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join support  on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "where hw_primary = 1 and inv_status = 0 " . $where;
  $q_string .= "order by prod_name,inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    if ($a_inventory['prod_name'] == '') {
      $a_inventory['prod_name'] = 'Unassigned';
    }
    if ($product != $a_inventory['prod_name']) {
      print "\"" . $a_inventory['prod_name'] . "\"\n";
      print "\"System Name\",";
      print "\"Platform Owner\",";
      print "\"Application Owner\",";
      print "\"Function\",";
      print "\"Operating System\",";
      print "\"End of Life\",";
      print "\"Ticket\",";
      print "\"Hardware\",";
      print "\"End of Life\",";
      print "\"Ticket\"\n";
      $product = $a_inventory['prod_name'];
    }

    $q_string  = "select sw_software,sw_eol,sw_eolticket ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    if ($a_inventory['mod_vendor'] == 'Dell') {
      # For Dell, the end of support is 5 years after the purchase date
      $date = explode("-", $a_inventory['hw_purchased']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
      $newdate = date("Y-m-d",$support);
    } else {
      if ($a_inventory['mod_eol'] == '') {
        $a_inventory['mod_eol'] = '0000-00-00';
      }
      $date = explode("-", $a_inventory['mod_eol']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0]);
      $newdate = $a_inventory['mod_eol'];
    }
    $current = time();
    $moddate = $a_software['sw_eol'];

    $hwstatus = " class=\"ui-widget-content\"";
    if ($current > $support) {
      $hwstatus = " class=\"ui-state-error\"";
    }
    if ($a_software['sw_eol'] > date('Y-m-d')) {
      $swstatus = " class=\"ui-widget-content\"";
    } else {
      $swstatus = " class=\"ui-state-error\"";
    }

    if ($newdate == '' || $newdate == '0000-00-00') {
      $newdate = '----------';
    }
    if ($a_software['sw_eol'] == '' || $a_software['sw_eol'] == '0000-00-00') {
      $moddate = '----------';
    }

    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . $a_inventory['grp_name'] . "\",";
    print "\"" . $a_groups['grp_name'] . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . $a_software['sw_software'] . "\",";
    print "\"" . $moddate . "\",";
    print "\"" . $a_software['sw_eolticket'] . "\",";
    print "\"" . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name'] . "\",";
    print "\"" . $newdate . "\",";
    print "\"" . $a_inventory['hw_eolticket'] . "\"\n";
  }

?>
