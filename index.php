<?php
# Script: index.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . "/guest.php");

  $package = "index.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  if (isset($_GET['search'])) {
    $formVars['search'] = clean($_GET['search'], 80);
  } else {
    $formVars['search'] = '';
  }

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
<title>Server Management System</title>

<?php include($Sitepath . "/head.php"); ?>

<script language="javascript">

function attach_group( p_script_url ) {
  var ag_form = document.index;
  var ag_url;
  var ag_group;

  if (ag_form.group.value === 0) {
    ag_group = -1;
  } else {
    ag_group = ag_form.group.value;
  }

  ag_url  = '?group='     + ag_group;
  ag_url += '&product='   + ag_form.product.value;
  if (ag_form.inwork.checked == true) {
    ag_url += '&inwork='    + ag_form.inwork.checked;
  }
  if (ag_form.project.value > 0) {
    ag_url += '&project='   + ag_form.project.value;
  }
  if (ag_form.country.value > 0) {
    ag_url += '&country='   + ag_form.country.value;
  }
  if (ag_form.state.value > 0) {
    ag_url += '&state='     + ag_form.state.value;
  }
  if (ag_form.city.value > 0) {
    ag_url += '&city='      + ag_form.city.value;
  }
  if (ag_form.location.value > 0) {
    ag_url += '&location='  + ag_form.location.value;
  }
  if (ag_form.csv.checked == true) {
    ag_url += '&csv='       + ag_form.csv.checked;
  }

  script = document.createElement('script');
  script.src = p_script_url + ag_url;
  window.location.href=script.src;
}

function attach_product( p_script_url ) {
  var ap_form = document.index;
  var ap_url = '';

  if (ap_form.csv.checked == true) {
    ap_url = '?csv='       + ap_form.csv.checked;
  }

  if (ap_form.product.value > 0) {
    p_script_url = '<?php print $Reportroot; ?>/show.product.php';
    ap_url += '?id='        + ap_form.product.value;
  }

  script = document.createElement('script');
  script.src = p_script_url + ap_url;
  window.location.href=script.src;
}

function attach_type( p_script_url ) {
  var at_form = document.index;
  var at_url;
  var at_group;

  if (at_form.group.value === 0) {
    at_group = -1;
  } else {
    at_group = at_form.group.value;
  }

  at_url  = '?type='      + '-1';
  at_url += '&group='     + at_group;
  at_url += '&product='   + at_form.product.value;
  if (at_form.project.value > 0) {
    at_url += '&project='   + at_form.project.value;
  }
  if (at_form.inwork.checked == true) {
    at_url += '&inwork='    + at_form.inwork.checked;
  }
  if (at_form.country.value > 0) {
    at_url += '&country='   + at_form.country.value;
  }
  if (at_form.state.value > 0) {
    at_url += '&state='     + at_form.state.value;
  }
  if (at_form.city.value > 0) {
    at_url += '&city='      + at_form.city.value;
  }
  if (at_form.location.value > 0) {
    at_url += '&location='  + at_form.location.value;
  }
  if (at_form.csv.checked == true) {
    at_url += '&csv='       + at_form.csv.checked;
  }

  script = document.createElement('script');
  script.src = p_script_url + at_url;
  window.location.href=script.src;
}

function attach_interface( p_script_url ) {
  var ai_form = document.index;
  var ai_url;
  var ai_group;

  if (ai_form.group.value === 0) {
    ai_group = -1;
  } else {
    ai_group = ai_form.group.value;
  }

  ai_url  = '?group='      + ai_group;
  ai_url += '&product='    + ai_form.product.value;
  if (ai.form.project.value > 0) {
    ai_url += '&project='    + ai_form.project.value;
  }
  ai_url += '&active='     + ai_form.active.checked;
  ai_url += '&ip6='        + ai_form.ip6.checked;
  ai_url += '&loopback='   + ai_form.loopback.checked;
  ai_url += '&virtual='    + ai_form.virtual.checked;
  if (ai_form.inwork.checked == true) {
    ai_url += '&inwork='     + ai_form.inwork.checked;
  }
  if (ai_form.country.value > 0) {
    ai_url += '&country='    + ai_form.country.value;
  }
  if (ai_form.state.value > 0) {
    ai_url += '&state='      + ai_form.state.value;
  }
  if (ai_form.city.value > 0) {
    ai_url += '&city='       + ai_form.city.value;
  }
  if (ai_form.location.value > 0) {
    ai_url += '&location='   + ai_form.location.value;
  }
  if (ai_form.csv.checked == true) {
    ai_url += '&csv='        + ai_form.csv.checked;
  }

  script = document.createElement('script');
  script.src = p_script_url + ai_url;
  window.location.href=script.src;
}

