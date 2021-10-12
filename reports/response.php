<?php
# Script: response.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "response.php";

  logaccess($db, $formVars['uid'], $package, "Searching for Dell equipment.");

  $formVars['sort'] = 'inv_response';
  $leftjoin = "left join supportlevel on supportlevel.slv_id = inventory.inv_response ";

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $formVars['sort'] = clean($_GET['sort'], 30);

    if ($formVars['sort'] == 'inv_response') {
      $leftjoin = "left join supportlevel on supportlevel.slv_id = inventory.inv_response ";
      $formVars['sort'] = 'slv_value';
      $orderby .= $formVars['sort'] . ",";
      $formVars['sort'] = 'inv_response';
    }

# if hw_response
    if ($formVars['sort'] == 'hw_response') {
      $leftjoin = "left join supportlevel on supportlevel.slv_id = hardware.hw_response ";
      $formVars['sort'] = 'slv_value';
      $orderby .= $formVars['sort'] . ",";
      $formVars['sort'] = 'hw_response';
    }

# if inv_name (default)
    if ($formVars['sort'] != 'inv_name') {
      $orderby .= $formVars['sort'] . ",";
    }
  }
  $orderby .= "inv_name";

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = -1;
  }

#  $where = "where hw_primary = 1 and hw_serial != '' and mod_virtual = 0 and inv_status = 0 ";
  $where = "where hw_primary = 1 and mod_virtual = 0 and inv_status = 0 ";
  if ($formVars['group'] != -1) {
    $where .= " and (grp_id = " . $formVars['group'] . " or inv_appadmin = " . $formVars['group'] . ") ";
  }

  if (isset($_GET['support'])) {
    if ($_GET['support'] == 'no') {
      $where .= " and hw_supid_verified = 0 ";
    }
    if ($_GET['support'] == 'yes') {
      $where .= " and hw_supid_verified = 1 ";
    }
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
<title>Hardware Response Level</title>

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
  print "  <th class=\"ui-state-default\">Hardware Response</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report lists all Hardware with a Serial Number and/or a Dell Service tag in the Inventory for the selected group.</p>\n";

  print "<ul>\n";
  print "  <li><strong>System Name</strong> - The name of the system in the inventory. Clicking here will take you to the Edit form (limited if you aren't the system custodian).</li>\n";
  print "  <li><strong>Service Class</strong> - The selected Service Class of the system based on <a href=\"" . $Siteroot . "/admin/service.php\">this table</a>. An * next to the Service Class indicates the system is in the 911 Call Path.</li>\n";
  print "  <li><strong>Device Owner</strong> - The group that are the custodians of the system.</li>\n";
  print "  <li><strong>Hardware</strong> - The covered hardware.</li>\n";
  print "  <li><strong>Product</strong> - The Product this system is associated with.</li>\n";
  print "  <li><strong>Asset Tag</strong> - The asset tag. An '*' here indicates the Asset Tag could not be visually verified. If Asset Tag info is here, it was captured other than visually.</li>\n";
  print "  <li><strong>Serial Number</strong> - Vendor Serial Number. An '*' here indicates the Serial Number could not be visually verified. If Serial Number info is here, it was captured other than visually.</li>\n";
  print "  <li><strong>Service Tag</strong> - The Dell Service Tag. An '*' here indicates the Service Tag could not be visually verified. If Service Tag info is here, it was captured other than visually.</li>\n";
  print "  <li><strong>Recommended</strong> - The recommended or suggested vendor response time for this system. By default, LMCS and 911 Call Path systems are 24x7 2 Hour Response. Other production systems are 24x7 4 Hour Response. Operations Lab environment is 9x5 Next Business Day Response.</li>\n";
  print "  <li><strong>Vendor</strong> - The Support company.</li>\n";
  print "  <li><strong>Response</strong> - The contracted response time.</li>\n";
  print "</ul>\n";

  print "<p>The Vendor and Response information was captured from Lynda Lilly's contract spreadsheet. If it's not \n";
  print "on her spreadsheet, it's not supported. Systems that are <span class=\"ui-state-error\">highlighted</span> \n";
  print "are <strong>not supported</strong>. Systems that are <span class=\"ui-state-highlight\">highlighted</span> \n";
  print "have a Recommended support level that is <strong>different</strong> than the contracted Response level.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name"     . $passthrough . "\">System Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=svc_acronym,inv_callpath"  . $passthrough . "\">Service Class</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_manager"  . $passthrough . "\">Device Owner</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_name"     . $passthrough . "\">Hardware</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=prod_name"    . $passthrough . "\">Product</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_asset"     . $passthrough . "\">Asset Tag</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_serial"    . $passthrough . "\">Serial Number</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_response" . $passthrough . "\">Recommended</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=sup_company"  . $passthrough . "\">Vendor</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=hw_response"  . $passthrough . "\">Response</a></th>\n";
  print "  <th class=\"ui-state-default\">&nbsp;</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_response,inv_callpath,mod_name,sup_company,hw_asset,";
  $q_string .= "hw_serial,hw_response,hw_supid_verified,prod_name,grp_name,svc_acronym,slv_value ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join a_groups   on a_groups.grp_id    = hardware.hw_group ";
  $q_string .= "left join models   on models.mod_id    = hardware.hw_vendorid ";
  $q_string .= "left join support  on support.sup_id   = hardware.hw_supportid ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join service  on service.svc_id   = inventory.inv_class ";
  $q_string .= $leftjoin;
  $q_string .= $where;
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">";
    $linkend   = "</a>";

    if ($a_inventory['inv_callpath']) {
      $callpath = '*';
    } else {
      $callpath = '';
    }

    if ($formVars['sort'] == 'hw_response') {
      $q_string  = "select slv_value ";
      $q_string .= "from supportlevel ";
      $q_string .= "where slv_id = " . $a_inventory['inv_response'];
      $q_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_supportlevel) > 0) {
        $a_supportlevel = mysqli_fetch_array($q_supportlevel);
        $inv_response = $a_supportlevel['slv_value'];
        
      } else {
        $inv_response = "Unassigned";
      }
      $hw_response = $a_inventory['slv_value'];
    }

    if ($formVars['sort'] == 'inv_response') {
      $q_string  = "select slv_value ";
      $q_string .= "from supportlevel ";
      $q_string .= "where slv_id = " . $a_inventory['hw_response'];
      $q_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_supportlevel) > 0) {
        $a_supportlevel = mysqli_fetch_array($q_supportlevel);
        $hw_response = $a_supportlevel['slv_value'];
      } else {
        $hw_response = "Unassigned";
      }
      $inv_response = $a_inventory['slv_value'];
    }

    if ($a_inventory['hw_supid_verified']) {
      if ($hw_response == $inv_response) {
        $class = " class=\"ui-widget-content\"";
      } else {
        $class = " class=\"ui-state-highlight\"";
      }
    } else {
      $class = " class=\"ui-state-error\"";
    }

    print "<tr>\n";
    print "  <td" . $class . ">" . $linkstart . $a_inventory['inv_name']     . $linkend . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['svc_acronym'] . $callpath . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['grp_name']                . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['mod_name']                . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['prod_name']               . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['hw_asset']                . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['hw_serial']               . "</td>\n";
    print "  <td" . $class . ">"              . $inv_response                           . "</td>\n";
    print "  <td" . $class . ">"              . $a_inventory['sup_company']             . "</td>\n";
    print "  <td" . $class . ">"              . $hw_response                            . "</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
