<?php
# Script: webapps.certs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/nologin.php');

  $package = "webapps.certs.php";

  logaccess($formVars['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Certificate Review</title>

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

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Description</th>
  <th class="ui-state-default">Expiration</th>
  <th class="ui-state-default">Authority</th>
  <th class="ui-state-default">Certificate Manager</th>
</tr>
<tr>
  <th class="ui-state-default">Server Name</th>
  <th class="ui-state-default">Software</th>
  <th class="ui-state-default">Product</th>
  <th class="ui-state-default">Responsible Group</th>
</tr>
<?php
  $date = time();
  $warningdate = mktime(0, 0, 0, date('m'), date('d') + 90, date('Y'));

  $q_string  = "select cert_id,cert_desc,cert_url,cert_expire,cert_authority,grp_name ";
  $q_string .= "from certs ";
  $q_string .= "left join groups on groups.grp_id = certs.cert_group ";
  $q_string .= "order by cert_url,cert_expire";
  $q_certs = mysql_query($q_string) or die (mysql_error());
  while ($a_certs = mysql_fetch_array($q_certs)) {

    $certtime = strtotime($a_certs['cert_expire']);

    $class = " class=\"ui-widget-content\"";
    if ($certtime < $date) {
      $class = " class=\"ui-state-error\"";
    } else {
      if ($certtime < $warningdate) {
        $class = " class=\"ui-state-highlight\"";
      }
    }

    print "<tr>";
    print "  <td" . $class . " title=\"" . $a_certs['cert_url'] . "\">" . $a_certs['cert_desc']      . "</td>";
    print "  <td" . $class . ">"                                        . $a_certs['cert_expire']    . "</td>";
    print "  <td" . $class . ">"                                        . $a_certs['cert_authority'] . "</td>";
    print "  <td" . $class . ">"                                        . $a_certs['grp_name']       . "</td>";
    print "</tr>";

    $q_string  = "select sw_id,inv_name,sw_software,prod_name,grp_name ";
    $q_string .= "from software ";
    $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
    $q_string .= "left join products on products.prod_id = software.sw_product ";
    $q_string .= "left join groups on groups.grp_id = software.sw_group ";
    $q_string .= "where sw_cert = " . $a_certs['cert_id'];
    $q_software = mysql_query($q_string) or die(mysql_error());
    while ($a_software = mysql_fetch_array($q_software)) {

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">--" . $a_software['inv_name']    . "</a></td>";
      print "  <td class=\"ui-widget-content\">"   . $a_software['sw_software'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">"   . $a_software['prod_name']   . "</a></td>";
      print "  <td class=\"ui-widget-content\">"   . $a_software['grp_name']    . "</a></td>";
      print "</tr>\n";

    }
  }

  mysql_free_result($q_certs);

  print "</table>\n";
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
