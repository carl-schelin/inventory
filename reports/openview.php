<?php
# Script: openview.php
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

  $package = "openview.php";

  logaccess($db, $_SESSION['uid'], $package, "View openview alerts");

  if (isset($_POST['start']) || isset($_GET['start'])) {
    if (isset($_POST['start'])) {
      $formVars['start'] = clean($_POST['start'], 15);
    }
    if (isset($_GET['start'])) {
      $formVars['start'] = clean($_GET['start'], 15);
    }
  } else {
    $formVars['start'] = date('Y-m-d', strtotime("7 days ago"));
  }

  if (isset($_POST['end']) || isset($_GET['end'])) {
    if (isset($_POST['end'])) {
      $formVars['end'] = clean($_POST['end'], 15);
    }
    if (isset($_GET['end'])) {
      $formVars['end'] = clean($_GET['end'], 15);
    }
  } else {
    $formVars['end'] = date('Y-m-d', strtotime("tomorrow"));
  }

  if (isset($_POST['type'])) {
    $formVars['type'] = clean($_POST['type'], 20);
  } else {
    $formVars['type'] = 0;
  }

  if (isset($_GET['sort'])) {
    $_SESSION['sort'] = clean($_GET['sort'], 30);
  } else {
    $_SESSION['sort'] = '';
  }

  if (isset($_POST['search_a']) || isset($_GET['search_a'])) {
    if (isset($_POST['search_a'])) {
      $formVars['search_a'] = clean($_POST['search_a'], 60);
    }
    if (isset($_GET['search_a'])) {
      $formVars['search_a'] = clean($_GET['search_a'], 60);
    }
  } else {
    $formVars['search_a'] = '';
  }

  if (isset($_POST['group']) || isset($_GET['group'])) {
    if (isset($_POST['group'])) {
      $formVars['group'] = clean($_POST['group'], 20);
    }
    if (isset($_GET['group'])) {
      $formVars['group'] = clean($_GET['group'], 20);
    }
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

# if help has not been seen yet,
  if (show_Help($db, $Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Alarm Listing</title>

<style type="text/css" title="currentStyle" media="screen">
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
function delete_block( p_script_url ) {
  var answer = confirm("Delete this Block?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function attach_block(p_script_url, update) {
  var ab_form = document.block;
  var ab_url;

  ab_url  = '?update='   + update;
  ab_url += "&id="       + ab_form.id.value;

  ab_url += "&block_text="   + encode_URI(ab_form.block_text.value);

  script = document.createElement('script');
  script.src = p_script_url + ab_url;
  document.getElementsByTagName('head')[0].appendChild(script);
  show_file('openview.mysql.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>&search_a=<?php print $formVars['search_a']; ?>');
}

function clear_fields() {
  show_file('openview.mysql.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>&search_a=<?php print $formVars['search_a']; ?>');
  show_file('openview.block.php?start=<?php print $formVars['start']; ?>&end=<?php print $formVars['end']; ?>');
}

$(document).ready( function() {
  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#startdate" ).datepicker();
  $( "#enddate" ).datepicker();

});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Openview Alarms: <?php print $formVars['start']; ?> to <?php print $formVars['end']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('alarm-listing-help');">Help</a></th>
</tr>
</table>

<div id="alarm-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Start Date</strong> - The start range of the search. Starts at 00:00am</li>
  <li><strong>End Date</strong> - The end range of the search. Ends at 24:00am</li>
  <li><strong>Search Criteria</strong> - Filters the results by the entered data. Use % as a wildcard. If blank, all data in that range is displayed.</li>
</ul>

<p><strong>Note:</strong> Default date range for this report the past 30 days.</p>

<p><strong>WARNING:</strong> The amount of data can overwhelm a browser or system. Check your range. More than about 6 months will slow down your browser.</p>

</div>

</div>

<form name="block" action="openview.php" method="post">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="submit" value="Generate Listing"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Start Date <input type="text" name="start" id="startdate" value="<?php print $formVars['start']; ?>"></td>
  <td class="ui-widget-content">End Date <input type="text" name="end" id="enddate" value="<?php print $formVars['end']; ?>"></td>
  <td class="ui-widget-content">Search Criteria: <input type="text" name="search_a" size="60" value="<?php print $formVars['search_a']; ?>"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content delete"><input type="button" value="Block!" onClick="javascript:attach_block('openview.block.php', 0);">
  <td class="ui-widget-content">Block Text: <input type="text" name="block_text" size="80"></td>
</tr>
</table>

</form>

<p></p>

<span id="block_mysql"><?php print wait_Process("Please Wait"); ?></span>

<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>


</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