function attach_search( p_script_url ) {
  var as_form = document.index;
  var as_url;

  as_url  = '?search_by='     + as_form.search_by.value;
  as_url += '&search_for='    + encodeURI(as_form.search_for.value);
  as_url += '&retired='       + as_form.retired.checked;
  as_url += '&csv='           + as_form.csvoutput.checked;

  script = document.createElement('script');
  script.src = p_script_url + as_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_walkthrough( p_script_url ) {
  var aw_form = document.index;
  var aw_url;
  var aw_group;

  if (aw_form.group.value === 0) {
    aw_group = -1;
  } else {
    aw_group = aw_form.group.value;
  }

  aw_url  = '?group='      + aw_group;
  aw_url += '&product='    + aw_form.product.value;
  if (aw_form.project.value > 0) {
    aw_url += '&project='    + aw_form.project.value;
  }
  if (aw_form.inwork.checked == true) {
    aw_url += '&inwork='     + aw_form.inwork.checked;
  }
  if (aw_form.country.value > 0) {
    aw_url += '&country='    + aw_form.country.value;
  }
  if (aw_form.state.value > 0) {
    aw_url += '&state='      + aw_form.state.value;
  }
  if (aw_form.city.value > 0) {
    aw_url += '&city='       + aw_form.city.value;
  }
  if (aw_form.location.value > 0) {
    aw_url += '&location='   + aw_form.location.value;
  }
  if (aw_form.csv.checked == true) {
    aw_url += '&csv='        + aw_form.csv.checked;
  }

  script = document.createElement('script');
  script.src = p_script_url + aw_url;
  window.location.href=script.src;
}

function attach_location( p_script_url ) {
  var al_form = document.index;
  var al_url;

  if (al_form.country.value > 0) {
    al_url  = '?country='     + al_form.country.value;
  }
  if (al_form.state.value > 0) {
    al_url += '&state='       + al_form.state.value;
  }
  if (al_form.city.value > 0) {
    al_url += '&city='        + al_form.city.value;
  }
  if (al_form.location.value > 0) {
    al_url += '&location='    + al_form.location.value;
  }
  if (al_form.csv.checked == true) {
    al_url += '&csv='         + al_form.csv.checked;
  }

  script = document.createElement('script');
  script.src = p_script_url + al_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_tag( p_script_url ) {
  var at_form = document.index;
  var at_url;

  at_url  = '&product='    + at_form.product.value;
  if (at_form.project.value > 0) {
    at_url  = '&project='    + at_form.project.value;
  }
  if (at_form.inwork.checked == true) {
    at_url += '&inwork='     + at_form.inwork.checked;
  }
  if (at_form.country.value > 0) {
    at_url += '&country='    + at_form.country.value;
  }
  if (at_form.state.value > 0) {
    at_url += '&state='      + at_form.state.value;
  }
  if (at_form.city.value > 0) {
    at_url += '&city='       + at_form.city.value;
  }
  if (at_form.location.value > 0) {
    at_url += '&location='   + at_form.location.value;
  }
  if (at_form.csv.checked == true) {
    at_url += '&csv='        + at_form.csv.checked;
  }

  script = document.createElement('script');
  script.src = p_script_url + at_url;
  window.location.href=script.src;
}

function clear_fields() {
<?php
  if ($formVars['search'] != '') {
    print "  show_file('" . $Reportroot . "/search.php?search_for=" . $formVars['search'] . "');\n";
  }
?>
}

function submit_handler() {
    return (false);
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#search-tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#search-input' ).on("keypress", function(e) {
    if (e.keyCode == 13) {
      attach_search('<?php print $Reportroot; ?>/search.php');
      return false;
    }
  });

});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
 <li><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></li>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="help" style="<?php print $display; ?>">

