<?php
include('settings.php');
include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  $q_string  = "select inv_id,inv_name,zone_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where (inv_manager = 9 or sw_group = 9) and inv_status = 0 ";
  $q_string .= "group by inv_id ";
  $q_inventory = mysql_query($q_string) or die(mysql_error());

  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS'";
    $q_software = mysql_query($q_string) or die(mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $os = "";
    $pre = "";
    $note = "";
    $peering = "";

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'];
    $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_tags = mysql_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

# determine operating system
    $value = split(" ", $a_software['sw_software']);

    if ($value[0] == "Solaris") {
      $os = "SunOS";
    }
    if ($value[0] == "Red" || $value[0] == "RedHat") {
      $os = "Linux";
    }
    if ($value[0] == "Debian" || $value[0] == "Ubuntu" || $value[0] == "CentOS") {
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
    
# determine any notes or commented out systems

# servers are called one thing but listed as another.
    if ($value[0] == "inilpsx1") {
      $value[0] = "cilpsx1";
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

    print "$pre$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$note:" . $a_inventory['inv_id'] . "\n";

  }

?>
