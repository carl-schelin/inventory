<?php
# Script: servers.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "servers.php";

  logaccess($db, $formVars['uid'], $package, "Getting a report on vulnerabilities.");

  $formVars['product']   = 0;
  $formVars['project']   = 0;
  $formVars['inwork']    = 'false';
  $formVars['country']   = 0;
  $formVars['state']     = 0;
  $formVars['city']      = 0;
  $formVars['location']  = 0;
  $formVars['type']      = 0;
  $formVars['sort']      = 0;
  $formVars['group']     = 0;

  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  }
  if (isset($_GET['project'])) {
    $formVars['project']   = clean($_GET['project'],  10);
  }
  if (isset($_GET['inwork'])) {
    $formVars['inwork']   = clean($_GET['inwork'],  10);
  }
  if (isset($_GET['country'])) {
    $formVars['country']   = clean($_GET['country'],  10);
  }
  if (isset($_GET['state'])) {
    $formVars['state']   = clean($_GET['state'],  10);
  }
  if (isset($_GET['city'])) {
    $formVars['city']   = clean($_GET['city'],  10);
  }
  if (isset($_GET['location'])) {
    $formVars['location']   = clean($_GET['location'],  10);
  }
  if (isset($_GET['type'])) {
    $formVars['type']   = clean($_GET['type'],  10);
  }
  if (isset($_GET['group'])) {
    $formVars['group']   = clean($_GET['group'],  10);
  }

  if (isset($_GET['sort'])) {
    $orderby = "order by " . clean($_GET['sort'], 40) . " ";
  } else {
    $orderby = "order by inv_name,vuln_securityid,sev_id ";
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
<title>Scan Level Report</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( );
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Scan Level Report Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>This report lists the Critical, High, and Medium Vulnerabilities currently outstanding for the selected IP addresses.</p>

<p>When reviewing this report, pay particular attention to the 'Seen' column. This column only shows the date the vulnerability first appeared 
in the Inventory. The date is not updated with new data and the line disappears when the vulnerability falls off subsequent 
reports. It will reappear if the vulnerability was not addressed and the scan missed it or if it resurfaces due to reinstallation of software.</p>

<p><strong>Note</strong> that reports are pulled from Tenable once a week but can be requested and imported into the Inventory at any time 
if needed.</p>

<p>Per the InfoSec policy, all vulnerabilities must be addressed (excludes Info).</p>

<ul>
  <li><strong>Hostname</strong> - The primary name of the system.</li>
  <li><strong>Interface Name</strong> - The name associated with this interface.</li>
  <li><strong>IP Address</strong> - The IP address associated with this interface.</li>
  <li><strong>Name</strong> - A short description of the vulnerability. Note that clicking here will take you to the Nessus description of the vulnerability.</li>
  <li><strong>Seen</strong> - The first time the vulnerability was discovered for the system.</li>
  <li><strong>Severity</strong> - The severity of the vulnerability.</li>
  <li><strong>Group</strong> - The group that manages vulnerable system.</li>
  <li><strong>Product</strong> - The product associated with the server.</li>
  <li><strong>Resolved</strong> - The date the vulnerability was reported as corrected (not in the report any more).</li>
</ul>

</div>

</div>

<?php

  $critical = '';
  $high = '';
  $medium = '';
  $low = '';
  $info = '';

  $sorted = "";
  if ($formVars['group'] > 0) {
    $sorted .= "&group=" . $formVars['group'];
  }
  if ($formVars['product'] > 0) {
    $sorted .= "&product=" . $formVars['product'];
  }
  if ($formVars['project'] > 0) {
    $sorted .= "&project=" . $formVars['project'];
  }
  if ($formVars['location'] > 0) {
    $sorted .= "&location=" . $formVars['location'];
  }
  if ($formVars['country'] > 0) {
    $sorted .= "&country=" . $formVars['country'];
  }
  if ($formVars['city'] > 0) {
    $sorted .= "&city=" . $formVars['city'];
  }
  if ($formVars['state'] > 0) {
    $sorted .= "&state=" . $formVars['state'];
  }
  if ($formVars['type'] > 0) {
    $sorted .= "&type=" . $formVars['type'];
  }
  if ($formVars['inwork'] == 'true') {
    $sorted .= "&inwork=" . $formVars['inwork'];
  }

  $header  = "<table class=\"ui-styled-table\">\n";
  $header .= "<tr>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=inv_name,vuln_securityid,sev_id" . $sorted . "\">" . "Hostname"       . "</th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=int_server" . $sorted . "\">"                      . "Interface Name" . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=int_addr,inv_name" . $sorted . "\">"               . "IP Address"     . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=sec_name,inv_name" . $sorted . "\">"               . "Name"           . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=vuln_date,inv_name" . $sorted . "\">"              . "Seen"           . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=sev_id,inv_name" . $sorted . "\">"                 . "Severity"       . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=grp_name,inv_name" . $sorted . "\">"               . "Group"          . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=prod_name,inv_name" . $sorted . "\">"              . "Product"        . "</a></th>\n";
  $header .= "  <th class=\"ui-state-default\"><a href=\"servers.php?sort=vuln_deldate,inv_name" . $sorted . "\">"           . "Resolved"       . "</a></th>\n";
  $header .= "</tr>\n";

  $numhigh = 0;
  $numcritical = 0;
  $nummedium = 0;
  $numlow = 0;
  $numinfo = 0;

  $q_string  = "select vuln_id,vuln_securityid,vuln_date,vuln_delete,vuln_deldate,sec_name,sev_name,prod_name,int_server,int_addr,inv_name,grp_name ";
  $q_string .= "from inv_vulnerabilities ";
  $q_string .= "left join inv_security  on inv_security.sec_id   = inv_vulnerabilities.vuln_securityid ";
  $q_string .= "left join inv_severity  on inv_severity.sev_id   = inv_security.sec_severity ";
  $q_string .= "left join inv_interface on inv_interface.int_id  = inv_vulnerabilities.vuln_interface ";
  $q_string .= "left join inventory on inventory.inv_id  = inv_interface.int_companyid ";
  $q_string .= "left join inv_groups  on inv_groups.grp_id   = inventory.inv_manager ";
  $q_string .= "left join inv_products  on inv_products.prod_id  = inventory.inv_product ";
# add in bits if asked
  if ($formVars['projects'] > 0) {
    $q_string .= "left join inv_projects  on inv_projects.prj_id = inventory.inv_project ";
  }
  if (($formVars['locations'] + $formVars['country'] + $formVars['state'] + $formVars['city']) > 0) {
    $q_string .= "left join inv_locations  on inv_locations.loc_id = inventory.inv_location ";
  }
# want to add in cities, states, and country tables as well.
  if ($formVars['inwork'] == 'true') {
    $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  }
# per infosec, sev low and higher (1, 2, 3, and 4)
  $q_string .= "where sec_severity < 6 ";
# and the extra bits if called for
  if ($formVars['group'] > 0) {
    $q_string .= "and inv_manager = " . $formVars['group'] . " ";
  }
  if ($formVars['product'] > 0) {
    $q_string .= "and inv_product = " . $formVars['product'] . " ";
  }
  if ($formVars['project'] > 0) {
    $q_string .= "and inv_project = " . $formVars['project'] . " ";
  }
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $q_string .= "and inv_location = " . $formVars['location'] . " ";
  } else {
    if ($formVars['country'] > 0) {
      $q_string .= "and loc_country = " . $formVars['country'] . " ";
    }
    if ($formVars['state'] > 0) {
      $q_string .= "and loc_state = " . $formVars['state'] . " ";
    }
    if ($formVars['city'] > 0) {
      $q_string .= "and loc_city = " . $formVars['city'] . " ";
    }
    if ($formVars['location'] > 0) {
      $q_string .= "and inv_location = " . $formVars['location'] . " ";
    }
  }
  if ($formVars['inwork'] == 'true') {
    $q_string .= "and hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
  }
  if ($formVars['type'] != -1) {
    $q_string .= "and inv_status = 0 ";
  }
  $q_string .= $orderby;
  $q_inv_vulnerabilities = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_vulnerabilities = mysqli_fetch_array($q_inv_vulnerabilities)) {

    $class = "ui-widget-content";
    if ($a_inv_vulnerabilities['sev_name'] == 'High') {
      if ($a_inv_vulnerabilities['vuln_deldate'] != '0000-00-00') {
        $class = "ui-state-highlight";
      } else {
        $numhigh++;
      }
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Critical') {
      if ($a_inv_vulnerabilities['vuln_deldate'] != '0000-00-00') {
        $class = "ui-state-highlight";
      } else {
        $numcritical++;
      }
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Medium') {
      if ($a_inv_vulnerabilities['vuln_deldate'] != '0000-00-00') {
        $class = "ui-state-highlight";
      } else {
        $nummedium++;
      }
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Low') {
      if ($a_inv_vulnerabilities['vuln_deldate'] != '0000-00-00') {
        $class = "ui-state-highlight";
      } else {
        $numlow++;
      }
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Info') {
      if ($a_inv_vulnerabilities['vuln_deldate'] != '0000-00-00') {
        $class = "ui-state-highlight";
      } else {
        $numinfo++;
      }
    }

    $vuln_name[$a_inv_vulnerabilities['vuln_securityid']] = $a_inv_vulnerabilities['sec_name'];
    $vuln_severity[$a_inv_vulnerabilities['vuln_securityid']] = $a_inv_vulnerabilities['sev_name'];

    if ($a_inv_vulnerabilities['vuln_securityid'] < 10000) {
      $nessus = "<a href=\"https://www.tenable.com/plugins/nnm/" . $a_inv_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
    } else {
      $nessus = "<a href=\"https://www.tenable.com/plugins/nessus/" . $a_inv_vulnerabilities['vuln_securityid'] . "\" target=\"_blank\">";
    }
    $linkend = "</a>";

    $output  = "<tr>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['inv_name']     . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['int_server']   . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['int_addr']     . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $nessus . $a_inv_vulnerabilities['sec_name'] . $linkend . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['vuln_date']    . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['sev_name']     . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['grp_name']     . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['prod_name']    . "</td>\n";
    $output .= "  <td class=\"" . $class . "\">" . $a_inv_vulnerabilities['vuln_deldate'] . "</td>\n";
    $output .= "</tr>\n";

    if ($a_inv_vulnerabilities['sev_name'] == 'Critical') {
      $critical .= $output;
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'High') {
      $high .= $output;
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Medium') {
      $medium .= $output;
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Low') {
      $low .= $output;
    }
    if ($a_inv_vulnerabilities['sev_name'] == 'Info') {
      $info .= $output;
    }
  }

  $critical = $header . $critical . "</table>" . "<p>Total Critical Vulnerabilities: " . $numcritical . "</p>";
  $high     = $header . $high     . "</table>" . "<p>Total High Vulnerabilities: " . $numhigh . "</p>";
  $medium   = $header . $medium   . "</table>" . "<p>Total Medium Vulnerabilities: " . $nummedium . "</p>";
  $low      = $header . $low      . "</table>" . "<p>Total Low Vulnerabilities: " . $numlow . "</p>";
  $info     = $header . $info     . "</table>" . "<p>Total Info Vulnerabilities: " . $numinfo . "</p>";

#  $unique = '';
#  foreach ($vuln_name as $i => $value) {
#    $unique .= "<p class=\"ui-widget-content\"><strong>/etc/sysconfig/network-scripts/route-" . $i . ":</strong>" . $interface[$i] . "</p>";
#  }

?>

<div id="tabs">

<ul>
  <li><a href="#critical">Critical</a></li>
  <li><a href="#high">High</a></li>
  <li><a href="#medium">Medium</a></li>
  <li><a href="#low">Low</a></li>
  <li><a href="#info">Info</a></li>
</ul>

<div id="critical">

<?php print $critical; ?>

</div>


<div id="high">

<?php print $high; ?>

</div>


<div id="medium">

<?php print $medium; ?>

</div>


<div id="low">

<?php print $low; ?>

</div>


<div id="info">

<?php print $info; ?>

</div>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
