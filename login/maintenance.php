<?php
session_start(); 
include('settings.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Inventory Management</title>
<?php

# use when the main site tanks for some reason
# print "<meta http-equiv=\"REFRESH\" content=\"10; url=http://192.168.208.168/inventory\">\n";

?>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<META NAME="robots" content="index,follow">

<link rel="stylesheet" href="<?php print $Loginroot; ?>/stylesheet.css" />

</head>
<body>

<div id="header">
    
<div id="title">

<h1>Inventory Login</h1>

</div>

</div>

<div id="main">

<h1 style='margin: 0; padding: 0; font-size: 20px;'>Maintenance Mode</h1>

<p>Sorry, currently maintenance is being done on the Inventory system.</p>

<p>Please contact the system administrator for further information.</p>

<?php

#  print "<h2>What to do now?</h2>\n\n";

#  print "<p>You are being redirected to the backup site in 10 seconds.</p>\n\n";

#  print "<div class=\"error_message\">Do not make changes to information at this site as the main site will be restored.</div>\n\n";

#  print "<p>Click <a href=\"http://192.168.208.168/inventory/\">here</a> if not redirected.</p>\n\n";

?>
</div>

<div id="footer"><a href="<?php print $Siteroot; ?>">Inventory Management</a></div>

</body>
</html>
