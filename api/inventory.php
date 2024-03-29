<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "inventory.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
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
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_groups on inv_groups.grp_id = inv_inventory.inv_manager ";
  $q_string .= "where inv_status = 0 and (grp_id = 1 or grp_id = 26)";
  if ($formVars['group'] != '') {
    $q_string .= "and grp_name like \"%" . $formVars['group'] . "%\" ";
  }
  $q_string .= "order by inv_name ";
  $q_inv_inventory = mysqli_query($db, $q_string) or die($q_string  . ": " . mysqli_error($db));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

    $q_string  = "select grp_name ";
    $q_string .= "from inv_groups ";
    $q_string .= "where grp_id = " . $a_inv_inventory['inv_appadmin'] . " ";
    $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inv_groups = mysqli_fetch_array($q_inv_groups);


    $servers[$a_inv_inventory['inv_name']] = new Server();
    $servers[$a_inv_inventory['inv_name']]->inventory_appowner             = $a_inv_groups['grp_name'];
    $servers[$a_inv_inventory['inv_name']]->inventory_serverid             = $a_inv_inventory['inv_id'];
    $servers[$a_inv_inventory['inv_name']]->inventory_servername           = $a_inv_inventory['inv_name'];
    $servers[$a_inv_inventory['inv_name']]->inventory_sysowner             = $a_inv_inventory['grp_name'];
    $servers[$a_inv_inventory['inv_name']]->inventory_uuid                 = $a_inv_inventory['inv_uuid'];
    $servers[$a_inv_inventory['inv_name']]->inventory_satellite_uuid       = $a_inv_inventory['inv_satuuid'];
  }

  echo json_encode($servers);

?>
