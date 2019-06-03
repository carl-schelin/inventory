<?php
session_start(); 
include('settings.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Inventory Management</title>
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

<h1 style='margin: 0; padding: 0; font-size: 20px;'>Oops, Access Denied</h1>

<p>We have detected that your user level does not entitle you to view the page requested.<br /><br />
Please contact the website administrator if you feel this is in error.</p>
<br />
<h2>What to do now?</h2><br />
To see this page you must <a href='logout.php'>logout</a> and login with sufficiant privileges.</li>

</div>

<div id="footer"><a href="<?php print $Siteroot; ?>">Inventory Management</a></div>

</body>
</html>