<div class="main">

<div class="main-help ui-widget-content">

<h2>Welcome to Inventory 3.0!</h2>

<p>Inventory has been updated as a result of several years of working with the tool to see where improvements 
might be made, years of constant learning about new technologies and new methods, and of course the input of 
you, the user of this system. See the 'What's New' link under the Account menu for details.</p>

<p><strong>Report Filter</strong> - The filter is used to reduce the size of the report being requested. Filters 
have been implemented in some part to most of the reports available below. In some case, a filter option didn't 
seem to fit, such as the Location filter for the Products and Services listing.</p>

<ul>
  <li><strong>Filter on Group</strong> - By default, the main group you belong to is selected. For a list of everything, select the <strong>All Groups</strong> option from this drop down menu.</li>
  <li><strong>Filter on Product or Service</strong> - By default <strong>All Products and Services</strong> is selected. Use this drop down menu to select a subset of systems based on product or service.</li>
  <li><strong>Filter on Location</strong> - You can list devices by Country, drill down to further reduce your search by State and City and finally by Data Center. There is a 
selection of commonly selected Data Centers in the Data Center menu. By default you'll get a report for all locations.</li>
  <li><strong>Filter on Devices 'In Work'</strong> - This further reduces the list to devices currently in the pipeline to be deployed into production.</li>
  <li><strong>Fromat Output to 'CSV'</strong> - This changes the output from a web display to a CSV output for import into a spreadsheet or other manipulation.</li>
</ul>


<p><strong>Inventory Reports</strong> - These reports provide listings of devices filtered by the selected Filters.</p>

<p><strong>General Reports</strong> - These reports are looking for more specific information.</p>

<p><strong>Group Reports</strong> - Reports as requested by the listed group. In general while they can be selected for other groups, the information may not be relevant (such as the Centrify Report).</p>

<p><strong>Archived Reports</strong> - Old reports that have been superceeded but might still be useful or interesting.</p>

<p><strong>Tag Cloud</strong> - You can create custom listings of devices by adding a Tag to a device. This page shows the current list of tags.</p>

<p><strong>Search</strong> - If you're looking for something, a server, devices in a specific location, etc, the Search tab might be for you.</p>

<p style="text-align: center;"><a href="javascript:;" onmousedown="toggleDiv('help-more');">How do I send an email to the Inventory?</a></p>

<div id="help-more" style="<?php print $display; ?>">

<p>In addition, you can send a query to the database via email. From the Help message:</p>

<p>To: inventory@<?php print $Sitehttp; ?>
<br>Subject: [{devicename} | {product} | help | active | products | {blank}] [keyword]</p>

<p>The Subject line consists of up to two keywords. The first can be one of five options;</p>

<ul>
  <li><strong>{blank} or active</strong> - If the Subject line is empty or contains 'active', a list of all active devices will be returned via e-mail.</li>
  <li><strong>{devicename}</strong> - An e-mail will be returned containing information about the identified device. If it's a partial name then a partial listing of devices will be returned.</li>
  <li><strong>{product}</strong> - An e-mail will be returned containing a list of all devices assigned to this Product or Service</li>
  <li><strong>products</strong> - A list of all Intrado products and services will be returned. <strong>NOTE:</strong> Replace spaces with underscores for any products with more than one word.</li>
  <li><strong>help</strong> - An e-mail will be returned with this message.</li>
</ul>

<p>The second keyword describes what information you want to retrieve. This only works if the first keyword is the name of a device. Note that only the first letter of the keyword is necessary to retrieve the requested information.</p>

<ul>
  <li><strong>{blank}</strong> - An e-mail will be returned containing basic details about the requested device.
  <li><strong>*/all</strong> - An e-mail will be returned containing details from all the following keywords.
  <li><strong>hardware</strong> - An e-mail will be returned containing minimal details plus a list of the hardware.
  <li><strong>filesystems</strong> - An e-mail will be returned containing minimal details plus a list of the filesystems.
  <li><strong>software</strong> - An e-mail will be returned containing minimal details plus a list of the installed software, not including the list of installed packages.
  <li><strong>network</strong> - An e-mail will be returned containing minimal details plus a list of the active network interfaces.
  <li><strong>route/routing</strong> - An e-mail will be returned containing minimal details plus a list of the baseline routes.
  <li><strong>issues</strong> - An e-mail will be returned containing minimal details plus all issues for this server.
