<?php
# Script: patches.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "patches.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the Bigfix patch listing.");

  if (isset($_GET['date'])) {
    $formVars['scheduled'] = clean($_GET['date'], 10);
  } else {
    $formVars['scheduled'] = date('Y-m-d');
  }

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

  $current_date = $formVars['scheduled'];
  $formVars['back'] = date('Y-m-d', strtotime($current_date . ' -1 day'));
  $formVars['forward'] = date('Y-m-d', strtotime($current_date . ' +1 day'));

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
<title>BigFix Patch Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {

  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#scheduled" ).datepicker();

  $( "#dialogBigFix" ).dialog({
    autoOpen: false,
    modal: true,
    height: 600,
    width: 1200,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogBigFix" ).hide();
    },
    buttons: [
      {
        text: "Close",
        click: function() {
          $( this ).dialog( "close" );
        }
      }
    ]
  });

});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>


<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">BigFix Patch Form</th>
</tr>
  <td class="ui-widget-content button"><a href="<?php print $Reportroot . "/patches.php?group=" . $formVars['group'] . "&date=" . $formVars['back']; ?>">Back One Day</a> Select Patch Date: <input type="date" name="big_scheduled" id="scheduled" size="10" value="<?php print $formVars['scheduled']; ?>"><a href="<?php print $Reportroot . "/patches.php?group=" . $formVars['group'] . "&date=" . $formVars['forward']; ?>">Forward One Day</a></td>
</tr>
</table>

</div>

<div id="main">

<?php

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Patch Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the servers that are scheduled to be patched and possibly rebooted on " . $formVars['scheduled'] . ".</p>\n";

  print "<p>Select the date in the drop down.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Patch/Fixlet</th>\n";
  print "  <th class=\"ui-state-default\">Severity</th>\n";
  print "</tr>\n";

  $flagged = '';
  $q_string  = "select big_id,big_fixlet,big_severity ";
  $q_string .= "from bigfix ";
  $q_string .= "left join inventory on inventory.inv_id       = bigfix.big_companyid ";
  $q_string .= "where (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . ") and big_scheduled = \"" . $formVars['scheduled'] . "\" ";
  $q_string .= "order by big_severity,big_fixlet ";
  $q_bigfix = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_bigfix) > 0) {
    while ($a_bigfix = mysqli_fetch_array($q_bigfix)) {

      $linkstart = "<a href=\"#\" onclick=\"show_file('patches.fill.php?id=" . $a_bigfix['big_id'] . "&scheduled=" . $formVars['scheduled'] . "');jQuery('#dialogBigFix').dialog('open');return false;\">";
      $linkend   = "</a>";

      $class = "ui-widget-content";
      if ($a_bigfix['big_severity'] == 1) {
        $bigfix = "Unspecified";
        $class = "ui-widget-content";
      }
      if ($a_bigfix['big_severity'] == 2) {
        $bigfix = "Critical";
        $class = "ui-state-error";
      }
      if ($a_bigfix['big_severity'] == 3) {
        $bigfix = "Important";
        $class = "ui-state-highlight";
      }
      if ($a_bigfix['big_severity'] == 4) {
        $bigfix = "Moderate";
        $class = "ui-widget-content";
      }
      if ($a_bigfix['big_severity'] == 5) {
        $bigfix = "Low";
        $class = "ui-widget-content";
      }

      if ($flagged != $a_bigfix['big_fixlet']) {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">" . $linkstart . $a_bigfix['big_fixlet']     . $linkend . "</td>\n";
        print "  <td class=\"" . $class . "\">"              . $bigfix            . "</td>\n";
        print "</tr>\n";
        $flagged = $a_bigfix['big_fixlet'];
      }
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"11\">No records found</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>


<div id="dialogBigFix" title="BigFix Patch Listing">

<form name="bigfix">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">BigFix Patching of: <span id="big_servername"></span> on Date: <?php print $formVars['scheduled']; ?></th>
</tr>
</table>

<textarea id="big_patches" name="big_patches" cols="130" rows="30"></textarea>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
