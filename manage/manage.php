<?php
# Script: manage.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "manage.php";

  logaccess($db, $formVars['uid'], $package, "Managing Events.");

  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product']   = 0;
  }
  if (isset($_GET['project'])) {
    $formVars['project']   = clean($_GET['project'],  10);
  } else {
    $formVars['project']   = 0;
  }
  if (isset($_GET['location'])) {
    $formVars['location']   = clean($_GET['location'],  10);
  } else {
    $formVars['location']   = 0;
  }
  if (isset($_GET["sort"])) {
    $formVars['sort']      = clean($_GET["sort"],     20);
  } else {
    $formVars['sort'] = 'none';
  }

  $passthrough = 
    "?product="  . $formVars['product'] .
    "&project="  . $formVars['project'] .
    "&location=" . $formVars['location'] .
    "&sort="     . $formVars['sort'];

  $where = '';
  if ($formVars['product'] > 0) {
    $where = "and inv_product = " . $formVars['product'] . " ";
  }
  if ($formVars['project'] > 0) {
    $where = "and inv_project = " . $formVars['project'] . " ";
  }
  if ($formVars['location'] > 0) {
    $where = "and inv_location = " . $formVars['location'] . " ";
  }

# get the totals for each problem to properly display tabs
  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where ce_priority = 1 and chk_status = 0 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $priority1 = $a_chkserver['count(chk_id)'];

  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where ce_priority = 2 and chk_status = 0 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $priority2 = $a_chkserver['count(chk_id)'];

  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where ce_priority = 3 and chk_status = 0 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $priority3 = $a_chkserver['count(chk_id)'];

  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where ce_priority = 4 and chk_status = 0 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $priority4 = $a_chkserver['count(chk_id)'];

  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where ce_priority = 5 and chk_status = 0 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $priority5 = $a_chkserver['count(chk_id)'];

  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where chk_closed != '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $closed = $a_chkserver['count(chk_id)'];

# chk_status = 1 == claimed
  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where chk_status = 1 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $claimed = $a_chkserver['count(chk_id)'];

# chk_status = 2 == pending
  $q_string  = "select count(chk_id) ";
  $q_string .= "from chkserver ";
  $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
  $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where chk_status = 2 and chk_closed = '1971-01-01 00:00:00' " . $where;
  $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_chkserver = mysqli_fetch_array($q_chkserver);
  $pending = $a_chkserver['count(chk_id)'];

# if help has not been seen yet,
  if (show_Help($db, 'manageerrors')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Managing Events</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function attach_error( p_script_url ) {
  var ae_form = document.error;
  var ae_url;

  ae_url  = "?update="        + "1";
  ae_url += "&id="            + ae_form.chk_id.value;

  ae_url += "&chk_userid="    + ae_form.chk_userid.value;
  ae_url += "&chk_status="    + ae_form.chk_status.checked;
  ae_url += "&chk_priority="  + ae_form.chk_priority.value;
  ae_url += "&chk_closed="    + ae_form.chk_closed.checked;
  ae_url += "&error_text="    + encode_URI(ae_form.error_text.value);
  ae_url += "&product="       + <?php print $formVars['product']; ?>;
  ae_url += "&project="       + <?php print $formVars['project']; ?>;
  ae_url += "&sort="          + <?php print $formVars['sort']; ?>;

  script = document.createElement('script');
  script.src = p_script_url + ae_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('manage.mysql.php<?php print $passthrough; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( "#dialogError" ).dialog({
    autoOpen: false,
    modal: true,
    height: 520,
    width:  800,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogError" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update",
        click: function() {
          attach_error('manage.mysql.php');
          $( this ).dialog( "close" );
        }
      }
    ]
  });

});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="manage">


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server Error Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<h2>Instructions</h2>

<p>This page lists all the errors generated by the chkserver script. The errors are retrieved each night and imported via the chkserver.import.php script. Priority is initially set to 2 for new errors and 5 for 
new warnings. Use this page to set specific server and error priorities. In order to change the general error priority, you'll need to use the Error Management page which changes it for all servers.</p>

<h2>Features</h2>

<ul>
  <li>The page refreshes every 5 seconds.</li>
  <li>There are 8 tabs. Priority 1 - 5, Closed, Claimed, and Pending. Tabs are only displayed when there are errors set with the priority or closed, claimed, or pending.</li>
  <li>The priorities are ordered by Service Class, 911 Call Path, Individual Priority, Error, and then Server name. <span class="ui-state-error">Highlighted errors</span> are identified as Call Path servers.</li>
  <li>The server name takes you to the edit page for that server. You might use it to update the service class if it's missing.</li>
  <li>The error displayes a dialog box where you can identify who's working on an error, if work is pending due to external issues, the individual server error priority, and a brief message explaining the status. Claimed and Pending errors will be relocated to their appropriate tabs.</li>