</ul>

</div>

</div>

</div>

</div>

<form name="index" onsubmit="submit_handler();" method="GET">

<div class="main">

<div class="main-help ui-widget-content">

<p><strong>Report Filter</strong></p>

<ul>
  <li>Filter on Group: <select name="group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_id = " . $formVars['group'] . " ";
  $q_string .= "order by grp_name";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_groups = mysqli_fetch_array($q_groups);

  print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";

  if ($formVars['group'] == 0) {
    print "<option selected value=\"-1\">All Groups</option>\n";
  } else {
    print "<option value=\"-1\">All Groups</option>\n";
  }

  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 and grp_id != " . $formVars['group'] . " ";
  $q_string .= "order by grp_name";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups ['grp_name'] . "</option>\n";
  }
?>
</select></li>
  <li>Filter on Product or Service: <select name="product" onchange="show_file('index.options.php?product=' + document.index.product.value);">
<option value="0">All Products and Services</option>
<option value="-1">Unassigned</option>
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name";
  $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_products = mysqli_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
  }
?>
</select> By Project: <select name="project">
<option value="0">All Servers</option>
<?php

?>
</select></li>
  <li>Filter on Location: 

<select name="country" onchange="attach_location('index.mysql.php');">
<option value="0">Country</option>
<?php
  $q_string = "select cn_id,cn_country "
            . "from country "
            . "where cn_acronym = 'US' ";
  $q_country = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_country = mysqli_fetch_array($q_country);
  print "<option value=\"" . $a_country['cn_id'] . "\">" . $a_country['cn_country'] . "</option>";

  $q_string = "select cn_id,cn_country "
            . "from country "
            . "where cn_acronym != 'US' "
            . "order by cn_country ";
  $q_country = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_country = mysqli_fetch_array($q_country)) {
    print "<option value=\"" . $a_country['cn_id'] . "\">" . $a_country['cn_country'] . "</option>";
  }
?>
</select>

<select disabled name="state" onchange="attach_location('index.mysql.php');">
<option value="0">State</option>
</select>

<select disabled name="city" onchange="attach_location('index.mysql.php');">
<option value="0">City</option>
</select>

<select name="location" onchange="attach_location('index.mysql.php');">
<option value="0">Data Center</option>
<?php
  $q_string  = "select loc_id,loc_name,loc_west ";
  $q_string .= "from locations ";
  $q_string .= "where loc_type = 1 ";
  $q_string .= "order by loc_name ";
  $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_locations = mysqli_fetch_array($q_locations)) {
    print "<option value=\"" . $a_locations['loc_id'] . "\">" . $a_locations['loc_name'] . " (" . $a_locations['loc_west'] . ")</option>";
  }
?>
</select>

</li>
  <li><input type="checkbox" name="inwork"> Filter on Servers 'In Work'</li>
  <li><input type="checkbox" name="csv"> Format Output to 'CSV'</li>
</ul>

</div>

</div>


<div id="main">

<div id="tabs">

<ul>
  <li><a href="#inventory">Inventory Reports</a></li>
  <li><a href="#general">General Reports</a></li>
  <li><a href="#group">Group Reports</a></li>
  <li><a href="#security">Security Reports</a></li>
  <li><a href="#lifecycle">Life-Cycle Reports</a></li>
  <li><a href="#admin">Administrative Reports</a></li>
  <li><a href="#archived">Archived Reports</a></li>
  <li><a href="#tagcloud">Tag Cloud</a></li>
  <li><a href="#search">Search</a></li>
</ul>


<div id="inventory">

