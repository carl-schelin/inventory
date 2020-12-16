<?php
# Script: issue.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "issue.php";

  logaccess($db, $_SESSION['uid'], $package, "Managing issues");

  $issue = "Issue Tracker";
  $server = "";
  $formVars['server'] = 0;
  if (isset($_GET['server']) || isset($_GET['servername'])) {
    $formVars['server']     = clean($_GET['server'],     10);
    $formVars['servername'] = clean($_GET['servername'], 60);

    if (strlen($formVars['servername']) > 0) {
      $formVars['server'] = return_ServerID($db, $formVars['servername']);
    }

    $q_string  = "select inv_id,inv_name,inv_manager ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_id = " . $formVars['server'];
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_inventory = mysqli_fetch_array($q_inventory);

    $issue = "Issue: " . $a_inventory['inv_name'];
    $server = $a_inventory['inv_name'];
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php print $issue; ?></title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_issue( p_script_url ) {
  var answer = confirm("Deleting this Issue will also delete all associated support ticket and timeline records.\n\nDelete this Issue?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url ) {
  // create new script element, set its relative URL, and load it
  script = document.createElement('script');
  script.src = p_script_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_search( p_script_url ) {
  var as_form = document.search;
  var as_url;

  as_url  = '?search_by='     + as_form.search_by.value;
  as_url += '&search_for='    + encodeURI(as_form.search_for.value);

  script = document.createElement('script');
  script.src = p_script_url + as_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function check_hostname() {
  show_file('validate.hostname.php?server=' + document.newissue.servername.value);
}

function clear_fields() {
  show_file('issue.open.mysql.php?server=<?php print $formVars['server']; ?>');
  show_file('issue.closed.mysql.php?server=<?php print $formVars['server']; ?>');
  check_hostname();
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
  $( "#search-tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#search-input' ).on("keypress", function(e) {
    if (e.keyCode == 13) {
      attach_search('search.mysql.php');
      return false;
    }
  });

});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<!-- Start of main (0) -->
<div id="main">

<!--  Start of tabs block (1) -->
<div id="tabs">

<!--   Start of issue form (2) -->
<form name="newissue" action="issue.mysql.php" method="post">

<ul>
  <li><a href="#issue">Issue Form</a></li>
  <li><a href="#open">Open Issues</a></li>
  <li><a href="#closed">Closed Issues</a></li>
  <li><a href="#tags">Tagged Issues</a></li>
  <li><a href="#search">Search Issues</a></li>
</ul>


<!--    Start of issue block (3) -->
<div id="issue">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Issue Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('issue-help');">Help</a></th>
</tr>
</table>

<!--     Start of issue-help block (4) -->
<div id="issue-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Submit A New Issue</strong> - This button stays disabled until a valid server is entered in the Server field. Click to create a new Issue.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Issue Form</strong>
  <ul>
    <li><strong>Server</strong> - Fill in the name of the server here. The Server field stays <span class="ui-state-error">highlighted</span> until a valid server is entered.</li>
    <li><strong>Discovery Date</strong> - Enter the date of the discovery.</li>
    <li><strong>Tech Resource</strong> - Select the name of the person who discovered the issue. Default is you.</li>
    <li><strong>Description</strong> - Brief description of the problem.</li>
  </ul></li>
</ul>

</div>

</div>
<!--     End of issue-help block (4) -->

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="submit" disabled name="clone" value="Submit A New Issue">
<input type="hidden" name="id" value="<?php print $formVars['server']; ?>"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Issue Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="system">Server: <input type="text" name="servername" value="<?php print $server; ?>" onkeyup="check_hostname()"></td>
  <td class="ui-widget-content">Discovery Date: <input type="text" name="iss_discovered" value="<?php print date('Y-m-d'); ?>" size=10></td>
  <td class="ui-widget-content">Tech Resource: <select name="iss_user">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Description: <input type="text" name="iss_subject" size="70"></td>
</tr>
</table>

</div>
<!--    End of issue tab (3) -->


<!--    Start of open tab (3) -->
<div id="open">

<span id="open_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>
<!--    End of open tab (3) -->


<!--    Start of closed tab (3) -->
<div id="closed">

<span id="closed_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>
<!--    End of closed tab (3) -->


<!--    Start of tags (3) -->
<div id="tags">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Tag Cloud</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help-tagcloud');">Help</a></th>
</tr>
</table>

<!--     Start of help-tagcloud block (4) -->
<div id="help-tagcloud" style="display:none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Tag Cloud</strong>
  <ul>
    <li><strong>Private Tags</strong> - Shows tags that only you can manipulate. These tags are only visible to you so they let you create personalized server lists.</li>
    <li><strong>Group Tags</strong> - Shows group tags manageable by your group. These tags are visible by your group. They are also added to the servers file for each team. Externals scripts may be run using these tags.</li>
    <li><strong>Public Tags</strong> - Tags that are viewable by all users of the Inventory software. These tags may be useful for grouping like systems that may cross projects. Use the Project listing page for single project server lists.</li>
  </ul></li>
</ul>

</div>

</div>
<!--     End of help-tagcloud (4) -->


<!--     Start of Private Cloud block (4) -->
<div class="main ui-widget-content">

<t4>Private Cloud</t4>

<ul id="cloud">
<?php
  $q_string  = "select tag_name,count(tag_name) ";
  $q_string .= "from tags ";
  $q_string .= "where tag_view = 0 and tag_owner = " . $_SESSION['uid'] . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Issueroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "&type=0\">";
    $linkend   = "</a>";

    print "  <li>" . $linkstart . $a_tags['tag_name'] . " (" . $a_tags['count(tag_name)'] . ")" . $linkend . "</li>\n";
  }
?>
</ul>

</div>
<!--     End of Private Cloud block (4) -->


<!--     Start of Group Cloud block (4) -->
<div class="main ui-widget-content">

<t4>Group Cloud</t4>

<ul id="cloud">
<?php
  $q_string  = "select tag_name,count(tag_name) ";
  $q_string .= "from tags ";
  $q_string .= "where tag_view = 1 and tag_group = " . $_SESSION['group'] . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Issueroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "&type=1\">";
    $linkend   = "</a>";

    print "  <li>" . $linkstart . $a_tags['tag_name'] . " (" . $a_tags['count(tag_name)'] . ")" . $linkend . "</li>\n";
  }
