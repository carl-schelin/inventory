<?php
# Script: changelog.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "changelog.php";

  logaccess($formVars['uid'], $package, "How does Changelog work?");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Changelog, how does it work?</title>

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

<p><strong><u>Changes</u></strong></p>

<p>Changes are mini documentation for systems that provide the little details on what changed on a system. These are generally not large encompassing multi step changes as these are documented elsewhere 
but are the little day-to-day changes that occur due to a ticket request or small management change.</p>


<p><strong><u>Logging Changes</u></strong></p>

<p>Typically changes are not something where a ticket is created. In many cases, a ticket was already created to request the change so opening a ticket seems unnecessary. But a ticket isn't always 
opened, especially for simple well known changes. For example from a systems admin point of view, identifying that a server is not able to communicate with another server might add a route to correct 
this. It is a change and it might adversely impact a system; adding a network based route vs a host based route would redirect application traffic out the wrong interface where a host base route 
would have been a better choice.</p>


<p><strong><u>Changelog</u></strong></p>

<p>Changelog was created to easily document these little changes for the team. It's simple to shoot an email to your groups changelog account.</p>


<p><strong><u>Server Listings</u></strong></p>

<p>The list of servers where you can apply changes is generated automatically. There are four ways of having a server added to your changelog server listing.</p>

<ul>
  <li>You are the system custodian. This would be the group that manages the Operating System such as the Unix or Windows teams.</li>
  <li>You are the application admin. Once this server has been identified as being managed by the Application group such as Web Apps or Mobility, the server is added to the list.</li>
  <li>You have software on the server. Any group can edit a server's software if it's owned by that group. If you're not the Custodian or AppAdmin, if you add software as owend by your group, the server will be added to your server listing.</li>
  <li>Under the <strong>Changelog</strong> menu, select the <strong>Server/Applications Management</strong> option. On the second tab, you can add any server name or application that you want to enter changes for such as Sharepoint or Exchange.</li>
</ul>

<p>Note that the server listings are automatically generated at 4pm each day. You can regenerate your server listing manually in the <strong>Changelog</strong>, <strong>Server/Applications Management</strong> page by clicking on the <strong>Refresh Listing</strong> button.</p>


<p><strong><u>Technical Bits</u></strong></p>

<p>Here is the process that occurs when you send a change:</p>

<ul>
  <li>You create a text based email message, sending to your changelog account with the server or application name in the subject line, and what you did in the body of the email.</li>
  <li>The inventory server receives the email and checks the sender (you) against a list of email addresses that are permitted to send to your changelog and checks the subject against your team's list of servers and applications.</li>
  <li>Assuming everything passes, the script saves the email to a mailbox named after the server or application.</li>
  <li>The email is also passed to a submission script. This script reformats the email and sends it to the ticketing system if it's enabled (Magic or Remedy).</li>
</ul>


<p><strong><u>Group Reports</u></strong></p>

<p>There are several reports that let you review changes. All of them are accessible through the Inventory.</p>

<p>Anywhere in the Inventory, hover over the 'Changelogs' menu option on the orange menu bar. This brings up the reports that are appropriate for your group. If you click on the 'Changelogs' menu option, you can see 
changes made for other groups.</p>

<p>There are currently 6 links for each group.</p>

<ul>
  <li><strong>Server Changelog Pages</strong> - This link puts you into your changelog mailbox directory. Your servers and applications list is parsed and a selection screen is presented. Where you have changes entered, there'll be a link to the mailbox itself where you can review every change for the selected system or application. Years are presented first, click to expand to Months, click again on a Month to expand to all changes for that month.</li>
  <li><strong>Sorted Changelog Pages</strong> - This returns a list of all the changes made to your servers or applications as a single line per change. The page is reverse date sorted so the newest change is at the top. Each line contains the date and time of the change, the server or application that was affected, who made the change if it can be figured out, and the first line of the change that was sent to changelog. Clicking on the server name will bring up the mailbox as it is in the 'Server Changelog Pages'. You can drill down and review the full details of the change.</li>
  <li><strong>Weekly Changelog Count</strong> - This counts the changes for every system since you started entering changes. It's reverse date ordered so the newest is at the top. At the bottom are the total number of changes made for all active systems.</li>
  <li><strong>Server/Applications Management</strong> - This lists all the servers that will be automatically added to your file plus the list of applications you can manually add for changes. Some groups make application level changes and not changes to individual servers so a single location for changes can be made at the application level.</li>
  <li><strong>Application Changelog Report</strong> - As you can go to a server to see all the changes that were applied, this page gives you the same view to the application changes.</li>
  <li><strong>servers.[group] Listing Review</strong> - This is a link to the servers.[group] file used to permit changes. If you get an error when you submit a change, you can check this file to make sure you have the right name or even the right to send changelog emails about it.</li>
</ul>


<p><strong><u>General Reports</u></strong></p>

<p>In addition there are two other reports you can view. Under the main page, <strong>General Reports</strong> tab, are two links.</p>

<ul>
  <li><strong>Changelog By Server or Service</strong> - Where <strong>Application Changelog Report</strong> is for your specific group, this report lists the manually entered applications and servers for all groups.</li>
  <li><strong>Changelog by Month and Year</strong> - This report is essentially the same as <strong>Sorted Changelog Pages</strong> but for all groups and servers and for only the current month due to the number of entries (Unix has 25,000 changelog entries). It does take about 30 seconds or so as it has to parse through every groups list of servers and applications and then through each mailbox to generate a listing. Much like the <strong>Sorted Changelog Pages</strong>, you get the date and time of the change, who made the change, which server, and the first line of the change as a summary. One nice addition though is you can click on the user's name and you'll see all the changes made by that user for the month. This might help you with your status reports.</li>
</ul>

<p>One other note. On the main page, you can click on <strong>Product and Service Map</strong> to see the list of Intrado Products and then click on a product, or set your <strong>Product or Service</strong> application in the drop down and click on the <strong>Product and Service Map</strong> and the report will give you a list of all the servers associated with the selected Product, a list of all the software, and in the third tab, a list of all the Changes that were recorded for every server associated with that Product.</p>

<p>And as noted a couple of times before, you can search or a server or select one from the listing to get into a server detail record and under the <strong>Changelog</strong> tab are all the changes made by any group for that server.</p>


<p><strong><u>Tickets</u></strong></p>

<p>As a note, changelog is capable of opening tickets in Magic and in Remedy. It's currently disabled at management's request as how changes are recorded is being sorted so it may return.</p>


<p><strong><u>Finally</u></strong></p>

<p>This page was created to try and provide an overall view of the Changelog process and available reports. Let me know if you have questions or need clarification.</p>


</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
