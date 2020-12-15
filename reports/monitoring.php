<?php
# Script: monitoring.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "monitoring.php";

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
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
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
    $orderby = "order by inv_name,int_server ";
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

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
    $and = " and";
  }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

  $location = '';
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

#  $where = $product . $group . $inwork . $location . $type;
  $where = $product . $group . $inwork . $type;

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
<title>Current Monitoring Configuration</title>

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

  print "<p>This page lists all servers for your group and their Nagios monitoring configuration. Click on the server entry to open the detail page for that server. The monitoring ";
  print "flags are toggles. The OpenView listing just provides an indication that OpenView is monitoring this interface. Changing from 'Yes' to '--' (No) doesn't affect monitoring ";
  print "by OpenView. The remainder of the flags are for Nagios. If Nagios is disabled ('--') then the remaining flags are ignored. Clicking on a 'Yes' or '--' will toggle enable ";
  print "and disable.</p>\n";
  print "<p><strong>Note:</strong> Any IP where both OpenView and Nagios are not monitoring is <span class=\"ui-state-highlight\">highlighted</span></p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Hostname</th>\n";
  print "  <th class=\"ui-state-default\">Interface</th>\n";
  print "  <th class=\"ui-state-default\">IP Address</th>\n";
  print "  <th class=\"ui-state-default\">Type</th>\n";
  print "  <th class=\"ui-state-default\">OpenView</th>\n";
  print "  <th class=\"ui-state-default\">Nagios</th>\n";
  print "  <th class=\"ui-state-default\">Ping</th>\n";
  print "  <th class=\"ui-state-default\">SSH</th>\n";
  print "  <th class=\"ui-state-default\">HTTP</th>\n";
  print "  <th class=\"ui-state-default\">FTP</th>\n";
  print "  <th class=\"ui-state-default\">SMTP</th>\n";
  print "  <th class=\"ui-state-default\">Cfg2HTML</th>\n";
  print "</tr>\n";

  $q_string  = "select int_id,inv_name,int_companyid,int_server,int_addr,int_type,int_openview,int_nagios,int_ping,int_ssh,int_http,int_ftp,int_smtp,int_cfg2html ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id      = interface.int_companyid ";
#  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
# don't want to see signaling, serial, loopback, interconnect, or backup interfaces as they won't be monitored regardless.
  $q_string .= $where . " and int_ip6 = 0 and int_addr != '' and int_type != 3 and int_type != 5 and int_type != 7 and int_type != 8 and int_type != 16 ";
  $q_string .= $orderby;
  $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

      $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_interface['int_companyid'] . "#network\" target=\"blank\">";
      $linkend   = "</a>";

      $openview = '--';
      $nagios = '--';
      $ping = '--';
      $ssh = '--';
      $http = '--';
      $ftp = '--';
      $smtp = '--';
      $cfg2html = 'Check';

      if ($a_interface['int_openview'] == 1) {
        $openview = 'Yes';
      }
      if ($a_interface['int_nagios'] == 1) {
        $nagios = 'Yes';
      }
      if ($a_interface['int_ping'] == 1) {
        $ping = 'Yes';
      }
      if ($a_interface['int_ssh'] == 1) {
        $ssh = 'Yes';
      }
      if ($a_interface['int_http'] == 1) {
        $http = 'Yes';
      }
      if ($a_interface['int_ftp'] == 1) {
        $ftp = 'Yes';
      }
      if ($a_interface['int_smtp'] == 1) {
        $smtp = 'Yes';
      }
      if ($a_interface['int_cfg2html'] == 1) {
        $cfg2html = '--';
      }

      if ($openview == '--' && $nagios == '--') {
        $class = "ui-state-highlight";
      } else {
        $class = "ui-widget-content";
      }


      print "<tr>\n";
      print "  <td class=\"" . $class . "\">"        . $linkstart . $a_interface['inv_name']     . $linkend . "</td>\n";
      print "  <td class=\"" . $class . "\">"                     . $a_interface['int_server']              . "</td>\n";
      print "  <td class=\"" . $class . "\">"                     . $a_interface['int_addr']                . "</td>\n";
      print "  <td class=\"" . $class . " delete\">"              . $inttype[$a_interface['int_type']]      . "</td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"ov_"   . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'openview');\"><u>"  . $openview  . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"nag_"  . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'nagios');\"><u>"    . $nagios    . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"ping_" . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'ping');\"><u>"      . $ping      . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"ssh_"  . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'ssh');\"><u>"       . $ssh       . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"http_" . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'http');\"><u>"      . $http      . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"ftp_"  . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'ftp');\"><u>"       . $ftp       . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"smtp_" . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'smtp');\"><u>"      . $smtp      . "</u></td>\n";
      print "  <td class=\"" . $class . " delete\" id=\"cfg_"  . $a_interface['int_id'] . "\" onclick=\"javascript:flip_Bit(" . $a_interface['int_id'] . ",'cfg2html');\"><u>"  . $cfg2html  . "</u></td>\n";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"6\">No records found</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
