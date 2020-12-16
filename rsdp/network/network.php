<?php
# Script: network.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "network.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

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
  $task[4] = '&gt; ';

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_osteam) > 0) {
    $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);
  } else {
    $a_rsdp_osteam['os_sysname'] = "New Server";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSDP: <?php print $a_rsdp_osteam['os_sysname']; ?></title>

<style type="text/css" title="currentStyle" media="screen">
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

  af_url  = '?complete=' + complete;
  af_url += '&id='       + af_form.id.value;
  af_url += '&rsdp='     + af_form.rsdp.value;

  af_url += '&if_netcheck='     + af_form.if_netcheck.checked;

  if (complete === 1) {
    var question  = "The Network Engineering Task is ready to submit.\n\n";
        question += "Are you sure you are ready to submit this form?";
    answer = confirm(question);
  }

  if (answer === true || complete < 1 || complete === 2) {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_interface( p_script_url, update ) {
  var ai_form = document.rsdp;
  var tbl = document.getElementById('interface');
  var trs = tbl.rows.length;
  var ai_url = new Array(trs);

  for (var i = 1; i < trs; i++) {

    var ID = tbl.rows[i].id;

    ai_url[i]  = '?update='   + update;
    ai_url[i] += '&if_id='    + ID;
    ai_url[i] += '&rsdp='     + ai_form.rsdp.value;

    ai_url[i] += "&if_ip="       + encode_URI(document.getElementById("if_ip_"     + ID).value);
    ai_url[i] += "&if_mask="     + document.getElementById("if_mask_"   + ID).value;
    ai_url[i] += "&if_gate="     + encode_URI(document.getElementById("if_gate_"   + ID).value);
    ai_url[i] += "&if_vlan="     + encode_URI(document.getElementById("if_vlan_"   + ID).value);
    ai_url[i] += "&if_switch="   + encode_URI(document.getElementById("if_switch_" + ID).value);
    ai_url[i] += "&if_port="     + encode_URI(document.getElementById("if_port_"   + ID).value);

  }

  document.getElementById('interface_mysql').innerHTML = '<?php print wait_Process("Pinging Configured Interfaces, Please Wait"); ?>';

  for (var i = 1; i < trs; i++) {
    script = document.createElement('script');
    script.src = p_script_url + ai_url[i];
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function inet_defaults( p_ifid ) {
  var id_form = document.rsdp;
  var id_ip   = document.getElementById("if_ip_"   + p_ifid).value;
  var id_mask = document.getElementById("if_mask_" + p_ifid).value;
  var id_gate = document.getElementById("if_gate_" + p_ifid).value;
  var id_vlan = document.getElementById("if_vlan_" + p_ifid).value;

// get the last period of the ip
  if (id_ip != '') {
    strnpos = id_ip.lastIndexOf('.');
    strnet  = id_ip.slice(0, strnpos);
    strvpos = strnet.lastIndexOf('.');
    strvlan = strnet.slice(strvpos + 1);

    if (id_mask == 0) {
      document.getElementById('if_mask_' + p_ifid)[24].selected = true;
    }

    if (id_gate == '') {
      document.getElementById('if_gate_' + p_ifid).value = strnet + ".254";
    }

    if (id_vlan == '') {
      document.getElementById('if_vlan_' + p_ifid).value = "vl" + strvlan;
    }
    show_file('ping.php?address=' + id_ip);
  }
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

  var vf_submit = 1;

  var tbl = document.getElementById('interface');
  var trs = tbl.rows.length;

  for (var i = 1; i < trs; i++) {

    var ID = tbl.rows[i].id;

    if (document.getElementById("if_ip_" + ID).value == '' && document.getElementById('if_ipcheck_' + ID).value == 1) {
      set_Class('val_ip_' + ID, 'ui-state-error');
      vf_submit = 0;
      document.rsdp.if_save.value = vf_submit;
    } else {
      set_Class('val_ip_' + ID, 'ui-widget-content');
    }
    if (document.getElementById("if_mask_" + ID).value == 0 && document.getElementById('if_ipcheck_' + ID).value == 1) {
      set_Class('val_mask_' + ID, 'ui-state-error');
      vf_submit = 0;
      document.rsdp.if_save.value = vf_submit;
    } else {
      set_Class('val_mask_' + ID, 'ui-widget-content');
    }
    if (document.getElementById("if_gate_" + ID).value == '' && document.getElementById('if_ipcheck_' + ID).value == 1) {
      set_Class('val_gate_' + ID, 'ui-state-error');
      vf_submit = 0;
      document.rsdp.if_save.value = vf_submit;
    } else {
      set_Class('val_gate_' + ID, 'ui-widget-content');
    }
    if (document.getElementById("if_vlan_" + ID).value == '' && document.getElementById('if_ipcheck_' + ID).value == 1) {
      set_Class('val_vlan_' + ID, 'ui-state-error');
      vf_submit = 0;
      document.rsdp.if_save.value = vf_submit;
    } else {
      set_Class('val_vlan_' + ID, 'ui-widget-content');
    }

    if (document.rsdp.rsdp_virtual.value == 0) {
      if (document.getElementById("if_switch_" + ID).value == '' && document.getElementById("if_swcheck_" + ID).value == 1) {
        set_Class('val_switch_' + ID, 'ui-state-error');
        vf_submit = 0;
        document.rsdp.if_save.value = vf_submit;
      } else {
        set_Class('val_switch_' + ID, 'ui-widget-content');
      }
      if (document.getElementById("if_port_" + ID).value == '' && document.getElementById("if_swcheck_" + ID).value == 1) {
        set_Class('val_port_' + ID, 'ui-state-error');
        vf_submit = 0;
        document.rsdp.if_save.value = vf_submit;
      } else {
        set_Class('val_port_' + ID, 'ui-widget-content');
      }
    }
  }

  if (document.rsdp.if_save.value == 0) {
    set_Class('val_update', 'ui-state-error button');
    vf_submit = 0;
  } else {
    set_Class('val_update', 'ui-widget-content button');
  }

  if (document.rsdp.if_netcheck.checked === false) {
    set_Class('if_netcheck', 'ui-state-error');
    $( "#check-1" ).button( "option", "label", "The network checklist has NOT been completed." );
    vf_submit = 0;
  } else {
    set_Class('if_netcheck', 'ui-widget-content');
    $( "#check-1" ).button( "option", "label", "The network checklist HAS been completed." );
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }
}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/network/network.fill.php'     + '?rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/network/interface.mysql.php'  + '?update=-1&rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php'     + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $( "#check-1" ).button();
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
  <th class="ui-state-default">Network Configuration: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('configuration-help');">Help</a></th>
</tr>
</table>

<div id="configuration-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>Network Configuration Page</h2>

<p>This task is typically performed by the Network Engineering Team. There are seven tabs providing Project and System information,
more information about the Interfaces, a Network Form which provides the fields that need to be updated, a Network Checklist, a Task 
Checklist, and a comments page for passing information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task. Since there are no required tasks, clicking Save will not make any changes.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this task and exit back to the Task screen. Since there are no required tasks, clicking Save will only exit back to the Task screen.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. Once completed, the button is enabled. Clicking it identifies the task as completed. A ticket will be created for the next task and an email will be sent notifying the responsible team or individual that their task is ready to be worked.</li>
  </ul></li>
</ul>

</div>

</div>

<?php print submit_RSDP($db, $formVars['rsdp'], 4, $RSDProot . "/network/network.mysql.php", "rsdp_networkpoc", "", $GRP_Networking); ?>


<div id="tabs">

<ul>
  <li><a href="#tabs-1">Project Information</a></li>
  <li><a href="#tabs-2">System Information</a></li>
  <li><a href="#tabs-3">Interface Information</a></li>
  <li><a href="#tabs-4">Network Form</a></li>
  <li><a href="#tabs-5">Network Checklist</a></li>
  <li><a href="#comments">Comments
<?php
  $q_string  = "select count(*) ";
  $q_string .= "from rsdp_comments ";
  $q_string .= "where com_rsdp = " . $formVars['rsdp'];
  $q_rsdp_comments = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_comments = mysqli_fetch_array($q_rsdp_comments);

  if (mysqli_num_rows($q_rsdp_comments)) {
    print " (" . $a_rsdp_comments['count(*)'] . ")";
  } else {
    print " (0)";
  }
?>
</a></li>
</ul>

<div id="tabs-1">

<?php print request_Header($db, $formVars['rsdp']); ?>

</div>


<div id="tabs-2">

<?php print request_Server($db, $formVars['rsdp']); ?>

</div>


<div id="tabs-3">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="9">Interface Information</th>
</tr>
<tr>
  <th class="ui-state-default">Interface Name</th>
  <th class="ui-state-default">Type</th>
  <th class="ui-state-default">Logical Interface</th>
  <th class="ui-state-default">Zone</th>
<?php
  if (rsdp_Virtual($db, $formVars['rsdp']) == 0) {
?>
  <th class="ui-state-default">Physical Port</th>
  <th class="ui-state-default">Media</th>
  <th class="ui-state-default">Speed</th>
  <th class="ui-state-default">Duplex</th>
  <th class="ui-state-default">Redundant</th>
<?php
  }
?>
</tr>
<?php
  $q_string  = "select if_id,if_name,if_sysport,if_interface,zone_name,med_text,spd_text,dup_text,red_text,itp_acronym,if_description ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
  $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
  $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
  $q_string .= "left join int_speed on int_speed.spd_id = rsdp_interface.if_speed ";
  $q_string .= "left join int_duplex on int_duplex.dup_id = rsdp_interface.if_duplex ";
  $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
  $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = 0 ";
  $q_string .= "order by if_interface";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_name']      . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['itp_acronym']  . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_sysport']   . "</td>\n";
    print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['zone_name']    . "</td>\n";
    if (rsdp_Virtual($db, $formVars['rsdp']) == 0) {
      print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['if_interface'] . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['med_text']     . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['spd_text']     . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['dup_text']     . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_rsdp_interface['red_text']     . "</td>\n";
    }
    print "</tr>\n";
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"9\"><strong>Interface Description</strong>: " . $a_rsdp_interface['if_description'] . "</td>\n";
    print "</tr>\n";

    $q_string  = "select if_id,if_name,if_sysport,if_interface,zone_name,med_text,spd_text,dup_text,red_text,itp_acronym,if_description ";
    $q_string .= "from rsdp_interface ";
    $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
    $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
    $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
    $q_string .= "left join int_speed on int_speed.spd_id = rsdp_interface.if_speed ";
    $q_string .= "left join int_duplex on int_duplex.dup_id = rsdp_interface.if_duplex ";
    $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
    $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
    $q_string .= "order by if_interface";
    $q_redundant = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_redundant = mysqli_fetch_array($q_redundant)) {

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">&gt; " . $a_redundant['if_name']   . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_redundant['itp_acronym']    . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_redundant['if_sysport']     . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_redundant['zone_name']      . "</td>\n";
      if (rsdp_Virtual($db, $formVars['rsdp']) == 0) {
        print "  <td class=\"ui-widget-content\">" . $a_redundant['if_interface'] . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_redundant['med_text']     . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_redundant['spd_text']     . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_redundant['dup_text']     . "</td>\n";
        print "  <td class=\"ui-widget-content\">" . $a_redundant['red_text']     . "</td>\n";
      }
      print "</tr>\n";
      print "<tr>\n";
      print "  <td class=\"ui-widget-content\" colspan=\"9\"><strong>Interface Description</strong>: " . $a_redundant['if_description'] . "</td>\n";
      print "</tr>\n";
    }
  }
?>
</table>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Network Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('network-help');">Help</a></th>
</tr>
</table>

<div id="network-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Save Network Changes</strong> - This saves changes made to any of the fields. The button field is <span class="ui-state-error">highlighted</span> until all required fields in the form are <strong>saved</strong>. Changing the fields to add data will change from <span class="ui-state-error">highlighted</span> to normal. This shows the field has data. But it still must be saved. The form is redrawn which will let you verify the data has been saved.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td id="val_update" class="ui-widget-content button">
<input type="button" name="if_update" value="Save Network Changes" onClick="javascript:attach_interface('interface.mysql.php', 1);">
<input type="hidden" name="if_id" value="0">
<input type="hidden" name="if_save" value="0"></td>
</tr>
</table>

<span id="interface_mysql"><?php wait_Process("Pinging Configured Interfaces, Please Wait"); ?></span>

</div>


<div id="tabs-5">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Network Checklist</th>
</tr>
<tr>
  <td id="if_netcheck"><input type="checkbox" id="check-1" name="if_netcheck" onchange="validate_Form();"><label for="check-1"></label></td>
</tr>
<?php print return_Checklist($db, $formVars['rsdp'], 4); ?>
</table>

</div>


<div id="comments">

<?php include ($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
