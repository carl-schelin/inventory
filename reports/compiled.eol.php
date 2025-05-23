<?php
# Script: compiled.eol.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "compiled.eol.php";

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
    $orderby .= "order by prod_name,inv_name";
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
<title>End of Life Listing</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<?php

  $passthrough = "&group=" . $formVars['group'];

  $product = '';
  $q_string  = "select inv_id,inv_name,inv_function,prod_name,hw_group,ven_name,mod_name,mod_virtual,mod_eol,";
  $q_string .= "hw_serial,hw_purchased,grp_name,inv_appadmin,sup_company,sup_contract,hw_eolticket ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_svr_software on inv_svr_software.svr_companyid = inv_inventory.inv_id ";
  $q_string .= "left join inv_software  on inv_software.sw_id                = inv_svr_software.svr_softwareid ";
  $q_string .= "left join inv_sw_types  on inv_sw_types.typ_id               = inv_software.sw_type ";
  $q_string .= "left join inv_hardware  on inv_inventory.inv_id              = inv_hardware.hw_companyid ";
  $q_string .= "left join inv_groups    on inv_groups.grp_id                 = inv_hardware.hw_group ";
  $q_string .= "left join inv_models    on inv_models.mod_id                 = inv_hardware.hw_vendorid ";
  $q_string .= "left join inv_vendors   on inv_vendors.ven_id                = inv_models.mod_vendor ";
  $q_string .= "left join inv_support   on inv_support.sup_id                = inv_hardware.hw_supportid ";
  $q_string .= "left join inv_products  on inv_products.prod_id              = inv_inventory.inv_product ";
  $q_string .= $where . "and typ_name = 'Operating System' ";
  $q_string .= $orderby;
  $q_inv_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

    $total++;
