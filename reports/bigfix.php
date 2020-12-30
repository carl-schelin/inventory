<?php
# Script: bigfix.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "bigfix.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the Bigfix listing.");

  if (isset($_GET['date'])) {
    $formVars['scheduled'] = clean($_GET['date'], 10);
  } else {
    $formVars['scheduled'] = date('Y-m-d');
  }
  $daterange = "and big_scheduled = \"" . $formVars['scheduled'] . "\" ";

# if a range of dates, change the search to include both.
  if (isset($_GET['enddate'])) {
    $formVars['enddate'] = clean($_GET['enddate'], 10);
    $daterange = "and big_scheduled >= \"" . $formVars['scheduled'] . "\" and big_scheduled <= \"" . $formVars['enddate'] . "\" ";
  } else {
    $formVars['enddate'] = $formVars['scheduled'];
  }

# to compare two dates (like preprod and prod), pass 'anddate'
  if (isset($_GET['anddate'])) {
    $formVars['anddate'] = clean($_GET['anddate'], 10);
    $daterange = "and (big_scheduled = \"" . $formVars['scheduled'] . "\" or big_scheduled = \"" . $formVars['anddate'] . "\") ";
  } else {
    $formVars['anddate'] = $formVars['scheduled'];
  }

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

  if (isset($_GET['product'])) {
    $formVars['product'] = clean($_GET['product'], 10);
    if ($formVars['product'] > 0) {
      $product = "and inv_product = " . $formVars['product'] . " ";
      $prodlink = "&product=" . $formVars['product'];
    } else {
      $formVars['product'] = 0;
      $product = '';
      $prodlink = '';
    }
  } else {
    $formVars['product'] = 0;
    $product = '';
    $prodlink = '';
  }

  if (isset($_GET['project'])) {
    $formVars['project'] = clean($_GET['project'], 10);
    if ($formVars['project'] > 0) {
      $project = "and inv_project = " . $formVars['project'] . " ";
      $projlink = "&project=" . $formVars['project'];
    } else {
      $formVars['project'] = 0;
      $project = '';
      $projlink = '';
    }
  } else {
    $formVars['project'] = 0;
    $project = '';
    $projlink = '';
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
<title>BigFix Listing</title>

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
    height: 610,
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
  <td class="ui-widget-content button"><a href="<?php print $Reportroot . "/bigfix.php?group=" . $formVars['group'] . "&date=" . $formVars['back'] . $prodlink . $projlink; ?>">Back One Day</a> Select Patch Date: <input type="date" name="big_scheduled" id="scheduled" size="10" value="<?php print $formVars['scheduled']; ?>"><a href="<?php print $Reportroot . "/bigfix.php?group=" . $formVars['group'] . "&date=" . $formVars['forward'] . $prodlink . $projlink; ?>">Forward One Day</a></td>
</tr>
</table>

</div>

<div id="main">

<?php

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Server Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the servers that are scheduled to be patched and possibly rebooted on " . $formVars['scheduled'] . ".</p>\n";

  print "<p>Currently the date drop down isn't working. If you click the forward or backward button, it adds the &date option to the URL. ";
  print "You can then change the date there without clicking the link over and over.</p>\n";

  print "<p>In addition, you can select a range of dates by added a &enddate=YYYY-mm-dd to the URL. Of course it needs to be farther ahead ";
  print "than the current or selected date.</p>\n";

  print "<p>Finally, you can compare two dates by adding an &anddate=YYYY-mm-dd to the URL.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Server</th>\n";
  print "  <th class=\"ui-state-default\">Function</th>\n";
  print "  <th class=\"ui-state-default\">Product</th>\n";
  print "  <th class=\"ui-state-default\">Service Class</th>\n";
  print "  <th class=\"ui-state-default\">Maintenance Window</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,big_id,inv_name,inv_function,win_text,svc_name,prod_name ";
  $q_string .= "from bigfix ";
  $q_string .= "left join inventory on inventory.inv_id       = bigfix.big_companyid ";
  $q_string .= "left join maint_window on maint_window.win_id             = inventory.inv_maint ";
  $q_string .= "left join products on products.prod_id        = inventory.inv_product ";
  $q_string .= "left join service on service.svc_id           = inventory.inv_class ";
  $q_string .= "where (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . ") " . $daterange . $product . $project;
  $q_string .= "group by inv_name ";
  $q_bigfix = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_bigfix) > 0) {
    while ($a_bigfix = mysqli_fetch_array($q_bigfix)) {

      $linkstart = "<a href=\"#\" onclick=\"show_file('bigfix.fill.php?id=" . $a_bigfix['inv_id'] . "&scheduled=" . $formVars['scheduled'] . "&enddate=" . $formVars['enddate'] . "&anddate=" . $formVars['anddate'] . "');jQuery('#dialogBigFix').dialog('open');return false;\">";
      $linkend   = "</a>";

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $linkstart . $a_bigfix['inv_name']     . $linkend . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_bigfix['inv_function']            . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_bigfix['prod_name']               . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_bigfix['svc_name']                . "</td>\n";
      print "  <td class=\"ui-widget-content\">"              . $a_bigfix['win_text']                . "</td>\n";
      print "</tr>\n";
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

<textarea id="big_patches" name="big_patches" cols="130" rows="28"></textarea>

<p>You can go to: <input type="text" value="https://access.redhat.com/errata/RHSA-[value]" size="60"> to review the Red Hat patch listed above.</p>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
