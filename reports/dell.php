<?php
# Script: dell.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "dell.php";

  logaccess($db, $formVars['uid'], $package, "Searching for Dell equipment.");

  $formVars['group']     = clean($_GET['group'],      10);
  $formVars['product']   = clean($_GET['product'],    10);
  $formVars['project']   = clean($_GET['project'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],     10);
  $formVars['country']   = clean($_GET['country'],    10);
  $formVars['state']     = clean($_GET['state'],      10);
  $formVars['city']      = clean($_GET['city'],       10);
  $formVars['location']  = clean($_GET['location'],   10);
  $formVars['csv']       = clean($_GET['csv'],        10);

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by inv_name";
    $_SESSION['sort'] = '';
  }

  $formVars['type'] = '';
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  }

  if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['project'] == '') {
    $formVars['project'] = 0;
  }
  if ($formVars['country'] == '') {
    $formVars['country'] = 0;
  }
  if ($formVars['state'] == '') {
    $formVars['state'] = 0;
  }
  if ($formVars['city'] == '') {
    $formVars['city'] = 0;
  }
  if ($formVars['location'] == '') {
    $formVars['location'] = 0;
  }
  if ($formVars['csv'] == '') {
    $formVars['csv'] = 'false';
  }

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $formVars['sort'] = clean($_GET['sort'], 30);
    $orderby .= $formVars['sort'] . ",";
  }
  $orderby .= "inv_name";

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = -1;
  }

  $where = "where hw_serial != '' and mod_vendor = 'Dell' and mod_virtual = 0 and inv_status = 0 ";
  if ($formVars['group'] != -1) {
    $where .= " and (grp_id = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . ") ";
  }

  if ($formVars['project'] != 0) {
    $where .= "and inv_project = " . $formVars['project'] . " ";
  }
  if ($formVars['product'] != 0) {
    $where .= "and inv_product = " . $formVars['product'] . " ";
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

  $passthrough = 
    "&group=" . $formVars['group'];

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Dell Hardware End-of-Life</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This page lists all the Dell hardware and the end of life dates.</p>\n";

  print "<p>Dell hardware end of life is based on the purchase date of the hardware. The hardware was added to the Dell account via \n";
  print "Service tag and the purchase date was inserted into the inventory for each system.</p>\n";

  print "<p>The Supported column checkboxes are checked if the system is on Lynda Lilly's contract spreadsheet.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";


  if ($formVars['csv'] == 'true') {
    print "\"System Name\",";
    if ($formVars['group'] == -1) {
      print "\"Device Owner\",";
    } else {
      print "\"Function\",";
    }
    print "\"Product\",";
    print "\"Service Tag\",";
    print "\"Purchase Date\",";
    print "\"End of Support\",";
    print "\"Support Type\",";
    print "\"Supported\"</br>\n";
  } else {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"     . $passthrough . "\">System Name</a></th>\n";
    if ($formVars['group'] == -1) {
      print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_group"     . $passthrough . "\">Device Owner</a></th>\n";
    } else {
      print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function" . $passthrough . "\">Function</a></th>\n";
    }
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"      . $passthrough . "\">Product</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_serial"      . $passthrough . "\">Service Tag</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_purchased" . $passthrough . "\">Purchase Date</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_purchased" . $passthrough . "\">End of Support</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sup_contract" . $passthrough . "\">Support Type</a></th>\n";
    print "  <th class=\"ui-state-default\">Supported</th>\n";
    print "</tr>\n";
  }

  $q_string  = "select inv_id,inv_name,inv_function,prod_name,hw_group,hw_serial,hw_purchased,grp_name,sup_company,sup_contract,hw_supid_verified ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join groups   on groups.grp_id    = hardware.hw_group ";
  $q_string .= "left join models   on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join support  on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($a_inventory['hw_serial'] != "" && $a_inventory['hw_serial'] != "N/A" && $a_inventory['hw_serial'] != "None" && $a_inventory['hw_serial'] != "VM") {

      # For Dell, the end of support is 5 years after the purchase date
      $date = explode("-", $a_inventory['hw_purchased']);
      $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
      $newdate = date("Y-m-d",$support);
      $current = time();

      if ($current > $support) {
        $status = "ui-state-error";
      } else {
        $status = "ui-widget-content";
      }
      if ($a_inventory['hw_purchased'] == '0000-00-00') {
        $nodate = "ui-state-highlight";
      } else {
        $nodate = "ui-widget-content";
      }

      $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"blank_\">";
      $linkend   = "</a>";

      if ($formVars['csv'] == 'true') {
        print "\"" . $a_inventory['inv_name'] . "\",";
        if ($formVars['group'] == -1) {
          print "\"" . $a_inventory['grp_name'] . "\",";
        } else {
          print "\"" . $a_inventory['inv_function'] . "\",";
        }
        print "\"" . $a_inventory['prod_name'] . "\",";
        print "\"" . $a_inventory['hw_serial'] . "\",";
        print "\"" . $a_inventory['hw_purchased'] . "\",";
        print "\"" . $newdate;
        print "\"" . $a_inventory['sup_company']  . " - " . $a_inventory['sup_contract'] . "\",";
        if ($a_inventory['hw_supid_verified']) {
          print "\"Yes\"</br>\n";
        } else {
          print "\"No\"</br>\n";
        }
      } else {
        print "<tr>\n";
        print "  <td class=\"" . $nodate . "\">" . $linkstart . $a_inventory['inv_name']     . $linkend . "</td>\n";
        if ($formVars['group'] == -1) {
          print "  <td class=\"" . $nodate . "\">"            . $a_inventory['grp_name']     . "</td>\n";
        } else {
          print "  <td class=\"" . $nodate . "\">"            . $a_inventory['inv_function'] . "</td>\n";
        }
        print "  <td class=\"" . $nodate . "\">"              . $a_inventory['prod_name']    . "</td>\n";
        print "  <td class=\"" . $nodate . "\">"              . $a_inventory['hw_serial']   . "</td>\n";
        print "  <td class=\"" . $nodate . "\">"              . $a_inventory['hw_purchased'] . "</td>\n";
        print "  <td class=\"" . $status . "\">"              . $newdate                     . "</td>\n";
        print "  <td class=\"" . $nodate . "\">"              . $a_inventory['sup_company']  . " - " . $a_inventory['sup_contract']            . "</td>\n";
        if ($a_inventory['hw_supid_verified']) {
          print "  <td class=\"" . $nodate . " delete\">"              . "<input type=\"checkbox\" checked>"  . "</td>\n";
        } else {
          print "  <td class=\"" . $nodate . " delete\">"              . "<input type=\"checkbox\">"  . "</td>\n";
        }
        print "</tr>\n";
      }
    }

  }

?>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
