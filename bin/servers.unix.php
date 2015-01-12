<?php
include('settings.php');
include($Sitepath . 'function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  print "#Server Name(1):Cluster Name(2):Operating System(3):Time Zone(4):,Tag,(5):,Interface Name,(6):Inventory ID(7):Product Name(8)\n";

  $q_string  = "select inv_id,inv_name,inv_ssh,zone_name,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());

  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS'";
    $q_software = mysql_query($q_string) or die(mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $os = "";
    $pre = "";
    $tags .= "";

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
    if ($a_inventory['inv_ssh'] == 0) {
      $pre = '#';
    }

# determine operating system
    $value = split(" ", $a_software['sw_software']);

# straight linux check
    if ($value[0] == 'Linux' || $value[1] == 'Linux' || $value[2] == 'Linux') {
      $os = "Linux";
    }
# red hat based systems
    if ($value[0] == 'CentOS' || $value[0] == 'Fedora' || $value[0] == 'Red') {
      $os = "Linux";
    }
# misc non redhat/linux systems
    if ($value[0] == 'Debian' || $value[0] == 'Ubuntu' || $value[0] == 'SUSE') {
      $os = "Linux";
    }
    if ($value[0] == "Solaris" || $value[1] == 'Solaris') {
      $os = "SunOS";
    }
    if ($value[0] == "HP-UX") {
      $os = "HP-UX";
    }
    if ($value[0] == "Tru64") {
      $os = "OSF1";
    }
    if ($value[0] == "Free") {
      $os = "FreeBSD";
    }
    if ($os == "") {
      $os = $value[0];
    }

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'];
    $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_tags = mysql_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

    $value = split("/", $a_inventory['inv_name']);

    $interfaces = '';
    $q_string  = "select int_server ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
    $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_interface = mysql_fetch_array($q_interface)) {
      $interfaces .= "," . $a_interface['int_server'] . ",";
    }

# determine any notes or commented out systems

# servers are called one thing but listed as another.
    if ($value[0] == "inilpsx1") {
      $value[0] = "cilpsx1";
    }
    if ($value[0] == "miamfldctxc0") {
      $value[0] = "miamfldctxc1";
    }
    if ($value[0] == "lnmtcodctxc0") {
      $value[0] = "lnmtcodctxc1";
    }
    if ($value[0] == "incoag13") {
      $value[0] = "incoag10";
    }
    if ($value[0] == "incoag23") {
      $value[0] = "incoag20";
    }
    if ($value[0] == "incoga13") {
      $value[0] = "incoga10";
    }
    if ($value[0] == "incoga23") {
      $value[0] = "incoga20";
    }
    if ($value[0] == "incolp10") {
      $value[0] = "incolp11";
    }
    if ($value[0] == "incolp20") {
      $value[0] = "incolp21";
    }
    if ($value[0] == "incolp30") {
      $value[0] = "incolp31";
    }
    if ($value[0] == "incoce04") { # server is part of a manual cluster
      $value[0] = "incoce00";
    }

    $product = str_replace(" ", "_", $a_inventory['prod_name']);

    print "$pre$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$interfaces:" . $a_inventory['inv_id'] . ":" . $product . "\n";

  }
# add the centrify application for changelog work
  print "#centrify:::::,centrify,:0:centrify\n";

?>
