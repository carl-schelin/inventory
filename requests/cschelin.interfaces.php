<?php
# Script: cschelin.interfaces.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "cschelin.interfaces.php";

  logaccess($formVars['uid'], $package, "Getting a listing of unassigned Interfaces.");

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
  print "  <th class=\"ui-state-default\">Interface</th>\n";
  print "  <th class=\"ui-state-default\">IP Address</th>\n";
  print "  <th class=\"ui-state-default\">Gateway</th>\n";
  print "  <th class=\"ui-state-default\">Interface</th>\n";
  print "  <th class=\"ui-state-default\">Virt</th>\n";
  print "  <th class=\"ui-state-default\">Type</th>\n";
  print "  <th class=\"ui-state-default\">Changed</th>\n";
  print "  <th class=\"ui-state-default\">U</th>\n";
  print "</tr>\n";

  $q_string  = "select inv_id,int_id,int_server,inv_name,int_face,int_addr,itp_name,";
  $q_string .= "int_gate,int_virtual,int_update,int_verified,int_user,usr_last ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "left join inttype   on inttype.itp_id   = interface.int_type ";
  $q_string .= "left join users     on users.usr_id     = interface.int_user ";
  $q_string .= "where inv_manager = " . $formVars['group'] . " and inv_status = 0 and int_user != 2 and int_ip6 = 0 " . $formVars['filter'];
  $q_string .= "order by int_addr,inv_name,int_server ";
  $q_interface = mysql_query($q_string) or die(mysql_error());
  while ($a_interface = mysql_fetch_array($q_interface) ) {

    if ($a_interface['int_verified']) {
      $checked = "&#x2713;";
    } else {
      $checked = "&#x2717;";
    }

    if ($a_interface['int_user'] != 2) {
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_id'] . "</td>\n";
      print "  <td class=\"ui-widget-content\"><a href=\"" . $Editroot . "/inventory.php?server=" . $a_interface['inv_id'] . "\" target=\"_blank\">" . $a_interface['inv_name'] . "</a></td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_server'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_addr'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_gate'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_face'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_virtual'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['itp_name'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['int_update'] . $checked . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_interface['usr_last'] . "</td>\n";
      print "</tr>\n";

      $count++;
    }
    $total++;
  }

  print "</table>\n";

  print "<p>Total: " . $total . " Left: " . $count . " Completed: " . ($total - $count) . "</p>\n";
?>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
