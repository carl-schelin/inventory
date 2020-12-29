<?php
# Script: vulnowner.php
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

  $package = "vulnowner.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = 0;
  }

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $_SESSION['sort'] = '';
    $formVars['sort'] = '';
  }

  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product']   = 0;
  }

  if (isset($_GET['group'])) {
    $formVars['group']     = clean($_GET['group'],    10);
  } else {
    $formVars['group']     = 1;
  }

  if (isset($_GET['inwork'])) {
    $formVars['inwork']    = clean($_GET['inwork'],   10);
  } else {
    $formVars['inwork']    = 'false';
  }

  if (isset($_GET['country'])) {
    $formVars['country']   = clean($_GET['country'],  10);
  } else {
    $formVars['country']   = 0;
  }
  if (isset($_GET['state'])) {
    $formVars['state']     = clean($_GET['state'],    10);
  } else {
    $formVars['state']     = 0;
  }
  if (isset($_GET['city'])) {
    $formVars['city']      = clean($_GET['city'],     10);
  } else {
    $formVars['city']      = 0;
  }
  if (isset($_GET['location'])) {
    $formVars['location']  = clean($_GET['location'], 10);
  } else {
    $formVars['location']  = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Vulnerability Ownership</title>

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
  var answer = confirm("Delete this Association?")

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
  var af_form = document.owner;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&product="          + encode_URI(<?php print $formVars['product']; ?>);
  af_url += "&group="            + encode_URI(<?php print $formVars['group']; ?>);
  af_url += "&inwork="           + encode_URI(<?php print $formVars['inwork']; ?>);
  af_url += "&country="          + encode_URI(<?php print $formVars['country']; ?>);
  af_url += "&state="            + encode_URI(<?php print $formVars['state']; ?>);
  af_url += "&city="             + encode_URI(<?php print $formVars['city']; ?>);
  af_url += "&location="         + encode_URI(<?php print $formVars['location']; ?>);
  af_url += "&type="             + encode_URI(<?php print $formVars['type']; ?>);
  af_url += "&sort="             + encode_URI(<?php print $formVars['sort']; ?>);
  af_url += "&int_id="           + af_form.int_id.value;
  af_url += "&sec_id="           + af_form.sec_id.value;
  af_url += "&vul_group="        + af_form.vul_group.value;
  af_url += "&vul_ticket="       + encode_URI(af_form.vul_ticket.value);
  af_url += "&vul_exception="    + af_form.vul_exception.checked;
  af_url += "&vul_description="  + encode_URI(af_form.vul_description.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('vulnowner.mysql.php?update=-2<?php print "&group=" . $formVars['group'] . "&inwork=" . $formVars['inwork'] . "&product=" . $formVars['product'] . "&country=" . $formVars['country'] . "&state=" . $formVars['state'] . "&city=" . $formVars['city'] . "&location=" . $formVars['location']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="owner">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('vulnerability-hide');">Vulnerability Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('vulnerability-help');">Help</a></th>
</tr>
</table>

<div id="vulnerability-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Owner</strong> - Save any changes to this form.</li>
    <li><strong>Add Owner</strong> - Assign an Owner to this Vulnerability.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Vulnerability Form</strong>
  <ul>
    <li><strong>IP Address</strong> - The IP Address the Vulnerability was detected on.</li>
    <li><strong>Vulnerability</strong> - The discovered Vulnerability.</li>
    <li><strong>Responsible Group</strong> - Select the group that needs to resolve the Vulnerability.</li>
    <li><strong>Ticket</strong> - Enter the Ticket number that was opened to address this Vulnerability.</li>
    <li><strong>Exception</strong> - If the server will be replaced or decommissioned within the next six months, check this box. The server will go through an approval process for the Exception.</li>
    <li><strong>Details</strong> - Add any details about this Vulnerability. The Project number or expected date of decommission for example.</li>
  </ul></li>
</ul>

<p>Note: <a href="#" onclick="javascript:attach_file('vulnerability.csv.php', 0);">CSV Listing</a></p>

</div>

</div>


<div id="vulnerability-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button"                 name="refresh" value="Refresh"      onClick="javascript:attach_file('vulowner.mysql.php', -2);">
<input type="button" disabled="true" name="update"  value="Update Owner" onClick="javascript:attach_file('vulowner.mysql.php', 1);hideDiv('vulnerability-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                 name="addbtn"  value="Add Owner"    onClick="javascript:attach_file('vulowner.mysql.php', 0);">
</td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Vulnerability Form</th>
</tr>
<tr>
  <td class="ui-widget-content">IP Address: <span id="vuln_interface"></span><input type="hidden" name="int_id" value="0"></td>
  <td class="ui-widget-content" colspan="3">Vulnerability: <span id="vuln_securityid"></span><input type="hidden" name="sec_id" value="0"></td>
</tr>
<tr>
  <td class="ui-widget-content">Responsible Group: <select name="vul_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Ticket: <input type="text" name="vul_ticket" size="20"></td>
  <td class="ui-widget-content"><label>Exception? <input type="checkbox" name="vul_exception"></label></td>
  <td class="ui-widget-content"><label>Details: <input type="text" name="vul_description" size="50"></label></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"><?php print wait_Process("Loading..."); ?></span>


</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
