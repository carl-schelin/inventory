<?php
# Script: bulkedit.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Guest);

  $package = "bulkedit.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing Detail information for the servers");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['project']   = clean($_GET['project'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);
  $formVars['csv']       = clean($_GET['csv'],      10);

  if (!isset($_GET['inwork'])) {
    $formVars['inwork'] = 'false';
  }

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby .= "order by prod_name,inv_name";
    $_SESSION['sort'] = '';
  }

  if (isset($_GET['csv'])) {
    if ($_GET['csv'] == 'true') {
      $formVars['csv'] = 1;
    } else {
      $formVars['csv'] = 0;
    }
  } else {
    $formVars['csv'] = 0;
  }

# start where build process
  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . " or sw_group = " . $formVars['group'] . ") ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

  $location = '';
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $inwork . $location . $type . $and . " sw_type = 'OS' ";;

# if help has not been seen yet,
  if (show_Help($db, 'bulkedit')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Bulk Edit Servers</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function clear_fields() {
  show_file('bulkedit.mysql.php?group=<?php print $formVars['group']; ?>&project=<?php print $formVars['project']; ?>&product=<?php print $formVars['product']; ?>');
}

function systems_Group( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=1');
}

function select_Systems( p_id ) {
  var ss_value = document.getElementById(p_id + '_groups').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=1&select=' + ss_value);
}

function applications_Group( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=2');
}

function select_Applications( p_id ) {
  var sa_value = document.getElementById(p_id + '_groups').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=2&select=' + sa_value);
}

function applications_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=3');
}

function select_Appadmin( p_id ) {
  var sa_value = document.getElementById(p_id + '_appadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=3&select=' + sa_value);
}

function service_Class( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=4');
}

function select_Service( p_id ) {
  var ss_value = document.getElementById(p_id + '_service').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=4&select=' + ss_value);
}

function data_Center( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=5');
}

function select_Location( p_id ) {
  var sl_value = document.getElementById(p_id + '_location').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=5&select=' + sl_value);
}


function server_Function( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=6');
}

function select_Function( p_id ) {
  var sf_value = encode_URI(document.getElementById(p_id + '_function').value);

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=6&select=' + sf_value);
}

function operating_System( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=7');
}

function select_Platform( p_id ) {
  var sp_value = document.getElementById(p_id + '_system').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=7&select=' + sp_value);
}

function central_Processor( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=8');
}

function select_Processor( p_id ) {
  var sp_value = document.getElementById(p_id + '_cpu').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=8&select=' + sp_value);
}

function system_Memory( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=9');
}

function select_Memory( p_id ) {
  var sm_value = document.getElementById(p_id + '_memory').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=9&select=' + sm_value);
}

function system_Size( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=10');
}

function select_Size( p_id ) {
  var ss_value = document.getElementById(p_id + '_size').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=10&select=' + ss_value);
}

function file_Systems( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=11');
}

function select_filesystem( p_id ) {
  var sf_value = document.getElementById(p_id + '_filesystem').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=11&select=' + sf_value);
}

function vendor_Name( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=12');
}

function select_vendor( p_id ) {
  var sv_value = document.getElementById(p_id + '_vendor').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=12&select=' + sv_value);
}

function model_Name( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=13');
}

function select_Model( p_id ) {
  var sm_value = document.getElementById(p_id + '_model').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=13&select=' + sm_value);
}


function interface_Name( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=14');
}

function select_Name( p_id ) {
  var sn_value = document.getElementById(p_id + '_name').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=14&select=' + sn_value);
}

function interface_Acronym( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=15');
}

function select_Acronym( p_id ) {
  var sa_value = document.getElementById(p_id + '_acronym').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=15&select=' + sa_value);
}

function interface_Description( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=16');
}

function select_Description( p_id ) {
  var sd_value = document.getElementById(p_id + '_description').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=16&select=' + sd_value);
}

function interface_Address( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=18');
}

function select_Address( p_id ) {
  var sa_value = document.getElementById(p_id + '_address').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=18&select=' + sa_value);
}

function interface_Zone( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=19');
}

function select_Zone( p_id ) {
  var sz_value = document.getElementById(p_id + '_zone').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=19&select=' + sz_value);
}

function interface_Gateway( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=20');
}

function select_Gateway( p_id ) {
  var sg_value = document.getElementById(p_id + '_gateway').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=20&select=' + sg_value);
}

function interface_Device( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=21');
}