</ul>

<h2>Notes</h2>

<ul>
  <li>Errors fall off the report each night as conditions change.</li>
  <li>Claiming an error tells others that you're already working on that error.</li>
  <li>Pending an error indicates there is an external block or that the error will be mitigated by a server being retired for example.</li>
  <li>Headers can be used to sort the output. The default sort is appended to the requested sort.</li>
</ul>

</div>

</div>


<div id="tabs">

<ul>
<?php
  if ($priority1 > 0) {
    print "  <li><a href=\"#priority_1\">Priority 1 (<span id=\"priority1\">" . $priority1 . "</span>)</a></li>\n";
  }
  if ($priority2 > 0) {
    print "  <li><a href=\"#priority_2\">Priority 2 (<span id=\"priority2\">" . $priority2 . "</span>)</a></li>\n";
  }
  if ($priority3 > 0) {
    print "  <li><a href=\"#priority_3\">Priority 3 (<span id=\"priority3\">" . $priority3 . "</span>)</a></li>\n";
  }
  if ($priority4 > 0) {
    print "  <li><a href=\"#priority_4\">Priority 4 (<span id=\"priority4\">" . $priority4 . "</span>)</a></li>\n";
  }
  if ($priority5 > 0) {
    print "  <li><a href=\"#priority_5\">Priority 5 (<span id=\"priority5\">" . $priority5 . "</span>)</a></li>\n";
  }
  if ($closed > 0) {
    print "  <li><a href=\"#closed\">Closed (<span id=\"is_closed\">" . $closed . "</span>)</a></li>\n";
  }
  if ($claimed > 0) {
    print "  <li><a href=\"#claimed\">Claimed (<span id=\"is_claimed\">" . $claimed . "</span>)</a></li>\n";
  }
  if ($pending > 0) {
    print "  <li><a href=\"#pending\">Pending (<span id=\"is_pending\">" . $pending . "</span>)</a></li>\n";
  }
  print "</ul>\n\n";

  if ($priority1 > 0) {
    print "<div id=\"priority_1\">\n\n";
    print "<span id=\"pri1_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($priority2 > 0) {
    print "<div id=\"priority_2\">\n\n";
    print "<span id=\"pri2_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($priority3 > 0) {
    print "<div id=\"priority_3\">\n\n";
    print "<span id=\"pri3_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($priority4 > 0) {
    print "<div id=\"priority_4\">\n\n";
    print "<span id=\"pri4_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($priority5 > 0) {
    print "<div id=\"priority_5\">\n\n";
    print "<span id=\"pri5_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($closed > 0) {
    print "<div id=\"closed\">\n\n";
    print "<span id=\"closed_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($claimed > 0) {
    print "<div id=\"claimed\">\n\n";
    print "<span id=\"claimed_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
  if ($pending > 0) {
    print "<div id=\"pending\">\n\n";
    print "<span id=\"pending_mysql\">" . wait_Process('Waiting...') . "</span>\n\n";
    print "</div>\n\n\n";
  }
?>

</div>

</form>

</div>


<div id="dialogError" title="Manage Server Errors">

<form name="error">

<input type="hidden" name="chk_id" value="0">

<p>Select the information you want to copy from this server into a new server. By default, the Server Initialization and Server Provisioning steps will be copied. Additional information will be copied based on your selections. Note that none of the tasks will be marked as completed.</p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Manage <span id="error_server"></span> Errors</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">Error: <span id="error_message"></span></td>
</tr>
<tr>
  <td class="ui-widget-content">Claimed by: <select name="chk_userid">
  <option value="0">Unclaimed</option>
<?php

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "left join grouplist on grouplist.gpl_user = users.usr_id ";
  $q_string .= "where gpl_group = 1 and usr_disabled = 0 ";
  $q_string .= "order by usr_last ";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?></select></td>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_status"> Work Pending</label></td>
  <td class="ui-widget-content">Set Priority: <select name="chk_priority">
  <option value="0">Unassigned</option>
  <option value="1">Priority 1</option>
  <option value="2">Priority 2</option>
  <option value="3">Priority 3</option>
  <option value="4">Priority 4</option>
  <option value="5">Priority 5</option>
</select></td>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_closed"> Close</label></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">
<textarea id="error_text" name="error_text" cols="124" rows="12"
  onKeyDown="textCounter(document.error.error_text, document.error.remLen, 1800);"
  onKeyUp  ="textCounter(document.error.error_text, document.error.remLen, 1800);">
</textarea>
<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1800"> characters left
</td>


<inputarea columns="60" rows="4"></inputarea></td>
</tr>
</table>

</form>

</div>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
