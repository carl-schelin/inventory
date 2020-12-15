<?php
# Script: jsherard.listing.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "jsherard.listing.php";

  logaccess($db, $formVars['uid'], $package, "Viewing Jeff's oracle listing.");

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $orderby .= $_GET['sort'] . ", ";
  }
  $orderby .= "sw_name";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Jeff Sherard: Oracle Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_name">Name</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_manufacturer">Manufacturer</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_type">Type</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_use">Use</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_cpuuser">CPU/User</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_server">Server</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_inservice">In Service</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_podate">PO Date</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_accounted">Accounted</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_department">Department</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_project">Project</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_yearlycost">Yearly Cost</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_cost">Cost</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_maintdate">Maintenance Date</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_vendor">Vendor</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_responsible">Responsible</a></th>
  <th class="ui-state-default"><a href="\" . $package . "?sort=sw_support">Support</a></th>
</tr>
<?php

  $q_string = "select sw_name,sw_manufacturer,sw_type,sw_use,sw_cpuuser,sw_server,sw_inservice,";
  $q_string .= "sw_podate,sw_accounted,sw_department,sw_project,sw_yearlycost,sw_cost,sw_maintdate,";
  $q_string .= "sw_vendor,sw_responsible,sw_support ";
  $q_string .= "from swbackup ";
  $q_string .= $orderby;
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_name']         . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_manufacturer'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_type']         . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_use']          . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_cpuuser']      . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_server']       . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_inservice']    . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_podate']       . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_accounted']    . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_department']   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_project']      . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_yearlycost']   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_cost']         . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_maintdate']    . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_vendor']       . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_responsible']  . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_software['sw_support']      . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