<p><strong>Inventory Reports</strong></p>
<ul>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/inventory.php');">Server Listing</a> - Lists all in work and live devices based on the filtering criteria.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Invroot; ?>/inventory.php');">Tabular Server Listing</a> - Lists all in work and live devices in a tabular format.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/locations.php');">Server Location Listing</a> - Lists all in work and live devices and their locations based on the filtering criteria.</li>
  <li><a href="javascript:;" onClick="javascript:attach_product('<?php print $Reportroot; ?>/products.php');">Product and Service Map</a> - List of all installed products and services. Selecting a Product or Service from the filters will bring up that product or service page.</li>
  <li><a href="javascript:;" onClick="javascript:attach_interface('<?php print $Reportroot; ?>/interfaces.php');">Interface Listing</a>: <input type="checkbox" checked name="active"> Active Interfaces <input type="checkbox" name="ip6"> IP6 Interfaces <input type="checkbox" name="loopback"> Loopback Interfaces <input type="checkbox" checked name="virtual"> Virtual Interfaces</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/hardware.php');">Hardware Listing</a> - Shows all hardware for the group of devices.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/software.php');">Software Listing</a> - Shows all software for the group of devices.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/firewall.php');">Firewall Rules</a> - Shows firewall rule listing.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/routing.php');">Routing Table</a> - Shows all routes.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/filesystems.php');">Filesystem Listing</a> - Shows filesystems as owned the the filtered group.</li>
</ul>

</div>


<div id="general">

<p><strong>General Reports</strong></p>

<ul>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/incident.php');">Incident Management Listing</a> - All production devices here.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/serverstats.php');">Device Growth Chart</a> - By year, by month, and graphically.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/contracts.php');">Lynda's Support Contract Report</a> - Basically duplicated Lynda Lilly's spreadsheet for what's in the Inventory.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/support.contract.php');">Support Contract</a> - Shows the support details for your group's Active devices.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/licenses.php');">Software Licenses</a> - Shows all software and license information.</li>
<?php
    if (check_userlevel($db, $AL_Admin)) {
?>
  <li><a href="<?php print $Reportroot; ?>/tags.php">View all Tags</a> - List of all Tags assigned in the system.</li>
<?php
    }
?>
  <li><a href="<?php print $Reportroot; ?>/hostname.php">Hostname Encode/Decode</a> - Build hostname here or figure out what a hostname means.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/physical.php');">Show all Physical Systems</a> - Lists every system where the MAC is not 00:50:56 (VMWare).</li>
  <li><a href="<?php print $Reportroot; ?>/changelog.php">Changelog by Server or Service</a> - List all changes for servers and services.</li>
  <li><a href="<?php print $Reportroot; ?>/changelog.report.php">Changelog by Month and Year</a> - List all changes for a month. Default is July 2016. Add year and month on the URL to change reports (see Help).</li>
</ul>

</div>


<div id="group">

<p><strong>Group Reports</strong></p>

<ul>
  <li><strong>Data Base Admins</strong>
  <ul>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/dba.report.php');">DBA Report on RDBMS, Function, and IP</a></li>
  </ul></li>
  <li><strong>Unix Admins</strong>
  <ul>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/morning.report.php');">Morning Report</a> - Shows the performance graphs, filtered log files, and audit information for the group's devices. Filters only.</li>
    <li><a href="javascript:;" onClick="javascript:attach_walkthrough('<?php print $Reportroot; ?>/walkthrough.php');">Data Center Walkthroughs</a> - Select a Location to review and then print a walkthrough form.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/centrify.php');">Centrify Server Listing</a></li>
    <li>Monitoring<ul>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/monitoring.php');">Monitoring Report</a> - Togglable list of selected systems for Openview and Nagios flags.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/monitor.php');">Openview Monitoring Report</a> - Provides a togglablelist of all servers being monitored by Openview.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/openview.report.php');">Openview Alarm Report</a> - Provides a year/month count of alarms received.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/openview.php');">OpenView Alarm Listing</a> - List of alarms in the database for the default 2 week period.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/ovmessages.php');">OpenView Health</a> - List of servers where an alarm has not been received in the past 5 days.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/monitorvers.php');">OpenView Version Listing</a> - List of servers and the version of Openview installed.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/ovstatus.php');">OpenView Notification Status</a> - List of servers and a count of alarms per server since 2009.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/ovpolicy.php');">OpenView Policy Listing</a> - List of servers and the associated policies.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/status.php');">Current Monitoring Status</a> - List of servers and their current status.</li>
    </li></ul>
