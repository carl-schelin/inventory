<?php
# Script: monitor.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "monitor.php";

  logaccess($db, $formVars['uid'], $package, "Checking out the interfaces.");

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
    $orderby = "order by inv_name,int_addr ";
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
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
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
<title>Current Openview Configuration</title>

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

  print "<p>This page lists all servers for your group and their Openview monitoring status. You can enable/disable the OpenView monitoring status by clicking on the last column. ";
  print "This does exclude interfaces identified as signaling, serial, loopback, interconnect, and backup. Clicking on the server will take you to the edit record for the server.</p>";
  print "<p><strong>Note:</strong> Any IP where OpenView is not monitoring is <span class=\"ui-state-highlight\">highlighted</span></p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  if ($formVars['csv'] == 'true') {
    print "<p>\"IP Address\",\"Hostname\",\"Function\",\"System Owner\",\"Application Owner\"</br>";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">IP Address</th>\n";
    print "  <th class=\"ui-state-default\">Type</th>\n";
    print "  <th class=\"ui-state-default\">Hostname</th>\n";
    print "  <th class=\"ui-state-default\">Function</th>\n";
    print "  <th class=\"ui-state-default\">Software</th>\n";
    print "  <th class=\"ui-state-default\">System Owner</th>\n";
    print "  <th class=\"ui-state-default\">Application Owner</th>\n";
    print "  <th class=\"ui-state-default\">Openview Toggle</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select int_id,inv_name,inv_function,int_companyid,int_server,int_addr,inv_appadmin,grp_name,int_type,int_openview ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id      = interface.int_companyid ";
  $q_string .= "left join groups on groups.grp_id      = inventory.inv_manager ";
# don't want to see signaling, serial, loopback, interconnect, or backup interfaces as they won't be monitored regardless.
  $q_string .= $where . " and int_ip6 = 0 and int_addr != '' and int_type != 3 and int_type != 5 and int_type != 7 and int_type != 8 and int_type != 16 ";
  $q_string .= $orderby;
  $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_interface['int_companyid'] . "#network\" target=\"blank\">";
      $linkend   = "</a>";

      $q_string  = "select grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $a_interface['inv_appadmin'] . " ";
      $q_appadmin = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_appadmin = mysqli_fetch_array($q_appadmin);

      $q_string  = "select sw_software ";
      $q_string .= "from software ";
      $q_string .= "where sw_companyid = " . $a_interface['int_companyid'] . " and sw_group = " . $GRP_Monitoring . " and sw_vendor = 'HP' ";
      $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_software) == 0) {
        $a_software['sw_software'] = 'None';
      } else {
        if (mysqli_num_rows($q_software) > 1) {
          $a_software['sw_software'] = 'More than 1';
        } else {
          $a_software = mysqli_fetch_array($q_software);
        }
      }

      $openview = '--';

      if ($a_interface['int_openview'] == 1) {
        $openview = 'Yes';
      }

      if ($openview == '--') {
        $class = "ui-state-highlight";
      } else {
        $class = "ui-widget-content";
      }

      if ($formVars['csv'] == 'true') {
        if ($a_interface['int_openview']) {
          print "\"" . $a_interface['int_addr'] . "\",";
          print "\"" . $a_interface['inv_name'] . "\",";
          print "\"" . $a_interface['inv_function'] . "\",";
          print "\"" . $a_software['sw_software'] . "\",";
          print "\"" . $a_interface['grp_name'] . "\",";
          print "\"" . $a_appadmin['grp_name'] . "\"";
          print "</br>\n";
        }
      } else {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['int_addr']                . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $inttype[$a_interface['int_type']]      . "</td>\n";
        print "  <td class=\"" . $class . "\">"        . $linkstart . $a_interface['inv_name']     . $linkend . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['inv_function']            . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_software['sw_software']              . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_interface['grp_name']                . "</td>\n";
        print "  <td class=\"" . $class . "\">"                     . $a_appadmin['grp_name']                 . "</td>\n";
        print "  <td class=\"" . $class . " delete\" id=\"ov_"   . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'openview');\"><u>"  . $openview  . "</u></td>\n";
        print "</tr>\n";
      }

    }
  } else {
    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No records found</td>\n";
    $output .= "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
