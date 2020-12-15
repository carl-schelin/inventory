<?php
# Script: cschelin.disks.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "cschelin.disks.php";

  logaccess($db, $formVars['uid'], $package, "Getting a listing of hard disks.");

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

  if (isset($_GET['filter'])) {
    $formVars['filter'] = clean($_GET['filter'], 10);
# 4 is miami
# 31 is lab
# 39 is sqa
# 3 is prod
#    $formVars['filter'] = " and inv_location = 3 ";
#    $formVars['filter'] = " and inv_location = 4 and (int_type = 0 or int_gate = '') ";
  } else {
    $formVars['filter'] = " ";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Listing of Disks</title>

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
  $count = 0;
  $total = 0;

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">System ID</th>\n";
  print "  <th class=\"ui-state-default\">System Name</th>\n";
  print "  <th class=\"ui-state-default\">Disk</th>\n";
  print "  <th class=\"ui-state-default\">Mod Size</th>\n";
  print "  <th class=\"ui-state-default\">HW Size</th>\n";
  print "  <th class=\"ui-state-default\">Speed</th>\n";
  print "  <th class=\"ui-state-default\">Changed</th>\n";
  print "  <th class=\"ui-state-default\">User</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,inv_name,inv_ssh,mod_name,mod_size,hw_size,mod_speed,hw_update,hw_verified,usr_last ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join users     on users.usr_id     = hardware.hw_user ";
  $q_string .= "where inv_manager = " . $formVars['group'] . " and inv_status = 0 and hw_type = 2 and hw_verified = 0 " . $formVars['filter'];
  $q_string .= "order by inv_name ";
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_hardware = mysqli_fetch_array($q_hardware) ) {

    if ($a_hardware['inv_ssh']) {
      $ssh = "*";
    } else {
      $ssh = "";
    }

    if ($a_hardware['hw_verified']) {
      $checked = "&#x2713;";
    } else {
      $checked = "";
    }

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['inv_id'] . "</td>\n";
    print "  <td class=\"ui-widget-content\"><a href=\"" . $Editroot . "/inventory.php?server=" . $a_hardware['inv_id'] . "#hardware\" target=\"_blank\">" . $a_hardware['inv_name'] . "</a>" . $ssh . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_name'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_size'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['hw_size'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['mod_speed'] . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['hw_update'] . $checked . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_hardware['usr_last'] . "</td>\n";
    print "</tr>\n";

  }

  print "</table>\n";
?>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
