<?php
# Script: build.php
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

  $package = "build.php";

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
  $task[2] = '&gt; ';

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

function delete_interface( p_script_url ) {
  var answer = 0;
  var popanswer = 0;
  var di_id = '';

  di_id = p_script_url.split("?");

  show_file("interface.check.php?" + di_id[1]);

  answer = confirm("Delete Interface?")

  if (answer) {

    if (document.rsdp.ipokay.value == 1) {
      popanswer = confirm("This interface has an IP address assigned to it.\nNetwork Engineering should be notified to recover the IP.\n\nNotify Network Engineering?");
    }
    if (document.rsdp.ipokay.value == 2) {
      popanswer = confirm("This interface has a switch and port configuration.\nNetwork Engineering should be notified to recover the configuration.\n\nNotify Network Engineering?");
    }
    if (document.rsdp.ipokay.value == 3) {
      popanswer = confirm("This interface has an IP address and a switch and port configuration.\nNetwork Engineering should be notified to recover the configuration.\n\nNotify Network Engineering?");
    }

    if (popanswer) {
      show_file("interface.email.php?" + di_id[1] + "&rsdp=<?php print $formVars['rsdp']; ?>");
    }

    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);

  }
}

function delete_san( p_script_url ) {
  var answer = 0;
  var sananswer = 0;
  var ds_id = '';

  ds_id = p_script_url.split("?");

  show_file("san.check.php?" + ds_id[1]);

  var answer = confirm("Delete SAN Interface?")

  if (answer) {

    if (document.rsdp.sanokay.value == 1) {
      sananswer = confirm("This interface has a switch and port configuration.\nThe SAN team should be notified to recover the configuration.\n\nNotify Storage?");
    }

    if (sananswer) {
      show_file("san.email.php?" + ds_id[1] + "&rsdp=<?php print $formVars['rsdp']; ?>");
    }

    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, complete ) {
  var af_form = document.rsdp;
  var af_url;
  var answer = false;

  af_url  = '?complete=' + complete;
  af_url += '&rsdp='     + af_form.rsdp.value;
  af_url += '&pf_id='    + af_form.pf_id.value;
  af_url += '&os_id='    + af_form.os_id.value;

  af_url += "&os_sysname="  + encode_URI(af_form.os_sysname.value);
  af_url += "&os_fqdn="     + encode_URI(af_form.os_fqdn.value);
  af_url += "&os_software=" + encode_URI(af_form.os_software.value);

  af_url += "&pf_model="     + af_form.pf_model.value;
  af_url += "&pf_redundant=" + af_form.pf_redundant.checked;
  af_url += "&pf_row="       + encode_URI(af_form.pf_row.value);
  af_url += "&pf_rack="      + encode_URI(af_form.pf_rack.value);
  af_url += "&pf_unit="      + encode_URI(af_form.pf_unit.value);
  af_url += "&pf_special="   + encode_URI(af_form.pf_special.value);

  af_url += "&pf_asset="     + encode_URI(af_form.pf_asset.value);
  af_url += "&pf_serial="    + encode_URI(af_form.pf_serial.value);

  af_url += "&mod_type="     + af_form.mod_type.value;
  af_url += "&mod_size="     + encode_URI(af_form.mod_size.value);
  af_url += "&mod_plugs="    + encode_URI(af_form.mod_plugs.value);
  af_url += "&mod_plugtype=" + af_form.mod_plugtype.value;
  af_url += "&mod_volts="    + encode_URI(af_form.mod_volts.value);
  af_url += "&mod_draw="     + encode_URI(af_form.mod_draw.value);
  af_url += "&mod_start="    + encode_URI(af_form.mod_start.value);

  if (complete === 1) {
    var question  = "This platform build form is ready to submit.\n\n";
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

  ai_url  = '?update='   + update;
  ai_url += '&if_id='    + ai_form.if_id.value;
  ai_url += '&if_rsdp='  + ai_form.if_rsdp.value;

  ai_url += "&if_name="        + encode_URI(ai_form.if_name.value);
  ai_url += "&if_sysport="     + encode_URI(ai_form.if_sysport.value);
  ai_url += "&if_ipcheck="     + ai_form.if_ipcheck.checked;
  ai_url += "&if_interface="   + encode_URI(ai_form.if_interface.value);
  ai_url += "&if_type="        + ai_form.if_type.value;
  ai_url += "&if_virtual="     + ai_form.if_virtual.checked;
  ai_url += "&if_monitored="   + ai_form.if_monitored.checked;
  ai_url += "&if_description=" + encode_URI(ai_form.if_description.value);
  ai_url += "&if_redundant="   + ai_form.if_redundant.value;
  ai_url += "&if_groupname="   + encode_URI(ai_form.if_groupname.value);
  ai_url += "&if_if_id="       + ai_form.if_if_id.value;
  ai_url += "&if_media="       + ai_form.if_media.value;
  ai_url += "&if_speed="       + ai_form.if_speed.value;
  ai_url += "&if_duplex="      + ai_form.if_duplex.value;
  ai_url += "&if_swcheck="     + ai_form.if_swcheck.checked;
  ai_url += "&if_zone="        + ai_form.if_zone.value;

  script = document.createElement('script');
  script.src = p_script_url + ai_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_san( p_script_url, update ) {
  var as_form = document.san;
  var as_url;

  as_url  = '?update='   + update;
  as_url += '&san_id='   + as_form.san_id.value;
  as_url += '&san_rsdp=' + as_form.san_rsdp.value;

  as_url += '&san_sysport='  + encode_URI(as_form.san_sysport.value);
  as_url += '&san_switch='   + encode_URI(as_form.san_switch.value);
  as_url += '&san_port='     + encode_URI(as_form.san_port.value);
  as_url += '&san_media='    + as_form.san_media.value;

  script = document.createElement('script');
  script.src = p_script_url + as_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function populate_System() {
  var ps_form = document.rsdp;
  var ps_url;

  ps_url  = 'build.system.php';
  ps_url += '?name_location='    + encode_URI(ps_form.name_location.value);
  ps_url += '&name_instance='    + encode_URI(ps_form.name_instance.value);
  ps_url += '&name_zone='        + ps_form.name_zone.value;
  ps_url += '&name_device='      + ps_form.name_device.value;
  ps_url += '&name_service='     + encode_URI(ps_form.name_service.value);
  ps_url += '&name_freeform='    + encode_URI(ps_form.name_freeform.value);

  script = document.createElement('script');
  script.src = ps_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function validate_Interface() {
  var vi_form = document.interface;
  var vi_submit = 1;

  if (vi_form.if_ipcheck.checked === false) {
    set_Class('ipaddr', 'ui-state-error');
    vi_submit = 0;
  } else {
    set_Class('ipaddr', 'ui-widget-content');
  }

  if (vi_form.if_swcheck.checked === false) {
    set_Class('switch', 'ui-state-error');
    vi_submit = 0;
  } else {
    set_Class('switch', 'ui-widget-content');
  }

  if (vi_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }

}

function validate_Form() {

  var vf_form = document.rsdp;
  var vf_submit = 1;

// only keep submit from showing if no system name provided
  if (vf_form.os_sysname.value === "") {
    vf_submit = 0;
  } else {
    if (document.interface.if_name.value === '') {
      document.interface.if_name.value = vf_form.os_sysname.value;
    }
  }

  if (vf_form.os_fqdn.value === "") {
    set_Class('os_fqdn', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('os_fqdn', 'ui-widget-content');
  }

  if (vf_form.os_software[0].selected === true) {
    set_Class('os_software', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('os_software', 'ui-widget-content');
  }

  if (vf_form.pf_model[0].selected === true) {
    set_Class('pf_model', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('pf_model', 'ui-widget-content');
  }

  if (vf_form.virtual.value == 0) {
    if (vf_form.mod_type[0].selected === true) {
      set_Class('mod_type', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_type', 'ui-widget-content');
    }

    if (vf_form.mod_size.value === "") {
      set_Class('mod_size', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_size', 'ui-widget-content');
    }

    if (vf_form.mod_plugtype[0].selected === true) {
      set_Class('mod_plugtype', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_plugtype', 'ui-widget-content');
    }

    if (isNaN(vf_form.mod_plugs.value)) {
      alert('Plugs must be numeric');
      vf_form.mod_plugs.value = '';
    }

    if (vf_form.mod_plugs.value === '') {
      set_Class('mod_plugs', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_plugs', 'ui-widget-content');
    }

    if (vf_form.mod_volts[0].selected === true) {
      set_Class('mod_volts', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_volts', 'ui-widget-content');
    }

    if (isNaN(vf_form.mod_start.value)) {
      alert('Start Amps must be numeric');
      vf_form.mod_start.value = '';
    }

    if (vf_form.mod_start.value === "") {
      set_Class('mod_start', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_start', 'ui-widget-content');
    }

    if (isNaN(vf_form.mod_draw.value)) {
      alert('Draw Amps must be numeric');
      vf_form.mod_draw.value = '';
    }

    if (vf_form.mod_draw.value === "") {
      set_Class('mod_draw', 'ui-state-error');
      vf_submit = 0;
    } else {
      set_Class('mod_draw', 'ui-widget-content');
    }
  } else {
    set_Class('mod_type', 'ui-widget-content');
    set_Class('mod_size', 'ui-widget-content');
    set_Class('mod_plugtype', 'ui-widget-content');
    set_Class('mod_plugs', 'ui-widget-content');
    set_Class('mod_volts', 'ui-widget-content');
    set_Class('mod_start', 'ui-widget-content');
    set_Class('mod_draw', 'ui-widget-content');
  }

  if (vf_submit) {
    document.rsdp.addbtn.disabled = false;
  } else {
    document.rsdp.addbtn.disabled = true;
  }

}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/build/interface.mysql.php'   + '?update=-1&if_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/build/san.mysql.php'         + '?update=-1&san_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php'    + '?update=-1&com_rsdp=<?php print $formVars['rsdp']; ?>');
  show_file('<?php print $RSDProot; ?>/build/build.fill.php'        + '?rsdp=<?php print $formVars['rsdp']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );
});

$(function() {

  $( '#clickAddInterface' ).click(function() {
    $( "#dialogInterface" ).dialog('open');
  });

  $( "#dialogInterface" ).dialog({
    autoOpen: false,
    modal: true,
    height: 450,
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
      },
      {
        text: "Add Interface",
        click: function() {
          attach_interface('interface.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });


  $( '#clickAddSAN' ).click(function() {
    $( "#dialogSAN" ).dialog('open');
  });

  $( "#dialogSAN" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
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
      },
      {
        text: "Add SAN",
        click: function() {
          attach_san('san.mysql.php', 0);
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
  <th class="ui-state-default">System Provisioning: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('provisioning-help');">Help</a></th>
</tr>
</table>

<div id="provisioning-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>System Provisioning Page</h2>

<p>This task is typically performed by one the Platform Teams. There are eight tabs used to configure the new system. 
Under the 'Task Checklist' are option to generate tickets for the appropriate groups and a comments page for passing 
information to various teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reminder E-Mail</strong> - Send a reminder email to the team or individual responsible for completing this task.</li>
    <li><strong>Save Changes</strong> - Save any changes to this task.</li>
    <li><strong>Save And Exit</strong> - Save changes and return to the Task Menu.</li>
    <li><strong>Task Completed</strong> - This button stays disabled until all identified tasks have been completed. 
Once completed, the button is enabled. Clicking it identifies the task as completed. If selected, a ticket will be 
created for the next task as selected and an email will be sent notifying the responsible team or individual that 
their task is ready to be worked.</li>
  </ul></li>
</ul>

</div>

</div>

<?php print submit_RSDP($db, $formVars['rsdp'], 2, $RSDProot . "/build/build.mysql.php", "rsdp_platformspoc", "rsdp_platform", 0); ?>

<input type="hidden" name="virtual" value="0">
<input type="hidden" name="ipokay" value="0">
<input type="hidden" name="sanokay" value="0">

<div id="tabs">

<ul>
  <li><a href="#tabs-1">System Form</a></li>
  <li><a href="#tabs-2">Platform Form</a></li>
  <li><a href="#tabs-3">Power Form</a></li>
  <li><a href="#tabs-4">Data Center Form</a></li>
  <li><a href="#tabs-5">Interface Form</a></li>
  <li><a href="#tabs-6">HBA Form</a></li>
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">System Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('system-help');">Help</a></th>
</tr>
</table>

<div id="system-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>System Form</strong>
  <ul>
    <li><strong>System Name</strong> - This is the <strong>hostname</strong> of the system being provisioned. This name will be used when saving this sytem to the Inventory.</li>
    <li><strong>Operating System</strong> - Select the Operating System that will used on this system.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">System Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="os_sysname">System Name: <input type="text" name="os_sysname" size="35" onblur="javascript:show_file('validate.hostname.php?rsdp=' + rsdp.value + '&os_sysname=' + os_sysname.value);" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="os_fqdn">Domain Name <input type="text" name="os_fqdn" size="35" onchange="validate_Form();">
  <td class="ui-widget-content" id="os_software">Operating System <select name="os_software" onchange="validate_Form();">
  <option value="0">N/A</option>
<?php
  $q_string  = "select os_id,os_software ";
  $q_string .= "from operatingsystem ";
  $q_string .= "where os_delete = 0 ";
  $q_string .= "order by os_software";
  $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_operatingsystem = mysqli_fetch_array($q_operatingsystem)) {
      print "<option value=\"" . $a_operatingsystem['os_id'] . "\">" . htmlspecialchars($a_operatingsystem['os_software']) . "</option>\n";
  }
?>
  </select><input type="hidden" name="os_id" value="0"></td>
</tr>
</table>

<?php

# can we figure out what the servername breakdown is based on the name?

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'] . " ";
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  if (strlen($a_rsdp_osteam['os_sysname']) > 0) {
    $os_location = strtolower(substr($a_rsdp_osteam['os_sysname'],  0, 4));
    $os_instance = strtolower(substr($a_rsdp_osteam['os_sysname'],  4, 1));
    $os_zone     = strtolower(substr($a_rsdp_osteam['os_sysname'],  5, 1));
    $os_device   = strtolower(substr($a_rsdp_osteam['os_sysname'],  6, 3));
    $os_service  = strtolower(substr($a_rsdp_osteam['os_sysname'],  9, 2));
    $os_freeform = strtolower(substr($a_rsdp_osteam['os_sysname'], 11));
  } else {
    $os_location = '';
    $os_instance = '';
    $os_device   = '';
    $os_service  = '';
    $os_freeform = '';
  }

  $q_string  = "select ct_clli,loc_instance,prod_code ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'] . " ";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

?>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="7">Server Name Builder</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('servername-help');">Help</a></th>
</tr>
</table>

<div id="servername-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>Server Name Builder</h2>

<p>Per Plan and with coordination from Operations, a Server Naming Standard has been created. The following form 
lets you use this standard in creating new servers. The form is populated where possible by breaking down the server name
and filling in the blanks for you. You can also populate the server name from the form by selecting the necessary 
data from the form the clicking on the Populate System Name button. This will take what you have selected and fill in the 
System Name. Once you click one of the task buttons at the top, the name is saved for this server.</p>

<ul>
  <li><strong>Fields</strong>
  <ul>
    <li><strong>Location</strong> - This data is populated from the location drop down on the Server Initialization task.</li>
    <li><strong>Instance</strong> - The instance is the data center instance. Zero (0) indicates you've selected a Lab location, 1 indicates a production and first instance at a city. For example, the first Englewood data center for IEN voice would be 1 and the new Englewood data center for IEN Voice would be 2.</li>
    <li><strong>Zone</strong> - The network zone.</li>
    <li><strong>Unique Device Type</strong> - A selection of Unique Device Types has been created to make it easy to identify a system's Custodian.</li>
    <li><strong>Service/Project</strong> - This data is populated from the Project information you select in the Server Initialization task. <strong>Note:</strong> Infrastructure servers have 'IF' for this data however the standard does not mandate these two characters. You can delete them from the final System Name field at your discretion.</li>
    <li><strong>Freeform</strong> - This field can have no more than 4 characters but can be any data that further identifies a system. Typically the last character would be the system instance. For example lnmt0duwslfui1 is the first instance and lnmt0duwslfui2 is the second instance.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Location: <strong><?php print $a_rsdp_server['ct_clli']; ?></strong><input type="hidden" name="name_location" value="<?php print $a_rsdp_server['ct_clli']; ?>"></td>
  <td class="ui-widget-content">Instance <strong><?php print $a_rsdp_server['loc_instance']; ?></strong><input type="hidden" name="name_instance" value="<?php print $a_rsdp_server['loc_instance']; ?>"></td>
  <td class="ui-widget-content">Zone <select name="name_zone">
<?php
  if ($os_zone == '') {
    print "<option selected value=\"0\">Unknown</option>\n";
  } else {
    print "<option value=\"0\">Unknown</option>\n";
  }
  if ($os_zone == 'c') {
    print "<option selected value=\"1\">C (Enterprise/Corporate)</option>\n";
  } else {
    print "<option value=\"1\">C (Enterprise/Corporate)</option>\n";
  }
  if ($os_zone == 'e') {
    print "<option selected value=\"2\">E (E911)</option>\n";
  } else {
    print "<option value=\"2\">E (E911)</option>\n";
  }
  if ($os_zone == 'd') {
    print "<option selected value=\"3\">D (DMZ)</option>\n";
  } else {
    print "<option value=\"3\">D (DMZ)</option>\n";
  }
  if ($os_zone == 'a') {
    print "<option selected value=\"4\">A (Agnostic/Cross Zones)</option>\n";
  } else {
    print "<option value=\"4\">A (Agnostic/Cross Zones)</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Unique Device Type <select name="name_device">
<option value="0">Not Selected</option>
<?php
  $q_string  = "select dev_id,dev_type,dev_description ";
  $q_string .= "from device ";
  $q_string .= "order by dev_type ";
  $q_device = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_device = mysqli_fetch_array($q_device)) {
    if ($os_device == strtolower($a_device['dev_type'])) {
      print "<option selected=\"true\" value=\"" . $a_device['dev_id'] . "\">" . $a_device['dev_type'] . " (" . $a_device['dev_description'] . ")</option>\n";
    } else {
      print "<option value=\"" . $a_device['dev_id'] . "\">" . $a_device['dev_type'] . " (" . $a_device['dev_description'] . ")</option>\n";
    }
  }
?>
</select></td>
  <td class="ui-widget-content">Service/Project: <strong><?php print $a_rsdp_server['prod_code']; ?></strong><input type="hidden" name="name_service" value="<?php print $a_rsdp_server['prod_code']; ?>"></td>
  <td class="ui-widget-content">Freeform (<i>n</i> characters) <input type="text" name="name_freeform" value="<?php print $os_freeform; ?>" size="10">
  <td class="ui-widget-content"><input type="button" value="Populate System Name" onclick="populate_System();"></td>
</tr>
</table>

</div>


<div id="tabs-2">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Platform Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('platform-help');">Help</a></th>
</tr>
</table>

<div id="platform-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Platform Form</strong>
  <ul>
    <li><strong>Model</strong> - Select the Model from the drop down. This selection will autopopulate the Power fields.</li>
    <li><strong>Type</strong> - This is autopopulated when selecting the Model so you can confirm the right type of equipment has been selected.</li>
    <li><strong>Asset Tag</strong> - The company assigned Asset Tag number.</li>
    <li><strong>Serial Number</strong> - The Serial Number.</li>
    <li><strong>Dell Service Tag</strong> - If Dell equipment, the Dell Service Tag number.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="6">Platform Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="pf_model" colspan="3">Model: <select name="pf_model" onchange="show_file('model.fill.php?pf_model=' + pf_model.value);validate_Form();">
  <option value="0">Unassigned</option>
<?php
  $q_string  = "select mod_id,mod_name,mod_vendor ";
  $q_string .= "from models ";
  $q_string .= "where mod_primary = 1 ";
  $q_string .= "order by mod_vendor,mod_name";
  $q_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_models = mysqli_fetch_array($q_models)) {
    print "<option value=\"" . $a_models['mod_id'] . "\">" . htmlspecialchars($a_models['mod_vendor']) . " " . htmlspecialchars($a_models['mod_name']) . "</option>\n";
  }
?>
  </select></td>
  <td class="ui-widget-content" id="mod_type" colspan="3">Type: <select name="mod_type" onchange="validate_Form();">
  <option value="0">Unassigned</option>
<?php
  $q_string  = "select part_id,part_name ";
  $q_string .= "from parts ";
  $q_string .= "order by part_name";
  $q_parts = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_parts = mysqli_fetch_array($q_parts)) {
    print "<option value=\"" . $a_parts['part_id'] . "\">" . htmlspecialchars($a_parts['part_name']) . "</option>\n";
  }
?>
  </select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Asset Tag: <input type="text" name="pf_asset" size="20" onchange="validate_Form();"></td>
  <td class="ui-widget-content" colspan="3">Serial Number: <input type="text" name="pf_serial" size="20" onchange="validate_Form();"></td>
</tr>
</table>

</div>


<div id="tabs-3">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Power Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('power-help');">Help</a></th>
</tr>
</table>

<div id="power-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This form provides power information to the Data Center personnel with regards to setting up your physical system.</p>

<ul>
  <li><strong>Power Form</strong>
  <ul>
    <li><strong>Redundant Power?</strong> - This physical system requires power be redundant to ensure the system stays on line in the event of a power issue.</li>
    <li><strong>Plug Type</strong> - Select the type of plug used by this system.</li>
    <li><strong>Number of Plugs</strong> - Identify the number of plugs.</li>
    <li><strong>Volts</strong> - Select the Voltage used by the plugs on this system.</li>
    <li><strong>Start Amps</strong> - List the Starting Amps.</li>
    <li><strong>Draw Amps</strong> - List the Amps used while this system is operating. Generally Starting amps are greater than running amps.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Power Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="pf_redundant"><label>Redundant Power? <input type="checkbox" name="pf_redundant" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content" id="mod_plugtype">Plug Type <select name="mod_plugtype" onchange="validate_Form();">
<option value="0">N/A</option>
<?php
  $q_string  = "select plug_id,plug_text ";
  $q_string .= "from int_plugtype";
  $q_int_plugtype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_plugtype = mysqli_fetch_array($q_int_plugtype)) {
    print "<option value=\"" . $a_int_plugtype['plug_id'] . "\">" . htmlspecialchars($a_int_plugtype['plug_text']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" id="mod_plugs">Number of Plugs <input type="text" name="mod_plugs" size="2" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content" id="mod_volts">Volts <select name="mod_volts" onchange="validate_Form();">
<option value="0">N/A</option>
<?php
  $q_string  = "select volt_id,volt_text ";
  $q_string .= "from int_volts";
  $q_int_volts = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_volts = mysqli_fetch_array($q_int_volts)) {
    print "<option value=\"" . $a_int_volts['volt_id'] . "\">" . htmlspecialchars($a_int_volts['volt_text']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" id="mod_start">Start Amps <input type="text" name="mod_start" size="10" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="mod_draw">Draw Amps <input type="text" name="mod_draw" size="10" onchange="validate_Form();"></td>
</tr>
</table>

</div>


<div id="tabs-4">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Data Center Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('datacenter-help');">Help</a></th>
</tr>
</table>

<div id="datacenter-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Data Center Form</strong>
  <ul>
    <li><strong>Suggested Row</strong> - Identify a Row you'd like this system to be located. This is a request for the Data Center personnel.</li>
    <li><strong>Suggested Rack</strong> - Identify a Rack you'd like this system to be located.</li>
    <li><strong>Height of the Server</strong> - Note the number of Units this system takes (a 1U, 2U, etc). This is a numeric value, 'U' is implied.</li>
    <li><strong>Low Unit Number</strong> - Select where on the Rack you want the system to be located. Units start at the bottom of a rack and end at 44.</li>
    <li><strong>Special</strong> - Add any special notes to the Data Center personnel for this location request.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="datacenter" style="display:none">

<div id="main">

<h2>Data Center Form</h2>

<p>The Data Center location fields are suggestions or preferences. This assumes the space is available. 
The Data Center personnel will likely set it up where you suggest but don't be surprised if the space 
has been designated with another project in mind.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Data Center Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="pf_row">Suggested Row <input type="text" name="pf_row" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="pf_rack">Suggested Rack <input type="text" name="pf_rack" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="mod_size">Height of the Server: <input type="text" name="mod_size" size="2" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="pf_unit">Low Unit Number <input type="text" name="pf_unit" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">Special <input type="text" name="pf_special" size="80" onchange="validate_Form();"><input type="hidden" name="pf_id" value="0"></td>
</tr>
</table>

</div>


<div id="tabs-5">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Interface Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('interface-help');">Help</a></th>
</tr>
</table>

<div id="interface-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Interface</strong> - After selecting the interface to edit, click here to save changes.</li>
    <li><strong>Add Interface</strong> - Add a new interface. You can also select an existing item, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Network Table From:</strong> - Select a server from the listing to duplicate it's list of interfaces.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>IP Address</strong>
  <ul>
    <li><strong>IP Address Required</strong> - Tells the Network Team that an IP Address is required for this Interface.</li>
    <li><strong>Interface Name</strong> - The name of the interface. Typically the hostname and one of the interface names will match. The interface that's not a hostname will generally be the management interface.</li>
    <li><strong>Logical Interface Name</strong> - The name of the interface as assigned by the operating system (such as eth0, e1000g0, bge0, etc).</li>
    <li><strong>Interface Type</strong> - Identify the purpose of this interface. For several reports to properly work, one interface must be identified as a Management interface even if there's just one IP.</li>
    <li><strong>Zone</strong> - Network zone this IP belongs to. There may be sub-configurations to insure proper traffic shaping.</li>
    <li><strong>Note</strong> - Enter a note here specific to the Interface which will provide additional details to the Network Team to help with configuration.</li>
  </ul></li>
  <li><strong>Bonded, Team, or IPMP Interface</strong>
  <ul>
    <li><strong>Virtual Interface?</strong> - If a virtual interface, identify it here. Note that there are virtual interfaces that aren't part of a redundant configuration such as Oracle's Virtual interfaces. Identify all virtual interfaces here.</li>
    <li><strong>Redundancy</strong> - If part of a redundant interface configuration such as Bond, IPMP, APA, or Teaming, select the virtual interface type here. Anything other than 'N/A' will automatically display in the 'Assignment' drop down. This will let you select the physical members of a Redundant interface configuration.</li>
    <li><strong>Group/Teaming Name</strong> - Some operating systems such as Solaris and Windows assign group names to the physical members of a virtual interface. Enter the group or team name for the physical interfaces here.</li>
    <li><strong>Bond/IPMP/APA/Teaming Assignment</strong> - If the physical member of a redundant configuration, select the redundant virtual interface this interface is a member of.</li>
  </ul></li>
  <li><strong>Physical System Requirements</strong>
  <ul>
    <li><strong>Switch Configuration Required</strong> - Notifies the Network Team that this physical interface needs to be connected to a switch.</li>
    <li><strong>Physical Port</strong> - The physical port on the server where the network cable is plugged in.</li>
    <li><strong>Media</strong> - What physical cable type is being used for this system.</li>
    <li><strong>Speed</strong> - Generally systems auto-negotiate however some systems have issues with properly syncing with the switch if it's set to auto-negotiate.</li>
    <li><strong>Duplex</strong> - Same here. Most systems are auto-negotiate but there are some systems with issues.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>Interface Management</strong> title bar to toggle the <strong>Interface Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddInterface" value="Add Interface"></td>
</tr>
</table>

<span id="interface_mysql"></span>

</div>


<div id="tabs-6">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">SAN Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('san-help');">Help</a></th>
</tr>
</table>

<div id="san-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This form is used by the Data Center personnel when they are racking the physical system. You enter in the 
HBA card location and which port will be used. If the locations aren't clear when you look at the back of a 
system, use a permanent marker to clearly identify the location for the Data Center personnel.</p>


<ul>
  <li><strong>SAN Form</strong>
  <ul>
    <li><strong>System HBA Slot and Port</strong> - Identify the location of the HBA card on the system and the port to be used.</li>
  </ul></li>
  <li>The following fields will likely be filled in by the SAN team during their task, but is here for completeness.
  <ul>
    <li><strong>Switch</strong> - Identify the Switch the system will be plugged in to.</li>
    <li><strong>Switch Port</strong> - Identify the Switch Port.</li>
    <li><strong>Media Type</strong> - Select the Media Type. This is most likely Fiber but new technologies might provide better cable options.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddSAN" value="Add SAN"></td>
</tr>
</table>

<span id="san_mysql"></span>

</div>


<div id="comments">

<?php include($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>

</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<div id="dialogInterface" title="Interface Form">

<form name="interface">

<input type="hidden" name="if_id"      value="0">
<input type="hidden" name="if_rsdp"    value="<?php print $formVars['rsdp']; ?>">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">IP Address</th>
</tr>
<tr>
  <td class="ui-state-error" id="ipaddr"><label>Check Here if This Interface Requires an IP Address <input type="checkbox" name="if_ipcheck" onchange="validate_Interface();"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">External Name For This Interface (lnmtcodcetl10 for example) <input type="text" name="if_name" size="20" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content">OS Defined Interface Name (eth0 for example) <input type="text" name="if_interface" size="10" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content">Function of This Interface: <select name="if_type" onchange="validate_Form();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select itp_id,itp_name ";
  $q_string .= "from inttype ";
  $q_string .= "order by itp_id";
  $q_inttype = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inttype = mysqli_fetch_array($q_inttype)) {
    print "<option value=\"" . $a_inttype['itp_id'] . "\">" . $a_inttype['itp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">In What Network Zone Will This System Be Located <select name="if_zone" onchange="validate_Form();">
<option value="0">Unknown</option>
<?php
  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from ip_zones ";
  $q_string .= "order by zone_name";
  $q_ip_zones = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_ip_zones = mysqli_fetch_array($q_ip_zones)) {
    print "<option value=\"" . $a_ip_zones['zone_id'] . "\">" . $a_ip_zones['zone_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">If this system will be monitored by OpenView, is this the interface that will be monitored? <input type="checkbox" name="if_monitored" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Additional Details for NetEng: <input type="text" name="if_description" size="80" onchange="validate_Form();"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Bonded, Team, or IPMP Interface</th>
</tr>
<tr>
  <td class="ui-state-highlight" colspan="3"><label>Is This A Virtual (Bonded, Team, IPMP) Interface? <input type="checkbox" name="if_virtual" onchange="validate_Form();"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Type of Redundancy: <select name="if_redundant" onchange="validate_Form();">
<option value="0">N/A</option>
<?php
  $q_string  = "select red_id,red_text ";
  $q_string .= "from int_redundancy ";
  $q_string .= "order by red_text";
  $q_int_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_redundancy = mysqli_fetch_array($q_int_redundancy)) {
    print "<option value=\"" . $a_int_redundancy['red_id'] . "\">" . $a_int_redundancy['red_text'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Group Name If Needed: <input type="text" name="if_groupname" size="20" onchange="validate_Form();"></td>
  <td class="ui-widget-content">
<?php
  $os = rsdp_System($db, $formVars['rsdp']);

  if ($os == "Linux") {
    print "Bond ";
  }
  if ($os == "HP-UX") {
    print "APA ";
  }
  if ($os == "SunOS") {
    print "IPMP ";
  }
  if ($os == "Windows") {
    print "Teaming ";
  }
?>
Assignment <select name="if_if_id" onchange="validate_Form();">
<option value="0">Unassigned</option>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Physical System Requirements</th>
</tr>
<tr>
  <td class="ui-state-error" id="switch" colspan="3"><label>Check Here if This is a Physical System and Switch Configuration is Required <input type="checkbox" name="if_swcheck" onchange="validate_Interface();"></label></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Physical Port Details ('slot 0 port 0' or 'mb 0 port 0' for example) <input type="text" name="if_sysport" size="20" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content">Media Type: <select name="if_media" onchange="validate_Form();">
<option value="0">N/A</option>
<?php
  $q_string  = "select med_id,med_text ";
  $q_string .= "from int_media ";
  $q_string .= "order by med_text";
  $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_media = mysqli_fetch_array($q_int_media)) {
    print "<option value=\"" . $a_int_media['med_id'] . "\">" . $a_int_media['med_text'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Network Speed: <select name="if_speed" onchange="validate_Form();">
<option value="0">N/A</option>
<?php
  $q_string  = "select spd_id,spd_text ";
  $q_string .= "from int_speed ";
  $q_string .= "order by spd_text";
  $q_int_speed = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {
    print "<option value=\"" . $a_int_speed['spd_id'] . "\">" . $a_int_speed['spd_text'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Duplex: <select name="if_duplex" onchange="validate_Form();">
<option value="0">N/A</option>
<?php
  $q_string  = "select dup_id,dup_text ";
  $q_string .= "from int_duplex ";
  $q_string .= "order by dup_text";
  $q_int_duplex = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_duplex = mysqli_fetch_array($q_int_duplex)) {
    print "<option value=\"" . $a_int_duplex['dup_id'] . "\">" . $a_int_duplex['dup_text'] . "</option>\n";
  }
?>
</select></td>
</table>

</form>

</div>


<div id="dialogSAN" title="SAN Form">

<form name="san">

<input type="hidden"                 name="san_id"     value="0">
<input type="hidden"                 name="san_rsdp"   value="<?php print $formVars['rsdp']; ?>">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">SAN Form</th>
</tr>
<tr>
  <td class="ui-widget-content">System HBA Slot and Port <input type="text" name="san_sysport" size="20"></td>
  <td class="ui-widget-content">Switch <input type="text" name="san_switch" size="20"></td>
  <td class="ui-widget-content">Switch Port <input type="text" name="san_port" size="20"></td>
  <td class="ui-widget-content">Media Type <select name="san_media">
<option value="0">N/A</option>
<?php
  $q_string  = "select med_id,med_text ";
  $q_string .= "from int_media ";
  $q_string .= "order by med_text";
  $q_int_media = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_int_media = mysqli_fetch_array($q_int_media)) {
    print "<option value=\"" . $a_int_media['med_id'] . "\">" . $a_int_media['med_text'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
