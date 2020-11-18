<?php
# Script: compiled.eol.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "compiled.eol.php";

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
<title>End of Life Listing</title>

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

  $passthrough = "&group=" . $formVars['group'];

  $product = '';
  $q_string  = "select inv_id,inv_name,inv_function,prod_name,hw_group,mod_vendor,mod_name,mod_virtual,mod_eol,";
  $q_string .= "hw_serial,hw_purchased,grp_name,inv_appadmin,sup_company,sup_contract,hw_eolticket ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "left join hardware  on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join groups    on groups.grp_id    = hardware.hw_group ";
  $q_string .= "left join models    on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join support   on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products  on products.prod_id = inventory.inv_product ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $total++;
# vm's are subtracted below
    $totalhardware++;

    if ($a_inventory['prod_name'] == '') {
      $a_inventory['prod_name'] = 'Unassigned';
    }
    if ($product != $a_inventory['prod_name']) {
      if ($product != '') {
        print "</table>\n";
      }

      if ($formVars['csv']) {
        print "\"" . $a_inventory['prod_name'] . "\"</br>\n";
        print "\"System Name\",";
        print "\"Platform Owner\",";
        print "\"Application Owner\",";
        print "\"Function\",";
        print "\"Operating System\",";
        print "\"End of Life\",";
        print "\"Ticket\",";
        print "\"Hardware\",";
        print "\"End of Life\",";
        print "\"Ticket\"</br>\n";
      } else {
        print "<table class=\"ui-styled-table\">\n";
        print "<tr>\n";
        print "  <th class=\"ui-state-default\" colspan=\"10\">" . $a_inventory['prod_name'] . "</th>\n";
        print "</tr>\n";
        print "<tr>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"     . $passthrough . "\">System Name</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_group"     . $passthrough . "\">Platform Owner</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_appadmin" . $passthrough . "\">Application Owner</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function" . $passthrough . "\">Function</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function" . $passthrough . "\">Operating System</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_eol"      . $passthrough . "\">End of Life</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sw_eolticket" . $passthrough . "\">Ticket</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_purchased" . $passthrough . "\">Hardware</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_eol"      . $passthrough . "\">End of Life</a></th>\n";
        print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_eolticket" . $passthrough . "\">Ticket</a></th>\n";
        print "</tr>\n";
      }
      $product = $a_inventory['prod_name'];
    }

    $q_string  = "select sw_software,sw_eol,sw_eolticket ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_software = mysqli_fetch_array($q_software);

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_groups = mysqli_fetch_array($q_groups);

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
    $current = time();
    $moddate = $a_software['sw_eol'];

    $hwstatus = " class=\"ui-widget-content\"";
    if ($current > $support) {
      $hardware++;
      $grandtotal++;
      $hwstatus = " class=\"ui-state-error\"";
    }
    if ($a_software['sw_eol'] > date('Y-m-d')) {
      $swstatus = " class=\"ui-widget-content\"";
    } else {
      $software++;
      $grandtotal++;
      $swstatus = " class=\"ui-state-error\"";
    }

    if ($newdate == '' || $newdate == '0000-00-00') {
      $newdate = '----------';
      $hwstatus = " class=\"ui-state-highlight\"";
    }
    if ($a_software['sw_eol'] == '' || $a_software['sw_eol'] == '0000-00-00') {
      $moddate = '----------';
      $swstatus = " class=\"ui-state-highlight\"";
    }

    if ($a_inventory['hw_purchased'] == '0000-00-00' && $a_inventory['mod_eol'] == '0000-00-00') {
      $hwstatus = " class=\"ui-widget-content\"";
      $newdate = "Purchase Date Unset";
    }
    if ($a_inventory['mod_virtual'] == 1) {
      $totalhardware--;
      $hardware--;
      $grandtotal--;
      $hwstatus = " class=\"ui-widget-content\"";
      $newdate = '----------';
    }

    $nodate = " class=\"ui-widget-content\"";

    $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"blank_\">";
    $linkend   = "</a>";

    if ($formVars['csv']) {
      print "\"" . $a_inventory['inv_name'] . "\",";
      print "\"" . $a_inventory['grp_name'] . "\",";
      print "\"" . $a_groups['grp_name'] . "\",";
      print "\"" . $a_inventory['inv_function'] . "\",";
      print "\"" . $a_software['sw_software'] . "\",";
      print "\"" . $moddate . "\",";
      print "\"" . $a_software['sw_eolticket'] . "\",";
      print "\"" . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name'] . "\",";
      print "\"" . $newdate . "\",";
      print "\"" . $a_inventory['hw_eolticket'] . "\"</br>\n";
    } else {
      print "<tr>\n";
      print "  <td" . $nodate   . ">" . $linkstart . $a_inventory['inv_name']                         . $linkend . "</td>\n";
      print "  <td" . $nodate   . ">"              . $a_inventory['grp_name']                                    . "</td>\n";
      print "  <td" . $nodate   . ">"              . $a_groups['grp_name']                                       . "</td>\n";
      print "  <td" . $nodate   . ">"              . $a_inventory['inv_function']                                . "</td>\n";
      print "  <td" . $swstatus . ">"              . $a_software['sw_software']                                  . "</td>\n";
      print "  <td" . $swstatus . ">"              . $moddate                                                    . "</td>\n";
      print "  <td" . $swstatus . ">"              . $a_software['sw_eolticket']                                 . "</td>\n";
      print "  <td" . $hwstatus . ">"              . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name'] . "</td>\n";
      print "  <td" . $hwstatus . ">"              . $newdate                                                    . "</td>\n";
      print "  <td" . $hwstatus . ">"              . $a_inventory['hw_eolticket']                                . "</td>\n";
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
