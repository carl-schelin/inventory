<?php
# Script: ovstatus.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "ovstatus.php";

  logaccess($formVars['uid'], $package, "Checking out the openview messages.");

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
  if (isset($_GET['group'])) {
    $formVars['group']    = clean($_GET['group'],   10);
  } else {
    $formVars['group']    = 1;
  }
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }
  if (isset($_GET['csv'])) {
    $formVars['csv'] = clean($_GET['csv'], 10);
  } else {
    $formVars['csv'] = '';
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
    $orderby = "group by inv_name ";
    $_SESSION['sort'] = '';
  }

  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
      }
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . ") ";
    $and = " and";
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $type;

  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from ip_zones";
  $q_ip_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    $zoneval[$a_ip_zones['zone_id']] = $a_ip_zones['zone_name'];
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
<title>Openview Messages</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript" language="javascript">

function flip_Bit( p_id, p_bit ) {
  script = document.createElement('script');
  script.src = 'monitoring.toggle.php?id=' + p_id + '&flip=' + p_bit;
  document.getElementsByTagName('head')[0].appendChild(script);
}

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  $q_string  = "select itp_id,itp_acronym ";
  $q_string .= "from inttype ";
  $q_inttype = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inttype = mysqli_fetch_array($q_inttype)) {
    $inttype[$a_inttype['itp_id']] = $a_inttype['itp_acronym'];
  }

  $passthrough = "&group=" . $formVars['group'] . "&product=" . $formVars['product'] . "&inwork=" . $formVars['inwork'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Current Monitoring Status</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page displays all servers where at least one interface on the system has been identified as being monitored by Openview</p>";

  print "<p>Notifications by Openview are sent to the Unix Admins On Call group. A member of this group is the Openview Message Capture ";
  print "(ovmc) account. As the group receives notifications from Openview, the notification is also received by the ovmc account. Every ";
  print "30 minutes, notifications are imported into the Inventory database and associated with a server. Any notifications that the Unix ";
  print "Team receives and a server can't be found is emailed to the team to be corrected so the storage is considered accurate.</p>";

  print "<p>There are five columns for each server. A value that is zero is <span class=\"ui-state-highlight\">highlighted like this</span>. ";
  print "If a server has had no alarms since it's built date, the column is <span class=\"ui-state-error\">highlighted like this</span>.</p>";

  print "<p><strong>Important Note:</strong> The ability to send a test message through the Openview system has been configured and every ";
  print "server has been tested several times. If there aren't any alarms for the current year or no alarms at all, then there may be a ";
  print "problem with the agent or the notification system.</p>";

  print "<ul>";
  print "  <li>C - Count of Critical Notifications received.</li>";
  print "  <li>M - Count of Major Notifications received.</li>";
  print "  <li>m - Count of Minor Notifications received.</li>";
  print "  <li>W - Count of Warning Notifications received.</li>";
  print "  <li>N - Count of Normal Notifications received.</li>";
  print "</ul>";

  print "<p>A value of '--' indicates the server wasn't built during that year.</p>";

  print "<p>There are several possible reasons for a lack of notifications. Notifications could be disabled due to a new system, being disabled in the past ";
  print "due to an incident or maintenance event, or by request for another reason. In addition, the message parsing script is enormous, ";
  print "complicated, and prone to errors. The issue needs to be reviewed and addressed regardless of the error.</p>";

  print "<p>Clicking on the server name will take you to the server detail record, Monitoring tab, to see the historical information for the server.</p>";

  print "</div>\n\n";

  print "</div>\n\n";

  if ($formVars['csv'] == 'true') {
    print "<p>\"IP Address\",\"Hostname\",\"Function\",\"System Owner\",\"Application Owner\"</br>";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Hostname</th>\n";
    print "  <th class=\"ui-state-default\">Built</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2009</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2010</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2011</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2012</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2013</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2014</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2015</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2016</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2017</th>\n";
    print "  <th class=\"ui-state-default\" colspan=\"5\">2018</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">&nbsp;</th>\n";
    print "  <th class=\"ui-state-default\">&nbsp;</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "  <th class=\"ui-state-default\">N</th>\n";
    print "  <th class=\"ui-state-default\">W</th>\n";
    print "  <th class=\"ui-state-default\">m</th>\n";
    print "  <th class=\"ui-state-default\">M</th>\n";
    print "  <th class=\"ui-state-default\">C</th>\n";
    print "</tr>\n";
  }

  $count = 0;
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= $where . " and int_openview = 1 ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inventory) > 0) {
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#alarms\" target=\"blank\">";
      $linkend   = "</a>";

      $q_string  = "select count(alarm_id) ";
      $q_string .= "from alarms ";
      $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " ";
      $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_alarms = mysqli_fetch_array($q_alarms);

      if ($a_alarms['count(alarm_id)'] == 0) {
        $main_class = "ui-state-error";
      } else {
        $main_class = "ui-widget-content";
      }

      $q_string  = "select hw_built ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1 ";
      $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_hardware = mysqli_fetch_array($q_hardware);

      print "<tr>\n";
      print "  <td class=\"" . $main_class . "\">" . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";
      print "  <td class=\"" . $main_class . "\">"              . $a_hardware['hw_built']            . "</td>\n";

      for ($year = 2009; $year < 2019; $year++) {
# If the date the server was built (2010-05-03) is greater than (2010-01-01) then give me a count
# else print --
        $compareyear = date('Y', strtotime($a_hardware['hw_built']));
        if ( $compareyear <= $year) {
          $q_string  = "select count(alarm_level) ";
          $q_string .= "from alarms ";
          $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_timestamp > \"" . ($year - 1) . "-12-31\" and alarm_timestamp < \"" . ($year + 1) . "-01-01\" and alarm_level = 1 ";
          $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_alarms = mysqli_fetch_array($q_alarms);
          $normal = $a_alarms['count(alarm_level)'];
          if ($normal == 0) {
            $class = "ui-state-highlight";
            $normal = '';
          } else {
            $class = "ui-widget-content";
          }
          if ($main_class == 'ui-state-error') {
            $class = $main_class;
          }
          print "  <td title=\"Normal\" class=\"" . $class . "\">" . $normal . "</td>\n";

          $q_string  = "select count(alarm_level) ";
          $q_string .= "from alarms ";
          $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_timestamp > \"" . ($year - 1) . "-12-31\" and alarm_timestamp < \"" . ($year + 1) . "-01-01\" and alarm_level = 2 ";
          $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_alarms = mysqli_fetch_array($q_alarms);
          $warning = $a_alarms['count(alarm_level)'];
          if ($warning == 0) {
            $class = "ui-state-highlight";
            $warning = '';
          } else {
            $class = "ui-widget-content";
          }
          if ($main_class == 'ui-state-error') {
            $class = $main_class;
          }
          print "  <td title=\"Warning\" class=\"" . $class . "\">" . $warning . "</td>\n";

          $q_string  = "select count(alarm_level) ";
          $q_string .= "from alarms ";
          $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_timestamp > \"" . ($year - 1) . "-12-31\" and alarm_timestamp < \"" . ($year + 1) . "-01-01\" and alarm_level = 3 ";
          $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_alarms = mysqli_fetch_array($q_alarms);
          $minor = $a_alarms['count(alarm_level)'];
          if ($minor == 0) {
            $class = "ui-state-highlight";
            $minor = '';
          } else {
            $class = "ui-widget-content";
          }
          if ($main_class == 'ui-state-error') {
            $class = $main_class;
          }
          print "  <td title=\"Minor\" class=\"" . $class . "\">" . $minor . "</td>\n";

          $q_string  = "select count(alarm_level) ";
          $q_string .= "from alarms ";
          $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_timestamp > \"" . ($year - 1) . "-12-31\" and alarm_timestamp < \"" . ($year + 1) . "-01-01\" and alarm_level = 4 ";
          $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_alarms = mysqli_fetch_array($q_alarms);
          $major = $a_alarms['count(alarm_level)'];
          if ($major == 0) {
            $class = "ui-state-highlight";
            $major = '';
          } else {
            $class = "ui-widget-content";
          }
          if ($main_class == 'ui-state-error') {
            $class = $main_class;
          }
          print "  <td title=\"Major\" class=\"" . $class . "\">" . $major . "</td>\n";

          $q_string  = "select count(alarm_level) ";
          $q_string .= "from alarms ";
          $q_string .= "where alarm_companyid = " . $a_inventory['inv_id'] . " and alarm_timestamp > \"" . ($year - 1) . "-12-31\" and alarm_timestamp < \"" . ($year + 1) . "-01-01\" and alarm_level = 5 ";
          $q_alarms = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          $a_alarms = mysqli_fetch_array($q_alarms);
          $critical = $a_alarms['count(alarm_level)'];
          if ($critical == 0) {
            $class = "ui-state-highlight";
            $critical = '';
          } else {
            $class = "ui-widget-content";
          }
          if ($main_class == 'ui-state-error') {
            $class = $main_class;
          }
          print "  <td title=\"Critical\" class=\"" . $class . "\">" . $critical . "</td>\n";

        } else {
          print "  <td class=\"" . $main_class . "\" colspan=\"5\">--</td>\n";
        }
      }
      print "</tr>\n";
      if ($class == $main_class) {
        $count++;
      }
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">No records found</td>\n";
    print "</tr>\n";
  }

  print "</table>\n";
  print "<p class=\"ui-widget-content\">Total with no notifications: " . $count . "</p>";

?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
