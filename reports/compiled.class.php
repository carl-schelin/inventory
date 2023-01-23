<?php
# Script: compiled.class.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "compiled.class.php";

  logaccess($db, $formVars['uid'], $package, "Searching for EOL HW and SW.");

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
    $group = $and . " (inv_manager = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . " or svr_groupid = " . $formVars['group'] . ") ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
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

  $where = $product . $group . $inwork . $location . $type;

  $total = 0;
  $grandtotal = 0;
  $totalhardware = 0;
  $hardware = 0;
  $software = 0;

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
<title>End of Life Listing by Service Class</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( );
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
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_svr_software  on inv_svr_software.svr_companyid = inv_inventory.inv_id ";
  $q_string .= "left join inv_software      on inv_software.sw_id             = inv_svr_software.svr_softwareid ";
  $q_string .= "left join inv_sw_types      on inv_sw_types.typ_id            = inv_software.sw_type ";
  $q_string .= "left join inv_hardware      on inv_inventory.inv_id           = inv_hardware.hw_companyid ";
  $q_string .= "left join inv_groups        on inv_groups.grp_id              = inv_hardware.hw_group ";
  $q_string .= "left join inv_models        on inv_models.mod_id              = inv_hardware.hw_vendorid ";
  $q_string .= "left join inv_support       on inv_support.sup_id             = inv_hardware.hw_supportid ";
  $q_string .= "left join inv_products      on inv_products.prod_id           = inv_inventory.inv_product ";
  $q_string .= "left join inv_service       on inv_service.svc_id             = inv_inventory.inv_class ";
  $q_string .= $where . " and typ_name = 'OS' ";
  $q_string .= $orderby;
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_inventory) > 0) {
    while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

      $class = " class=\"ui-widget-content\"";

      $callpath = "No";
      if ($a_inv_inventory['inv_callpath']) {
        $callpath = "Yes";
      }

      if ($a_inv_inventory['sw_eol'] < date('Y-m-d') && $a_inv_inventory['sw_eol'] != '1971-01-01') {

        if ($formVars['csv']) {
          print "\"" . $a_inv_inventory['inv_name']     . "\",";
          print "\"" . $callpath                    . "\",";
          print "\"" . $a_inv_inventory['svc_name']     . "\",";
          print "\"" . $a_inv_inventory['sw_software']  . "\",";
          print "\"" . $a_inv_inventory['sw_eol']       . "\"</br>\n";
        } else {
          print "<tr>\n";
          print "<td" . $class . ">" . $a_inv_inventory['inv_name'] . "</td>\n";
          print "<td" . $class . ">" . $callpath . "</td>\n";
          print "<td" . $class . ">" . $a_inv_inventory['svc_name'] . "</td>\n";
          print "<td" . $class . ">" . $a_inv_inventory['sw_software'] . "</td>\n";
          print "<td" . $class . ">" . $a_inv_inventory['sw_eol'] . "</td>\n";
          print "</tr>\n";
        }
      }
    }
  } else {
    if ($formVars['csv']) {
      print "No records found.\n";
      print "</p>\n";
    } else {
      print "<tr>\n";
      print "<td class=\"wi-widget-content\" colspan=\"5\">No records found.</td>\n";
      print "</tr>\n";
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

  $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,hw_purchased,mod_eol,ven_name,mod_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_svr_software  on inv_svr_software.svr_companyid = inv_inventory.inv_id ";
  $q_string .= "left join inv_software      on inv_software.sw_id             = inv_svr_software.svr_softwareid ";
  $q_string .= "left join inv_sw_types      on inv_sw_types.typ_id            = inv_software.sw_type ";
  $q_string .= "left join inv_hardware      on inv_inventory.inv_id           = inv_hardware.hw_companyid ";
  $q_string .= "left join inv_groups        on inv_groups.grp_id              = inv_hardware.hw_group ";
  $q_string .= "left join inv_models        on inv_models.mod_id              = inv_hardware.hw_vendorid ";
  $q_string .= "left join inv_vendors       on inv_vendors.ven_id             = inv_models.mod_vendor ";
  $q_string .= "left join inv_support       on inv_support.sup_id             = inv_hardware.hw_supportid ";
  $q_string .= "left join inv_products      on inv_products.prod_id           = inv_inventory.inv_product ";
  $q_string .= "left join inv_service       on inv_service.svc_id             = inv_inventory.inv_class ";
  $q_string .= $where . " and hw_primary = 1 and hw_deleted = 0 and typ_name = 'OS' ";
  $q_string .= $orderby;
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_inventory) > 0) {
    while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

      $class = " class=\"ui-widget-content\"";

      $callpath = "No";
      if ($a_inv_inventory['inv_callpath']) {
        $callpath = "Yes";
      }

      if ($a_inv_inventory['ven_name'] == 'Dell') {
        # For Dell, the end of support is 5 years after the purchase date
        $date = explode("-", $a_inv_inventory['hw_purchased']);
        $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
        $newdate = date("Y-m-d",$support);
      } else {
        if ($a_inv_inventory['mod_eol'] == '') {
          $a_inv_inventory['mod_eol'] = '1971-01-01';
        }
        $date = explode("-", $a_inv_inventory['mod_eol']);
        $support = mktime(0,0,0,$date[1],$date[2],$date[0]);
        $newdate = $a_inv_inventory['mod_eol'];
      }

      if ($newdate < date('Y-m-d') && $newdate != '1971-01-01') {

        if ($formVars['csv']) {
          print "\"" . $a_inv_inventory['inv_name']   . "\",";
          print "\"" . $callpath                  . "\",";
          print "\"" . $a_inv_inventory['svc_name']   . "\",";
          print "\"" . $a_inv_inventory['ven_name']   . "\",";
          print "\"" . $a_inv_inventory['mod_name']   . "\",";
          print "\"" . $newdate                   . "\"</br>\n";
        } else {
          print "<tr>\n";
          print "<td" . $class . ">" . $a_inv_inventory['inv_name'] . "</td>\n";
          print "<td" . $class . ">" . $callpath . "</td>\n";
          print "<td" . $class . ">" . $a_inv_inventory['svc_name'] . "</td>\n";
          print "<td" . $class . ">" . $a_inv_inventory['ven_name'] . "</td>\n";
          print "<td" . $class . ">" . $a_inv_inventory['mod_name'] . "</td>\n";
          print "<td" . $class . ">" . $newdate . "</td>\n";
          print "</tr>\n";
        }
      }
    }
  } else {
    if ($formVars['csv']) {
      print "No records found.\n";
      print "</p>\n";
    } else {
      print "<tr>\n";
      print "<td class=\"wi-widget-content\" colspan=\"6\">No records found.</td>\n";
      print "</tr>\n";
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
