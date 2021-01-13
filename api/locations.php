<?php
# Script: locations.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  $package = "locations.php";

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if (isset($_GET['site'])) {
    $formVars['site'] = clean($_GET['site'], 40);
  } else {
    $formVars['site'] = '';
  }
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 20);
  } else {
    $formVars['type'] = '';
  }

  $where = '';
  $and = "where ";
  if ($formVars['site'] != '') {
    $where .= $and . "loc_name like \"%" . $formVars['site'] . "%\" ";
    $and = "and ";
  }
  if ($formVars['type'] != '') {
    $where .= $and . "typ_name = \"" . $formVars['type'] . "\" ";
    $and = "and ";
  }

  class Location {
    public $location_type = '';
    public $location_name = '';
    public $location_address1 = '';
    public $location_address2 = '';
    public $location_suite = '';
    public $location_city = '';
    public $location_state = '';
    public $location_zipcode = '';
    public $location_country = '';
    public $location_clli = '';
    public $location_instance = '';
    public $location_designation = '';
    public $location_environment = '';
  }

  $q_string  = "select loc_id,typ_name,loc_name,loc_addr1,loc_addr2,loc_suite,ct_city,st_state,loc_zipcode,cn_country,ct_clli,loc_instance,loc_identity,loc_environment ";
  $q_string .= "from locations ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = cities.ct_state ";
  $q_string .= "left join country on country.cn_id = states.st_country ";
  $q_string .= "left join loc_types on loc_types.typ_id = locations.loc_type ";
  $q_string .= $where;
  $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_locations = mysqli_fetch_array($q_locations)) {

    $environment = '';
    if ($a_locations['loc_environment'] == 1) {
      $environment = 'Production';
    }
    if ($a_locations['loc_environment'] == 2) {
      $environment = 'Pre-Production';
    }
    if ($a_locations['loc_environment'] == 3) {
      $environment = 'Quality Assurance';
    }
    if ($a_locations['loc_environment'] == 4) {
      $environment = 'Engineering';
    }
    if ($a_locations['loc_environment'] == 5) {
      $environment = 'Development';
    }

    $location[$a_locations['loc_id']] = new Location();
    $location[$a_locations['loc_id']]->location_type        = $a_locations['typ_name'];
    $location[$a_locations['loc_id']]->location_name        = $a_locations['loc_name'];
    $location[$a_locations['loc_id']]->location_address1    = $a_locations['loc_addr1'];
    $location[$a_locations['loc_id']]->location_address2    = $a_locations['loc_addr2'];
    $location[$a_locations['loc_id']]->location_suite       = $a_locations['loc_suite'];
    $location[$a_locations['loc_id']]->location_city        = $a_locations['ct_city'];
    $location[$a_locations['loc_id']]->location_state       = $a_locations['st_state'];
    $location[$a_locations['loc_id']]->location_zipcode     = $a_locations['loc_zipcode'];
    $location[$a_locations['loc_id']]->location_country     = $a_locations['cn_country'];
    $location[$a_locations['loc_id']]->location_clli        = $a_locations['ct_clli'];
    $location[$a_locations['loc_id']]->location_instance    = $a_locations['loc_instance'];
    $location[$a_locations['loc_id']]->location_designation = $a_locations['loc_identity'];
    $location[$a_locations['loc_id']]->location_environment = $environment;

  }

  echo json_encode($location);

?>
