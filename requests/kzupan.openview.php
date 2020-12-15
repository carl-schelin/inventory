<?php
# Script: kzupan.openview.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "kzupan.openview.php";

  logaccess($db, $formVars['uid'], $package, "Getting a listing of Openview.");

  if (isset($_GET['csv'])) {
    $formVars['csv'] = 1;
  } else {
    $formVars['csv'] = 0;
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Kevin Zupan: Openview Listing</title>

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

<?php

  if ($formVars['csv'] == 1) {
    print "<p>\"Location\",\"Operating System\",\"Count\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">Location</th>\n";
    print "  <th class=\"ui-state-default\">Operating System</th>\n";
    print "  <th class=\"ui-state-default\">Count</th>\n";
    print "</tr>\n";
  }

  $location = '';
  $software = '';
  $count = 0;
  $q_string  = "select inv_id,inv_name,ct_city,sw_software ";
  $q_string .= "from interface ";
  $q_string .= "join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where int_ip6 = 0 and inv_manager = 1 and inv_status = 0 and int_openview = 1 and sw_type = 'OS' ";
  $q_string .= "order by ct_city,sw_software,inv_name ";
  $q_interface = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ( $a_interface = mysqli_fetch_array($q_interface) ) {

    if ($a_interface['ct_city'] != $location && $count != 0) {
      if ($formVars['csv'] == 1) {
        print "\"" . $a_interface['ct_city'] . "\",\"" . $a_interface['sw_software'] . "\",\"" . $count . "\"</br>\n";
      } else {
        print "<tr>\n";
        print "  <td class=\"ui-widget-content\">" . $a_interface['ct_city']     . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_interface['sw_software'] . "</td>\n";
        print "  <td class=\"ui-widget-content\"><strong>Total: " . $count . "</td>\n";
        print "</tr>\n";
      }
      $count = 0;
      $location = $a_interface['ct_city'];
    }

    if ($a_interface['sw_software'] != $software && $count != 0) {
      if ($formVars['csv'] == 1) {
        print "\"" . $a_interface['ct_city'] . "\",\"" . $a_interface['sw_software'] . "\",\"" . $count . "\"</br>\n";
      } else {
        print "<tr>\n";
        print "  <td class=\"ui-widget-content\">" . $a_interface['ct_city']     . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_interface['sw_software'] . "</td>\n";
        print "  <td class=\"ui-widget-content\"><strong>Total: " . $count . "</td>\n";
        print "</tr>\n";
      }
      $count = 0;
      $software = $a_interface['sw_software'];
    }

    $count++;

  }
  if ($formVars['csv'] == 0) {
    print "</table>\n";
  }
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
