<?php
# Script: initial.php
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

  $package = "initial.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['rsdp'])) {
    $formVars['rsdp'] = clean($_GET['rsdp'], 10);
  } else {
# if called from the index.php script
    if (isset($_POST['rsdp'])) {
      $formVars['rsdp'] = clean($_POST['rsdp'], 10);
    } else {
      $formVars['rsdp'] = 0;
    }
  }

  if ($formVars['rsdp'] == '') {
    $formVars['rsdp'] = 0;
  }

# where are we?
  for ($i = 0; $i < 19; $i++) {
    $task[$i] = '';
  }
  $task[1] = '&gt; ';

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $formVars['rsdp'];
  $q_rsdp_osteam = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_rsdp_osteam) > 0) {
    $a_rsdp_osteam = mysql_fetch_array($q_rsdp_osteam);
  } else {
    $a_rsdp_osteam['os_sysname'] = "New Server";
  }

  $ticket = 'yes';
  $ticket = 'no';

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

function delete_line( p_script_url ) {
  var answer = confirm("Delete Filesystem?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function populatevm() {
  if (document.rsdp.virtual.checked === true) {
    document.rsdp.rsdp_processors.value = 1;
    document.rsdp.rsdp_memory.value = 2;
    document.rsdp.rsdp_ossize.value = 80;
  }
}

function checkos() {
  if (document.rsdp.rsdp_appmonitor.checked === true) {
    document.rsdp.rsdp_osmonitor.checked = true;
  }
}

function populatestart() {
  if (document.rsdp.bu_start.value === '0000-00-00') {
    document.rsdp.bu_start.value = document.rsdp.rsdp_completion.value;
  }
  if (document.rsdp.bu_start.value === '') {
    document.rsdp.bu_start.value = document.rsdp.rsdp_completion.value;
  }
}

function update_Platform() {
  var up_form = document.rsdp;
  var up_url;

  up_url  = 'initial.platform.php';
  up_url += '?rsdp='          + <?php print $formVars['rsdp']; ?>;

  up_url += "&rsdp_platformspoc=" + up_form.rsdp_platformspoc.value;

  script = document.createElement('script');
  script.src = up_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_Application() {
  var ua_form = document.rsdp;
  var ua_url;

  ua_url  = 'initial.application.php';
  ua_url += '?rsdp='          + <?php print $formVars['rsdp']; ?>;

  ua_url += "&rsdp_apppoc=" + ua_form.rsdp_apppoc.value;

  script = document.createElement('script');
  script.src = ua_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_duplicate( p_script_url, complete ) {
  var ad_form = document.duplicate;
  var ad_url;

  ad_url  = '?complete='      + complete;
  ad_url += '&rsdp='          + <?php print $formVars['rsdp']; ?>;

  ad_url += "&chk_filesystem="    + ad_form.chk_filesystem.checked;
  ad_url += "&chk_ipaddr="        + ad_form.chk_ipaddr.checked;
  ad_url += "&chk_san1="          + ad_form.chk_san1.checked;
  ad_url += "&chk_net="           + ad_form.chk_net.checked;
  ad_url += "&chk_virt="          + ad_form.chk_virt.checked;
  ad_url += "&chk_sys1="          + ad_form.chk_sys1.checked;
  ad_url += "&chk_san2="          + ad_form.chk_san2.checked;
  ad_url += "&chk_sys2="          + ad_form.chk_sys2.checked;
  ad_url += "&chk_backup="        + ad_form.chk_backup.checked;
  ad_url += "&chk_mon1="          + ad_form.chk_mon1.checked;
  ad_url += "&chk_app1="          + ad_form.chk_app1.checked;
  ad_url += "&chk_mon2="          + ad_form.chk_mon2.checked;
  ad_url += "&chk_app2="          + ad_form.chk_app2.checked;
  ad_url += "&chk_infosec="       + ad_form.chk_infosec.checked;

  script = document.createElement('script');
  script.src = p_script_url + ad_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_file( p_script_url, complete ) {
  var af_form = document.rsdp;
  var af_url;
  var answer = false;

  af_url  = '?complete='      + complete;
  af_url += '&rsdp='          + af_form.rsdp.value;

  af_url += "&usr_phone="    + encode_URI(af_form.usr_phone.value);
  af_url += "&usr_email="    + encode_URI(af_form.usr_email.value);
  af_url += "&usr_deptname=" + af_form.usr_deptname.value;

  af_url += "&rsdp_requestor="    + af_form.rsdp_requestor.value;
  af_url += "&rsdp_location="     + af_form.rsdp_location.value;
  af_url += "&rsdp_product="      + af_form.rsdp_product.value;
  af_url += "&rsdp_completion="   + encode_URI(af_form.rsdp_completion.value);
  af_url += "&rsdp_project="      + af_form.rsdp_project.value;
  af_url += "&rsdp_platformspoc=" + af_form.rsdp_platformspoc.value;
  af_url += "&rsdp_sanpoc="       + af_form.rsdp_sanpoc.value;
  af_url += "&rsdp_networkpoc="   + af_form.rsdp_networkpoc.value;
  af_url += "&rsdp_virtpoc="      + af_form.rsdp_virtpoc.value;
  af_url += "&rsdp_dcpoc="        + af_form.rsdp_dcpoc.value;
  af_url += "&rsdp_monitorpoc="   + af_form.rsdp_monitorpoc.value;
  af_url += "&rsdp_apppoc="       + af_form.rsdp_apppoc.value;
  af_url += "&rsdp_backuppoc="    + af_form.rsdp_backuppoc.value;
  af_url += "&rsdp_platform="     + af_form.rsdp_platform.value;
  af_url += "&rsdp_application="  + af_form.rsdp_application.value;
  af_url += "&rsdp_service="      + af_form.rsdp_service.value;
  af_url += "&rsdp_vendor="       + af_form.rsdp_vendor.value;
  af_url += "&rsdp_function="     + encode_URI(af_form.rsdp_function.value);
  af_url += "&rsdp_processors="   + encode_URI(af_form.rsdp_processors.value);
  af_url += "&rsdp_memory="       + encode_URI(af_form.rsdp_memory.value);
  af_url += "&rsdp_ossize="       + encode_URI(af_form.rsdp_ossize.value);
  af_url += "&rsdp_osmonitor="    + af_form.rsdp_osmonitor.checked;
  af_url += "&rsdp_appmonitor="   + af_form.rsdp_appmonitor.checked;
  af_url += "&rsdp_datapalette="  + af_form.rsdp_datapalette.checked;
  af_url += "&rsdp_opnet="        + af_form.rsdp_opnet.checked;
  af_url += "&rsdp_newrelic="     + af_form.rsdp_newrelic.checked;
  af_url += "&rsdp_centrify="     + af_form.rsdp_centrify.checked;
  af_url += "&rsdp_backup="       + af_form.rsdp_backup.checked;

<?php
  if ($ticket == 'yes') {
?>
  af_url += "&tkt_id="         + af_form.tkt_id.value;
  af_url += "&tkt_build="      + af_form.tkt_build.checked;
  af_url += "&tkt_san="        + af_form.tkt_san.checked;
  af_url += "&tkt_network="    + af_form.tkt_network.checked;
  af_url += "&tkt_datacenter=" + af_form.tkt_datacenter.checked;
  af_url += "&tkt_virtual="    + af_form.tkt_virtual.checked;
  af_url += "&tkt_sysins="     + af_form.tkt_sysins.checked;
  af_url += "&tkt_sysdns="     + af_form.tkt_sysdns.checked;
  af_url += "&tkt_storage="    + af_form.tkt_storage.checked;
  af_url += "&tkt_syscnf="     + af_form.tkt_syscnf.checked;
  af_url += "&tkt_backups="    + af_form.tkt_backups.checked;
  af_url += "&tkt_monitor="    + af_form.tkt_monitor.checked;
  af_url += "&tkt_appins="     + af_form.tkt_appins.checked;
  af_url += "&tkt_appmon="     + af_form.tkt_appmon.checked;
  af_url += "&tkt_appcnf="     + af_form.tkt_appcnf.checked;
  af_url += "&tkt_infosec="    + af_form.tkt_infosec.checked;
  af_url += "&tkt_sysscan="    + af_form.tkt_sysscan.checked;
<?php
  }
?>

  af_url += "&bu_id="        + af_form.bu_id.value;
  af_url += "&bu_start="     + encode_URI(af_form.bu_start.value);
  af_url += "&bu_include="   + af_form.bu_include.checked;
  af_url += "&bu_retention=" + af_form.bu_retention.value;
  af_url += "&bu_sunday="    + radio_Loop(af_form.bu_sunday, 2);
  af_url += "&bu_monday="    + radio_Loop(af_form.bu_monday, 2);
  af_url += "&bu_tuesday="   + radio_Loop(af_form.bu_tuesday, 2);
  af_url += "&bu_wednesday=" + radio_Loop(af_form.bu_wednesday, 2);
  af_url += "&bu_thursday="  + radio_Loop(af_form.bu_thursday, 2);
  af_url += "&bu_friday="    + radio_Loop(af_form.bu_friday, 2);
  af_url += "&bu_saturday="  + radio_Loop(af_form.bu_saturday, 2);
  af_url += "&bu_suntime="   + encode_URI(af_form.bu_suntime.value);
  af_url += "&bu_montime="   + encode_URI(af_form.bu_montime.value);
  af_url += "&bu_tuetime="   + encode_URI(af_form.bu_tuetime.value);
  af_url += "&bu_wedtime="   + encode_URI(af_form.bu_wedtime.value);
  af_url += "&bu_thutime="   + encode_URI(af_form.bu_thutime.value);
  af_url += "&bu_fritime="   + encode_URI(af_form.bu_fritime.value);
  af_url += "&bu_sattime="   + encode_URI(af_form.bu_sattime.value);

  if (complete === 1) {
    var question  = "This server build request is ready to submit.\n\n";
        question += "Submitting this request will notify the appropriate teams.\n";
        question += "Making changes to the information here after submission will require seperate notification.\n\n";
        question += "Are you sure you're ready to submit this server build request?";
    answer = confirm(question);
  }

  if (answer === true || complete === 0 || complete == 2) {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);

    if (answer === true) {
      alert("New System Submitted");
      window.location.href = '<?php print $RSDProot; ?>/index.php';
    }
    if (complete === 2) {
      alert("Data Saved");
      window.location.href = '<?php print $RSDProot; ?>/index.php';
    }
  }
}

function attach_drive( p_script_url, update ) {
  var ad_form = document.filesystem;
  var ad_url;

  ad_url  = '?update='   + update;
  ad_url += '&id='       + ad_form.fs_id.value;
  ad_url += "&rsdp="     + ad_form.fs_rsdp.value;

  ad_url += "&fs_volume=" + encode_URI(ad_form.fs_volume.value);
  ad_url += "&fs_size="   + encode_URI(ad_form.fs_size.value);
  ad_url += "&fs_backup=" + ad_form.fs_backup.checked;

  script = document.createElement('script');
  script.src = p_script_url + ad_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_project( p_script_url, update ) {
  var af_form = document.dialog;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&prj_name="     + encode_URI(af_form.prj_name.value);
  af_url += "&prj_code="     + encode_URI(af_form.prj_code.value);
  af_url += "&prj_close="    + 'false';
  af_url += "&prj_group="    + <?php print $_SESSION['group']; ?>;
  af_url += "&prj_product="  + af_form.prj_product.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function validate_Form() {
  var vf_submit = 1;
  var vf_form = document.rsdp;

  if (vf_form.rsdp_requestor[0].selected === true) {
    set_Class('rsdp_requestor', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_requestor', 'ui-widget-content');
  }

  if (vf_form.rsdp_product[0].selected === true) {
    set_Class('rsdp_product', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_product', 'ui-widget-content');
  }

  if (vf_form.rsdp_completion.value === "" || vf_form.rsdp_completion.value === '0000-00-00') {
    set_Class('rsdp_completion', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_completion', 'ui-widget-content');
  }

  if (vf_form.rsdp_project[0].selected === true) {
    set_Class('rsdp_project', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_project', 'ui-widget-content');
  }


  if (vf_form.rsdp_platform[0].selected === true) {
    set_Class('rsdp_platform', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_platform', 'ui-widget-content');
  }

  if (vf_form.rsdp_application[0].selected === true) {
    set_Class('rsdp_application', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_application', 'ui-widget-content');
  }

  if (vf_form.rsdp_service[0].selected === true) {
    set_Class('rsdp_service', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_service', 'ui-widget-content');
  }

  if (vf_form.rsdp_vendor[0].selected === true) {
    set_Class('rsdp_vendor', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_vendor', 'ui-widget-content');
  }

  if (vf_form.rsdp_function.value === "") {
    set_Class('rsdp_purpose', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_purpose', 'ui-widget-content');
  }

  if (isNaN(vf_form.rsdp_processors.value)) {
    alert("CPUs must be a number");
    vf_form.rsdp_processors.value = '';
  }

  if (vf_form.rsdp_processors.value === "" || vf_form.value === '0') {
    set_Class('rsdp_processors', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_processors', 'ui-widget-content');
  }

  if (isNaN(vf_form.rsdp_memory.value)) {
    alert("RAM must be a number in Gigabytes");
    vf_form.rsdp_memory.value = '';
  }

  if (vf_form.rsdp_memory.value === "") {
    set_Class('rsdp_memory', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_memory', 'ui-widget-content');
  }

  if (isNaN(vf_form.rsdp_ossize.value)) {
    alert("OS Size must be a number in Gigabytes");
    vf_form.rsdp_ossize.value = '';
  }

  if (vf_form.rsdp_ossize.value === "") {
    set_Class('rsdp_ossize', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_ossize', 'ui-widget-content');
  }

  if (vf_form.rsdp_location[0].selected === true) {
    set_Class('rsdp_location', 'ui-state-error');
    vf_submit = 0;
  } else {
    set_Class('rsdp_location', 'ui-widget-content');
  }

  if (vf_submit) {
    vf_form.addbtn.disabled = false;
  } else {
    vf_form.addbtn.disabled = true;
  }
}

function clear_fields() {
  show_file('<?php print $RSDProot; ?>/build/filesystem.mysql.php' + '?update=-1&rsdp='     + document.filesystem.fs_rsdp.value);
  show_file('<?php print $RSDProot; ?>/admin/comments.mysql.php'   + '?update=-1&com_rsdp=' + document.comments.com_rsdp.value);
  show_file('<?php print $RSDProot; ?>/build/initial.fill.php'     + '?id=-1&rsdp='         + document.rsdp.rsdp.value);
}

$(document).ready( function() {
  $( "#tabs" ).tabs().addClass( "tab-shadow" );

  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'
  });

  $( "#compdate" ).datepicker();
  $( "#backupdate" ).datepicker();

  $( '#clickAddFilesystem' ).click(function() {
    $( "#dialogFilesystem" ).dialog('open');
  });

  $( "#dialogFilesystem" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogFilesystem" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_drive('filesystem.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Filesystem",
        click: function() {
          attach_drive('filesystem.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Filesystem",
        click: function() {
          attach_drive('filesystem.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( '#clickAddProject' ).click(function() {
    $( "#dialogProject" ).dialog('open');
  });

  $( "#dialogProject" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width:  700,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogProject" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_project('project.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Project",
        click: function() {
          attach_project('project.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Project",
        click: function() {
          attach_project('project.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( '#clickDuplicate' ).click(function() {
    $( "#dialogDuplicate" ).dialog('open');
  });

  $( "#dialogDuplicate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 520,
    width:  700,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogDuplicate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Duplicate",
        click: function() {
          attach_duplicate('initial.dup.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

});

</script>

</head>
<body onload="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($RSDPpath . '/topmenu.rsdp.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="rsdp">

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">System Initialization: <?php print $a_rsdp_osteam['os_sysname']; ?></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('system-help');">Help</a></th>
</tr>
</table>

<div id="system-help" style="display: none">

<div class="main-help ui-widget-content">

<h2>System Initialization Page</h2>

<p>This task is performed by the person who needs a new system. The data below provide the basic information needed by the Platforms team to 
build an appropriate system. There are ten tabs which will require information before you can proceed.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Save Changes</strong> - Save any changes to this request. An RSDP id will be created for this request if one doesn't exist yet.</li>
    <li><strong>Save And Exit</strong> - Save any changes to this request and exit back to MyRSDP. An RSDP id will be created for this request if one doesn't exist yet.</li>
    <li><strong>Duplicate Request</strong> - This button is initially disabled because a new request will not have the data saved in the Inventory yet. There's nothing to copy yet.  When you click the 'Save Changes' button, this request will be saved and an RSDP ID created if one doesn't exist yet.  The 'Duplicate Request' button will then be enabled. Clicking the 'Duplicate Request' will exit this request, duplicate the information from the Server Initialization task for this RSDP ID, and the server name and operating system from the Server Provisioning task (adds '-dup' to server name or Unnamed-ID if not named yet), and then load up the new RSDP request.</li>
    <li><strong>Request Completed</strong> - This button stays disabled until all identified tasks have been completed. Once completed, the button is enabled. Clicking it identifies the task as completed. A ticket will be created for the next task and an email will be sent notifying the responsible team or individual that their task is ready to be worked.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content">
<?php
  $q_string  = "select st_completed,st_timestamp,st_user ";
  $q_string .= "from rsdp_status ";
  $q_string .= "where st_step = 1 and st_rsdp = " . $formVars['rsdp'];
  $q_rsdp_status = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_rsdp_status = mysql_fetch_array($q_rsdp_status);

  print "<input type=\"button\" name=\"addnew\"   value=\"Save Changes\"     onClick=\"javascript:attach_file('initial.mysql.php', 0);\">\n";
  print "<input type=\"button\" name=\"addexit\"   value=\"Save and Exit\"    onClick=\"javascript:attach_file('initial.mysql.php', 2);\">\n";
  if ($formVars['rsdp'] > 0) {
    print "<input type=\"button\" name=\"adddup\" id=\"clickDuplicate\" value=\"Duplicate Request\">\n";
  } else {
    print "<input type=\"button\" disabled=\"true\" name=\"adddup\" id=\"clickDuplicate\" value=\"Duplicate Request\">\n";
  }
  print "<input type=\"hidden\" name=\"rsdp\" value=\"" . $formVars['rsdp'] . "\">\n";

  if (mysql_num_rows($q_rsdp_status) == 0 || $a_rsdp_status['st_completed'] == 0) {
    print "<input type=\"button\" disabled name=\"addbtn\" value=\"Request Completed\" onClick=\"javascript:attach_file('initial.mysql.php', 1);\">\n";
  } else {
    print "<input type=\"hidden\" name=\"addbtn\">\n";

    $q_string  = "select usr_last,usr_first ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_rsdp_status['st_user'];
    $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_users = mysql_fetch_array($q_users);

    print "<br><a href=\"" . $RSDProot . "/index.php\">Task was completed by " . $a_users['usr_first'] . " " . $a_users['usr_last'] . " on " . $a_rsdp_status['st_timestamp'] . ".</a>";
  }
?>
</td>
</tr>
</table>

<div id="tabs">

<ul>
  <li><a href="#requestor">Requestor</a></li>
  <li><a href="#project">Project</a></li>
  <li><a href="#tickets">Tickets</a></li>
  <li><a href="#contacts">Contacts</a></li>
  <li><a href="#support">Support</a></li>
  <li><a href="#system">System</a></li>
  <li><a href="#filesystems">Filesystems</a></li>
  <li><a href="#agents">Agents</a></li>
  <li><a href="#backups">Backups</a></li>
  <li><a href="#comments">Comments
<?php
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

<div id="requestor">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Requestor Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('requestor-help');">Help</a></th>
</tr>
</table>

<div id="requestor-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Requestor Form</strong>
  <ul>
    <li><strong>Requestor</strong> - Your information is inserted by default. A Requestor receives copies of all Task emails. This could be the Platforms person building 
the system or the Project Manager so tasks can be tracked. While you're the default, you can change this to assign anyone else as a Requestor. Note: This information is 
saved in the Requestor's profile when the server is saved or submitted as complete.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Requestor Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_requestor">Requestor: <select name="rsdp_requestor" onchange="show_file('user.fill.php?rsdp_requestor=' + rsdp_requestor.value);validate_Form();">
  <option value="0">Unassigned</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first,usr_group ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "  <option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
  </select></td>
  <td class="ui-widget-content" id="usr_phone">Phone Number <input type="phone" name="usr_phone" size="15" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="usr_email">E-Mail <input type="email" name="usr_email" size="30" onchange="validate_Form();"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3" id="usr_deptname">Department: <select name="usr_deptname" onchange="validate_Form();">
  <option value="0">Unassigned</option>
<?php
  $q_string  = "select dep_id,dep_unit,dep_dept,dep_name,bus_name ";
  $q_string .= "from department ";
  $q_string .= "left join business_unit on business_unit.bus_unit = department.dep_unit ";
  $q_string .= "order by bus_name,dep_name";
  $q_department = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_department = mysql_fetch_array($q_department)) {
    print "  <option value=\"" . $a_department['dep_id'] . "\">" . htmlspecialchars($a_department['bus_name']) . " " . htmlspecialchars($a_department['dep_name']) . " (" . htmlspecialchars($a_department['dep_unit']) . "-" . htmlspecialchars($a_department['dep_dept']) . ")</option>\n";
  }
?>
  </select></td>
</tr>
</table>

</div>


<div id="project">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('project-help');">Help</a></th>
</tr>
</table>

<div id="project-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Project Form</strong>
  <ul>
    <li><strong>Intrado Product</strong> - Select the Product this server is being built for. This information is stored in the Inventory and used to associate all 
servers with the necessary Product.</li>
    <li><strong>Project Name</strong> - Select the Project this server is associated with. Use the Project Codes link in the menu to add a new Project. 
Click the <strong>Add New Project</strong> button to add a new Project to the listing.</li>
    <li><strong>Requested Completion Date</strong> - Select the date this server should be going live. This date is copied to the Backup tab as the date to start backups.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Project Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_product">Intrado Product <select name="rsdp_product" onchange="validate_Form();">
  <option value="0">Unassigned</option>
<?php
    $q_string  = "select prod_id,prod_name ";
    $q_string .= "from products ";
    $q_string .= "order by prod_name";
    $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_products = mysql_fetch_array($q_products)) {
      print "  <option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
    }
?>
  </select></td>
  <td class="ui-widget-content" id="rsdp_project">Project Name <select name="rsdp_project" onchange="validate_Form();">
  <option value="0">Unassigned</option>
<?php
  $q_string  = "select rsdp_project ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $formVars['rsdp'];
  $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_rsdp_server = mysql_fetch_array($q_rsdp_server);

  if ($a_rsdp_server['rsdp_project'] == '') {
    $a_rsdp_server['rsdp_project'] = 0;
  } else {
    $q_string  = "select prj_name ";
    $q_string .= "from projects ";
    $q_string .= "where prj_id = " . $a_rsdp_server['rsdp_project'];
    $q_projects = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_projects = mysql_fetch_array($q_projects);

    print "  <option selected value=\"" . $a_rsdp_server['rsdp_project'] . "\">" . htmlspecialchars($a_projects['prj_name']) . "</option>\n";
  }
  $q_string  = "select prj_id,prj_name ";
  $q_string .= "from projects ";
  $q_string .= "where prj_group = " . $_SESSION['group'] . " and prj_id != " . $a_rsdp_server['rsdp_project'] . " and prj_close = 0 ";
  $q_string .= "group by prj_name";
  $q_projects = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_projects = mysql_fetch_array($q_projects)) {
    print "<option value=\"" . $a_projects['prj_id'] . "\">" . htmlspecialchars($a_projects['prj_name']) . "</option>\n";
  }
?>
  </select><input type="button" name="addprj" id="clickAddProject" value="Add New Project"></td>
  <td class="ui-widget-content" id="rsdp_completion">Requested Completion Date: <input type="date" name="rsdp_completion" id="compdate" size="10" value="0000-00-00" onclick="populatestart();validate_Form();"></td>
</tr>
</table>

</div>


<div id="tickets">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Ticket Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('ticket-help');">Help</a></th>
</tr>
</table>

<div id="ticket-help" style="display: none">

<div class="main-help ui-widget-content">

<p>The Ticket tab gives you the option of selecting which task will have a ticket generated for it. By default, the Network 
Engineering team and Data Center team are checked by their request. It's still optional since tasks may be done by groups 
outside of the two groups.</p>

<p>If a task is skipped such as the SAN group if there are no attached SAN filesystems or bypassed such as the Data Center 
group if this is a Virtualization server and vice versa, then a ticket <strong>is not</strong> generated. Tickets 
are only generated for tasks that need work if the checkbox has been checked.</p>

<p>The DNS and InfoSec ticket generations are just for this server. If you want to have a ticket with all the 
servers of this project for either of these tickets, <strong>do not</strong> check the boxes. In the System 
Installation and InfoSec Scan tasks, a Tab is available with all servers for the project that you can copy/paste 
into a ticket manually.</p>

</div>

</div>

<p><span class="ui-state-highlight">With the transition to Remedy as of September 1st 2015, RSDP will not create tickets and the function has been disabled.</span></p>

<p>This includes DNS and InfoSec tickets as well.</p>

<p>An investigation is under way to determine whether this feature will be available in the future.</p>

<p>Sorry for any inconvenience.</p>

<p>Note: Network Engineering and Data Center (for physical systems) still requires a ticket to work their tasks. You'll need to manually create the ticket in Remedy.</p>

<?php
  if ($ticket == 'yes') {
?>
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Ticket Listing<input type="hidden" name="tkt_id" value="0"></th>
</tr>
<tr>
  <td id="tkt_build"><label><input type="checkbox" name="tkt_build">Generate a Ticket for the Platforms group to configure the new server.</label></td>
</tr>
<tr>
  <td id="tkt_san"><label><input type="checkbox" name="tkt_san">Generate a Ticket for the SAN group to configure the switch and port.</label></td>
</tr>
<tr>
  <td id="tkt_network"><label><input type="checkbox" checked name="tkt_network">Generate a Ticket for Network Engineering to provide network information.</label></td>
</tr>
<tr>
  <td id="tkt_datacenter"><label><input type="checkbox" checked name="tkt_datacenter">Generate a Ticket for the Data Center team to retrieve, rack, and cable the Physical Machine.</label></td>
</tr>
<tr>
  <td id="tkt_virtual"><label><input type="checkbox" name="tkt_virtual">Generate a Ticket for the Virtualization group to provision a Virtual Machine.</label></td>
</tr>
<tr>
  <td id="tkt_sysins"><label><input type="checkbox" name="tkt_sysins">Generate a Ticket for the Platforms group to being the installation and configuration of the system.</label></td>
</tr>
<tr>
  <td id="tkt_sysdns"><label><input type="checkbox" name="tkt_sysdns">Generate a DNS Ticket for this system.</label></td>
</tr>
<tr>
  <td id="tkt_storage"><label><input type="checkbox" name="tkt_storage">Generate a Ticket for the SAN group to present the new mounts to the system.</label></td>
</tr>
<tr>
  <td id="tkt_syscnf"><label><input type="checkbox" name="tkt_syscnf">Generate a Ticket for the Platforms group to complete the system installation.</label></td>
</tr>
<tr>
  <td id="tkt_backups"><label><input type="checkbox" name="tkt_backups">Generate a Ticket for the Backup team to configure backups.</label></td>
</tr>
<tr>
  <td id="tkt_monitor"><label><input type="checkbox" name="tkt_monitor">Generate a Ticket for the Monitoring team to configure system level monitoring.</label></td>
</tr>
<tr>
  <td id="tkt_appins"><label><input type="checkbox" name="tkt_appins">Generate a Ticket for the Applications group to install the application.</label></td>
</tr>
<tr>
  <td id="tkt_appmon"><label><input type="checkbox" name="tkt_appmon">Generate a Ticket for the Monitoring Group to work with Applications to ensure application level monitoring.</label></td>
</tr>
<tr>
  <td id="tkt_appcnf"><label><input type="checkbox" name="tkt_appcnf">Generate a Ticket for the Applications group to complete configuration and testing of the application.</label></td>
</tr>
<tr>
  <td id="tkt_infosec"><label><input type="checkbox" name="tkt_infosec">Generate a Ticket to Platforms to complete the system build and resolve InfoSec security scans prior to going live.</label></td>
</tr>
<tr>
  <td id="tkt_sysscan"><label><input type="checkbox" name="tkt_sysscan">Generate a Ticket to InfoSec to scan this system.</label></td>
</tr>
</table>
<?php
  }
?>

</div>


<div id="contacts">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Point of Contact Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('contact-help');">Help</a></th>
</tr>
</table>

<div id="contact-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Team Point of Contact</strong>
  <ul>
    <li><strong>Contacts</strong> - Select the appropriate person who will be doing the work on the various tasks. This person will be receiving the email notifications and 
if selected, and the user has their ID in their profile, the Tickets. If you leave it at the default group, the group will receive the email and Ticket. Note: If you leave 
the Platforms and Applications selections at the default, the groups selected under the Support tab will be the recipient of the email and ticket.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Team Point of Contact</th>
</tr>
<tr>
  <td class="ui-widget-content">Platforms: <select name="rsdp_platformspoc" onchange="update_Platform();">
<option value="0">Platforms Team</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">SAN: <select name="rsdp_sanpoc">
<option value="0">SAN Team</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_SAN . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_SAN . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Network: <select name="rsdp_networkpoc">
<option value="0">Network Engineering</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Networking . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Networking . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Virtualization: <select name="rsdp_virtpoc">
<option value="0">Virtualization Team</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Virtualization . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Virtualization . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Data Center: <select name="rsdp_dcpoc">
<option value="0">Data Center Operations</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_DataCenter . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_DataCenter . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Monitoring: <select name="rsdp_monitorpoc">
<option value="0">Monitoring Team</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Monitoring . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Monitoring . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Applications: <select name="rsdp_apppoc" onchange="update_Application();">
<option value="0">Application Team</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Backup: <select name="rsdp_backuppoc">
<option value="0">Backup Team</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group = " . $GRP_Backups . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }

  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 and usr_id != 1 and usr_group != " . $GRP_Backups . " ";
  $q_string .= "order by usr_last";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . htmlspecialchars($a_users['usr_last']) . ", " . htmlspecialchars($a_users['usr_first']) . "</option>\n";
  }
?>
</select></td>
</table>

</div>


<div id="support">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Support Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('support-help');">Help</a></th>
</tr>
</table>

<div id="support-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>System Support Form</strong>
  <ul>
    <li><strong>Platform Support</strong> - Select the Platform support team. This group will receive email notifications and tickets if requested.</li>
    <li><strong>Application Support</strong> - Select the Application support team. this group will receive email notifications and tickets if requested.</li>
    <li><strong>Service Class</strong> - Select the Service Class. The Service Class table has been generated to provide more information on the levels.</li>
    <li><strong>Vendor Support Level</strong> - Select Vendor Support Level. This is a Hardware level so selecting a level for a Virtual Machine is unnecessary.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">System Support Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_platform">Platform Support: <select name="rsdp_platform" onchange="validate_Form();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" id="rsdp_application">Application Support: <select name="rsdp_application" onchange="validate_Form();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_service">Service Class <select name="rsdp_service" onchange="validate_Form();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select svc_id,svc_name ";
  $q_string .= "from service";
  $q_service = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_service = mysql_fetch_array($q_service)) {
    print "<option value=\"" . $a_service['svc_id'] . "\">" . htmlspecialchars($a_service['svc_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" id="rsdp_vendor" colspan="2">Vendor Support Level <select name="rsdp_vendor" onchange="validate_Form();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from supportlevel ";
  $q_string .= "order by slv_value";
  $q_supportlevel = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_supportlevel = mysql_fetch_array($q_supportlevel)) {
    print "<option value=" . $a_supportlevel['slv_id'] . ">" . htmlspecialchars($a_supportlevel['slv_value']) . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="9">Service Class Definitions</th>
</tr>
<tr>
  <th class="ui-state-default">Name</th>
  <th class="ui-state-default">Acronym</th>
  <th class="ui-state-default">Avail</th>
  <th class="ui-state-default">Downtime</th>
  <th class="ui-state-default">MTBF</th>
  <th class="ui-state-default">Geo</th>
  <th class="ui-state-default">MTTR</th>
  <th class="ui-state-default">Res</th>
  <th class="ui-state-default">Restore</th>
</tr>
<?php
  $q_string  = "select svc_name,svc_acronym,svc_availability,svc_downtime,";
  $q_string .= "svc_mtbf,svc_geographic,svc_mttr,svc_resource,svc_restore ";
  $q_string .= "from service ";
  $q_string .= "order by svc_id ";
  $q_service = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_service) > 0) {
    while ($a_service = mysql_fetch_array($q_service)) {

      $geographic = "No";
      if ($a_service['svc_geographic']) {
        $geographic = "Yes";
      }
      $resource = "No";
      if ($a_service['svc_resource']) {
        $resource = "Yes";
      }

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_name']          . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_acronym']       . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_availability']  . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_downtime']      . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_mtbf']          . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $geographic                     . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_mttr']          . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $resource                       . "</td>\n";
      print "  <td class=\"ui-widget-content\">" . $a_service['svc_restore']       . "</td>\n";
      print "</tr>\n";
    }
  }
?>
</table>

</div>


<div id="system">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">System Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('management-help');">Help</a></th>
</tr>
</table>

<div id="management-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>System Form</strong>
  <ul>
    <li><strong>Function</strong> - This should be a description of the server sufficient so that someone reading this will understand the server's task. Remember that the 
Product and Groups are generally displayed along with Function so IEN Voice Database Server duplicates the Product information.</li>
    <li><strong>Requested Server Location</strong> - Select where this server will be located.</li>
    <li><strong>Is This A Virtual Machine?</strong> - This checkbox automatically populates the CPU, RAM, and Base Disk Size to the Gold Image defaults (1, 2 GB, and 80 GB 
respectively).</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">System Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_purpose" align="left">Function: <input type="text" name="rsdp_function" size="50" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="rsdp_location" colspan="3" align="left">Requested Server Location <select type="text" name="rsdp_location" onchange="validate_Form();">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select loc_id,loc_name,ct_city ";
  $q_string .= "from locations ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "where loc_type = 1 ";
  $q_string .= "order by ct_city,loc_name";
  $q_locations = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_locations = mysql_fetch_array($q_locations)) {
    print "<option value=\"" . $a_locations['loc_id'] . "\">" . htmlspecialchars($a_locations['ct_city']) . " (" . htmlspecialchars($a_locations['loc_name']) . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="virtual" onclick="populatevm();validate_Form();"> Is this a Virtual Machine?</label></td>
  <td class="ui-widget-content" id="rsdp_processors">CPUs: <input type="text" name="rsdp_processors" size="5" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="rsdp_memory">RAM: <input type="text" name="rsdp_memory" size="5" onchange="validate_Form();"> GB</td>
  <td class="ui-widget-content" id="rsdp_ossize">OS Size <input type="text" name="rsdp_ossize" size="5" onchange="validate_Form();"> GB</td>
</tr>
</table>

</div>

<a name="filesystems"></a>

<div id="filesystems">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Filesystem Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('filesystem-help');">Help</a></th>
</tr>
</table>

<div id="filesystem-help" style="display: none">

<div class="main-help ui-widget-content">

<p>The Filesystem Form lets you add additional space to your system. The base system will have a standard layout but you may need additional space for logs, databases, 
or other information. If this is to be a physical system, the SAN team will be notified to allocate the space. For virtual systems, the Virtualization team will add 
the new space. Identify the necessary additional space here.</p>

<ul>
  <li><strong>Mount Point</strong> - Where the new filesystem space will be located.</li>
  <li><strong>Volume Size</strong> - The amount of additional space in Gigabytes.</li>
  <li><strong>Back it up?</strong> - Assuming Backups are enabled, check this box to add this space to the backups.</li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddFilesystem" name="fs_addbtn" value="Add New Drive"></td>
</tr>
</table>

<span id="filesystem_mysql"></span>

</div>


<div id="agents">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Agent Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('agent-help');">Help</a></th>
</tr>
</table>

<div id="agent-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Agent Form</strong>
  <ul>
    <li><strong>Monitoring</strong> - Check whether the OS and/or Application will require monitoring. Checking the Application box will also check the OS box.</li>
    <li><strong>Data Palette</strong> - Check to require Data Palette agent installation.</li>
    <li><strong>OpNet</strong> - Check to require OpNet agent installation.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Agent Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_osmonitor"><label><input type="checkbox" name="rsdp_osmonitor" onclick="checkos();validate_Form();"> Will operating system monitoring be required?</label></td>
  <td class="ui-widget-content" id="rsdp_appmonitor"><label><input type="checkbox" name="rsdp_appmonitor" onclick="checkos();validate_Form();"> Will application monitoring be required?</label></td>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_backup" colspan="2"><label><input type="checkbox" name="rsdp_backup" onclick="validate_Form();"> Check this if backups are using the encryption on the ESX vs an on-system backup agent.</label></td>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_datapalette" colspan="2"><label><input type="checkbox" name="rsdp_datapalette" onclick="validate_Form();"> Will the Data Palette agent be required?</label></td>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_opnet" colspan="2"><label><input type="checkbox" name="rsdp_opnet" onclick="validate_Form();"> Will the OpNet agent be required?</label></td>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_newrelic" colspan="2"><label><input type="checkbox" name="rsdp_newrelic" onclick="validate_Form();"> Will the New Relic agent be required?</label></td>
</tr>
<tr>
  <td class="ui-widget-content" id="rsdp_centrify" colspan="2"><label><input type="checkbox" name="rsdp_centrify" onclick="validate_Form();"> Will accounts be managed through Centrify?</label></td>
</tr>
</table>

</div>


<div id="backups">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Backup Retention Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('backup-help');">Help</a></th>
</tr>
</table>

<div id="backup-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Backup Retention Form</strong>
  <ul>
    <li><strong>Backups</strong> - Review the Backup Start Date. The rest of the settings are at default. Change Retention Length to None to remove the requirement for 
backups.</li>
  </ul></li>
</ul>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Backup Retention Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="bu_start">Backup Start Date <input type="text" id="backupdate" name="bu_start" size="12" onchange="validate_Form();">
<input type="hidden" name="bu_id" value="0" onchange="validate_Form();"></td>
  <td class="ui-widget-content" id="bu_include"><label>Include all filesystems? <input type="checkbox" checked name="bu_include" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content" id="bu_retention">Retention Length <select name="bu_retention" onchange="validate_Form();">
<option selected value="0">None</option>
<option value="1">Less than 6 Months (Details Required)</option>
<option value="2">6 Months</option>
<option value="3">1 Year</option>
<option value="4">3 Years (Standard)</option>
<option value="5">7 Years</option>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3"><strong>Note:</strong> Backups are not able to be taken from systems in the DMZ</td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content" id="rsdp_sunday">Sunday</td>
  <td class="ui-widget-content" id="rsdp_monday">Monday</td>
  <td class="ui-widget-content" id="rsdp_tuesday">Tuesday</td>
  <td class="ui-widget-content" id="rsdp_wednesday">Wednesday</td>
  <td class="ui-widget-content" id="rsdp_thursday">Thursday</td>
  <td class="ui-widget-content" id="rsdp_friday">Friday</td>
  <td class="ui-widget-content" id="rsdp_saturday">Saturday</td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_sunday"           type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_sunday"    checked type="radio" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_monday"           type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_monday"    checked type="radio" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_tuesday"          type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_tuesday"   checked type="radio" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_wednesday"        type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_wednesday" checked type="radio" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_thursday"         type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_thursday"  checked type="radio" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_friday"           type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_friday"    checked type="radio" onchange="validate_Form();"></label></td>
  <td class="ui-widget-content"><label>Full <input value="0" name="bu_saturday" checked type="radio" onchange="validate_Form();"></label> <label>Incr <input value="1" name="bu_saturday"          type="radio" onchange="validate_Form();"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Start: <input type="text" name="bu_suntime" value="00:00" size="4" onchange="validate_Form();"></td>
  <td class="ui-widget-content">Start: <input type="text" name="bu_montime" value="00:00" size="4" onchange="validate_Form();"></td>
  <td class="ui-widget-content">Start: <input type="text" name="bu_tuetime" value="00:00" size="4" onchange="validate_Form();"></td>
  <td class="ui-widget-content">Start: <input type="text" name="bu_wedtime" value="00:00" size="4" onchange="validate_Form();"></td>
  <td class="ui-widget-content">Start: <input type="text" name="bu_thutime" value="00:00" size="4" onchange="validate_Form();"></td>
  <td class="ui-widget-content">Start: <input type="text" name="bu_fritime" value="00:00" size="4" onchange="validate_Form();"></td>
  <td class="ui-widget-content">Start: <input type="text" name="bu_sattime" value="00:00" size="4" onchange="validate_Form();"></td>
</tr>
</table>
</div>


<div id="comments">

<?php include($RSDPpath . '/admin/comments.php'); ?>

</div>

</div>


</div>

</form>


<?php include($RSDPpath . '/admin/comments.dialog.php'); ?>


<div id="dialogFilesystem" title="Filesystem Form">

<form name="filesystem">

<input type="hidden" name="fs_id" value="0">
<input type="hidden" name="fs_rsdp" value="<?php print $formVars['rsdp']; ?>">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Filesystem Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Mount Point <input type="text" name="fs_volume" size="30"></td>
  <td class="ui-widget-content">Volume size <input type="text" name="fs_size" size="10"> GB</td>
  <td class="ui-widget-content"><label>Back it up? <input type="checkbox" checked name="fs_backup"></label></td>
</table>

</form>

</div>


<div id="dialogDuplicate" title="Duplicate Server Form">

<form name="duplicate">

<input type="hidden" name="id" value="0">

<p>Select the information you want to copy from this server into a new server. By default, the Server Initialization and Server Provisioning steps will be copied. Additional information will be copied based on your selections. Note that none of the tasks will be marked as completed.</p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Duplicate Server Form</th>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" checked name="chk_filesystem"> Copy any extra space details noted in the Filesystems tab.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" checked name="chk_ipaddr"> Copy all the network details including IP Addresses, Switch Configurations, etc if any.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_san1"> Duplicate the SAN Design task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_net"> Duplicate the Network Configuration Task. Does not include IP Address or Switch details.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_virt"> Duplicate the Virtualization or Data Center Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_sys1"> Duplicate the System Installation Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_san2"> Duplicate the SAN Provisioning Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_sys2"> Duplicate the System Configuration Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_backup"> Duplicate the System Backups Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_mon1"> Duplicate the Monitoring Configuration Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_app1"> Duplicate the Application Installation Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_mon2"> Duplicate the Monitoring Complete Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_app2"> Duplicate the Application Configured Task.</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="chk_infosec"> Duplicate the InfoSec Completed Task.</label></td>
</tr>
</table>

</form>

</div>


<div id="dialogProject" title="Project Form">

<form name="dialog">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Project Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Project Name: <input type="text" name="prj_name" size="30"></td>
  <td class="ui-widget-content">Project Code: <input type="number" name="prj_code" size="15"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Intrado Product: <select name="prj_product">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name ";
  $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_products = mysql_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
  }
?></select></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