<?php
  if (check_grouplevel($db, $GRP_Unix)) {
?>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Bulkroot; ?>/bulkedit.php');">Bulk Editor</a> - Provides a spreadsheet like view of a selected group of servers for editing.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Manageroot; ?>/errors.php');">Manage Error Priorities</a> - Manage the priority level of the server error messages.</li>
<?php
  }
?>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Manageroot; ?>/manage.php');">Manage Server Errors</a> - Review and address individual server errors from the chkserver script.</li>
  </ul></li>
  <li><strong>Kubernetes</strong>
  <ul>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Kubernetesroot; ?>/apigroups.php');">Manage apiGroups</a> - Manage all the apiGroups that are part of a Kubernetes cluster.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Kubernetesroot; ?>/resources.php');">Manage resources</a> - Manage all the resources that are part of a Kubernetes cluster.</li>
    <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Kubernetesroot; ?>/rights.php');">Manage Clusterrole Rights</a> - Manage the rights associated wth clusterroles in Kubernetes.</li>
  </ul></li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Handoffroot; ?>/handoff.php');">Shift/On-Call Transition Report</a></li>
  <li><a href="<?php print $Reportroot; ?>/esxlisting.php">Listing of ESX hosts and a count of Guests</a></li>
</ul>

</div>


<div id="security">

<p><strong>Security Management</strong></p>

<ul>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/tenable.php');">SecurityCenter</a> - IP Ranges for Security Center Asset Lists.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Securityroot; ?>/vulnerability.php');">Vulnerability Review</a> - Ticket management report. Assign vulnerability ownership, ticket numbers, unassigned vulnerabilities.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Securityroot; ?>/scanreport.php');">Scan Report</a> - Breakdown of the vulnerabilities by team and type.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Securityroot; ?>/servers.php');">Report on Vulnerabilities</a> - Lists vulnerabilities and includes when it arrived in the Inventory report.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Securityroot; ?>/server.report.php');">List of IPs, IP names, and count of vulnerabilities. If no interface was scanned, it's called out.</a></li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Securityroot; ?>/notscanned.php');">IPs Not Scanned</a> - List of IPs that have no scan results.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Securityroot; ?>/vulnreport.php');">List of Projects/Servers And Vulnerability Count</a> - If you select a Project in the Filters, a list of servers will be displayed.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/bigfix.php');">List of Servers being Patched by BigFix by date</a> - Note Group, Product, and Project filters work on this report.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/patches.php');">List of Patches being applied for BigFix by date</a> - Note only the Group filter works on this report.</li>
</ul>

</div>


<div id="lifecycle">

<p><strong>Life-Cycle Management</strong></p>

<ul>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/response.php');">Hardware Response Levels</a> - Report on the physical hardware and support contract details.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/dell.php');">Dell Hardware End-of-Life</a> - Report on the end of life date for all Dell Hardware.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/endoflife.php');">Operating System End-of-Life</a> - Report on the end of life date for all Operating Systems.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/compiled.eol.php');">Hardware/Software End-of-Life</a> - Report on the end of life date for all Operating Systems and Hardware.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/compiled.class.php');">Hardware/Software End-of-Life by Service Class</a> - Report on the end of life date for all Operating Systems and Hardware order by Service Class.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/eolreport.php');">End-of-Life Report by Project</a> - Report on the end of life date compiled for all Projects.</li>
</ul>

</div>


<div id="admin">

<p><strong>Administrative Reports</strong></p>

<ul>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Sanityroot; ?>/inventory.hardware.php');">Custodian not the same as the owner of all the Hardware</a> - Should be the same.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Sanityroot; ?>/inventory.software.php');">Custodian not the same as the owner of the OS</a> - Should be the same.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Sanityroot; ?>/product.hardware.php');">All hardware with no Product or Server identified.</a></li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Sanityroot; ?>/product.software.php');">All software with no Product or Server identified.</a></li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Sanityroot; ?>/inventory.duplicates.php');">Display all servers with duplicate entries.</a></li>
</ul>

</div>