function select_Device( p_id ) {
  var sd_value = document.getElementById(p_id + '_device').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=21&select=' + sd_value);
}

function interface_Media( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=22');
}

function select_Media( p_id ) {
  var sm_value = document.getElementById(p_id + '_media').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=22&select=' + sm_value);
}

function interface_Switch( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=23');
}

function select_Switch( p_id ) {
  var ss_value = document.getElementById(p_id + '_switch').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=23&select=' + ss_value);
}

function interface_Port( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=24');
}

function select_Port( p_id ) {
  var sp_value = document.getElementById(p_id + '_port').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=24&select=' + sp_value);
}

function interface_Netmask( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=25');
}

function select_Netmask( p_id ) {
  var sn_value = document.getElementById(p_id + '_netmask').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=25&select=' + sn_value);
}

function interface_VLAN( p_id ) {
  show_file('bulkedit.interface.php?id=' + p_id + '&status=1&type=26');
}

function select_VLAN( p_id ) {
  var sv_value = document.getElementById(p_id + '_vlan').value;

  show_file('bulkedit.interface.php?id=' + p_id + '&status=0&type=26&select=' + sv_value);
}


function platforms_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=14');
}

function select_PlatformsAdmin( p_id ) {
  var sp_value = document.getElementById(p_id + '_platformsadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=14&select=' + sp_value);
}

function SAN_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=15');
}

function select_SANAdmin( p_id ) {
  var ss_value = document.getElementById(p_id + '_sanadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=15&select=' + ss_value);
}

function network_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=16');
}

function select_NetworkAdmin( p_id ) {
  var sn_value = document.getElementById(p_id + '_networkadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=16&select=' + sn_value);
}

function virtualization_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=17');
}

function select_VirtualizationAdmin( p_id ) {
  var sv_value = document.getElementById(p_id + '_virtualizationadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=17&select=' + sv_value);
}

function datacenter_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=18');
}

function select_DataCenterAdmin( p_id ) {
  var sd_value = document.getElementById(p_id + '_datacenteradmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=18&select=' + sd_value);
}

function monitoring_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=19');
}

function select_MonitoringAdmin( p_id ) {
  var sm_value = document.getElementById(p_id + '_monitoringadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=19&select=' + sm_value);
}

function backup_Admin( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=20');
}

function select_BackupAdmin( p_id ) {
  var sb_value = document.getElementById(p_id + '_backupadmin').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=20&select=' + sb_value);
}


function volt_Text( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=21');
}

function select_voltText( p_id ) {
  var sv_value = document.getElementById(p_id + '_volttext').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=21&select=' + sv_value);
}

function power_Draw( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=22');
}

function select_powerDraw( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerdraw').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=22&select=' + sp_value);
}

function power_Start( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=23');
}

function select_powerStart( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerstart').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=23&select=' + sp_value);
}

function power_Plugs( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=24');
}

function select_powerPlugs( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerplugs').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=24&select=' + sp_value);
}

function power_Redundant( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=25');
}

function select_powerRedundant( p_id ) {
  var sp_value = document.getElementById(p_id + '_powerredundant').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=25&select=' + sp_value);
}

function plug_Text( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=26');
}

function select_plugText( p_id ) {
  var sp_value = document.getElementById(p_id + '_plugtext').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=26&select=' + sp_value);
}

function start_Row( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=27');
}

function select_startRow( p_id ) {
  var ss_value = document.getElementById(p_id + '_startrow').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=27&select=' + ss_value);
}

function start_Rack( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=28');
}

function select_startRack( p_id ) {
  var ss_value = document.getElementById(p_id + '_startrack').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=28&select=' + ss_value);
}

function start_Unit( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=29');
}

function select_startUnit( p_id ) {
  var ss_value = document.getElementById(p_id + '_startunit').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=29&select=' + ss_value);
}

function number_Units( p_id ) {
  show_file('bulkedit.inputs.php?id=' + p_id + '&status=1&type=30');
}

function select_numberUnits( p_id ) {
  var sn_value = document.getElementById(p_id + '_numberunits').value;

  show_file('bulkedit.inputs.php?id=' + p_id + '&status=0&type=30&select=' + sn_value);
}


$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="rsdp">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server Editing</th>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Add help here</p>

</div>

</div>

</form>

<div id="tabs">

<ul>
  <li><a href="#details">Details</a></li>
</ul>

<div id="details">

<span id="details_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
