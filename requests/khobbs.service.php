<?php
# Script: khobbs.service.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "khobbs.service.php";

  logaccess($formVars['uid'], $package, "Checking out the Service Levels.");

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $formVars['sort'] = clean($_GET['sort'], 30);
    $orderby .= $formVars['sort'] . ",";
  }
  $orderby .= "inv_name";

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

  if (isset($_GET['type'])) {
    $formVars['type'] = trim(clean($_GET['type'], 10));
  } else {
    $formVars['type'] = 0;
  }

  if ($formVars['group'] == -1) {
    $admin = "";
  } else {
    $admin = " where inv_manager = " . $formVars['group'];
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<base href="<?php print $Siteroot; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Ken Hobbs: Service Levels</title>

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
  <th class="ui-state-default">Service Class</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('service-help');">Help</a></th>
</tr>
</table>

<div id="service-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Servers marked with an asterisk (*) are systems that are accessible via ssh by the unixsvc account.</li>
    <li>Systems that are <span class="ui-state-error">highlighted</span> are identified as in the 911 Call Path.</li>
    <li>Click on the headers to change the sort order.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<?php

  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $Requestroot . "/" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_product\">Product</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $Requestroot . "/" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_class\">Service Class</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $Requestroot . "/" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_name\">Name</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $Requestroot . "/" . $package . "?group=" . $formVars['group'] . "&type=" . $formVars['type'] . "&sort=inv_function\">Description</a></th>\n";
  print "</tr>\n";

  if ($formVars['group'] == -1) {
    $admin = "";
  } else {
    $admin = " and inv_manager = " . $formVars['group'] . " ";
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = " and inv_status = 0 ";
  }

  $q_string  = "select hw_companyid,hw_vendorid,hw_serial,hw_asset,inv_ssh,prod_name,";
  $q_string .= "svc_acronym,inv_name,inv_function,inv_manager,inv_callpath ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join service on service.svc_id = inventory.inv_class ";
  $q_string .= "where hw_companyid != 0 and hw_primary = 1 and inv_status = 0 " . $admin . $type;
  $q_string .= $orderby;
  $q_hardware = mysql_query($q_string) or die("Inventory test: " . $q_string . ": " . mysql_error());
  while ($a_hardware = mysql_fetch_array($q_hardware)) {

    if (check_userlevel('2')) {
      if (check_grouplevel($a_hardware['inv_manager'])) {
        $linkedit = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_hardware['hw_companyid'] . "\"><img src=\"" . $Siteroot . "/imgs/pencil.gif\"></a>";
      }
    }
    $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server=" . $a_hardware['hw_companyid'] . "\">";
    $linkend   = "</a>";

    $class = "ui-widget-content";
    if ($a_hardware['inv_callpath']) {
      $class = "ui-state-error";
    }

    if ($a_hardware['inv_ssh'] == 1) {
      $sshaccess = "*";
    } else {
      $sshaccess = "";
    }

    print "<tr>\n";
    print "  <td class=\"" . $class . "\">"                          . $a_hardware['prod_name']                        . "</td>\n";
    print "  <td class=\"" . $class . "\">"                          . $a_hardware['svc_acronym']                      . "</td>\n";
    print "  <td class=\"" . $class . "\">" . $linkedit . $linkstart . $a_hardware['inv_name'] . $linkend . $sshaccess . "</td>\n";
    print "  <td class=\"" . $class . "\">"                          . $a_hardware['inv_function']                     . "</td>\n";
    print "</tr>\n";

  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