<div id="archived">

<p><strong>Archived Reports</strong></p>

<ul>
  <li><a href="javascript:;" onClick="javascript:attach_type('<?php print $Reportroot; ?>/inventory.php');">Complete Device Listing</a> - Includes retired/destroyed devices and information on old installations.</li>
  <li><a href="javascript:;" onClick="javascript:attach_group('<?php print $Reportroot; ?>/rrdtool.php');">Performance View of Active Servers.</a> - RRDTool graphs retrieved each morning before 7am Mountain.</li>
</ul>

</div>


<div id="tagcloud">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Tag Cloud</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help-tagcloud');">Help</a></th>
</tr>
</table>

<div id="help-tagcloud" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Tags</strong>
  <ul>
    <li><strong>Private Tags</strong> - Shows tags that only you can manipulate. These tags are only visible to you so they let you create personalized device lists.</li>
    <li><strong>Group Tags</strong> - Shows group tags manageable by your group. These tags are visible by your group. They are also added to the listing for each team. External scripts may be run using these tags.</li>
    <li><strong>Public Tags</strong> - Tags that are viewable by all users of the Inventory software. These tags may be useful for grouping like systems that may cross projects. Use the Project listing page for single project device lists.</li>
  </ul></li>
</ul>

</div>

</div>

<div class="main ui-widget-content">

<t4>Private Tags</t4>

<p>
<?php
  $q_string  = "select tag_name,count(tag_name) ";
  $q_string .= "from tags ";
  $q_string .= "where tag_view = 0 and tag_owner = " . $formVars['uid'] . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"javascript:;\" onClick=\"javascript:attach_tag('" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "&type=0');\">";
    $linkend   = "</a>";

    print $linkstart . $a_tags['tag_name'] . " (" . $a_tags['count(tag_name)'] . ")" . $linkend . "&nbsp;&nbsp;";
  }
?>
</p>

</div>

<div class="main ui-widget-content">

<t4>Group Tags</t4>

<p>
<?php
  $q_string  = "select tag_name,count(tag_name) ";
  $q_string .= "from tags ";
  $q_string .= "where tag_view = 1 and tag_group = " . $formVars['group'] . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"javascript:;\" onClick=\"javascript:attach_tag('" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "&type=1');\">";
    $linkend   = "</a>";

    print $linkstart . $a_tags['tag_name'] . " (" . $a_tags['count(tag_name)'] . ")" . $linkend . "&nbsp;&nbsp;";
  }
?>
</p>

</div>

<div class="main ui-widget-content">

<t4>Public Tags</t4>

<p>
<?php
  $q_string  = "select tag_name,count(tag_name) ";
  $q_string .= "from tags ";
  $q_string .= "where tag_view = 2 ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"javascript:;\" onClick=\"javascript:attach_tag('" . $Reportroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "&type=2');\">";
    $linkend   = "</a>";

    print $linkstart . $a_tags['tag_name'] . " (" . $a_tags['count(tag_name)'] . ")" . $linkend . "&nbsp;&nbsp;";
  }
?>
</p>

</div>

</div>


<div id="search">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Search Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help-search');">Help</a></th>
</tr>
</table>

<div id="help-search" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Select Search Field</strong> - Select the areas you wish to search. This will reduce the number of results and speed up the response. In 
addition, you can perform a Location specific search and then a Server Name search. As long as you select individual searchs vs selecting all, the 
various tabs will maintain the searched data.</p>

<p><strong>Search Criteria</strong> - Enter in the text you want to search for. Don't enter any wild cards, the search will add them for you.
You can enter multiple servers by separating them with a space. Example: <strong><u>server1 server2 server3</u></strong> or even parts of 
names such as <strong><u>kube knode lvfui neap</u></strong> although you'll get all sites for something like ESINet.</p>

