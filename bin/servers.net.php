<?php
include('settings.php');
include($Sitepath . 'function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

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

#  $db = dbconn('localhost','inventory','root','this4now!!');

  $q_string  = "select inv_id,inv_name,inv_zone ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_manager = 12 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ( $a_inventory = mysql_fetch_array($q_inventory) ) {

    $q_string = "select sw_software from software where sw_type = 'OS' and sw_companyid = " . $a_inventory['inv_id'];
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $q_string = "select hw_service from hardware where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_hardware = mysql_fetch_array($q_hardware);

# use $pre to add "<br>" in case you want to look at the output on a browser
    $pre = '<br>';
    $pre = '';
    $os = '';
    $note = '';
    $config = '';

# determine operating system
    $value = split(" ", $a_software['sw_software']);

    if ($value[0] == "Solaris") {
      $os = "SunOS";
    }
    if ($value[0] == "Red") {
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

# Convert all to lowercase
    $value[0] = strtolower($value[0]);
    $value[1] = strtolower($value[1]);
    $os = strtolower($os);
    $a_inventory['inv_zone'] = strtolower($zoneval[$a_inventory['inv_zone']]);
    $a_inventory['inv_notes'] = strtolower($a_inventory['inv_notes']);
    
    print "$pre$value[0]:$value[1]:$os:" . $a_inventory['inv_zone'] . ":::" . $a_inventory['inv_id'] . "\n";

  }

?>
