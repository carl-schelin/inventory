<?php
# Script: exclude.php
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

  $package = "exclude.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the message.exclude table");

# If group or an admin, allow access
  if (check_grouplevel($db, $GRP_Unix)) {

# if help has not been seen yet,
  if (show_Help($db, 'excludes')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Message Excludes</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Delete this regex?")

  if (answer) {
    document.getElementById('exclude_mysql').innerHTML = '<?php print wait_Process('Waiting...')?>';

    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.exclude;
  var af_url;

  document.getElementById('exclude_mysql').innerHTML = '<?php print wait_Process('Waiting...')?>';

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += '&ex_companyid='   + af_form.ex_companyid.value;
  af_url += '&ex_text='        + encode_URI(af_form.ex_text.value);
  af_url += '&ex_comments='    + encode_URI(af_form.ex_comments.value);
  af_url += '&ex_expiration='  + encode_URI(af_form.ex_expiration.value);
  af_url += '&ex_deleted='     + af_form.ex_deleted.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function check_Expiration() {
  show_file('exclude.checked.php?noexpire=' + document.exclude.noexpire.checked);
}


function clear_fields() {
  show_file('exclude.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

$(function() {

  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#ex_expiration" ).datepicker();

  $( '#clickAddExclude' ).click(function() {
    $( "#dialogExclude" ).dialog('open');
  });

  $( "#dialogExclude" ).dialog({
    autoOpen: false,
    modal: true,
    height: 240,
    width:  640,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogExclude" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Exclude",
        click: function() {
          attach_file('exclude.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Exclude",
        click: function() {
          attach_file('exclude.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="messages">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Message Exclude Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('exclude-help');">Help</a></th>
</tr>
</table>

<div id="exclude-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Overview</strong></p>

<p>The main purpose of the messages.exclude files is to make it easy to identify problems that need to be corrected which aren't being caught by the monitoring software. Once such a line is identified, 
it can be added to the monitoring team's filters so the team can be alerted on the problem. As such, you basically just continue to add lines to this file to reduce the size of the final messages file 
output.</p>

<p>This page lets you manage the two filter files (messages.exclude) in order to identify and remove the unimportant lines from the final message preview file for the past 24 hours.</p>

<p>There are two files that are created. A prefilter file which is also called messages.exclude which is located on each server in the /opt/intrado/etc directory. The script only pulls in lines that 
begin with the hostname of the server. The second more general filter is located on the central server in /usr/local/admin/etc. After all the prefiltered log files are pulled to the central 
server, the general filter is applied and a single messages file is created which can then be reviewed.

<p><strong>Adding and Editing</strong></p>

<ul>
  <li>Server - If you select a specific server for a message filter, the Management and Application interface name will be added to the prefilter file. This is done because the entry in the Inventory 
is generally the management interface name and not the hostname. Since the prefilter script only pulls the hostname lines from the file, extra lines aren't an issue.</li>
  <li>Expiration Date - You can set a date where the line will be ignored by the build script. In this way you can identify a problem that isn't immediately actionable but you want to filter out 
of the final messages file for now. At a later date, the messages will reappear if you haven't taken care of the problem.</li>
  <li>Doesn't Expire - You can set a line to effectively not expire. This simply changes the date to 2038-01-01. The checkbox toggles between that and today's date. Leaving it blank defaults to 
2038-01-01.</li>
  <li>RegEx - The Regular Expression you wish to use to ignore lines. The processing script is perl so using perl regex will work. Remember that you need to escape regex characters such as [] and (). 
The field is 255 characters long and remember the processing goes through each line so combining information may make the search more efficient.</p>
  <li>Comment - Lets you add a comment above the line in the final file. Of course it's also visible in the output below so it'll help identify why this is being filtered. The field is 255 characters 
long.</li>
</ul>

<p><strong>Output</strong></p>

<p>If you selected a server from the drop down, you'll see one or more lines in the output for that. Clicking on any of the entries will edit all lines so you only need to edit one of the entries. 
Following that are three items in parenthesis, Expiration Date, who entered or edited the line, and either a delete button which just marks a filter and prevents it from being added to the filter file 
or who deleted and a remove button you ultimately remove the line from the Inventory.

<p><strong>Final</strong></p>

<p>It's a regular expression. You can enter in a shorter name for a group of servers or any sort of regular expression that satisfies the search. I suggest you try your regex on the messages file 
before adding it here to ensure it works as expected. There is no checking of the regex expressions here. If you put in .*, all lines will be deleted from the final file.</p>

<p>If the line expires today, the line will be <span class="ui-state-highlight">highighted like this</span>. If the line expired prior to today, the line will be <span class="ui-state-error">highlighted 
like this</span>.</p>

<p>Important: The filter is read in to the perl script and then loops through the message file checking all lines against the filter. To speed up processing, add to the prefilter and make sure old, unnecessary 
lines are removed promptly. No point in checking against a server that doesn't exist any more.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="hidden" name="id" value="0">
<input type="button" name="addbtn"  id="clickAddExclude" value="Add Message Exclude Text"></td>
</td>
</tr>
</table>

<span id="exclude_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</form>

</div>


<div id="dialogExclude" title="Exclude List Form">

<form name="exclude">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Server <select name="ex_companyid">
<option value="0">All Servers</option>
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_ssh = 1 and inv_status = 0 and inv_manager = " . $GRP_Unix . " ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
    print "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
  }
?>
</select>
</td>
  <td class="ui-widget-content">Expiration Date <input type="text" name="ex_expiration" id="ex_expiration" value="" size="20"> Doesn't Expire <input type="checkbox" name="noexpire" onclick="check_Expiration();"></td>
</tr>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">RegEx <input type="text" name="ex_text" size="70"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Comment <input type="text" name="ex_comments" size="70"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Deleted <input type="checkbox" name="ex_deleted"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
<?php
  }
?>
