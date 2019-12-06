<?php
# Script: hostname.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "hostname.php";

  logaccess($formVars['uid'], $package, "Decoding Hostname.");

  if (isset($_GET['decode'])) {
    $formVars['decode'] = clean($_GET['decode'], 20);
  } else {
    $formVars['decode'] = '';
  }

# if help has not been seen yet,
  if (show_Help($Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Hostname Decoder</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function check_encode() {
  var ce_form = document.hostname;
  var ce_url;

  ce_url  = '?location='   + ce_form.location.value;
  ce_url += '&zone='       + ce_form.zone.value;
  ce_url += "&device="     + ce_form.device.value;
  ce_url += "&service="    + ce_form.service.value;
  ce_url += "&freeform="   + encode_URI(ce_form.freeform.value);

  script = document.createElement('script');
  script.src = 'hostname.encode.php' + ce_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function check_decode() {
  show_file('hostname.decode.php?server=' + document.hostname.hostname.value);
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content" onLoad="check_decode();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="hostname">

<div id="tabs">

<ul>
  <li><a href="#encode">Encode Hostname</a></li>
  <li><a href="#decode">Decode Hostname</a></li>
</ul>

<div id="encode">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hostname Encoder</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('encode-help');">Help</a></th>
</tr>
</table>

<div id="encode-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Data Center</strong> - This list is populated from the Applications,Location Manager form when a site is identified as a Data Center site.</li>
  <li><strong>Network Zone</strong> - Short list of the known network zones.</li>
  <li><strong>Device Type</strong> - This list is populated from the Database,Device Types form. If an <strong>Infrastructure</strong> device is selected, the Product or Service list will be disabled.</li>
  <li><strong>Product or Service</strong> - This list is populated from the Database,Products and Services form. This is disabled and not used if an <strong>Infrastructure</strong> device was selected.</li>
  <li><strong>Device Instance</strong> - This field lets you identify the instance of this device and can consist of any set of four or six characters (if an <strong>Infrastructure</strong> device).</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td style="font-size:120%" class="ui-state-default">Encoded Hostname: <span id="encodedhostname"></span></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Select the Data Center where the server will be located: <select name="location" onclick="check_encode();" onblur="check_encode();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select loc_id,loc_name,ct_clli,loc_instance ";
  $q_string .= "from locations ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "where loc_type = 1 and ct_clli != '' ";
  $q_string .= "order by ct_clli,loc_instance ";
  $q_locations = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_locations = mysql_fetch_array($q_locations)) {
    print "<option value=\"" . $a_locations['loc_id'] . "\">" . $a_locations['ct_clli'] . $a_locations['loc_instance'] . " (" . $a_locations['loc_name'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Select the Network Zone: <select name="zone" onclick="check_encode();" onblur="check_encode();">
<option value="0">Unassigned</option>
<option value="1">C (Enterprise/Corporate)</option>
<option value="2">E (E911)</option>
<option value="3">D (DMZ)</option>
<option value="4">A (Agnostic/Cross Zone)</option>
<option value="5">M (IDM Zone)</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Select the Unique Device Type: <select name="device" onclick="check_encode();" onblur="check_encode();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select dev_id,dev_type,dev_description ";
  $q_string .= "from device ";
  $q_string .= "order by dev_type ";
  $q_device = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_device = mysql_fetch_array($q_device)) {
    print "<option value=\"" . $a_device['dev_id'] . "\">" . $a_device['dev_type'] . " (" . $a_device['dev_description'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Select the Product or Service: <select name="service" id="service" onclick="check_encode();" onblur="check_encode();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select prod_id,prod_name,prod_code ";
  $q_string .= "from products ";
  $q_string .= "where prod_code != '' ";
  $q_string .= "order by prod_code ";
  $q_products = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_products = mysql_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_code'] . " (" . $a_products['prod_name'] . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Enter up to <strong><span id="characters">four</span></strong> characters to uniquely identify this Device Instance: <input type="text" name="freeform" onkeyup="check_encode();"></td>
</tr>
</table>

</div>


<div id="decode">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hostname Decoder</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('decode-help');">Help</a></th>
</tr>
</table>

<div id="decode-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Entering in the hostname will break it down to provide details. In addition, if the server is currently in the Inventory, a link will be provided so you can view the entry.</p>

<p>You can pass the servername to be decoded on the URL as a link by adding ?decode=[servername]#decode</p>

<p><strong>Note:</strong> Currently only the 2015 Internal Standard works.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Enter the Host Name to Decode: <input type="text" name="hostname" value="<?php print $formVars['decode']; ?>" onkeyup="check_decode();"> <span id="gohere"></span></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">2015 Internal Standard</th>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Example:</strong> lnmt0duwslfui1</td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Data Center Location</strong>: <span id="i15location"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Data Center Instance</strong>: <span id="i15instance"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Network Zone</strong>: <span id="i15zone"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Device Type</strong>: <span id="i15device"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Product or Service</strong>: <span id="i15service"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Device Instance</strong>: <span id="i15freeform"></span></td>
</tr>
</table>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">2015 Customer Standard</th>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Example:</strong> lnmtcopdrtr1</td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Location</strong>: <span id="c15location"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>State</strong>: <span id="c15state"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Location Type</strong>: <span id="c15type"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Device Type</strong>: <span id="c15device"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Device Instance</strong>: <span id="c15instance"></span></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">2008 Standard</th>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Example:</strong> lnmtcodcad10</td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Data Center Location</strong>: <span id="08location"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>State</strong>: <span id="08state"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Site Type</strong>: <span id="08type"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Product or Service</strong>: <span id="08device"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Device Instance</strong>: <span id="08instance"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Interface</strong>: <span id="08interface"></span></td>
</tr>
</table>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Standard</th>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Example:</strong> incojs01</td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Location</strong>: <span id="location"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Company</strong>: <span id="company"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>State</strong>: <span id="state"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Product or Service</strong>: <span id="product"></span></td>
</tr>
<tr>
  <td class="ui-widget-content"><strong>Device Instance</strong>: <span id="instance"></span></td>
</tr>
</table>

</div>

</div>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
