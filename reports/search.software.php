<?php
# Script: search.software.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "search.software.php";

  logaccess($db, $formVars['uid'], $package, "Software report.");

# search for software only
  $formVars['search_by'] = 3;

  if (isset($_GET['search_for'])) {
    $formVars['search_for'] = clean($_GET['search_for'], 180);
  } else {
    $formVars['search_for'] = 'Red Hat';
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
<title>Searching Software</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script language="javascript">

function attach_search( p_script_url ) {
  var as_form = document.index;
  var as_url;

  as_url  = '?search_by='     + as_form.search_by.value;
  as_url += '&search_for='    + encodeURI(as_form.search_for.value);

  script = document.createElement('script');
  script.src = p_script_url + as_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('search.php?search_by=3&search_for=<?php print $formVars['search_for']; ?>');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Search Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('search-listing-help');">Help</a></th>
</tr>
</table>

<div id="search-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Search Listing</strong>
  <ul>
    <li><strong>Server Name</strong> - Clicking on the Server Name will bring up the Detailed Server View with a focus on the Software tab.</li>
    <li><strong>Vendor</strong> - Clicking on a Vendor will restart the search using the selected Vendor as the search criteria.</li>
    <li><strong>Software</strong> - Clicking on Software will restart the search using the selected Software as the search criteria.</li>
    <li><strong>Type</strong> - Clicking on a Model Type will restart the search using the selected Type as the search criteria.</li>
    <li><strong>Managed By</strong> - Clicking on the Group managing this device will bring up the Detailed Server View starting from the Detail page (different than the server selection which starts you off on the Software tab).</li>
  </ul></li>
</ul>

</div>

</div>

<span id="software_search_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