<ul>
  <li><strong>Device Details</strong> - Searches against server name, interface name, IP address, City, or State.</li>
  <li><strong>IP Addresses</strong> - Searches against server name, IP address, City, or State.</li>
  <li><strong>Software</strong> - Searches against server name, Software: vendor, name, or type, City, or State.</li>
  <li><strong>Hardware</strong> - Searches against server name, Hardware: vendor, name, or type, City, or State.</li>
  <li><strong>Asset/Serial/Service Tag</strong> - Searches against server name, asset tag, serial number, service tag number, City, or State.</li>
  <li><strong>Locations</strong> - Searches against server name, location description, City, State acronym, Country acronym, or Site type.</li>
  <li><strong>Users</strong> - Searches against the User Name.</li>
  <li><strong>Packages</strong> - Searches against Package name.</li>
</ul>

<p><strong>Search</strong> - Click the button when ready or hit Enter. A table will be displayed with the search results.</p>

<p><strong>Note:</strong> - In the <strong>Device Details</strong> search tables, clicking on the Product or Project will bring up the report displaying 
all servers, software, and changelog entries for that <strong>Product</strong>.</p>

<p><strong>Note:</strong> - In the <strong>Software</strong> and <strong>Hardware</strong> search tables, clicking on the Vendor, Software/Model, 
or Type will restart the search based on the exact text in that field. For example you can enter 'Red' in the search box to bring up everything with 
the word 'Red' in it (not case sensitive). Further clicking on 'Red Hat' in the Vendor column will restart the search and return just systems 
associated with 'Red Hat'.</p>

<p><strong>Note:</strong> - In the <strong>Location</strong> search table, clicking on the Data Center, City, State, or Country will restart the 
search based on the exact text in that field. For example you can enter 'TX' in the search box to bring up everything with the word 'TX' in it (not 
case sensitive). Further clicking on 'Richardson' will restart the search and return just systems in the city of 'Richardson'.</p>

<p><strong>Note:</strong> - In the <strong>Package</strong> search table, clicking on the Package name will restart the search based on the exact text 
in that field. For example you can enter 'apache' in the search box to bring up every package with the word 'apache' in it (not case sensitive). Further 
clicking on 'pcp-pmda-apache-3.10.6-2.el7.x86_64' will restart the search and return just systems that have that exact package installed.</p>

<p>In all search tabs, clicking on any other links will take you to the detail record for the selected server.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Search Form</th>
</tr>
<tr>
  <td class="button ui-widget-content"><input type="button" name="search_addbtn" value="Search" onClick="javascript:attach_search('<?php print $Reportroot; ?>/search.php');"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Select Search Field: <select name="search_by">
<option value="0">All Fields</option>
<option selected="true" value="1">Server Names</option>
<option value="2">IP Addresses</option>
<option value="3">Software</option>
<option value="4">Hardware</option>
<option value="5">Asset/Serial/Service Tag</option>
<option value="6">Locations</option>
<option value="7">Users</option>
<option value="8">Packages</option>
</select></td>
  <td class="ui-widget-content"><label>Include Retired Servers? <input type="checkbox" name="retired"><label></td>
  <td class="ui-widget-content"><label>CSV Output? <input type="checkbox" name="csvoutput"></label></td>
  <td class="ui-widget-content">Search Criteria: <input type="text" id="search-input" name="search_for" size="80" value="<?php print $formVars['search']; ?>"></td>
</tr>
</table>

<p></p>

<div id="search-tabs">

<ul>
  <li><a href="#servername">Device Details</a></li>
  <li><a href="#ipaddr">IP Addresses</a></li>
  <li><a href="#software">Software</a></li>
  <li><a href="#hardware">Hardware</a></li>
  <li><a href="#asset">Asset</a></li>
  <li><a href="#location">Location</a></li>
  <li><a href="#users">Users</a></li>
  <li><a href="#packages">Packages</a></li>
</ul>


<div id="servername">

<span id="server_search_mysql"></span>

</div>


<div id="ipaddr">

<span id="address_search_mysql"></span>

</div>


<div id="software">

<span id="software_search_mysql"></span>

</div>


<div id="hardware">

<span id="hardware_search_mysql"></span>

</div>


<div id="asset">

<span id="asset_search_mysql"></span>

</div>


<div id="location">

<span id="location_search_mysql"></span>

</div>


<div id="users">

<span id="user_search_mysql"></span>

</div>


<div id="packages">

<span id="packages_search_mysql"></span>

</div>


</div>

</div>

</div>

</div>

</form>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
