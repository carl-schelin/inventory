<?php
# Script: compiled.class.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "compiled.class.php";

  logaccess($formVars['uid'], $package, "Searching for EOL HW and SW.");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['project']   = clean($_GET['project'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);
  $formVars['csv']       = clean($_GET['csv'],      10);

  if (!isset($_GET['inwork'])) {
    $formVars['inwork'] = 'false';
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
    $orderby .= "order by inv_callpath desc,inv_class,inv_name";
    $_SESSION['sort'] = '';
  }

  if (isset($_GET['csv'])) {
    if ($_GET['csv'] == 'true') {
      $formVars['csv'] = 1;
    } else {
      $formVars['csv'] = 0;
    }
  } else {
    $formVars['csv'] = 0;
  }

# start where build process
  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . " or sw_group = " . $formVars['group'] . ") ";
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

  $where = $product . $group . $inwork . $location . $type . $and . " sw_type = 'OS' ";;

  $total = 0;
  $grandtotal = 0;
  $totalhardware = 0;
  $hardware = 0;
  $software = 0;

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
<title>End of Life Listing by Service Class</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<div id="tabs">

<ul>
  <li><a href="#software">EOL Software</a></li>
  <li><a href="#hardware">EOL Hardware</a></li>
</ul>

<div id="software">

<?php

  if ($formVars['csv']) {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">";
    print "\"911-Call path\",";
    print "\"Service Class\",";
    print "\"Software\",";
    print "\"Software EOL\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\" colspan=\"10\">Listing</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">911-Call Path</th>\n";
    print "  <th class=\"ui-state-default\">Service Class</th>\n";
    print "  <th class=\"ui-state-default\">Software</th>\n";
    print "  <th class=\"ui-state-default\">Software EOL</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,sw_software,sw_eol ";
  $q_string .= "from inventory ";
  $q_string .= "left join software  on inventory.inv_id = software.sw_companyid ";
  $q_string .= "left join hardware  on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join groups    on groups.grp_id    = hardware.hw_group ";
  $q_string .= "left join models    on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join support   on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products  on products.prod_id = inventory.inv_product ";
  $q_string .= "left join service   on service.svc_id   = inventory.inv_class ";
  $q_string .= $where . " and sw_type = 'OS' ";
  $q_string .= $orderby;
  $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $class = " class=\"ui-widget-content\"";

    $callpath = "No";
    if ($a_inventory['inv_callpath']) {
      $callpath = "Yes";
    }

    if ($a_inventory['sw_eol'] < date('Y-m-d') && $a_inventory['sw_eol'] != '0000-00-00') {

      if ($formVars['csv']) {
        print "\"" . $a_inventory['inv_name']     . "\",";
        print "\"" . $callpath                    . "\",";
        print "\"" . $a_inventory['svc_name']     . "\",";
        print "\"" . $a_inventory['sw_software']  . "\",";
        print "\"" . $a_inventory['sw_eol']       . "\"</br>\n";
      } else {
        print "<tr>\n";
        print "<td" . $class . ">" . $a_inventory['inv_name'] . "</td>\n";
        print "<td" . $class . ">" . $callpath . "</td>\n";
        print "<td" . $class . ">" . $a_inventory['svc_name'] . "</td>\n";
        print "<td" . $class . ">" . $a_inventory['sw_software'] . "</td>\n";
        print "<td" . $class . ">" . $a_inventory['sw_eol'] . "</td>\n";
        print "</tr>\n";
      }
    }
  }

  if ($formVars['csv']) {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
  }

?>

</div>


<div id="hardware">

<?php

  if ($formVars['csv']) {
    print "<p style=\"text-align: left;\"><textarea cols=\"120\" rows=\"40\">";
    print "\"System Name\",";
    print "\"911-Call Path\",";
    print "\"Service Class\",";
    print "\"Hardware Vendor\",";
    print "\"Hardware Model\",";
    print "\"Hardware EOL\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\" colspan=\"10\">Listing</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\">System Name</th>\n";
    print "  <th class=\"ui-state-default\">911-Call Path</th>\n";
    print "  <th class=\"ui-state-default\">Service Class</th>\n";
    print "  <th class=\"ui-state-default\">Hardware Vendor</th>\n";
    print "  <th class=\"ui-state-default\">Hardware Model</th>\n";
    print "  <th class=\"ui-state-default\">Hardware EOL</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,hw_purchased,mod_eol,hw_eol,mod_vendor,mod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software  on inventory.inv_id = software.sw_companyid ";
  $q_string .= "left join hardware  on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join groups    on groups.grp_id    = hardware.hw_group ";
  $q_string .= "left join models    on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join support   on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products  on products.prod_id = inventory.inv_product ";
  $q_string .= "left join service   on service.svc_id   = inventory.inv_class ";
  $q_string .= $where . " and hw_primary = 1 and hw_deleted = 0 ";
  $q_string .= $orderby;
  $q_inventory = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $class = " class=\"ui-widget-content\"";

    $callpath = "No";
    if ($a_inventory['inv_callpath']) {
      $callpath = "Yes";
    }

    if ($a_inventory['mod_vendor'] == 'Dell') {
      # For Dell, the end of support is 5 years after the purchase date
      $date = explode("-", $a_inventory['hw_purchased']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
      $newdate = date("Y-m-d",$support);
    } else {
      if ($a_inventory['mod_eol'] == '') {
        $a_inventory['mod_eol'] = '0000-00-00';
      }
      $date = explode("-", $a_inventory['mod_eol']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0]);
      $newdate = $a_inventory['mod_eol'];
    }

    if ($newdate < date('Y-m-d') && $newdate != '0000-00-00') {

      if ($formVars['csv']) {
        print "\"" . $a_inventory['inv_name']   . "\",";
        print "\"" . $callpath                  . "\",";
        print "\"" . $a_inventory['svc_name']   . "\",";
        print "\"" . $a_inventory['mod_vendor'] . "\",";
        print "\"" . $a_inventory['mod_name']   . "\",";
        print "\"" . $newdate                   . "\"</br>\n";
      } else {
        print "<tr>\n";
        print "<td" . $class . ">" . $a_inventory['inv_name'] . "</td>\n";
        print "<td" . $class . ">" . $callpath . "</td>\n";
        print "<td" . $class . ">" . $a_inventory['svc_name'] . "</td>\n";
        print "<td" . $class . ">" . $a_inventory['mod_vendor'] . "</td>\n";
        print "<td" . $class . ">" . $a_inventory['mod_name'] . "</td>\n";
        print "<td" . $class . ">" . $newdate . "</td>\n";
        print "</tr>\n";
      }
    }
  }

  if ($formVars['csv']) {
    print "</textarea>";
    print "</p>\n";
  } else {
    print "</table>\n";
  }

?>


</div>



</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
