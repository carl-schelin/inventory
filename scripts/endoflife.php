<?php
# Script: endoflife.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

# root.cron: # Jeremy Holck requesting regular end of life info
# root.cron: 30 3 * * * /usr/local/bin/php /usr/local/httpd/bin/endoflife.php > /usr/local/httpd/htsecure/reports/endoflife.unix.prod.csv 2>/dev/null
# root.cron: 35 3 * * * /usr/local/bin/php /usr/local/httpd/bin/endoflife.php 26 > /usr/local/httpd/htsecure/reports/endoflife.unix.lab.csv 2>/dev/null

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $where = "and inv_manager = 1 ";
  if ($argc > 1) {
    $where = "and inv_manager = " . $argv[1] . " ";
  }

  $product = '';
  $q_string  = "select inv_id,inv_name,inv_function,prod_name,hw_group,ven_name,mod_name,mod_virtual,mod_eol,";
  $q_string .= "hw_serial,hw_purchased,grp_name,inv_appadmin,sup_company,sup_contract,hw_eolticket ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join a_groups on a_groups.grp_id  = hardware.hw_group ";
  $q_string .= "left join models   on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join vendors  on vendors.ven_id   = models.mod_vendor ";
  $q_string .= "left join support  on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "where hw_primary = 1 and inv_status = 0 " . $where;
  $q_string .= "order by prod_name,inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

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

    $q_string  = "select sw_software,sw_eol ";
    $q_string .= "from software ";
    $q_string .= "left join svr_software on svr_software.svr_softwareid = software.sw_id ";
    $q_string .= "left join sw_types on sw_types.typ_id = software.sw_type ";
    $q_string .= "where svr_companyid = " . $a_inventory['inv_id'] . " and typ_name = 'OS' ";
    $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_software = mysqli_fetch_array($q_software);

    $q_string  = "select grp_name ";
    $q_string .= "from a_groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    if ($a_inventory['ven_name'] == 'Dell') {
      # For Dell, the end of support is 5 years after the purchase date
      $date = explode("-", $a_inventory['hw_purchased']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
      $newdate = date("Y-m-d",$support);
    } else {
      if ($a_inventory['mod_eol'] == '') {
        $a_inventory['mod_eol'] = '1971-01-01';
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

    if ($newdate == '' || $newdate == '1971-01-01') {
      $newdate = '----------';
    }
    if ($a_software['sw_eol'] == '' || $a_software['sw_eol'] == '1971-01-01') {
      $moddate = '----------';
    }

    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . $a_inventory['grp_name'] . "\",";
    print "\"" . $a_groups['grp_name'] . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . $a_software['sw_software'] . "\",";
    print "\"" . $moddate . "\",";
    print "\"" . $a_inventory['ven_name'] . " " . $a_inventory['mod_name'] . "\",";
    print "\"" . $newdate . "\",";
    print "\"" . $a_inventory['hw_eolticket'] . "\"\n";
  }

  mysqli_close($db);

?>
