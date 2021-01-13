<?php
# Script: datacenter.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "datacenter.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Data Center Location table");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Data Center Locations</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Location?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function attach_file( p_script_url, update ) {
  var af_form = document.locations;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&loc_name="        + encode_URI(af_form.loc_name.value);
  af_url += "&loc_type="        + af_form.loc_type.value;
  af_url += "&loc_suite="       + encode_URI(af_form.loc_suite.value);
  af_url += "&loc_addr1="       + encode_URI(af_form.loc_addr1.value);
  af_url += "&loc_addr2="       + encode_URI(af_form.loc_addr2.value);
  af_url += "&loc_city="        + encode_URI(af_form.loc_city.value);
  af_url += "&loc_zipcode="     + encode_URI(af_form.loc_zipcode.value);
  af_url += "&loc_contact1="    + encode_URI(af_form.loc_contact1.value);
  af_url += "&loc_contact2="    + encode_URI(af_form.loc_contact2.value);
  af_url += "&loc_details="     + encode_URI(af_form.loc_details.value);
  af_url += "&loc_default="     + af_form.loc_default.checked;
  af_url += "&loc_instance="    + encode_URI(af_form.loc_instance.value);
  af_url += "&loc_xpoint="      + encode_URI(af_form.loc_xpoint.value);
  af_url += "&loc_ypoint="      + encode_URI(af_form.loc_ypoint.value);
  af_url += "&loc_xlen="        + encode_URI(af_form.loc_xlen.value);
  af_url += "&loc_ylen="        + encode_URI(af_form.loc_ylen.value);
  af_url += "&loc_identity="    + encode_URI(af_form.loc_identity.value);
  af_url += "&loc_environment=" + encode_URI(af_form.loc_environment.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('datacenter.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddDatacenter' ).click(function() {
    $( "#dialogDatacenter" ).dialog('open');
  });

  $( "#dialogDatacenter" ).dialog({
    autoOpen: false,
    modal: true,
    height: 440,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogDatacenter" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('datacenter.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Location",
        click: function() {
          attach_file('datacenter.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Location",
        click: function() {
          attach_file('datacenter.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Location Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('location-help');">Help</a></th>
</tr>
</table>

<div id="location-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Location</strong> - Save any changes to this form.</li>
    <li><strong>Add Location</strong> - Create a new location record. You can copy an existing location by editing it, changing a field and saving it again.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Location Form</strong>
  <ul>
    <li><strong>Name</strong> Enter the descriptive name of the Location.</li>
    <li><strong>Suite</strong> If the devices are in a suite, enter that here.</li>
    <li><strong>Address</strong> Enter the street address. The second Address is for additional information regarding the address.</li>
    <li><strong>Select a Location</strong> This is a list of cities, states, and countries that can be selected for this data center.</li>
    <li><strong>Default</strong> Checking this puts this location into the default Home Page Data Center drop down box. Default sites are <span class="ui-state-highlight">highlighted</span>.</li>
    <li><strong>Zipcode</strong> The location zipcode.</li>
    <li><strong>CLLI Prefix</strong> The Standard Naming Convention server name prefix for this location. Four character city plus two character state plus data center instance number.</li>
    <li><strong>DC Identity</strong> The 5 character code identifying a data center.</li>
  </li></ul>
  <li><strong>Location Contact Form</strong> - Provide contact information for a location.</li>
  <li><strong>Location Access Form</strong> - Provide a link to additional documentation on how a field engineer can access this site.</li>
  <li><strong>Network Grid Form</strong> - Future: for use in creating a site map.</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddDatacenter" value="Add Location"></td>
</tr>
</table>

</form>

<div id="tabs">

<ul>
  <li><a href="#datacenter">Data Centers</a></li>
  <li><a href="#psap">PSAPs</a></li>
  <li><a href="#noc">NOC Contacts</a></li>
  <li><a href="#customer">Customers</a></li>
</ul>

<div id="datacenter">
<span id="datacenter_mysql"><?php print wait_Process('Waiting...')?></span>
</div>


<div id="psap">
<span id="psap_mysql"><?php print wait_Process('Waiting...')?></span>
</div>


<div id="noc">
<span id="noc_mysql"><?php print wait_Process('Waiting...')?></span>
</div>


<div id="customer">
<span id="customer_mysql"><?php print wait_Process('Waiting...')?></span>
</div>


</div>

</div>


<div id="dialogDatacenter" title="Location Form">

<form name="locations">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Location Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="loc_name" size="50"></td>
  <td class="ui-widget-content">Type: <select name="loc_type">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select typ_id,typ_name ";
  $q_string .= "from loc_types ";
  $q_string .= "order by typ_name ";
  $q_loc_types = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_loc_types = mysqli_fetch_array($q_loc_types)) {
    print "<option value=\"" . $a_loc_types['typ_id'] . "\">" . $a_loc_types['typ_name'] . "</option>\n";
  }
?>
</select></td>
<td class="ui-widget-content">Environment: <select name="loc_environment">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select env_id,env_name ";
  $q_string .= "from environment ";
  $q_string .= "order by env_id ";
  $q_environment = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_environment = mysqli_fetch_array($q_environment)) {
    print "<option value=\"" . $a_environment['env_id'] . "\">" . $a_environment['env_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Address 1: <input type="text" name="loc_addr1" size="30"></td>
  <td class="ui-widget-content" colspan="2">Suite: <input type="text" name="loc_suite" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Address 2: <input type="text" name="loc_addr2" size="30"></td>
  <td class="ui-widget-content" colspan="2"><label>Default Location? <input type="checkbox" name="loc_default"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Select a City/State/Country: <select name="loc_city">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select ct_id,ct_city,st_acronym,cn_acronym ";
  $q_string .= "from cities ";
  $q_string .= "left join states on states.st_id = cities.ct_state ";
  $q_string .= "left join country on country.cn_id = states.st_country ";
  $q_string .= "order by ct_city,st_acronym,cn_acronym ";
  $q_cities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_cities = mysqli_fetch_array($q_cities)) {
    print "<option value=\"" . $a_cities['ct_id'] . "\">" . $a_cities['ct_city'] . ", " . $a_cities['st_acronym'] . ", " . $a_cities['cn_acronym'] . "</option>";
  }
?>
</select></td>
  <td class="ui-widget-content" colspan="2">Zipcode: <input type="text" name="loc_zipcode" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Instance (0 for Lab, 1, 2, etc): <input type="text" name="loc_instance" size="5"></td>
  <td class="ui-widget-content" colspan="2">DC Identity: <input type="text" name="loc_identity" size="10"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Location Contact Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Contact Info <input type="text" name="loc_contact1" size="100"></td>
</tr>
<tr>
  <td class="ui-widget-content">Alternate Contact Info <input type="text" name="loc_contact2" size="100"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Location Access Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Link to Additional Details <input type="text" name="loc_details" size="100"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Network Grid Form</th>
</tr>
<tr>
  <td class="ui-widget-content">X Axis <input type="number" name="loc_xpoint" size="10"></td>
  <td class="ui-widget-content">Y Axis <input type="number" name="loc_ypoint" size="10"></td>
  <td class="ui-widget-content">X Length <input type="number" name="loc_xlen" size="10"></td>
  <td class="ui-widget-content">Y Height <input type="number" name="loc_ylen" size="10"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
