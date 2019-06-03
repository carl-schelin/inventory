<?php
# Script: installed.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');
  check_login('2');

  $package = "installed.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'], 10);
  } else {
    $formVars['rsdp'] = 0;
  }
  if ($formVars['rsdp'] == '' || $formVars['rsdp'] == 0) {
    include($RSDPpath . "/redirect.php");
    exit(0);
  }

# where are we?
  for ($i = 0; $i < 19; $i++) {
    $task[$i] = '';
  }
  $task[10] = '&gt; ';

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_rsdp_osteam) > 0) {
    $a_rsdp_osteam = mysql_fetch_array($q_rsdp_osteam);
  } else {
    $a_rsdp_osteam = "New System";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: <?php print $a_rsdp_osteam['os_sysname']; ?></title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" src="<?php print $RSDProot; ?>/admin/comments.js"></script>

<script type="text/javascript">

function attach_file( p_script_url, complete ) {
  var af_form = document.rsdp;
  var af_url;
  var answer = false;

  af_url  = '?complete='    + complete;
  af_url += '&rsdp='        + <?php print $formVars['rsdp']; ?>;
  af_url += '&id='          + af_form.id.value;

  af_url += '&if_config='   + af_form.if_config.checked;
  af_url += '&if_built='    + af_form.if_built.checked;
  af_url += '&if_network='  + af_form.if_network.checked;
  af_url += '&if_dns='      + af_form.if_dns.checked;
  af_url += '&if_inscheck=' + af_form.if_inscheck.checked;

  if (complete === 1) {
    var question  = "This system form is ready to submit.\n\n";
        question += "Submitting this request will notify the appropriate teams.\n";
        question += "Making changes to the information here after submission will require seperate notification.\n\n";
        question += "Are you sure you are ready to submit this form?";
    answer = confirm(question);
  }

  if (answer === true || complete < 1 || complete == 2) {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_interface( p_script_url, update ) {
  var ai_form = document.interface;
  var ai_url;

  ai_url  = '?update='      + update;
  ai_url += '&rsdp='        + <?php print $formVars['rsdp']; ?>;
  ai_url += '&id='          + ai_form.if_id.value;

  ai_url += '&if_mac='    + encode_URI(ai_form.if_mac.value);

  script = document.createElement('script');
  script.src = p_script_url + ai_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_san( p_script_url, update ) {
  var as_form = document.san;
  var as_url;

  as_url  = '?update='      + update;
  as_url += '&rsdp='        + <?php print $formVars['rsdp']; ?>;
  as_url += '&id='          + as_form.san_id.value;

  as_url += '&san_wwnnzone='  + encode_URI(as_form.san_wwnnzone.value);

  script = document.createElement('script');
  script.src = p_script_url + as_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_checklist( p_script_url, p_index, p_task, p_check, p_comment ) {
  var ac_form = document.rsdp;
  var ac_url;

  ac_url  = '?id='       + ac_form.id.value;
  ac_url += '&rsdp='     + <?php print $formVars['rsdp']; ?>;

  ac_url += '&chk_task='     + encode_URI(p_task);
  ac_url += '&chk_group='    + ac_form.chk_group.value;
  ac_url += '&chk_index='    + encode_URI(p_index);
  ac_url += '&chk_checked='  + encode_URI(p_check);
  ac_url += '&chk_comment='  + encode_URI(p_comment);

  script = document.createElement('script');
  script.src = p_script_url + ac_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function validate_Form() {
  var vf_form = document.rsdp;
  var vf_submit = 1;

  if (vf_form.if_inscheck.checked === false) {
    set_Class('if_inscheck', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "System checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('if_inscheck', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "System checklist HAS been completed." );
  }

  if (vf_form.if_config.checked === false) {
    set_Class('if_config', 'ui-state-error');
    $( "#check-2" ).button( "option", "label", "System hardware has NOT been configured." );
    vf_submit = 0;
  } else {
    set_Class('if_config', 'ui-widget-content');
    $( "#check-2" ).button( "option", "label", "System hardware HAS been configured." );
  }

  if (vf_form.if_built.checked === false) {
    set_Class('if_built', 'ui-state-error');
    $( "#check-3" ).button( "option", "label", "Operating System has NOT been installed." );
    vf_submit = 0;
  } else {
    set_Class('if_built', 'ui-widget-content');
    $( "#check-3" ).button( "option", "label", "Operating System HAS been installed." );
  }

  if (vf_form.if_network.checked === false) {
    set_Class('if_network', 'ui-state-error');
    $( "#check-4" ).button( "option", "label", "The Network and Filesystems are NOT configured." );
    vf_submit = 0;
  } else {
    set_Class('if_network', 'ui-widget-content');
    $( "#check-4" ).button( "option", "label", "The Network and Filesystems ARE configured." );
  }

  if (vf_form.if_dns.checked === false) {
    set_Class('if_dns', 'ui-state-error');
    $( "#check-5" ).button( "option", "label", "The Interfaces have NOT been added to DNS." );
    vf_submit = 0;
  } else {
    set_Class('if_dns', 'ui-widget-content');
    $( "#check-5" ).button( "option", "label", "The Interfaces HAVE been added to DNS." );
  }

  if (vf_submit) {
    vf_form.addbtn.disabled = false;
  } else {
    vf_form.addbtn.disabled = true;
  }

}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php' + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/system/installed.fill.php' + '?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('interface.mysql.php?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('san.mysql.php?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();
  $( "#check-2" ).button();
  $( "#check-3" ).button();
  $( "#check-4" ).button();
  $( "#check-5" ).button();
  $( "#check-6" ).button();

  $( "#dialogInterface" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogInterface" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_interface('interface.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Interface",
        click: function() {
          attach_interface('interface.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogSAN" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    close: function(event, ui) {
      $( "#dialogSAN" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_san('san.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update SAN",
        click: function() {
          attach_san('san.mysql.php', 1);
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
<?php include($RSDPpath . '/topmenu.rsdp.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="rsdp">

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">System Installation: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('system-help');">Help</a></th>
</tr>
</table>

<div id="system-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>System Installation Page</h2>

<p>This task is typically performed by the Platforms Team (Windows or Unix). There are seven tabs providing Project and System information,
Forms which may need updating, a list of items that must be checked off, a list of task checkboxes that needs to be completed, and a comments 
page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and return to the task list.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. Once 
completed, the button is enabled. Clicking it identifies the task as completed. If selected, a ticket will be created for 
the next task and an email will be sent notifying the responsible team or individual that their task is ready to be worked.</li>
  </ul></li>
</ul>

</div>

</div>

<?php print submit_RSDP( $formVars['rsdp'], 10, $RSDProot . "/system/installed.mysql.php", "rsdp_platformspoc", "rsdp_platform", 0); ?>

<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#iplisting">IP Listing</a></li>
  <li><a href="#tabs-3">Interface Form</a></li>
  <li><a href="#tabs-4">SAN Form</a></li>
  <li><a href="#tabs-5">System Checklist</a></li>
  <li><a href="#tabs-6">Task Checklist</a></li>
  <li><a href="#tabs-7">Comments<?php
  $q_string  = "select count(*) ";
  $q_string .= "from rsdp_comments ";
  $q_string .= "where com_rsdp = " . $formVars['rsdp'];
  $q_rsdp_comments = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_rsdp_comments = mysql_fetch_array($q_rsdp_comments);

  if (mysql_num_rows($q_rsdp_comments)) {
    print " (" . $a_rsdp_comments['count(*)'] . ")";
  } else {
    print " (0)";
  }
?>
</a></li>
</ul>

<div id="tabs-1">

<?php print request_Header($formVars['rsdp']); ?>

</div>


<div id="tabs-2">

<?php print request_Server($formVars['rsdp']); ?>

</div>


<div id="iplisting">

<p>

<p>The following is a list of all the IPs associated with this project. If you selected the 
checkbox for opening a DNS ticket back on the Server Request Page, upon clicking the 
<strong>Task Completed</strong> button a ticket will be generated for <strong>just this 
server</strong>. If you want to generate a single ticket for all servers in this Project, 
copy and paste the following text into a new ticket and submit it.</p>

<div class="main-help">

<?php
# get the project code
  $q_string  = "select rsdp_project ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_rsdp_server) > 0) {
    $a_rsdp_server = mysql_fetch_array($q_rsdp_server);
  } else {
    $a_rsdp_server['rsdp_project'] = 0;
  }

# using the project code, get all the interfaces for this project. Makes it easier to create the ticket.
  $q_string  = "select if_name,if_ip,os_fqdn ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "left join rsdp_server on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
  $q_string .= "where rsdp_project = " . $a_rsdp_server['rsdp_project'] . " and if_ipcheck = 1 ";
  $q_string .= "group by if_ip";
  $q_rsdp_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_rsdp_interface) > 0) {

    print "<p>Request a forward and reverse DNS record for the following hostnames and IP addresses.</p>\n";

# set the start variable for the display of the information
    $start = "<p>";

    while ($a_rsdp_interface = mysql_fetch_array($q_rsdp_interface)) {

      print $start . $a_rsdp_interface['if_name'] . "." . $a_rsdp_interface['os_fqdn'] . " - " . $a_rsdp_interface['if_ip'] . "\n";
      $start = "<br>";

    }
    print "</p>\n";

    print "<p>See me if you have any questions.</p>\n";

  } else {
    print "<p>If you are seeing this, you probably failed to check the box indicating you need an IP.</p>\n";
  }
?>

</div>

</div>


<div id="tabs-3">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Interface Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('interface-help');">Help</a></th>
</tr>
</table>

<div id="interface-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This form is intended for the Systems Admin to enter the MAC address for the interfaces in order to complete the configuration. Select the interface to edit, update the MAC, and save the changes.</p>

</div>

</div>

<span id="interface_mysql"></span>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">SAN Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('san-help');">Help</a></th>
</tr>
</table>

<div id="san-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This form is intended for the Systems Admin to enter the WWNN Zone information SAN interfaces in order to complete the configuration. Select the interface to edit, update the WWNN, and save the changes.</p>

</div>

</div>

<span id="san_mysql"></span>

</div>


<div id="tabs-5">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">System Checklist</th>
</tr>
<tr>
  <td id="if_inscheck" colspan="2"><input type="checkbox" id="check-1" name="if_inscheck" onchange="validate_Form();"><label for="check-1"></label></td>
</tr>
<?php print return_Checklist( $formVars['rsdp'], 10); ?>
</table>

</div>


<div id="tabs-6">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">System Checklist</th>
</tr>
<tr>
  <td id="if_config"  ><input type="checkbox" id="check-2" name="if_config"   onclick="validate_Form();"><label for="check-2"></label></td>
</tr>
<tr>
  <td id="if_built"   ><input type="checkbox" id="check-3" name="if_built"    onclick="validate_Form();"><label for="check-3"></label></td>
</tr>
<tr>
  <td id="if_network" ><input type="checkbox" id="check-4" name="if_network"  onclick="validate_Form();"><label for="check-4"></label></td>
</tr>
<tr>
  <td id="if_dns"     ><input type="checkbox" id="check-5" name="if_dns"      onclick="validate_Form();"><label for="check-5"></label></td>
</tr>
</table>

</div>


<div id="tabs-7">

<?php include ($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<div id="dialogInterface" title="Interface Form">

<form name="interface">

<input type="hidden" name="if_id" value="0"></td>
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Interface Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Name: <span id="if_name"></span></td>
  <td class="ui-widget-content">IP <span id="if_ip"></span></td>
  <td class="ui-widget-content">MAC <input type="text" name="if_mac" size="20"></td>
</tr>
</table>

</form>

</div>


<div id="dialogSAN" title="SAN Form">

<form name="san">

<input type="hidden" name="san_id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">SAN Form</th>
</tr>
<tr>
  <td class="ui-widget-content">System Slot/Port: <span id="san_sysport"></span></td>
  <td class="ui-widget-content">Switch <span id="san_switch"></span></td>
  <td class="ui-widget-content">Port <span id="san_port"></span></td>
  <td class="ui-widget-content">Media <span id="san_media"></span></td>
  <td class="ui-widget-content">WWNN Zone <input type="text" name="san_wwnnzone" size="20" onchange="validate_Form();"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
