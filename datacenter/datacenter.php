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

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Data Center Editor</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

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
  af_url += "&loc_identity="    + encode_URI(af_form.loc_identity.value);
  af_url += "&loc_tags="        + encode_URI(af_form.loc_tags.value);
  af_url += "&loc_environment=" + encode_URI(af_form.loc_environment.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&loc_name="        + encode_URI(uf_form.loc_name.value);
  uf_url += "&loc_type="        + uf_form.loc_type.value;
  uf_url += "&loc_suite="       + encode_URI(uf_form.loc_suite.value);
  uf_url += "&loc_addr1="       + encode_URI(uf_form.loc_addr1.value);
  uf_url += "&loc_addr2="       + encode_URI(uf_form.loc_addr2.value);
  uf_url += "&loc_city="        + encode_URI(uf_form.loc_city.value);
  uf_url += "&loc_zipcode="     + encode_URI(uf_form.loc_zipcode.value);
  uf_url += "&loc_contact1="    + encode_URI(uf_form.loc_contact1.value);
  uf_url += "&loc_contact2="    + encode_URI(uf_form.loc_contact2.value);
  uf_url += "&loc_details="     + encode_URI(uf_form.loc_details.value);
  uf_url += "&loc_default="     + uf_form.loc_default.checked;
  uf_url += "&loc_instance="    + encode_URI(uf_form.loc_instance.value);
  uf_url += "&loc_identity="    + encode_URI(uf_form.loc_identity.value);
  uf_url += "&loc_tags="        + encode_URI(uf_form.loc_tags.value);
  uf_url += "&loc_environment=" + encode_URI(uf_form.loc_environment.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('datacenter.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 500,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCreate" ).hide();
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
        text: "Add",
        click: function() {
          attach_file('datacenter.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 500,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUpdate" ).hide();
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
        text: "Update",
        click: function() {
          update_file('datacenter.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add",
        click: function() {
          update_file('datacenter.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Data Center Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('location-help');">Help</a></th>
</tr>
</table>

<div id="location-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A Data Center is where you have servers and other equipment located. This page lets you create the necessary 
address both for a Data Center and for any other address you might require.</p>


</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Data Center"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Data Center Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('datacenter-listing-help');">Help</a></th>
</tr>
</table>

<div id="datacenter-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Data Center Listing</strong></p>

<p>This page lists all the locations in the address book including Data Centes.</p>

<p>To edit a Location, click on the entry in the listing. A dialog box will be displayed where 
you can edit the current entry, or if there's some change you wish to make, you can add a new 
Location.</p>

<p>To add a new Location, click the Add Data Center button. A dialog box will be displayed 
where you can add the necessary information and then save the new Location.</p>

<p>Note there is a <strong>Location Tags</strong> option in the forms. This lets you 
add tags to a location for use in identifying servers in a data center by selecting the 
tag from the Tag Cloud option but also for use with Ansible for filtering your run 
against a specific site. The expectation is a single word as a tag so separate 
tags by a space or a comma.</p>

<p>Note that an entry that is <span class="ui-state-highlight">highlighted</span> has been 
selected to show up in the report filters. They show up at the top of the report for clarity.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Data Center Form">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Descriptive Label: <input type="text" name="loc_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Location Type: <select name="loc_type">
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
</tr>
<tr>
<td class="ui-widget-content">Environment: <select name="loc_environment">
<?php
  $q_string  = "select env_id,env_name ";
  $q_string .= "from environment ";
  $q_string .= "order by env_name ";
  $q_environment = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_environment = mysqli_fetch_array($q_environment)) {
    print "<option value=\"" . $a_environment['env_id'] . "\">" . $a_environment['env_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Address 1: <input type="text" name="loc_addr1" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Address 2: <input type="text" name="loc_addr2" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Suite: <input type="text" name="loc_suite" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2"><label>Use in Report Filter? <input type="checkbox" name="loc_default"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Select a City/State/Country: <select name="loc_city">
<?php
  $q_string  = "select ct_id,ct_city,st_acronym,cn_acronym ";
  $q_string .= "from cities ";
  $q_string .= "left join inv_states on inv_states.st_id = cities.ct_state ";
  $q_string .= "left join country on country.cn_id = inv_states.st_country ";
  $q_string .= "order by ct_city,st_acronym,cn_acronym ";
  $q_cities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_cities = mysqli_fetch_array($q_cities)) {
    print "<option value=\"" . $a_cities['ct_id'] . "\">" . $a_cities['ct_city'] . ", " . $a_cities['st_acronym'] . ", " . $a_cities['cn_acronym'] . "</option>";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Zipcode: <input type="text" name="loc_zipcode" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Site Instance (0 for Lab, 1, 2, etc): <input type="text" name="loc_instance" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Data Center Identifier: <input type="text" name="loc_identity" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Contact Info <input type="text" name="loc_contact1" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Secondary Contact Info <input type="text" name="loc_contact2" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Location Tags <input type="text" name="loc_tags" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Link to Additional Details <input type="text" name="loc_details" size="60"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Data Center Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Descriptive Label: <input type="text" name="loc_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Location Type: <select name="loc_type">
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
</tr>
<tr>
<td class="ui-widget-content">Environment: <select name="loc_environment">
<?php
  $q_string  = "select env_id,env_name ";
  $q_string .= "from environment ";
  $q_string .= "order by env_name ";
  $q_environment = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_environment = mysqli_fetch_array($q_environment)) {
    print "<option value=\"" . $a_environment['env_id'] . "\">" . $a_environment['env_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Address 1: <input type="text" name="loc_addr1" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Address 2: <input type="text" name="loc_addr2" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Suite: <input type="text" name="loc_suite" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2"><label>Use in Report Filter? <input type="checkbox" name="loc_default"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Select a City/State/Country: <select name="loc_city">
<?php
  $q_string  = "select ct_id,ct_city,st_acronym,cn_acronym ";
  $q_string .= "from cities ";
  $q_string .= "left join inv_states on inv_states.st_id = cities.ct_state ";
  $q_string .= "left join country on country.cn_id = inv_states.st_country ";
  $q_string .= "order by ct_city,st_acronym,cn_acronym ";
  $q_cities = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_cities = mysqli_fetch_array($q_cities)) {
    print "<option value=\"" . $a_cities['ct_id'] . "\">" . $a_cities['ct_city'] . ", " . $a_cities['st_acronym'] . ", " . $a_cities['cn_acronym'] . "</option>";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Zipcode: <input type="text" name="loc_zipcode" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Site Instance (0 for Lab, 1, 2, etc): <input type="text" name="loc_instance" size="5"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Data Center Identifier: <input type="text" name="loc_identity" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Contact Info <input type="text" name="loc_contact1" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Secondary Contact Info <input type="text" name="loc_contact2" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Location Tags <input type="text" name="loc_tags" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Link to Additional Details <input type="text" name="loc_details" size="60"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
