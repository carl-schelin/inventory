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

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

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
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
  <th class="ui-state-default">Certificate Viewing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('cert-viewing-help');">Help</a></th>
</tr>
</table>

<div id="cert-viewing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Certificate Viewing</strong></p>

<p>This page provides the viewing user a list of all the certificates currently being managed in addition to a list of services and servers that use the certificate.</p>

<p>The report displays a single line describing the certificate.</p>

<ul>
  <li><strong>Description</strong>> - A description of the certificate.</li>
  <li><strong>Expiration</strong> - The certificate expiration date. The certificate line will be <span class="ui-state-highlight">highlighted</span> 
if the expiration date is within 60 days and <span class="ui-state-error">highlighted</span> if the certificate has expired.</li>
  <li><strong>Authority</strong> - The name of the certificate authority for this certificate.</li>
  <li><strong>Certificate Manager</strong> - The team that manages the certificate.</li>
</ul>

<p>This can be followed by one or more lines listing the services or server that uses the certicate.</p>

<ul>
  <li><strong>Server Name</strong>> - The name of the server or service that uses this certificate.</li>
  <li><strong>Software</strong> - The software that is configured with this certificate.</li>
  <li><strong>Product</strong> - The company product that will need impacted if the certificate expires.</li>
  <li><strong>Responsible Group</strong> - The group that manages this service or server and will need to be contacted to get the certificate updated.</li>
</ul>

</div>

</div>


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
  $q_string .= "from inv_certs ";
  $q_string .= "left join inv_groups on inv_groups.grp_id = inv_certs.cert_group ";
  $q_string .= "order by cert_url,cert_expire";
  $q_inv_certs = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_certs) > 0) {
    while ($a_inv_certs = mysqli_fetch_array($q_inv_certs)) {

      $certtime = strtotime($a_inv_certs['cert_expire']);

      $class = " class=\"ui-widget-content\"";
      if ($certtime < $date) {
        $class = " class=\"ui-state-error\"";
      } else {
        if ($certtime < $warningdate) {
          $class = " class=\"ui-state-highlight\"";
        }
      }

      print "<tr>";
      print "  <td" . $class . " title=\"" . $a_inv_certs['cert_url'] . "\">" . $a_inv_certs['cert_desc']      . "</td>";
      print "  <td" . $class . ">"                                        . $a_inv_certs['cert_expire']    . "</td>";
      print "  <td" . $class . ">"                                        . $a_inv_certs['cert_authority'] . "</td>";
      print "  <td" . $class . " colspan=\"2\">"                                        . $a_inv_certs['grp_name']       . "</td>";
      print "</tr>";

      $q_string  = "select svr_id,inv_name,sw_software,prod_name,grp_name ";
      $q_string .= "from inv_svr_software ";
      $q_string .= "left join software  on software.sw_id     = inv_svr_software.svr_softwareid ";
      $q_string .= "left join inventory on inventory.inv_id   = inv_svr_software.svr_companyid ";
      $q_string .= "left join products  on products.prod_id   = software.sw_product ";
      $q_string .= "left join inv_groups  on inv_groups.grp_id    = inv_svr_software.svr_groupid ";
      $q_string .= "where svr_certid = " . $a_inv_certs['cert_id'];
      $q_inv_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inv_svr_software) > 0) {
        while ($a_inv_svr_software = mysqli_fetch_array($q_inv_svr_software)) {

          print "<tr>\n";
          print "  <td class=\"ui-widget-content\">--" . $a_inv_svr_software['inv_name']    . "</td>\n";
          print "  <td class=\"ui-widget-content\">"   . $a_inv_svr_software['sw_software'] . "</td>\n";
          print "  <td class=\"ui-widget-content\">"   . $a_inv_svr_software['prod_name']   . "</td>\n";
          print "  <td class=\"ui-widget-content\" colspan=\"2\">"   . $a_inv_svr_software['grp_name']    . "</td>\n";
          print "</tr>\n";

        }
      }
    }
  } else {
      print "<tr>";
      print "  <td class=\"ui-widget-content\" colspan=\"5\">No Certificates Defined</td>\n";
      print "</tr>";
  }

  mysqli_free_result($q_inv_certs);

  print "</table>\n";
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
