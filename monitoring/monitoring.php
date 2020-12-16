<?php
# Script: monitoring.php
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

  $package = "monitoring.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Monitoring</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Monitoring task?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>

function attach_file( p_script_url, update ) {
  var af_form = document.monitoring;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&mon_system="     + af_form.mon_openview.checked;
  af_url += "&mon_type="       + af_form.mon_type.value;
  af_url += "&mon_active="     + af_form.mon_active.checked;
  af_url += "&mon_group="      + af_form.mon_group.value;
  af_url += "&mon_user="       + af_form.mon_user.value;
  af_url += "&mon_notify="     + radio_Loop(ab_form.mon_notify, 3);
  af_url += "&mon_hours="      + radio_Loop(ab_form.mon_hours, 2);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('monitoring.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddMonitoring' ).click(function() {
    $( "#dialogMonitoring" ).dialog('open');
  });

  $( "#dialogMonitoring" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 1000,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogMonitoring" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('monitoring.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Monitoring",
        click: function() {
          attach_file('monitoring.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Monitoring",
        click: function() {
          attach_file('monitoring.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Monitoring Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('monitoring-help');">Help</a></th>
</tr>
</table>

<div id="monitoring-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Monitoring Form</strong>
  <ul>
    <li><strong>Monitoring Name</strong> - The name of the company Monitoring</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddMonitoring" value="Add Monitoring"></td>
</tr>
</table>

</form>

<span id="table_mysql"></span>

</div>

</div>

<div id="dialogMonitoring" title="Monitoring Form">

<form name="monitoring">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Monitoring Form</th>
</tr>
<tr>
  <td class="ui-widget-content">What system to monitor: <select name="mon_server">
<?php
  $inventory_id = 0;
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_manager = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
    if ($inventory_id == 0) {
      $inventory_id = $a_inventory['inv_id'];
    }
    print "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
  }
?>
</select> What interface? <select name="mon_interface">
<?php
  $q_string  = "select int_id,int_server ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $inventory_id . " ";
  $q_string .= "order by int_server ";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_interface = mysqli_fetch_array($q_interface)) {
    print "<option value=\"" . $a_interface['int_id'] . "\">" . $a_interface['int_server'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Monitoring System: <select name="mon_system">
<?php
  $q_string  = "select ms_id,ms_name ";
  $q_string .= "from mon_system ";
  $q_string .= "order by ms_name ";
  $q_mon_system = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_mon_system = mysqli_fetch_array($q_mon_system)) {
    print "<option value=\"" . $a_mon_system['ms_id'] . "\">" . $a_mon_system['ms_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">What to monitor: <select name="mon_type">
<?php
  $q_string  = "select mt_id,mt_name ";
  $q_string .= "from mon_type ";
  $q_string .= "order by mt_name ";
  $q_mon_type = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_mon_type = mysqli_fetch_array($q_mon_type)) {
    print "<option value=\"" . $a_mon_type['mt_id'] . "\">" . $a_mon_type['mt_name'] . "</option>\n";
  }
?>
</select> Activate check?: <input type="checkbox" name="mon_active"></td>
</tr>
<tr>
  <td class="ui-widget-content"><input type="radio" checked name="notification"> Group to be notified: <select name="mon_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_page != \"\" and grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    if ($a_groups['grp_id '] == $_SESSION['group']) {
      print "<option selected value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
    }
  }
?>
</select>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_page != \"\" and usr_disabled = 0 and usr_group = " . $_SESSION['group'] . " ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_users) > 0) {
    print "<strong>or</strong> <input type=\"radio\" name=\"notification\"> Who to notifiy: <select name=\"mon_user\">\n";
    while ($a_users = mysqli_fetch_array($q_users)) {
      print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
    }
    print "</select>";
  } else {
    print "<input type=\"hidden\" value=\"0\" name=\"mon_user\"> No users with a defined page email address found.\n";
  }
?>
</td>
</tr>
<tr>
  <td class="ui-widget-content">Notification: <input type="radio" checked value="0" name="mon_notify"> None <input type="radio" value="0" name="mon_notify"> Email <input type="radio" value="0" name="mon_notify"> Page</td>
</tr>
<tr>
  <td class="ui-widget-content">Paging Hours: <input type="radio" checked name="mon_hours"> Business Hours <input type="radio" name="mon_hours"> 24x7</td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
