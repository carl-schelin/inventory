<?php
include('settings.php');
include($Sitepath . 'function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  $field = clean($_REQUEST["sort"], 20);

  if (isset($_REQUEST["sort"])) {
    $orderby = " order by " . $field;
  } else {
    $orderby = " order by inv_name";
  }

  $q_string = "select zone_id,zone_name from zones";
  $q_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_zones = mysql_fetch_array($q_zones)) {
    $zoneval[$a_zones['zone_id']] = $a_zones['zone_name'];
  }

  $bgcolor = "#EEEEEE";

  $q_string = "select inv_id,inv_name,inv_zone,inv_ssh from inventory where inv_manager = 26 and inv_ssh = 1 order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());

  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string = "select sw_software from software where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS'";
    $q_software = mysql_query($q_string) or die(mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $os = "";
    $pre = "";
    $note = "";
    $peering = "";


# determine operating system
    $value = split(" ", $a_software['sw_software']);

    if ($value[0] == "Solaris") {
      $os = "SunOS";
    }
    if ($value[0] == "Red" || $value[0] == "RedHat") {
      $os = "Linux";
    }
    if ($value[0] == "Debian" || $value[0] == "Ubuntu") {
      $os = "Linux";
    }
    if ($value[0] == "Oracle") {
      $os = "Linux";
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

    $value = split("/", $a_inventory['inv_name']);
    
    print "$pre$value[0]:$value[1]:$os:" . $zoneval[$a_inventory['inv_zone']] . ":$peering:$note:" . $a_inventory['inv_id'] . "\n";

  }
# add the centrify application for changelog work
  print "#centrify:::::,centrify,:0\n";

?>
