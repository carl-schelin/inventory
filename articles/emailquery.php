<?php
# Script: emailquery.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "emailquery.php";

  logaccess($formVars['uid'], $package, "How does email query work?");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>E-Mail Query, how does it work?</title>

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

<div class="main-help ui-widget-content">

<p><strong><u>E-Mail Query</u></strong></p>

<p>The E-Mail Query system was put into place in order to provide a method of retrieving information from the Inventory when you're not at a computer, such as in a meeting and someone asks 
about a system specifications, or when at home responding to a monitoring alert. The E-Mail Query can be used by anyone who has an account in the Inventory and the sending email address 
matches their profile email or alternate email fields.</p>


<p><strong><u>Process</u></strong></p>

<p>The process is pretty simple. You send an email to <mailto>inventory@incojs01.scc911.com</mailto> with a subject line that consists of the server name, product for a listing of servers, 
or IP address. Without passing options on the Subject line, you'll get the detail record for the server. You can also pass a word or letter on the subject line for a specific query which returns 
the detail record plus the additional information.</p>


<p><strong><u>Technical Bits</u></strong></p>

<p>Here is the process that occurs when you send a query:</p>

<ul>
  <li>You create an email message, sending to the inventory server or product name in the subject line, and optionally, any flags for additional data.</li>
  <li>The inventory server receives the email and checks the sender (you) against a list of email addresses that are permitted to send to the Inventory. Only intrado.com and west.com addresses are permitted.</li>
  <li>Assuming everything passes, the script parses the server name or product and options, formats the output, and returns it to you.</li>
  <li>If you don't have an account in the Inventory, you won't get a response. If you do have an account and the subject line is incorrect or unclear, you'll receive a help email.</li>
</ul>


</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
