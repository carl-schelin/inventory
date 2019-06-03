<?php
# Script: west.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "west.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($_GET['server'])) {
    $formVars['server'] = clean($_GET['server'], 60);
  } else {
    $formVars['server'] = '';
  }

# looking for specific information from the inventory:
#yes         Data Point - Description
#yes         Asset IP Address - Server/Desktop IP address
#yes         Asset Names - Server/Desktop name
#no          Asset Operating System and Version - Windows2012/RHEL5/Etc.
#yes         Asset MAC Addresses - Media Access Control number
#yes         Asset Domain - What domain the system sits in
#yes         Asset Location - Data center location
#yes fm loc  Environment - Production/Pre-Prod/QA/Development

  class Server {
    public $inventory_function = '';
    public $inventory_name = '';
    public $inventory_location = '';
    public $inventory_domain = '';
    public $inventory_operating_system = '';
    public $inventory_patched = '';
    public $inventory_fqdn = '';
    public $inventory_satellite_uuid = '';
  }

  class IP_Address {
    public $interface_address = '';
    public $interface_ethernet = '';
    public $interface_scanned = '';
  }

  $q_string  = "select inv_id,inv_name,loc_instance,loc_west,inv_function,inv_domain,loc_environment,inv_kernel,inv_satuuid,inv_fqdn ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "where inv_status = 0 ";
  if ($formVars['server'] != '') {
    $q_string .= "and inv_name = \"" . $formVars['server'] . "\" ";
  }
  $q_inventory = mysql_query($q_string) or die($q_string  . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $environment = '';
    if ($a_inventory['loc_environment'] == 1) {
      $environment = 'Production';
    }
    if ($a_inventory['loc_environment'] == 2) {
      $environment = 'Pre-Production';
    }
    if ($a_inventory['loc_environment'] == 3) {
      $environment = 'Quality Assurance';
    }
    if ($a_inventory['loc_environment'] == 4) {
      $environment = 'Engineering';
    }
    if ($a_inventory['loc_environment'] == 5) {
      $environment = 'Development';
    }

    $servers[$a_inventory['inv_name']] = new Server();
    $servers[$a_inventory['inv_name']]->inventory_name           = $a_inventory['inv_name'];
    $servers[$a_inventory['inv_name']]->inventory_function       = $a_inventory['inv_function'];
    $servers[$a_inventory['inv_name']]->inventory_location       = $a_inventory['loc_west'];
    $servers[$a_inventory['inv_name']]->inventory_domain         = $a_inventory['inv_domain'];
    $servers[$a_inventory['inv_name']]->inventory_environment    = $environment;
    $servers[$a_inventory['inv_name']]->inventory_patched        = $a_inventory['inv_kernel'];
    $servers[$a_inventory['inv_name']]->inventory_satellite_uuid = $a_inventory['inv_satuuid'];
    $servers[$a_inventory['inv_name']]->inventory_fqdn           = $a_inventory['inv_fqdn'];

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $servers[$a_inventory['inv_name']]->inventory_operating_system   = $a_software['sw_software'];

    $count = 0;
    $q_string  = "select int_id,int_addr,int_eth ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " ";
    $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_interface = mysql_fetch_array($q_interface)) {

      $q_string  = "select vul_id ";
      $q_string .= "from vulnowner ";
      $q_string .= "where vul_interface = " . $a_interface['int_id'] . " ";
      $q_vulnowner = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_vulnowner) > 0) {
        $scanned = 'Yes';
      } else {
        $scanned = 'No';
      }

      $index = "interface_" . $count++;

      $servers[$a_inventory['inv_name']]->inventory_network[$index] = new IP_Address();
      $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_address   = $a_interface['int_addr'];
      $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_ethernet  = $a_interface['int_eth'];
      $servers[$a_inventory['inv_name']]->inventory_network[$index]->interface_scanned   = $scanned;
    }
  }

  echo json_encode($servers);

?>
