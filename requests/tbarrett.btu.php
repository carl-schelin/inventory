<?php
# Script: tbarrett.btu.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "tbarrett.btu.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

  $orderby = " order by ";
  if (isset($_GET['sort'])) {
    $orderby .= $_GET['sort'] . ", ";
  }
  $orderby .= "mod_vendor,mod_name";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Show Power/BTU Ratings</title>

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
<?php
  print "<tr>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_id\">ID</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_vendor\">Vendor</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_name\">Model</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=part_name\">Type</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=volt_text\">Volts</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_start\">Start</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_draw\">Draw</a></th>\n";
  print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_btu\">BTU</a></th>\n";
  print "</tr>\n";

  $q_string  = "select mod_id,mod_vendor,mod_name,part_name,volt_text,mod_start,mod_draw,mod_btu ";
  $q_string .= "from models ";
  $q_string .= "left join parts on parts.part_id = models.mod_type ";
  $q_string .= "left join int_volts on int_volts.volt_id = models.mod_volts ";
  $q_string .= "where mod_primary = 1 and mod_virtual = 0 ";
  $q_string .= $orderby;
  $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_models) > 0) {
    while ($a_models = mysqli_fetch_array($q_models)) {

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['mod_id']     . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['mod_vendor'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['mod_name']   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['part_name']  . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['volt_text']  . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['mod_start']  . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['mod_draw']   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_models['mod_btu']    . "</td>\n";
      print "</tr>\n";

    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"8\">No records found.</td>\n";
    print "</tr>\n";
  }

  mysqli_free_result($q_models);

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