?>
</ul>

</div>
<!--     End of Group Cloud block (4) -->

 
<!--     Start of Public Cloud block (4) -->
<div class="main ui-widget-content">

<t4>Public Cloud</t4>

<ul id="cloud">
<?php
  $q_string  = "select tag_name,count(tag_name) ";
  $q_string .= "from tags ";
  $q_string .= "where tag_view = 2 ";
  $q_string .= "group by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $linkstart = "<a href=\"" . $Issueroot . "/tag.view.php?tag=" . $a_tags['tag_name'] . "&type=2\">";
    $linkend   = "</a>";

    print "  <li>" . $linkstart . $a_tags['tag_name'] . " (" . $a_tags['count(tag_name)'] . ")" . $linkend . "</li>\n";
  }
?>
</ul>

</div>
<!--     End of Public Cloud block (4)-->

</div>
<!--    End of tags block (3) -->

</form>
<!--   End of Issue form block (2) -->


<!--   Start of search form block (2) -->
<form name="search">

<!--    Start of search block (3) -->
<div id="search">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Search Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help-search');">Help</a></th>
</tr>
</table>

<!--     Start of help-search block (4) -->
<div id="help-search" style="display:none">

<div class="main-help ui-widget-content">

<p><strong>Specify Search Subject</strong> - Select the areas you wish to search. This will reduce the number of results and speed up the response.</p>

<p><strong>Search For</strong> - Enter in the text you want to search for. Don't enter any wild cards, the search will add them for you.</p>

<p><strong>Search</strong> - Click the button when ready. A table will be displayed with the search results.</p>

</div>

</div>
<!--     End of help-search block (4) -->


<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content"><input type="button" name="search_addbtn" value="Search" onClick="javascript:attach_search('<?php print $Issueroot; ?>/search.mysql.php');"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Search Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Specify Search Subject: <select name="search_by">
<option value="0">All Fields</option>
<option value="1">Server Names</option>
</select></td>
  <td class="ui-widget-content">Search For: <input type="text" id="search-input" name="search_for" size="80"></td>
</tr>
</table>

<p></p>

<!--     Start of search-tabs block (4) -->
<div id="search-tabs">

<ul>
  <li><a href="#servername">Server Names</a></li>
</ul>


<!--      Start of servername tab (5) -->
<div id="servername">

<span id="server_search_mysql"></span>

</div>
<!--      End of servername tab (5) -->


</div>
<!--     End of search-tabs block (4) -->


</div>
<!--    End of search block (3) -->


</form>
<!--   End of search form (2) -->

</div>
<!--  End of tabs (1) -->

</div>
<!-- End of main window block (0) -->


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
