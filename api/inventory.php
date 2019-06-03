<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "inventory.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 60);
  } else {
    $formVars['group'] = '';
  }

  class Server {
    public $inventory_sysowner = '';
    public $inventory_appowner = '';
    public $inventory_serverid = 0;
    public $inventory_servername = '';
    public $inventory_uuid = '';
    public $inventory_satellite_uuid = '';
  }

  $q_string  = "select inv_id,inv_name,grp_name,inv_appadmin,inv_uuid,inv_satuuid ";
  $q_string .= "from inventory ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "where inv_status = 0 and (grp_id = 1 or grp_id = 26)";
  if ($formVars['group'] != '') {
    $q_string .= "and grp_name like \"%" . $formVars['group'] . "%\" ";
  }
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string  . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);


    $servers[$a_inventory['inv_name']] = new Server();
    $servers[$a_inventory['inv_name']]->inventory_appowner   = $a_groups['grp_name'];
    $servers[$a_inventory['inv_name']]->inventory_serverid   = $a_inventory['inv_id'];
    $servers[$a_inventory['inv_name']]->inventory_servername = $a_inventory['inv_name'];
    $servers[$a_inventory['inv_name']]->inventory_sysowner   = $a_inventory['grp_name'];
    $servers[$a_inventory['inv_name']]->inventory_uuid       = $a_inventory['inv_uuid'];
    $servers[$a_inventory['inv_name']]->inventory_satellite_uuid       = $a_inventory['inv_satuuid'];
  }

  echo json_encode($servers);

?>
