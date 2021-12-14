<?php
# Script: webapps.certs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/nologin.php');

  $package = "webapps.certs.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

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
  $q_string .= "left join a_groups on a_groups.grp_id = certs.cert_group ";
  $q_string .= "order by cert_url,cert_expire";
  $q_certs = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_certs) > 0) {
    while ($a_certs = mysqli_fetch_array($q_certs)) {

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

      $q_string  = "select svr_id,inv_name,sw_software,prod_name,grp_name ";
      $q_string .= "from svr_software ";
      $q_string .= "left join software  on software.sw_id     = svr_software.svr_softwareid ";
      $q_string .= "left join inventory on inventory.inv_id   = svr_software.svr_companyid ";
      $q_string .= "left join products  on products.prod_id   = software.sw_product ";
      $q_string .= "left join a_groups  on a_groups.grp_id    = svr_software.svr_groupid ";
      $q_string .= "where svr_certid = " . $a_certs['cert_id'];
      $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_svr_software) > 0) {
        while ($a_svr_software = mysqli_fetch_array($q_svr_software)) {

          print "<tr>\n";
          print "  <td class=\"ui-widget-content\">--" . $a_svr_software['inv_name']    . "</td>\n";
          print "  <td class=\"ui-widget-content\">"   . $a_svr_software['sw_software'] . "</td>\n";
          print "  <td class=\"ui-widget-content\">"   . $a_svr_software['prod_name']   . "</td>\n";
          print "  <td class=\"ui-widget-content\">"   . $a_svr_software['grp_name']    . "</td>\n";
          print "</tr>\n";

        }
      }
    }
  } else {
      print "<tr>";
      print "  <td class=\"ui-widget-content\" colspan=\"4\">No Certificates Defined</td>\n";
      print "</tr>";
  }

  mysqli_free_result($q_certs);

  print "</table>\n";
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
