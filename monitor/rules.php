<?php
# Script: rules.php
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

  $package = "rules.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['sort'])) {
    $_SESSION['sort'] = clean($_GET['sort'], 20);
  } else {
    unset($_SESSION['sort']);
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Rules</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Mark this Keyword Definition as Deleted?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    clear_fields();
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.rules;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&rule_parent="       + af_form.rule_parent.value;
  af_url += "&rule_description="  + encode_URI(af_form.rule_description.value);
  af_url += "&rule_annotate="     + encode_URI(af_form.rule_annotate.value);
  af_url += "&rule_group="        + af_form.rule_group.value;
  af_url += "&rule_source="       + af_form.rule_source.value;
  af_url += "&rule_application="  + af_form.rule_application.value;
  af_url += "&rule_object="       + af_form.rule_object.value;
  af_url += "&rule_message="      + af_form.rule_message.value;
  af_url += "&rule_page="         + radio_Loop(af_form.rule_page, 3);
  af_url += "&rule_email="        + radio_Loop(af_form.rule_email,3);
  af_url += "&rule_autoack="      + af_form.rule_autoack.checked;
  af_url += "&rule_deleted="      + af_form.rule_deleted.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('rules.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddRule' ).click(function() {
    $( "#dialogRule" ).dialog('open');
  });

  $( "#dialogRule" ).dialog({
    autoOpen: false,
    modal: true,
    height: 330,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogRule" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('rules.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Rule",
        click: function() {
          attach_file('rules.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Rule",
        click: function() {
          attach_file('rules.mysql.php', 0);
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

<div class="main">

<form name="dialog">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Rule Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('rules-help');">Help</a></th>
</tr>
</table>

<div id="rules-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Rule</strong> - Save any changes to this form.</li>
    <li><strong>Add Rule</strong> - Add a new Rule.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddRule" value="Add Rule"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</form>


<div id="dialogRule" title="Rules Form">

<form name="rules">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Define Rule</th>
</tr>
<tr>
  <td class="ui-widget-content">Current Rule and Selected Rule: <select name="rule_parent">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select rule_id,rule_description ";
  $q_string .= "from rules ";
  $q_string .= "where rule_deleted = 0 ";
  $q_string .= "order by rule_description ";
  $q_rules = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rules = mysqli_fetch_array($q_rules)) {
    print "<option value=\"" . $a_rules['rule_id'] . "\">" . $a_rules['rule_description'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Description: <input type="text" name="rule_description" size="80"></td>
  <td class="ui-widget-content">Mark Deleted? <input type="checkbox" name="rule_deleted"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Annotate Overwrite: <input type="text" name="rule_annotate" size="100"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Select Who Is Notified And Which Device</th>
</tr>
<tr>
  <td class="ui-widget-content">Page Group: <select name="rule_group">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select key_id,key_description ";
  $q_string .= "from keywords ";
  $q_string .= "where key_deleted = 0 ";
  $q_string .= "order by key_description ";
  $q_keywords = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_keywords = mysqli_fetch_array($q_keywords)) {
    print "<option value=\"" . $a_keywords['key_id'] . "\">" . $a_keywords['key_description'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Source Node : <select name="rule_source">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select src_id,src_node ";
  $q_string .= "from source_node ";
  $q_string .= "where src_deleted = 0 ";
  $q_string .= "order by src_node ";
  $q_source_node = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_source_node = mysqli_fetch_array($q_source_node)) {
    print "<option value=\"" . $a_source_node['src_id'] . "\">" . $a_source_node['src_node'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Select One Conditional</th>
</tr>
<tr>
  <td class="ui-widget-content">Application : <select name="rule_application">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select app_id,app_description ";
  $q_string .= "from application ";
  $q_string .= "where app_deleted = 0 ";
  $q_string .= "order by app_description ";
  $q_application = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_application = mysqli_fetch_array($q_application)) {
    print "<option value=\"" . $a_application['app_id'] . "\">" . $a_application['app_description'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Object : <select name="rule_object">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select obj_id,obj_name ";
  $q_string .= "from objects ";
  $q_string .= "where obj_deleted = 0 ";
  $q_string .= "order by obj_name ";
  $q_objects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_objects = mysqli_fetch_array($q_objects)) {
    print "<option value=\"" . $a_objects['obj_id'] . "\">" . $a_objects['obj_name'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Message Group : <select name="rule_message">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select msg_id,msg_group ";
  $q_string .= "from message_group ";
  $q_string .= "where msg_deleted = 0 ";
  $q_string .= "order by msg_group ";
  $q_message_group = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_message_group = mysqli_fetch_array($q_message_group)) {
    print "<option value=\"" . $a_message_group['msg_id'] . "\">" . $a_message_group['msg_group'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Select Notification Methods</th>
</tr>
<tr>
  <td class="ui-widget-content">Page: <input type="radio" value="0" name="rule_page"> Disable? <input type="radio" checked value="1" name="rule_page"> Page? <input type="radio" value="2" name="rule_page"> E-Mail?</td>
  <td class="ui-widget-content">E-Mail: <input type="radio" value="0" name="rule_email"> Disable? <input type="radio" value="1" name="rule_email"> Page? <input type="radio" checked value="2" name="rule_email"> E-Mail?</td>
  <td class="ui-widget-content">AutoAck? <input type="checkbox" name="rule_autoack"></td>
</tr>
</table>

</form>

</div>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