# vm's are subtracted below
    $totalhardware++;

    if ($a_inv_inventory['prod_name'] == '') {
      $a_inv_inventory['prod_name'] = 'Unassigned';
    }
    if ($product != $a_inv_inventory['prod_name']) {
      if ($product != '') {
        print "</table>\n";
      }

      if ($formVars['csv']) {
        print "\"" . $a_inv_inventory['prod_name'] . "\"</br>\n";
        print "\"System Name\",";
        print "\"Platform Owner\",";
        print "\"Application Owner\",";
        print "\"Function\",";
        print "\"Operating System\",";
        print "\"End of Life\",";
        print "\"Hardware\",";
        print "\"End of Life\",";
        print "\"Ticket\"</br>\n";
      } else {
        print "<table class=\"ui-styled-table\">\n";
        print "<tr>\n";
        print "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_inv_inventory['prod_name'] . "</th>\n";
        print "</tr>\n";
        print "<tr>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"     . $passthrough . "\">System Name</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_group"     . $passthrough . "\">Platform Owner</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_appadmin" . $passthrough . "\">Application Owner</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function" . $passthrough . "\">Function</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function" . $passthrough . "\">Operating System</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_eol"      . $passthrough . "\">End of Life</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_purchased" . $passthrough . "\">Hardware</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_eol"      . $passthrough . "\">End of Life</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_eolticket" . $passthrough . "\">Ticket</a></th>\n";
        print "</tr>\n";
      }
      $product = $a_inv_inventory['prod_name'];
    }

    $q_string  = "select sw_software,sw_eol ";
    $q_string .= "from inv_svr_software ";
    $q_string .= "left join inv_software on inv_software.sw_id  = inv_svr_software.svr_softwareid ";
    $q_string .= "left join inv_sw_types on inv_sw_types.typ_id = inv_software.sw_type ";
    $q_string .= "where svr_companyid = " . $a_inv_inventory['inv_id'] . " and typ_name = 'Operating System' ";
    $q_inv_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_inv_svr_software = mysqli_fetch_array($q_inv_svr_software);

    $q_string  = "select grp_name ";
    $q_string .= "from inv_groups ";
    $q_string .= "where grp_id = " . $a_inv_inventory['inv_appadmin'] . " ";
    $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_inv_groups = mysqli_fetch_array($q_inv_groups);

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
    $current = time();
    $moddate = $a_inv_svr_software['sw_eol'];

    $hwstatus = " class=\"ui-widget-content\"";
    if ($current > $support) {
      $hardware++;
      $grandtotal++;
      $hwstatus = " class=\"ui-state-error\"";
    }
    if ($a_inv_svr_software['sw_eol'] > date('Y-m-d')) {
      $swstatus = " class=\"ui-widget-content\"";
    } else {
      $software++;
      $grandtotal++;
      $swstatus = " class=\"ui-state-error\"";
    }

    if ($newdate == '' || $newdate == '1971-01-01') {
      $newdate = '----------';
      $hwstatus = " class=\"ui-state-highlight\"";
    }
    if ($a_inv_svr_software['sw_eol'] == '' || $a_inv_svr_software['sw_eol'] == '1971-01-01') {
      $moddate = '----------';
      $swstatus = " class=\"ui-state-highlight\"";
    }

    if ($a_inv_inventory['hw_purchased'] == '1971-01-01' && $a_inv_inventory['mod_eol'] == '1971-01-01') {
      $hwstatus = " class=\"ui-widget-content\"";
      $newdate = "Purchase Date Unset";
    }
    if ($a_inv_inventory['mod_virtual'] == 1) {
      $totalhardware--;
      $hardware--;
      $grandtotal--;
      $hwstatus = " class=\"ui-widget-content\"";
      $newdate = '----------';
    }

    $nodate = " class=\"ui-widget-content\"";

    $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inv_inventory['inv_id'] . "\" target=\"blank_\">";
    $linkend   = "</a>";

    if ($formVars['csv']) {
      print "\"" . $a_inv_inventory['inv_name'] . "\",";
      print "\"" . $a_inv_inventory['grp_name'] . "\",";
      print "\"" . $a_inv_groups['grp_name'] . "\",";
      print "\"" . $a_inv_inventory['inv_function'] . "\",";
      print "\"" . $a_inv_svr_software['sw_software'] . "\",";
      print "\"" . $moddate . "\",";
      print "\"" . $a_inv_inventory['ven_name'] . " " . $a_inv_inventory['mod_name'] . "\",";
      print "\"" . $newdate . "\",";
      print "\"" . $a_inv_inventory['hw_eolticket'] . "\"</br>\n";
    } else {
      print "<tr>\n";
      print "  <td" . $nodate   . ">" . $linkstart . $a_inv_inventory['inv_name']                         . $linkend . "</td>\n";
      print "  <td" . $nodate   . ">"              . $a_inv_inventory['grp_name']                                    . "</td>\n";
      print "  <td" . $nodate   . ">"              . $a_inv_groups['grp_name']                                       . "</td>\n";
      print "  <td" . $nodate   . ">"              . $a_inv_inventory['inv_function']                                . "</td>\n";
      print "  <td" . $swstatus . ">"              . $a_inv_svr_software['sw_software']                                  . "</td>\n";
      print "  <td" . $swstatus . ">"              . $moddate                                                    . "</td>\n";
      print "  <td" . $hwstatus . ">"              . $a_inv_inventory['ven_name'] . " " . $a_inv_inventory['mod_name'] . "</td>\n";
      print "  <td" . $hwstatus . ">"              . $newdate                                                    . "</td>\n";
      print "  <td" . $hwstatus . ">"              . $a_inv_inventory['hw_eolticket']                                . "</td>\n";
      print "</tr>\n";
    }
  }

  if ($formVars['csv'] == 0) {
    print "</table>\n";
    $swpercent = ($software / $total) * 100;
    $hwpercent = ($hardware / $totalhardware) * 100;
    $gtpercent = ($grandtotal / $total) * 100;
    print "<p class=\"ui-widget-content\">Total Systems: " . number_format($total, 0, ".", ",") . " Grand Total EOL: " . number_format($grandtotal, 0, ".", ",") . " (" . number_format($gtpercent, 2, ".", ",") . "%) Total Operating System EOL: " . number_format($software, 0, ".", ",") . " (" . number_format($swpercent, 2, ".", ",") . "%) Total Hardware EOL: " . number_format($hardware, 0, ".", ",") . " (" . number_format($hwpercent, 2, ".", ",") . "%)</p>\n";
  }

?>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
