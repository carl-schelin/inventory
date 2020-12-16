<?php
# Script: whatsnew.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath  . '/guest.php');

  $package = "whatsnew.php";

  logaccess($db, $formVars['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>What's New!</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div class="main">

<div id="tabs">

<ul>
  <li><a href="#intro">What's New?</a></li>
  <li><a href="#home">Home Page</a></li>
  <li><a href="#tags">Tag Clouds</a></li>
  <li><a href="#search">Search</a></li>
  <li><a href="#rsdp">RSDP</a></li>
  <li><a href="#bugs">Bugs!</a></li>
  <li><a href="#features">Feature Requests</a></li>
  <li><a href="#exchange">InfoExchange</a></li>
  <li><a href="#morning">Morning Report</a></li>
  <li><a href="#servers">Device Listings</a></li>
  <li><a href="#updates">Progress!</a></li>
</ul>


<div id="intro">

<h4>What's New?</h4>

<p>The intention of this page is to show you, the user, what has changed between Inventory 2.2 (I2) and Inventory 3.0 (I3). 
There are some obvious changes such as the colors and tabs you see here but there are many other changes that have been added to I3.
By the way, you can change your color theme by going to your <a href="<?php print $Usersroot; ?>/profile.php">profile</a>.</p>

<p>Right off the bat, the biggest changes are things you are unlikely to even notice. I've gone through the entire I3 code base 
(76,254 lines) and reviewed each line. Over the course of the work on I2, I created a Coding Standards page in an effort to provide 
a framework to the code. A common look of the internals. I went through the I3 code with the Coding Standards in mind and ensured 
the code conformed to the standard. There are exceptions of course as specific requirements came up but overall the code is clean 
and reasonably bug free.</p>

<p>From an error review, I examined all the final HTML Markup and addressed all errors. I also have a log file for internal script 
errors which I monitored and corrected and the web server error log file that I reviewed. None of the scripts have any syntax 
errors. These kinds of issues generate strange errors that are usually more difficult to track down.</p>

<p>Next may be a little bit more noticeable. I moved the code in an effort to identify modules. The I2 code was pretty much all in a 
single directory. With I3, I've identified modules and moved them into subdirectories. This makes it a lot easier to manage since 
looking over 471 or more files is somewhat bulky.</p>

<p>The next biggest change is the look of I3. Not the themes but the over all look and feel. The User Interface design. 
One of the main purposes was to look over the User Interface and come up with a standard view that works the same regardless of 
where you are in I3. Over the years as modules get added on, the way to access these modules changed. How to use them changed. 
This effort was taken to give you, the user, a common look and feel.</p>

<ul>
  <li>Forms with lists of data such as network interfaces are initially hidden from view regardless of which module you're in. 
Editing one of the interfaces will fill in and display the form. You can also click the Title Bar to display the form for 
new entries.</li>
  <li>Other Forms are initially visible such as your Profile page.</li>
  <li><strong>Help screens are everywhere.</strong> Most of the screens are accessible by clicking on the 'Help' on the title bar 
of the page you're on. In some cases where there is no title bar, 'Help' has been added to the main menu at the top.</li>
</ul>

<p>Let's move on to the next tab, to the right.</p>

</div>


<div id="home">

<h4>Home Page</h4>

<p>I reorganized the <strong>Inventory Reports</strong> page to be a bit more logical. The filter area is clearly different 
than the available links. The links work about the same however the <strong>Location</strong> drop down is new. Before, you 
could select a group and an Intrado product and come up with a page of systems. You can now filter it further by selecting 
a location from the drop down.</p>

<p>Some of the more <strong>General Reports</strong> were moved into their own area. The <strong>Inventory Reports</strong> 
tab is used by most folks at one time or another. The <strong>General Reports</strong> are used less often so moved away to 
reduce confusion and clutter.</p>

<p>The <strong>Group Reports</strong> also use the filters but are reports that were created for specific groups. If you 
want a report, head over to the <strong>Feature Tracker</strong>.</p>

<p><strong>Archived Reports</strong> are ones that were created and superceded or just aren't used any more. Kept around 
just in case. They may not work with all the filters.</p>

<p><strong>Filters</strong> in general haven't changed. You still have your group as the default selected group. The biggest 
update is the <strong>Filter on Location</strong> filter. This one was originally used only for Data Center walkthroughs but
it came to be useful when restricting searches to cities such as Longmont or even states. With that, I've modified the 
filter to let you select Country, State, City, and even Data Center within the City. This means you can select 'Canada' 
and get all the servers in Calgary and Toronto. As an additional feature, the Data Center Location drop down is enabled 
and by default lists a select set of Data Centers. Longmont, Englewood, Honolulu, and Miami, the locations with the largest 
amount of equipment.</p>

<p><strong>Reports</strong> use all the available filters where appropriate. There are some reports such as the Product Map 
that selecting by Location doesn't provide any benefit. I've also created the ability to view reports and menus without 
the requirement to log in to the system however that's being restricted for the moment.</p>

</div>


<div id="tags">

<p><strong>Tag Clouds</strong> - This lets anyone create a tag and associate it with a system. There are <strong>Private</strong> 
tags for individual use. There are <strong>Group</strong> tags which let a group define specific server reports, and 
<strong>Public</strong> tags which define reports for everyone who uses I3. Click on one of the tags and you'll get a list 
of systems that have that tag.</p>


</div>


<div id="search">

<p><strong>Search</strong> - I've had many requests for information where I needed to create a report or at the minimum, 
pop into the database to look something up. In addition, other teams have either had issues bringing up a server listing or 
matching a paged alert for a server that might be different than the main hostname in the Listings. In addition, the number of 
data centers (including equipment in PSAPs) makes the Location listing quite long. As a result, I've added the ability to 
search for information in the Inventory.</p>

<p>Searching is on the Home Page under the 'Search' tab. You can select a specific subject to search for or let it search 
all areas of the Inventory. The search criteria must match one of the listed columns though. It's not a 'search everything' 
search. For example, under the Hardware option, your search criteria will only return results that match the Vendor name, 
the Model information, or the Part type.</p>

<p>All searches return the System Custodian for that search.</p>

<ul>
  <li><strong>Server Name</strong> - Returns the name of the server, the IP Address, and what the IP is used for.</li>
  <li><strong>IP Address</strong> - Returns the IP's that match the search, what the IP is used for, and the server name.</li>
  <li><strong>Software</strong> - Returns the server name, the Vendor, the Software, and the Software type.</li>
  <li><strong>Hardware</strong> - Returns the server name, the Vendor, the Hardware model, and the Hardware type.</li>
  <li><strong>Asset</strong> - Returns the Server Name, the Asset tag, the Serial Number, the Dell Service Tag, and the Location.</li>
  <li><strong>Location</strong> - Returns the server name, the data center location, the city, the state, and the country.</li>
</ul>

<p>The Server Name, IP Address, and Asset tab reports link to the server view page. This provides additional detail on the 
server you are interested in.</p>

<p>The Software, Hardware, and Location tab reports have the server name as a link to the server view page, however the 
individual columns are further searches for just information in that column.</p>

<p><strong>Example 1</strong></p>

<p>Searching for VA will give you any Data Center, City, State, or Country that have 'VA' in it such as 'Uvalda Texas'. Once you get 
the results, clicking on 'VA' in the State column will list out just systems that are in 'VA'.</p>

<p><strong>Example 2</strong></p>

<p>There's a data center outage in Culpeper Virgina this weekend. You might spell it 'Culpepper' and get no results. However searching 
on 'VA' and then clicking on 'Culpeper' in the City column will further reduce the list to just servers in Culpeper Virginia.</p>

</div>


<div id="rsdp">

<h4>Rapid Server Deployment Process Module (RSDP)</h4>

<p>This module is the largest and most complicated part of the Inventory. It really could be a different package however it 
does use some information from the main Inventory and is connected where servers can be added to the Inventory at the press 
of a button.</p>

<p>The first part of the work here was to reorganize the scripts into their own area and then further organize them to make 
them easier to manage. Once that was done, work was done to bring them into line with the user interface concepts used by 
the Inventory. This included a full code review to ensure all code was brought up to 3.0 standards.</p>

<p>Some of the larger changes:</p>

<ul>
  <li>Changed the view method. The default now is to show all the Projects and drill down through the Server listing to the 
Task listing and then from there to any of the tasks that need to be done. In addition, the Orange menu gives you the ability to 
go back to the Task or Server page or navigate to any of the Server Tasks.</li>
  <li>Added Help to every page. Help provides information on what's expected and on any issues or notes about how the data 
interacts with the rest of RSDP.</li>
  <li>Added the ability to request a Ticket. This is in place with most of the RSDP tasks as a selectable toggle. If selected
  <li>Added the ability to request a DNS Ticket. Under the System Installed task, you will see a list of all the IP addresses 
associated with the project. You can select to have a ticket automatically created in this task.</li>
  <li>The Interface and Network tasks have been greatly enhanced. You can design your configuration which is then clearly 
presented to the Networking team to be completed as efficiently as possible.</li>
  <li>Checklists have been added for most tasks. There are unique Task Checklists but in addition, groups can design their 
own checklists for their tasks to ensure work has been successful.</li>
</ul>

<p>One specific note regarding the Interface and Network tasks is the addition of checkboxes indicating a necessary configuration 
update for the Network Engineering team. Under the Server Build page, you would specifically check the 'I Need An IP' checkbox 
which then displays all IP related fields under the Network task and if a physical system, check the 'I Need Switch Configuarion' 
checkbox will display all Switch specific information. It's interesting to look at older (current) RSDP requests under the 
Network Task and see no input fields because of this new feature.</p>

<p>There are many other improvements throughout the system which should improve the experience and make the process of 
requesting a server much more efficient.</p>


</div>

<div id="bugs">

<h4>Bugs!</h4>

<p>The original bug reporting system was pretty minimal and the last thing to be updated. Now the system is quite a bit more 
robust and able to let you report issues. The old bug data is there and I did work to address the reported bugs where I could.</p>


</div>

<div id="features">

<h4>Request a Feature or Enhancement</h4>

<p>The original bug system also let you request enhancements to the Inventory. This is now a new area and has also been made 
more robust. In this case though, with more room to expand, you can request things like specific reports that you can't from 
the tagging system or that is a bit more than you want to do (2,000 or 3,000 systems might be hard to tag).</p>

</div>

<div id="exchange">

<h4>InfoExchange</h4>

<p>The InfoExchange system is new and there's a new way of working on questions and answers. In this system you post a question and 
then anyone else can ask for more information or provide multiple answers. You then select the answer that works for you. It's 
based on the Stack Overflow model if you're familiar with it.</p>

<p>The main page shows you the top 20 upvoted questions. An upvoted question is one selected by all users and something valuable 
to ask. "That's a <strong>good</strong> question." These can be answered or unanswered questions.</p>

<p>When viewing the question, it'll show you the Question and any Answers along with any comments under each Question and Answer. 
The Comments let others ask for more clarification or more details on the Question or provide more clarification to Answers.</p>

<p>Each Question and Answer can be upvoted. Questions can be selected as a 'Favorite' which shows up in your 'Favorites' tab. 
Answers can be selected by the Questioner as the Answer that worked for them.</p>

<p>The neat thing is the Question and Answer period doesn't actually have to pertain to the Inventory or any of the modules.</p>

<p><strong>Note:</strong> This isn't functioning yet as I wanted to get the code in place and working before tackling a new 
module. Inventory 3.1 :) </p>

</div>


<div id="morning">

<h4>Morning Report</h4>

<p>The Morning Report app has been hosted on the Status Management site primarily due to the function of reporting the system 
status for various group's. Since they are system related, I moved the Morning Report over to the Inventory. Initially there 
isn't a reason for it to be in one place or the other except that the systems are in the Inventory.</p>

<p>The primary benefit is that the Inventory also contains an Issue Tracker. The Issue Tracker has been modified to accept 
one line "Morning Report" status messages. These single lines are then automatically inserted into the Morning Report when 
updated in the Issue Tracker.</p>

<p>This aligns with the idea of making changes in only one place. If a change is made in two or more places, then there is no 
guarantee the data will be complete in either place.</p>

</div>


<div id="servers">

<h4>Device Listings</h4>

<p>Prior to I3, each group had to wait on the nightly rebuild of the various files that list the systems or software the group 
manages. Alternately, they would contact me to rerun the script to regenerate a new file due to a recent change. I've also 
had to manually enter a server when building one as part of the checklist is to copy some files from the new server to the 
central location.</p>

<p>New to this is under the <strong>Changelogs</strong> header. Previously only teams that had special requirements would be 
able to rebuild their listings generally because it was a software or product listing vs a general list of devices. Now all 
groups can regenerate their listings when needed.</p>

<p>Of course the existing scripts didn't get lost. They'll still run at 4pm. But your group can now regenerate the list when 
needed vs waiting.</p>

<p>In addition you are now able to add applications to your listing. For example, in the Unix group Centrify is installed 
on many systems. If a change is made to a Centrify specific user or configuration, rather than updating every possible 
system that may be affected, a changelog entry is made just for centrify.</p>

<p>This lets you create unique changelog management. The DBAs can create changelog entries for Schema's instead of a server 
which may hold more than one Schema. Networking can create changelog entries for vlans which, like centrify, may be spread 
across multiple devices.</p>

<p>And since this is a custom entry, a report is ready to take you to a view of all the changelog entries for those custom 
entries.</p>

</div>

<div id="updates">

<h4>Progress!</h4>

<p>Added a warning popup if you make a change to a form in RSDP that isn't the main form. For example, the interface information 
is a list of interfaces that are associated with the new system. The form for managing the interfaces is a sub-form of the 
main page. With a 'Save' and a 'Save And Exit' button, folks were getting confused and were losing form information because 
they were thinking the two buttons also saved the form data. Now there is a visual warning (the Update/Add button box changes 
color during editing to highlight the Update/Add options) and a popup that warns you that a form is unsaved. You can still 
exit but at least you know the form data is lost.</p>

<p>Added the ability to associate systems with Blade Chassis. In the device edit page, you'll find a <strong>Blade Chassis</strong>
select menu where you can select the Blade Chassis this system is hosted in. Leave Row and Rack blank but put the slot 
this system is in, in the Unit field. In the view page, if the system is hosted in a Blade Chassis, you'll see the 
blade chassis name and slot. If you're viewing the Blade Chassis itself, you'll see a list of the systems that are 
in this chassis. Both are links to the Blade Chassis or System depending on the view.</p>


</div>

</div>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
