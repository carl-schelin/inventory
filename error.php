<?php
# Script: error.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  include($Sitepath . "/guest.php");

  $package = "error.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  $headers  = "From: Inventory Management <root@" . $Sitehttp . ">\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
  $headers .= "Reply-To: " . $Sitedev . "\r\n";

  $formVars['script'] = clean($_GET['script'], 60);
  $formVars['error'] = mysqli_real_escape_string($db, clean($_GET['error'], 1024));
  $formVars['mysql'] = mysqli_real_escape_string($db, clean($_GET['mysql'], 1024));
  $formVars['called'] = clean($_GET['called'], 10));

  $body  = "<p>Error generated in " . $formVars['script'] . "</p>\n\n";
  $body .= "<p>Query String:</br></br>";
  $body .= $formVars['error'] . "</p>\n";;
  $body .= "<p>MySQL Error:</br></br>";
  $body .= $formVars['mysql'] . "</p>\n";;

  mail($Sitedev, "Inventory Management Error", $body, $headers);

  if ($formVars['called'] == 'no') {
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Error!</title>

<?php include($Sitepath . "/head.php"); ?>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<h1>Error</h1>

<div class="main ui-widget-content">

<p>An error was generated when loading the web page. It's likely an invalid value that caused the error. Return to the page and try again or click on 'Home' to return to the main page. Thank you for your understanding.</p>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
<?php
} else {
  print "alert(\"Error: An error was generated when loading the web page.\\nIt's likely an invalid value that caused the error.\\nReturn to the page and try again or click on 'Home' to return to the main page.\\nThank you for your understanding.\\n\\n\");\n";
}
?>
