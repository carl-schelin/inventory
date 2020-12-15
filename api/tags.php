<?php
# Script: tags.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "tags.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($_GET['tags'])) {
    $formVars['tags'] = clean($_GET['tags'], 255);
  } else {
    $formVars['tags'] = '';
  }
  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = '1';
  }
  if (isset($_GET['location'])) {
    $formVars['location'] = clean($_GET['location'], 10);
  } else {
    $formVars['location'] = '';
  }
  if (isset($_GET['service'])) {
    $formVars['service'] = clean($_GET['service'], 10);
  } else {
    $formVars['service'] = '';
  }
  if (isset($_GET['product'])) {
    $formVars['product'] = clean($_GET['product'], 100);
  } else {
    $formVars['product'] = '';
  }
  if (isset($_GET['zone'])) {
    $formVars['zone'] = clean($_GET['zone'], 40);
  } else {
    $formVars['zone'] = '';
  }
  if (isset($_GET['admin'])) {
    $formVars['admin'] = clean($_GET['admin'], 50);
  } else {
    $formVars['admin'] = '';
  }

# we're using tags in order to pull information from the Inventory and build 
# a list of servers that ansible will act upon
# no tags, provide just a listing of servers.

  $where = "where inv_status = 0 and inv_manager = " . $formVars['group'] . " ";

  if ($formVars['location'] != '') {
    $where .= "and loc_west = \"" . $formVars['location'] . "\" ";
  } else {
    $formVars['location'] = 'no';
  }

  if ($formVars['service'] != '') {
    $where .= "and svc_acronym = \"" . $formVars['service'] . "\" ";
  } else { 
    $formVars['service'] = 'no';
  }

  if ($formVars['product'] != '') {
    $where .= "and prod_name = \"" . $formVars['product'] . "\" ";
  } else {
    $formVars['product'] = 'no';
  }

  if ($formVars['admin'] != '') {
    $where .= "and grp_name like \"%" . $formVars['admin'] . "%\" ";
  } else {
    $formVars['admin'] = 'no';
  }

  if ($formVars['manager'] != '') {
    $where .= "and grp_name like \"%" . $formVars['manager'] . "%\" ";
  } else {
    $formVars['manager'] = 'no';
  }

  if ($formVars['tags'] != '') {
    $where .= "and tag_group = " . $formVars['group'] . " ";
    $and = "and (";
    $tags = explode(',', $formVars['tags']);

    for ($i = 0; $i < count($tags); $i++) {
      $where .= $and . "tag_name = \"" . $tags[$i] . "\" ";
      $and = "or ";
    }
    $where .= ") ";
  }

  class Server {
    public $servername = '';
  }

  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join tags on tags.tag_companyid = inventory.inv_id ";
  if ($formVars['locations'] != 'no') {
    $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  }
  if ($formVars['service'] != 'no') {
    $q_string .= "left join service on service.svc_id = inventory.inv_class ";
  }
  if ($formVars['product'] != 'no') {
    $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  }
  if ($formVars['admin'] != 'no') {
    $q_string .= "left join groups on groups.grp_id = inventory.inv_appadmin ";
  } else {
    if ($formVars['manager'] != 'no') {
      $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
    }
  }
  $q_string .= $where;
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string  . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($formVars['zone'] != '') {
      $q_string  = "select int_zone ";
      $q_string .= "from interface ";
      $q_string .= "left join ip_zones on ip_zones.zone_id = interface.int_zone ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and zone_zone = \"" . $formVars['zone'] . "\" ";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      if (mysqli_num_rows($q_interface) > 0) {
        $servers[$a_inventory['inv_name']] = new Server();
        $servers[$a_inventory['inv_name']]->servername = $a_inventory['inv_name'];
      }
    } else {
      $servers[$a_inventory['inv_name']] = new Server();
      $servers[$a_inventory['inv_name']]->servername = $a_inventory['inv_name'];
    }
  }

  echo json_encode($servers);

?>
