<?php
# Script: inventory.php
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

  $package = "inventory.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

  if (isset($_GET['server'])) {
    $formVars['server'] = clean($_GET['server'], 10);
  }
  if (isset($_GET['servername'])) {
    $formVars['servername'] = clean($_GET['servername'], 20);
  }

  if (isset($formVars['servername'])) {
    $formVars['server'] = return_ServerID($db, $formVars['servername']);
  }

  if (isset($formVars['server'])) {
    $q_string  = "select inv_id,inv_name,inv_manager,inv_product,inv_project,inv_status,hw_active ";
    $q_string .= "from inventory ";
    $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
    $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
    $q_string .= "where inv_id = " . $formVars['server'] . " and part_type = 1 ";
    $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_inventory = mysqli_fetch_array($q_inventory);

    if (mysqli_num_rows($q_inventory) == 0) {
      $a_inventory['inv_id'] = $formVars['server'];
      $a_inventory['inv_name'] = 'Blank';
      $a_inventory['inv_manager'] = $_SESSION['group'];
      $a_inventory['inv_product'] = 0;
      $a_inventory['inv_project'] = 0;
      $a_inventory['inv_status'] = 0;
    }

  } else {
    $formVars['server'] = 0;
    $a_inventory['inv_id'] = $formVars['server'];
    $a_inventory['inv_name'] = 'Blank';
    $a_inventory['inv_manager'] = $_SESSION['group'];
    $a_inventory['inv_product'] = 0;
    $a_inventory['inv_project'] = 0;
    $a_inventory['inv_status'] = 0;
  }

  $status1 = '';
  $status2 = '';
  $status3 = '';
# if in work/live status is 0
  if ($a_inventory['inv_status'] == 0) {
    if ($a_inventory['hw_active'] == '1971-01-01') {
      $status1 = 'checked';
    } else {
      $status2 = 'checked';
    }
  } else {
    $status3 = 'checked';
  }

  logaccess($db, $_SESSION['uid'], $package, "Editing server: " . $a_inventory['inv_name'] . " (" . $formVars['server'] . ").");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Editing <?php print $a_inventory['inv_name'];?></title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_software( p_script_url ) {
  var answer = confirm("Delete this Software Package?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('software.mysql.php'    + '?update=-1' + '&svr_companyid=<?php    print $formVars['server']; ?>');
  }
}

<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>

function delete_comment( p_script_url ) {
  var answer = confirm("Delete Comment?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('comments.mysql.php'    + '?update=-1' + '&com_companyid=<?php    print $formVars['server']; ?>');
  }
}

function remove_hardware( p_script_url ) {
  var answer;
  var rh_answer;

  rh_answer  = "Remove: Selecting this identifies this piece of hardware as not in use but\n";
  rh_answer += "retains the association with the hardware for historical purposes. Selecting\n";
  rh_answer += "this also sets the retired date to today's date if not already set.\n\n";
  rh_answer += "Has this hardware been removed from the Primary Container?";
  answer = confirm(rh_answer);

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('hardware.mysql.php'    + '?update=-1' + '&hw_companyid=<?php    print $formVars['server']; ?>');
  }
}

function delete_hardware( p_script_url ) {
  var answer;
  var dh_answer;

  dh_answer  = "Delete: This option completely deletes a piece of hardware from the database.\n";
  dh_answer += "You would use this option when cleaning up or if a piece of hardware is not \n";
  dh_answer += "one we want to track.\n\n";
  dh_answer += "Delete this piece of hardware from the database?";
  answer = confirm(dh_answer);

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('hardware.mysql.php'    + '?update=-1' + '&hw_companyid=<?php    print $formVars['server']; ?>');
  }
}

function delete_route( p_script_url ) {
  var answer = confirm("Delete this Route?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('routing.mysql.php'     + '?update=-1' + '&route_companyid=<?php print $formVars['server']; ?>');
  }
}

function delete_association( p_script_url ) {
  var answer = confirm("Delete this Association?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('association.mysql.php' + '?update=-1' + '&clu_companyid=<?php   print $formVars['server']; ?>');
  }
}
<?php
  }
?>




function create_tags(p_script_url, update) {
  var ct_form = document.formTagCreate;
  var ct_url;
  
  ct_url  = '?update='         + update;
  ct_url += "&id="             + ct_form.tag_companyid.value;

  ct_url += "&tag_companyid="  + <?php print $formVars['server']; ?>;
  ct_url += "&tag_name="       + encode_URI(ct_form.tag_name.value);

  script = document.createElement('script');
  script.src = p_script_url + ct_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}



function create_software( p_script_url, update ) {
  var cs_form = document.formSoftwareCreate;
  var cs_url;

  cs_url  = '?update='   + update;

  cs_url += "&svr_companyid="    + <?php print $formVars['server']; ?>;
  cs_url += "&svr_softwareid="   + cs_form.svr_softwareid.value;
  cs_url += "&svr_groupid="      + cs_form.svr_groupid.value;
  cs_url += "&svr_certid="       + cs_form.svr_certid.value;
  cs_url += "&svr_facing="       + cs_form.svr_facing.checked;
  cs_url += "&svr_primary="      + cs_form.svr_primary.checked;
  cs_url += "&svr_locked="       + cs_form.svr_locked.checked;

  script = document.createElement('script');
  script.src = p_script_url + cs_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_software( p_script_url, update ) {
  var us_form = document.formSoftwareUpdate;
  var us_url;

  us_url  = '?update='   + update;
  us_url += '&id='       + us_form.svr_id.value;

  us_url += "&svr_companyid="    + <?php print $formVars['server']; ?>;
  us_url += "&svr_softwareid="   + us_form.svr_softwareid.value;
  us_url += "&svr_groupid="      + us_form.svr_groupid.value;
  us_url += "&svr_certid="       + us_form.svr_certid.value;
  us_url += "&svr_facing="       + us_form.svr_facing.checked;
  us_url += "&svr_primary="      + us_form.svr_primary.checked;
  us_url += "&svr_locked="       + us_form.svr_locked.checked;

  script = document.createElement('script');
  script.src = p_script_url + us_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>

function attach_comment( p_script_url, update ) {
  var ac_form = document.edit;
  var ac_url;

  ac_url  = '?update='   + update;
  ac_url += '&id='       + ac_form.com_id.value;

  ac_url += "&com_companyid="    + <?php print $formVars['server']; ?>;
  ac_url += "&com_text="         + encode_URI(ac_form.com_text.value);
  ac_url += "&com_timestamp="    + encode_URI(ac_form.com_timestamp.value);
  ac_url += "&com_user="         + ac_form.com_user.value;

  script = document.createElement('script');
  script.src = p_script_url + ac_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_detail( p_script_url, update ) {
  var am_form = document.edit;
  var am_url;

  am_url  = '?update='   + update;
  am_url += '&id='       + am_form.id.value;

  am_url += "&inv_name="        + encode_URI(am_form.inv_name.value);
  am_url += "&inv_companyid="   + am_form.inv_companyid.value;
  am_url += "&inv_function="    + encode_URI(am_form.inv_function.value);
  am_url += "&inv_callpath="    + am_form.inv_callpath.checked;
  am_url += "&inv_status="      + radio_Loop(am_form.inv_status, 3);
  am_url += "&inv_document="    + encode_URI(am_form.inv_document.value);
  am_url += "&inv_ssh="         + am_form.inv_ssh.checked;
  am_url += "&inv_location="    + am_form.inv_location.value;
  am_url += "&inv_rack="        + encode_URI(am_form.inv_rack.value);
  am_url += "&inv_row="         + encode_URI(am_form.inv_row.value);
  am_url += "&inv_unit="        + encode_URI(am_form.inv_unit.value);
  am_url += "&inv_zone="        + am_form.inv_zone.value;
  am_url += "&inv_front="       + am_form.inv_front.value;
  am_url += "&inv_rear="        + am_form.inv_rear.value;
  am_url += "&inv_manager="     + am_form.inv_manager.value;
  am_url += "&inv_appadmin="    + am_form.inv_appadmin.value;
  am_url += "&inv_class="       + am_form.inv_class.value;
  am_url += "&inv_response="    + am_form.inv_response.value;
  am_url += "&inv_product="     + am_form.inv_product.value;
  am_url += "&inv_project="     + am_form.inv_project.value;
  am_url += "&inv_department="  + am_form.inv_department.value;
  am_url += "&inv_notes="       + encode_URI(am_form.inv_notes.value);
  am_url += "&inv_ansible="     + am_form.inv_ansible.checked;
  am_url += "&inv_env="         + am_form.inv_env.value;
  am_url += "&inv_appliance="   + am_form.inv_appliance.checked;
  am_url += "&inv_ticket="      + encode_URI(am_form.inv_ticket.value);
  am_url += "&inv_maint="       + am_form.inv_maint.value;

  script = document.createElement('script');
  script.src = p_script_url + am_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function check_hostname() {
  show_file('validate.hostname.php?server=' + document.edit.inv_name.value);
}

function retire_detail( p_script_url, update ) {
  var rd_form = document.edit;
  var rd_url;
  var answer = confirm("This changes the Retired status of the server.\n\nChange the server status?")

  rd_url  = '?update='   + update;
  rd_url += '&id='       + rd_form.id.value;

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url + rd_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function activate_detail( p_script_url, update ) {
  var rd_form = document.edit;
  var rd_url;
  var answer = confirm("This changes the Active status of this server.\n\nChange the server status?")

  rd_url  = '?update='   + update;
  rd_url += '&id='       + rd_form.id.value;

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url + rd_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_hardware( p_script_url, update ) {
  var ah_form = document.edit;
  var ah_url;
  var answer = 0;

  ah_url  = '?update='   + update;
  ah_url += '&id='       + ah_form.hw_id.value;
  ah_url += '&copyfrom=' + ah_form.hw_copyfrom.value;

  ah_url += "&hw_companyid="  + <?php print $formVars['server']; ?>;
  ah_url += "&hw_hw_id="      + ah_form.hw_hw_id.value;
  ah_url += "&hw_hd_id="      + ah_form.hw_hd_id.value;
  ah_url += "&hw_serial="     + encode_URI(ah_form.hw_serial.value);
  ah_url += "&hw_asset="      + encode_URI(ah_form.hw_asset.value);
  ah_url += "&hw_group="      + <?php print $a_inventory['inv_manager']; ?>;
  ah_url += "&hw_product="    + <?php print $a_inventory['inv_product']; ?>;
  ah_url += "&hw_vendorid="   + ah_form.hw_vendorid.value;
  ah_url += "&hw_type="       + ah_form.hw_type.value;
  ah_url += "&hw_purchased="  + encode_URI(ah_form.hw_purchased.value);
  ah_url += "&hw_built="      + encode_URI(ah_form.hw_built.value);
  ah_url += "&hw_active="     + encode_URI(ah_form.hw_active.value);
  ah_url += "&hw_eolticket="  + encode_URI(ah_form.hw_eolticket.value);
  ah_url += "&hw_retired="    + encode_URI(ah_form.hw_retired.value);
  ah_url += "&hw_reused="     + encode_URI(ah_form.hw_reused.value);
  ah_url += "&hw_supportid="  + ah_form.hw_supportid.value;
  ah_url += "&hw_response="   + ah_form.hw_response.value;
  ah_url += "&hw_deleted="    + ah_form.hw_deleted.checked;
  ah_url += "&hw_rma="        + encode_URI(ah_form.hw_rma.value);
  ah_url += "&hw_note="       + encode_URI(ah_form.hw_note.value);

  script = document.createElement('script');
  script.src = p_script_url + ah_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_hardwaretype( p_script_url ) {
  script = document.createElement('script');
  script.src = p_script_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}





function delete_filesystem( p_script_url ) {
  var answer = confirm("Delete this Filesystem?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('filesystem.mysql.php'  + '?update=-1' + '&fs_companyid=<?php    print $formVars['server']; ?>');
  }
}

function create_filesystem( p_script_url, update ) {
  var cf_form = document.formFilesystemCreate;
  var cf_url;

  cf_url  = '?update='   + update;

  cf_url += "&fs_companyid=" + <?php print $formVars['server']; ?>;
  cf_url += "&fs_backup="    + cf_form.fs_backup.checked;
  cf_url += "&fs_device="    + encode_URI(cf_form.fs_device.value);
  cf_url += "&fs_mount="     + encode_URI(cf_form.fs_mount.value);
  cf_url += "&fs_group="     + cf_form.fs_group.value;
  cf_url += "&fs_size="      + encode_URI(cf_form.fs_size.value);
  cf_url += "&fs_wwid="      + encode_URI(cf_form.fs_wwid.value);
  cf_url += "&fs_subsystem=" + encode_URI(cf_form.fs_subsystem.value);
  cf_url += "&fs_volume="    + encode_URI(cf_form.fs_volume.value);
  cf_url += "&fs_lun="       + encode_URI(cf_form.fs_lun.value);
  cf_url += "&fs_volid="     + encode_URI(cf_form.fs_volid.value);
  cf_url += "&fs_path="      + encode_URI(cf_form.fs_path.value);
  cf_url += "&fs_switch="    + encode_URI(cf_form.fs_switch.value);
  cf_url += "&fs_port="      + encode_URI(cf_form.fs_port.value);
  cf_url += "&fs_sysport="   + encode_URI(cf_form.fs_sysport.value);

  script = document.createElement('script');
  script.src = p_script_url + cf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_filesystem( p_script_url, update ) {
  var uf_form = document.formFilesystemUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += "&fs_id="    + uf_form.fs_id.value;

  uf_url += "&fs_companyid=" + <?php print $formVars['server']; ?>;
  uf_url += "&fs_backup="    + uf_form.fs_backup.checked;
  uf_url += "&fs_device="    + encode_URI(uf_form.fs_device.value);
  uf_url += "&fs_mount="     + encode_URI(uf_form.fs_mount.value);
  uf_url += "&fs_group="     + uf_form.fs_group.value;
  uf_url += "&fs_size="      + encode_URI(uf_form.fs_size.value);
  uf_url += "&fs_wwid="      + encode_URI(uf_form.fs_wwid.value);
  uf_url += "&fs_subsystem=" + encode_URI(uf_form.fs_subsystem.value);
  uf_url += "&fs_volume="    + encode_URI(uf_form.fs_volume.value);
  uf_url += "&fs_lun="       + encode_URI(uf_form.fs_lun.value);
  uf_url += "&fs_volid="     + encode_URI(uf_form.fs_volid.value);
  uf_url += "&fs_path="      + encode_URI(uf_form.fs_path.value);
  uf_url += "&fs_switch="    + encode_URI(uf_form.fs_switch.value);
  uf_url += "&fs_port="      + encode_URI(uf_form.fs_port.value);
  uf_url += "&fs_sysport="   + encode_URI(uf_form.fs_sysport.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}






function attach_inventory( p_script_url, update ) {
  var ai_form = document.edit;
  var ai_url;

  ai_url  = '?update='   + update;
  ai_url += '&id='       + <?php print $formVars['server']; ?>;
  ai_url += '&copyfrom=' + ai_form.inv_copyfrom.value;

  script = document.createElement('script');
  script.src = p_script_url + ai_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}







function delete_interface( p_script_url ) {
  var answer = confirm("Delete this Interface?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('interface.mysql.php'   + '?update=-1' + '&int_companyid=<?php   print $formVars['server']; ?>');
  }
}

function create_interface( p_script_url, update ) {
  var ci_form = document.formInterfaceCreate;
  var ci_url;

  ci_url  = '?update='   + update;

  ci_url += "&int_companyid="    + <?php print $formVars['server']; ?>;
  ci_url += "&int_ipaddressid="  + ci_form.int_ipaddressid.value;
  ci_url += "&int_face="         + encode_URI(ci_form.int_face.value);
  ci_url += "&int_int_id="       + ci_form.int_int_id.value;
  ci_url += "&int_virtual="      + ci_form.int_virtual.checked;
  ci_url += "&int_eth="          + encode_URI(ci_form.int_eth.value);
  ci_url += "&int_note="         + encode_URI(ci_form.int_note.value);
  ci_url += "&int_switch="       + encode_URI(ci_form.int_switch.value);
  ci_url += "&int_port="         + encode_URI(ci_form.int_port.value);
  ci_url += "&int_sysport="      + encode_URI(ci_form.int_sysport.value);
  ci_url += "&int_primary="      + ci_form.int_primary.checked;
  ci_url += "&int_type="         + ci_form.int_type.value;
  ci_url += "&int_media="        + ci_form.int_media.value;
  ci_url += "&int_speed="        + ci_form.int_speed.value;
  ci_url += "&int_duplex="       + ci_form.int_duplex.value;
  ci_url += "&int_redundancy="   + ci_form.int_redundancy.value;
  ci_url += "&int_groupname="    + encode_URI(ci_form.int_groupname.value);
  ci_url += "&int_backup="       + ci_form.int_backup.checked;
  ci_url += "&int_management="   + ci_form.int_management.checked;
  ci_url += "&int_login="        + ci_form.int_login.checked;

  script = document.createElement('script');
  script.src = p_script_url + ci_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_interface( p_script_url, update ) {
  var ui_form = document.formInterfaceUpdate;
  var ui_url;

  ui_url  = '?update='       + update;
  ui_url += '&int_id='       + ui_form.int_id.value;

  ui_url += "&int_companyid="    + <?php print $formVars['server']; ?>;
  ui_url += "&int_ipaddressid="  + ui_form.int_ipaddressid.value;
  ui_url += "&int_face="         + encode_URI(ui_form.int_face.value);
  ui_url += "&int_int_id="       + ui_form.int_int_id.value;
  ui_url += "&int_virtual="      + ui_form.int_virtual.checked;
  ui_url += "&int_eth="          + encode_URI(ui_form.int_eth.value);
  ui_url += "&int_note="         + encode_URI(ui_form.int_note.value);
  ui_url += "&int_switch="       + encode_URI(ui_form.int_switch.value);
  ui_url += "&int_port="         + encode_URI(ui_form.int_port.value);
  ui_url += "&int_sysport="      + encode_URI(ui_form.int_sysport.value);
  ui_url += "&int_primary="      + ui_form.int_primary.checked;
  ui_url += "&int_type="         + ui_form.int_type.value;
  ui_url += "&int_media="        + ui_form.int_media.value;
  ui_url += "&int_speed="        + ui_form.int_speed.value;
  ui_url += "&int_duplex="       + ui_form.int_duplex.value;
  ui_url += "&int_redundancy="   + ui_form.int_redundancy.value;
  ui_url += "&int_groupname="    + encode_URI(ui_form.int_groupname.value);
  ui_url += "&int_backup="       + ui_form.int_backup.checked;
  ui_url += "&int_management="   + ui_form.int_management.checked;
  ui_url += "&int_login="        + ui_form.int_login.checked;

  if (ui_form.int_id.value != 0 && ui_form.int_id.value == ui_form.int_int_id.value) {
    alert("You cannot be a child of yourself.");
  } else {
    script = document.createElement('script');
    script.src = p_script_url + ui_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}







function attach_user(p_script_url, update) {
  var au_form = document.edit;
  var au_url;
  
  au_url  = '?update='   + update;
  au_url += "&id="       + au_form.mu_id.value;

  au_url += "&pwd_companyid=" + <?php print $formVars['server']; ?>;
  au_url += "&mu_username="   + encode_URI(au_form.mu_username.value);
  au_url += "&mu_name="       + encode_URI(au_form.mu_name.value);
  au_url += "&mu_email="      + encode_URI(au_form.mu_email.value);
  au_url += "&mu_account="    + radio_Loop(au_form.mu_account, 3);
  au_url += "&mu_comment="    + encode_URI(au_form.mu_comment.value);
  au_url += "&mu_locked="     + au_form.mu_locked.checked;
  au_url += "&mu_ticket="     + encode_URI(au_form.mu_ticket.value);

  script = document.createElement('script');
  script.src = p_script_url + au_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_route(p_script_url, update) {
  var ar_form = document.edit;
  var ar_url;
  var ar_answer = "Propagate this Description to all similar routes?";
  var answer;
  
  ar_url  = '?update='   + update;
  ar_url += "&id="       + ar_form.route_id.value;
  ar_url += "&copyfrom=" + ar_form.route_copyfrom.value;

  ar_url += "&route_companyid=" + <?php print $formVars['server']; ?>;
  ar_url += "&route_address="   + encode_URI(ar_form.route_address.value);
  ar_url += "&route_gateway="   + encode_URI(ar_form.route_gateway.value);
  ar_url += "&route_mask="      + ar_form.route_mask.value;
  ar_url += "&route_source="    + encode_URI(ar_form.route_source.value);
  ar_url += "&route_interface=" + ar_form.route_interface.value;
  ar_url += "&route_static="    + ar_form.route_static.checked;
  ar_url += "&route_desc="      + encode_URI(ar_form.route_desc.value);

  answer = confirm(ar_answer);

  if (answer) {
    ar_url += "&route_propagate=yes";
  }

  script = document.createElement('script');
  script.src = p_script_url + ar_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_backups( p_script_url, update ) {
  var ab_form = document.edit;
  var ab_url;

  ab_url  = '?update='   + update;
  ab_url += '&id='       + ab_form.bu_id.value;
  ab_url += '&copyfrom=' + ab_form.bu_copyfrom.value;

  ab_url += "&bu_companyid="   + <?php print $formVars['server']; ?>;
  ab_url += "&bu_start="       + encode_URI(ab_form.bu_start.value);
  ab_url += "&bu_include="     + ab_form.bu_include.checked;
  ab_url += "&bu_retention="   + ab_form.bu_retention.value;
  ab_url += "&bu_sunday="      + radio_Loop(ab_form.bu_sunday, 2);
  ab_url += "&bu_monday="      + radio_Loop(ab_form.bu_monday, 2);
  ab_url += "&bu_tuesday="     + radio_Loop(ab_form.bu_tuesday, 2);
  ab_url += "&bu_wednesday="   + radio_Loop(ab_form.bu_wednesday, 2);
  ab_url += "&bu_thursday="    + radio_Loop(ab_form.bu_thursday, 2);
  ab_url += "&bu_friday="      + radio_Loop(ab_form.bu_friday, 2);
  ab_url += "&bu_saturday="    + radio_Loop(ab_form.bu_saturday, 2);
  ab_url += "&bu_suntime="     + encode_URI(ab_form.bu_suntime.value);
  ab_url += "&bu_montime="     + encode_URI(ab_form.bu_montime.value);
  ab_url += "&bu_tuetime="     + encode_URI(ab_form.bu_tuetime.value);
  ab_url += "&bu_wedtime="     + encode_URI(ab_form.bu_wedtime.value);
  ab_url += "&bu_thutime="     + encode_URI(ab_form.bu_thutime.value);
  ab_url += "&bu_fritime="     + encode_URI(ab_form.bu_fritime.value);
  ab_url += "&bu_sattime="     + encode_URI(ab_form.bu_sattime.value);
  ab_url += "&bu_notes="       + encode_URI(ab_form.bu_notes.value);

  script = document.createElement('script');
  script.src = p_script_url + ab_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_association( p_script_url, update ) {
  var aa_form = document.edit;
  var aa_url;

  aa_url  = '?update='   + update;
  aa_url += '&id='       + aa_form.clu_id.value;
  aa_url += '&copyfrom=' + aa_form.clu_copyfrom.value;

  aa_url += "&clu_companyid="   + <?php print $formVars['server']; ?>;
  aa_url += "&clu_association=" + aa_form.clu_association.value;
  aa_url += "&clu_notes="       + encode_URI(aa_form.clu_notes.value);

  script = document.createElement('script');
  script.src = p_script_url + aa_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}


function reset_detail() {
  document.edit.com_text.value = '';
  document.edit.remLen.value = 1800;
  document.edit.com_user[0].selected = true;
  document.edit.com_timestamp.value = 'Current Time';
  document.edit.comupdate.disabled = true;
  document.edit.com_id.value = 0;
  document.edit.format_bold.value = 0;
  document.edit.format_italic.value = 0;
  document.edit.format_underline.value = 0;
  document.edit.format_preserve.value = 0;
<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
  document.getElementById('show_bold').innerHTML = 'Bold';
  document.getElementById('show_italic').innerHTML = 'Italic';
  document.getElementById('show_underline').innerHTML = 'Underline';
  document.getElementById('show_preserve').innerHTML = 'Preserve Formatting';
<?php
  }
?>
}

function textCounter(field,cntfield,maxlimit) {
  if (field.value.length > maxlimit)
    field.value = field.value.substring(0, maxlimit);
  else
    cntfield.value = maxlimit - field.value.length;
}

<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
// the purpose here is to permit the insertion/replacement of formatted text
function formatText(p_format) {
  var ft_form = document.edit;
  var ft_text = ft_form.com_text.value;

  ft_form.com_text.focus();
  var ft_cursor = getInputSelection(ft_form.com_text);

  var ft_st_start  = ft_text.substring(0, ft_cursor.start);
  var ft_st_middle = ft_text.substring(ft_cursor.start, ft_cursor.end);
  var ft_st_end    = ft_text.substring(ft_cursor.end);

  if (p_format == "bold") {

    if (ft_form.format_bold.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_bold').value = 'BOLD';
        ft_form.format_bold.value = 1;
        ft_com_text = ft_st_start + "<b>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_com_text = ft_st_start + "<b>" + ft_st_middle + "</b>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_bold').value = 'Bold';
        ft_form.format_bold.value = 0;
        ft_com_text = ft_st_start + "</b>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_com_text = ft_st_start + "</b>" + ft_st_middle + "<b>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "italic") {

    if (ft_form.format_italic.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_italic').value = 'ITALIC';
        ft_form.format_italic.value = 1;
        ft_com_text = ft_st_start + "<i>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_com_text = ft_st_start + "<i>" + ft_st_middle + "</i>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_italic').value = 'Italic';
        ft_form.format_italic.value = 0;
        ft_com_text = ft_st_start + "</i>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_com_text = ft_st_start + "</i>" + ft_st_middle + "<i>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "underline") {

    if (ft_form.format_underline.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_underline').value = 'UNDERLINE';
        ft_form.format_underline.value = 1;
        ft_com_text = ft_st_start + "<u>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_com_text = ft_st_start + "<u>" + ft_st_middle + "</u>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_underline').value = 'Underline';
        ft_form.format_underline.value = 0;
        ft_com_text = ft_st_start + "</u>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_com_text = ft_st_start + "</u>" + ft_st_middle + "<u>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "preserve") {

    if (ft_form.format_preserve.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_preserve').value = 'PRESERVE FORMATTING';
        ft_form.format_preserve.value = 1;
        ft_com_text = ft_st_start + "<pre>" + ft_st_end;
        ft_cursor.end += 5;
      } else {
        ft_com_text = ft_st_start + "<pre>" + ft_st_middle + "</pre>" + ft_st_end;
        ft_cursor.end += 11;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_preserve').value = 'Preserve Formatting';
        ft_form.format_preserve.value = 0;
        ft_com_text = ft_st_start + "</pre>" + ft_st_end;
        ft_cursor.end += 6;
      } else {
        ft_com_text = ft_st_start + "</pre>" + ft_st_middle + "<pre>" + ft_st_end;
        ft_cursor.end += 11;
      }
    }

  }

  ft_form.com_text.value = ft_com_text;
  setCaretPosition('com_text', ft_cursor.end);
}

function getInputSelection(el) {
  var start = 0, end = 0, normalizedValue, range, textInputRange, len, endRange;

  if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
    start = el.selectionStart;
    end = el.selectionEnd;
  } else {
    range = document.selection.createRange();

    if (range && range.parentElement() == el) {
      len = el.value.length;
      normalizedValue = el.value.replace(/\r\n/g, "\n");

      // Create a working TextRange that lives only in the input
      textInputRange = el.createTextRange();
      textInputRange.moveToBookmark(range.getBookmark());

      // Check if the start and end of the selection are at the very end
      // of the input, since moveStart/moveEnd doesn't return what we want
      // in those cases
      endRange = el.createTextRange();
      endRange.collapse(false);

      if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
        start = end = len;
      } else {
        start = -textInputRange.moveStart("character", -len);
        start += normalizedValue.slice(0, start).split("\n").length - 1;

        if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
          end = len;
        } else {
          end = -textInputRange.moveEnd("character", -len);
          end += normalizedValue.slice(0, end).split("\n").length - 1;
        }
      }
    }
  }

  return {
    start: start,
    end: end
  };
}

function setCaretPosition(elemId, caretPos) {
  var elem = document.getElementById(elemId);

  if (elem != null) {
    if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.move('character', caretPos);
      range.select();
    } else {
      if (elem.selectionStart) {
        elem.focus();
        elem.setSelectionRange(caretPos, caretPos);
      } else {
        elem.focus();
      }
    }
  }
}
<?php
  }
?>


<?php
  }
?>

function clear_fields() {

  show_file('inventory.fill.php'    + '?id=<?php print $formVars['server']; ?>');
<?php
# if a blank server, don't populate the tabs
  if ($a_inventory['inv_name'] != 'Blank') {
?>
  show_file('tags.mysql.php'        + '?update=-3' + '&tag_companyid=<?php print $formVars['server']; ?>');
  show_file('software.mysql.php'    + '?update=-3' + '&svr_companyid=<?php print $formVars['server']; ?>');
<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
  show_file('maintenance.mysql.php' + '?update=-3' + '&id=<?php              print $formVars['server']; ?>');
  show_file('hardware.mysql.php'    + '?update=-3' + '&hw_companyid=<?php    print $formVars['server']; ?>');
  show_file('filesystem.mysql.php'  + '?update=-1' + '&fs_companyid=<?php    print $formVars['server']; ?>');
  show_file('interface.mysql.php'   + '?update=-1' + '&int_companyid=<?php   print $formVars['server']; ?>');
  show_file('users.mysql.php'       + '?update=-3' + '&pwd_companyid=<?php   print $formVars['server']; ?>');
  show_file('routing.mysql.php'     + '?update=-3' + '&route_companyid=<?php print $formVars['server']; ?>');
  show_file('backups.fill.php'      + '?id=<?php                             print $formVars['server']; ?>');
  show_file('association.mysql.php' + '?update=-3' + '&clu_companyid=<?php   print $formVars['server']; ?>');
  show_file('comments.mysql.php'    + '?update=-3' + '&com_companyid=<?php   print $formVars['server']; ?>');
<?php
# end inv_manager if
  }
?>
<?php
# end blank server if
  }
?>
}


$(document).ready( function() {
  $( "#tabs" ).tabs( );
  $( "#sstatus" ).buttonset();


  $( '#clickTagCreate' ).click(function() {
    $( "#dialogTagCreate" ).dialog('open');
  });

  $( "#dialogTagCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 150,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogTagCreate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('tags.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Server Tag",
        click: function() {
          create_tags('tags.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });



  $( '#clickFilesystemCreate' ).click(function() {
    $( "#dialogFilesystemCreate" ).dialog('open');
  });

  $( "#dialogFilesystemCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 475,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogFilesystemCreate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('filesystem.mysql.php?update=-1&fs_companyid=<?php print $formVars['server']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Filesystem",
        click: function() {
          create_filesystem('filesystem.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogFilesystemUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 475,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogFilesystemUpdate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('filesystem.mysql.php?update=-1&fs_companyid=<?php print $formVars['server']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Filesystem",
        click: function() {
          update_filesystem('filesystem.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Filesystem",
        click: function() {
          update_filesystem('filesystem.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });


  $( '#clickInterfaceCreate' ).click(function() {
    $( "#dialogInterfaceCreate" ).dialog('open');
  });

  $( "#dialogInterfaceCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 600,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogInterfaceCreate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('interface.mysql.php?update=-1&int_companyid=<?php print $formVars['server']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Interface",
        click: function() {
          create_interface('interface.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogInterfaceUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 600,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogInterfaceUpdate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('interface.mysql.php?update=-1&int_companyid=<?php print $formVars['server']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Interface",
        click: function() {
          update_interface('interface.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Interface",
        click: function() {
          update_interface('interface.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });






  $( '#clickSoftwareCreate' ).click(function() {
    $( "#dialogSoftwareCreate" ).dialog('open');
  });

  $( "#dialogSoftwareCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogSoftwareCreate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('software.mysql.php?update=-1&svr_companyid=<?php print $formVars['server']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Software",
        click: function() {
          create_software('software.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogSoftwareUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 275,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogSoftwareUpdate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('software.mysql.php?update=-1&svr_companyid=<?php print $formVars['server']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Software",
        click: function() {
          update_software('software.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Software",
        click: function() {
          update_software('software.mysql.php', 0);
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

<form name="edit">

<input type="hidden" name="id" value="<?php print $formVars['server']; ?>">

<div class="main">

<div id="tabs">

<ul>
<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
  <li><a href="#detail"><?php print $a_inventory['inv_name']; ?> Detail</a></li>
  <li><a href="#tags">Tags</a></li>
<?php
  } else {
?>
  <li><a href="#tags"><?php print $a_inventory['inv_name']; ?> Tags</a></li>
<?php
  }
?>
<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
  <li><a href="#maintenance">Maintenance</a></li>
  <li><a href="#hardware">Hardware</a></li>
  <li><a href="#filesystem">Filesystem</a></li>
<?php
  }
?>
  <li><a href="#software">Software</a></li>
<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
  <li><a href="#interface">Interfaces</a></li>
  <li><a href="#users">Users</a></li>
  <li><a href="#routing">Routing</a></li>
  <li><a href="#backup">Backup</a></li>
  <li><a href="#association">Association</a></li>
  <li><a href="#comments">Comments</a></li>
<?php
  }
?>
</ul>

<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
<div id="detail">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Detail Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('detail-help');">Help</a></th>
</tr>
</table>

<div id="detail-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Platform Record</strong> - Save any changes made to this form.</li>
    <li><strong>Add New Platform Record</strong> - If you need to add a new platform, change the Platform Name field then click this button to add the device. This button is only active if there are no devices using the same Server Name.</li>
  </ul></li>
  <li><strong>Radio Selection</strong> - This area lets you select the current status of the system. Clicking on one of the options below and updating the system will change the status as noted. These three options change the date fields for the primary hardware device for this system, either resetting to '1971-01-01' or setting to today's date. For manual editing of the dates, see the Hardware tab.
  <ul>
    <li><strong>Being Built</strong> - Selecting this will identify the server as Being Built. This is how the server comes in from RSDP.</li>
    <li><strong>In Use</strong> - Selecting this will identify the server as completed and being used. If changing from Retired to In Use, the Unix Service account flag will remain unchecked.</li>
    <li><strong>Retired</strong> - Selecting this will identify the server as being Retired. It removes it from the active server listing and if a Unix system accessed by the Unix Service Account, unchecks the Unix Service account flag.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Server Form</strong>
  <ul>
    <li><strong>Server Name</strong> - This is the management interface name assigned to this system. Changing this field will activate the <strong>Add New Server Record</strong> button where you can create a new record in the inventory.</li>
    <li><strong>Description</strong> - Define the server function here. Oracle Server, JBoss Server, etc. This is used by the Incident team to quickly understand the function of an affected server.</li>
    <li><strong>911 Call Path</strong> - Is this server in the 911 Call Path?</li>
    <li><strong>Link to Documentation</strong> - Put a Wiki or Sharepoint link to server documentation. This shows up in the Inventory listing as a link in the Description column.</li>
    <li><strong>Blade Chassis</strong> - If this system is in a Blade Chassis, select the chassis. In the Location Form, enter the slot in the Unit field but leave Row and Rack blank.</li>
    <li><strong>System Notes</strong> - Quick notes about the system. The Documentation Link should provide sufficient details.</li>
  </ul></li>
  <li><strong>Location Form</strong>
  <ul>
    <li><strong>Location</strong> - Select the server location. Even Virtual Machines are located on the ESX hosts in a specific data center.</li>
    <li><strong>Row/Rack/Unit</strong> - Enter the row, rack and unit number for the system. The height of a server is noted in the Models table.</li>
    <li><strong>Time Zone</strong> - Select the time zone the server has been configured for.</li>
    <li><strong>Photos</strong> - Select the front and rear photos for the server. This helps when using Remote Hands to work on a server.</li>
  </ul></li>
  <li><strong>Support Form</strong>
  <ul>
    <li><strong>Platform Manager</strong> - This is the primary owner of this platform and controls the other configuration tabs. <strong>Note:</strong> If the Platform Manager is changed, all Hardware and Software owned by this group will change ownership as well.</li>
    <li><strong>Application Manager</strong> - This is the primary owner of the software running on this system.</li>
    <li><strong>Service Class</strong> - Select the correct Service Class for this server.</li>
    <li><strong>Suggested Response Level</strong> - Platform response level used by Contracts.</li>
  </ul></li>
  <li><strong>Maintenance Window Form</strong>
  <ul>
    <li><strong>Start/End</strong> - Define the hours the device can be brought off line for maintenance, patches, etc.</li>
    <li><strong>Day of Week</strong> - Select the day of the week the device be off line.</li>
    <li><strong>Interval</strong> - Select the interval where the device can be off line.</li>
  </ul></li>
  <li><strong>Patching Form</strong>
  <ul>
    <li><strong>Select a Patching Increment</strong> - The team will receive notification on each Monday prior to systems being identified for patching.</li>
    <li><strong>Enter the Date the system was last patched</strong> - This identified the next time the system should be patched. It'll be the selected Patching Increment next up from this date.</li>
  </ul></li>
  <li><strong>Product Form</strong>
  <ul>
    <li><strong>Product</strong> - The product this device falls under.</li>
    <li><strong>Business Unit (Department)</strong> - The group that requested this platform.</li>
  </ul></li>
  <li><strong>Platform Specific Form</strong>
  <ul>
    <li><strong>Centrify</strong> - These fields identify the server as having user accounts managed under Active Directory.</li>
    <li><strong>Accessible via SSH for unixsvc?</strong> - This server is managed by the Unix Team's service account for data gathering and management.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Fields marked with an asterisk (*) are automatically captured where possible.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content">
<input type="button" disabled="true" name="update" value="Update Platform Record"  onClick="javascript:attach_detail('inventory.mysql.php', 1);">
<input type="button"                 name="addnew" value="Add New Platform Record" onClick="javascript:attach_detail('inventory.mysql.php', 0);">
  </td>
</tr>
<tr>
  <td class="button ui-widget-content">
<input type="button" name="inv_copyitem" value="Copy Details From:" onClick="javascript:attach_inventory('inventory.mysql.php', -2);">
<select name="inv_copyfrom">
<option value="0">None</option>
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
  $q_string .= "order by inv_name";
  $q_c2inv = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_c2inv = mysqli_fetch_array($q_c2inv)) {
    print "<option value=\"" . $a_c2inv['inv_id'] . "\">" . $a_c2inv['inv_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="button ui-widget-content">
<div id="sstatus">
<input type="radio" id="radio1" name="inv_status" <?php print $status1; ?> value="0"><label for="radio1">Being Built</label>
<input type="radio" id="radio2" name="inv_status" <?php print $status2; ?> value="1"><label for="radio2">In Use</label>
<input type="radio" id="radio3" name="inv_status" <?php print $status3; ?> value="2"><label for="radio3">Retired</label>
</div>
</td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="8">Server Form</th>
</tr>
<tr>
  <td class="ui-widget-content" id="edit_hn">Server Name <input type="text" name="inv_name" size="20" onkeyup="check_hostname();"><span id="gohere"></span></td>
  <td class="ui-widget-content" colspan="5">Description <input type="text" name="inv_function" size="60"></td>
  <td class="ui-widget-content" ><label>911 Call Path? <input type="checkbox" name="inv_callpath"></label></td>
  <td class="ui-widget-content" ><label>Decommission Ticket Number: <input type="text" name="inv_ticket" size="20"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"  colspan="3">Link to Documentation: <input type="text" name="inv_document" size="80"></td>
  <td class="ui-widget-content">Server is an Appliance? <input type="checkbox" name="inv_appliance"></td>
  <td class="ui-widget-content"  colspan="3">Parent Device: <select name="inv_companyid">
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where mod_type = 13 and inv_manager = " . $_SESSION['group'] . " ";
  $q_string .= "order by inv_name ";
  $q_chassis = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_chassis = mysqli_fetch_array($q_chassis)) {
    print "<option value=\"" . $a_chassis['inv_id'] . "\">" . $a_chassis['inv_name'] . "</option>";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"  colspan="8">System Notes <input type="text" name="inv_notes" size="80"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="6">Location Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Location <select type="text" name="inv_location">
<?php
  $q_string  = "select loc_id,loc_name,ct_city ";
  $q_string .= "from locations ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "order by ct_city,loc_name";
  $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_locations = mysqli_fetch_array($q_locations)) {
    print "<option value=\"" . $a_locations['loc_id'] . "\">" . htmlspecialchars($a_locations['ct_city']) . " (" . htmlspecialchars($a_locations['loc_name']) . ")\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Environment <select name="inv_env">
<?php
  $q_string  = "select env_id,env_name ";
  $q_string .= "from environment ";
  $q_string .= "order by env_name ";
  $q_environment = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_environment = mysqli_fetch_array($q_environment)) {
    print "<option value=\"" . $a_environment['env_id'] . "\">" . htmlspecialchars($a_environment['env_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Row <input type="text" name="inv_row" size="10"></td>
  <td class="ui-widget-content">Rack <input type="text" name="inv_rack" size="10"></td>
  <td class="ui-widget-content">Unit U<input type="text" name="inv_unit" size="5"></td>
  <td class="ui-widget-content">Time Zone* <select name="inv_zone">
<?php
  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from timezones ";
  $q_string .= "order by zone_name";
  $q_timezones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_timezones = mysqli_fetch_array($q_timezones)) {
    print "<option value=\"" . $a_timezones['zone_id'] . "\">" . htmlspecialchars($a_timezones['zone_name']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="6">Front photo filename <select name="inv_front">
<?php
  $q_string  = "select img_id,img_title,img_file ";
  $q_string .= "from images ";
  $q_string .= "where img_facing = 1 ";
  $q_string .= "order by img_title,img_file ";
  $q_images = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_images = mysqli_fetch_array($q_images)) {
    print "<option value=\"" . $a_images['img_id'] . "\">" . htmlspecialchars($a_images['img_title']) . " (" . htmlspecialchars($a_images['img_file']) . ")</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="6">Rear photo filename <select name="inv_rear">
<?php
  $q_string  = "select img_id,img_title,img_file ";
  $q_string .= "from images ";
  $q_string .= "where img_facing = 0 ";
  $q_string .= "order by img_title,img_file ";
  $q_images = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_images = mysqli_fetch_array($q_images)) {
    print "<option value=\"" . $a_images['img_id'] . "\">" . htmlspecialchars($a_images['img_title']) . " (" . htmlspecialchars($a_images['img_file']) . ")</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Support Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Platform Manager <select name="inv_manager">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . htmlspecialchars($a_inv_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Application Manager <select name="inv_appadmin">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . htmlspecialchars($a_inv_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Service Class <select name="inv_class">
<?php
  $q_string  = "select svc_id,svc_name ";
  $q_string .= "from service ";
  $q_string .= "order by svc_id";
  $q_service = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_service = mysqli_fetch_array($q_service)) {
    print "<option value=\"" . $a_service['svc_id'] . "\">" . htmlspecialchars($a_service['svc_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Suggested Response Level <select name="inv_response">
<?php
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from supportlevel ";
  $q_string .= "order by slv_value";
  $q_supportlevel = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_supportlevel = mysqli_fetch_array($q_supportlevel)) {
    print "<option value=\"" . $a_supportlevel['slv_id'] . "\">" . htmlspecialchars($a_supportlevel['slv_value']) . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Product Information</th>
</tr>
<tr>
  <td class="ui-widget-content">Product <select name="inv_product" onchange="show_file('inventory.options.php?server=<?php print $formVars['server']; ?>&product=' + document.edit.inv_product.value);">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name";
  $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_products = mysqli_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . htmlspecialchars($a_products['prod_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Project <select name="inv_project">
<?php
  $q_string  = "select prj_id,prj_name ";
  $q_string .= "from projects ";
  $q_string .= "where prj_product = " . $a_inventory['inv_product'] . " ";
  $q_string .= "order by prj_name";
  $q_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_projects = mysqli_fetch_array($q_projects)) {
    print "<option value=\"" . $a_projects['prj_id'] . "\">" . htmlspecialchars($a_projects['prj_name']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Business Unit (Department) <select name="inv_department">
<?php
  $q_string  = "select dep_id,bus_name,dep_business,dep_name ";
  $q_string .= "from department  ";
  $q_string .= "left join business on business.bus_id = department.dep_business ";
  $q_string .= "order by dep_business,dep_name";
  $q_department = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_department = mysqli_fetch_array($q_department)) {
    print "<option value=\"" . $a_department['dep_id'] . "\">";
      print htmlspecialchars($a_department['bus_name']) . "-" . htmlspecialchars($a_department['dep_name']);
    print "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Maintenance Window Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Maintenance Window <select name="inv_maint">
<?php
  $q_string  = "select win_id,win_text ";
  $q_string .= "from maint_window  ";
  $q_string .= "order by win_text";
  $q_window = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_window = mysqli_fetch_array($q_window)) {
    print "<option value=\"" . $a_window['win_id'] . "\">" . htmlspecialchars($a_window['win_text']) . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Platform Specific Form</th>
</tr>
<tr>
  <td class="ui-widget-content"><label>Enable Ansible? <input type="checkbox" name="inv_ansible"></label></td>
  <td class="ui-widget-content"><label>Accessible via SSH for unixsvc? <input type="checkbox" name="inv_ssh"></label></td>
</tr>
</table>

</div>
<?php
  }
?>



<div id="tags">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Tag Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('tags-help');">Help</a></th>
</tr>
</table>

<div id="tags-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Tags have two uses in the Inventory. To let you create a collection of servers and as a hosts file that is used by Ansible to 
apply playbooks to a filtered list of servers.</p>

<p>The Server Tags window shows all the tags associated with the server we're currenting editing. Additional windows list out 
tags for other functions in the Inventory that are associated with this server. The idea is to manage tags without having to 
add a common tag to a large collection of servers. For example, Data Center Locations can have a tag associated with it. If 
you've associated a server with a Data Center, the location tag will be listed in the Location Tags window. This works the same 
for other functions.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickTagCreate" value="Add Server Tag"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Tag Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('tags-listing');">Help</a></th>
</tr>
</table>

<div id="tags-listing" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Tag Listing</strong></p>

<p>This page displays all the tags that are in the system and with a <strong>[bracketed]</strong> keyword, identifies the tags 
associated with this specific server.</p>

<p>To toggle tags, in the Server Tags list, click on a tag. The tag will then be either removed from the server or added 
if the server doesn't currently have the tag. If it's the last tag in the database, the tag will disappear and you'll need 
to manually add it back in.</p>

<p>To add a brand new tag, click on the Add Server Tag button and enter the new tag and save it. Note that if you try to create 
a tag that already exists, it does check for that and will simply toggle it vs adding a duplicate tag.</p>

</div>

</div>


<div class="main ui-widget-content">

<span id="Server_tags"><?php print wait_Process("Please Wait"); ?></span>

</div>

<?php

  $q_string  = "select type_name ";
  $q_string .= "from inv_tag_types ";
  $q_string .= "where type_id > 1 ";
  $q_string .= "order by type_name ";
  $q_inv_tag_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_tag_types) > 0) {
    while ($a_inv_tag_types = mysqli_fetch_array($q_inv_tag_types)) {

      print "<div class=\"main ui-widget-content\">\n\n";
      print "<span id=\"" . $a_inv_tag_types['type_name'] . "_tags\">" . wait_process("Please wait") . "</span>\n\n";
      print "</div>\n\n";

    }
  }

?>

</div>




<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
<div id="maintenance">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Maintenance and Patching Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('maintenance-help');">Help</a></th>
</tr>
</table>

<div id="maintenance-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Hardware Listing</strong> - Reloads the Hardware Listing table. At times, especially when removing several items, the table fails to refresh.</li>
    <li><strong>Update Hardware</strong> - After selecting a piece of hardware to edit, click here to save changes.</li>
    <li><strong>Add New Hardware</strong> - Add a new piece of hardware. You can also select an existing piece, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Hardware Table From:</strong> - Select a server from the listing to copy the hardware list from.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Hardware Form</strong>
  <ul>
    <li><strong>Asset Tag</strong> - the company Asset tag located somewhere on the device. Set it to asterisk (*) if you found the device but the Asset tag was inaccessible.</li>
    <li><strong>Serial</strong> - the serial number of the device.</li>
    <li><strong>Service Tag</strong> - used for Dell equipment in place of the serial number.</li>
    <li><strong>Delete?</strong> - This checkbox lets identify a piece of hardware as active again. If you deleted a <strong>Primary Container</strong>, you will need to recheck that checkbox when saving changes.</li>
    <li><strong>Type</strong> - when you select a device type, the Model menu automatically fills in with equipment identified as the selected type</li>
    <li><strong>Model</strong> - select the appropriate model information.</li>
    <li><strong>Size</strong> - Enter in the height of the physical device in Units, or the size of the drive or memory, or the number of cores if it's a CPU.</li>
    <li><strong>Speed</strong> - Enter in the drive speed or CPU speed.</li>
  </ul></li>
  <li><strong>Support Form</strong>
  <ul>
    <li><strong>Support Company</strong> - Select the support contract/contact information. This is displayed in the Issue Tracker when opening a Vendor Support issue.</li>
    <li><strong>Response Level</strong> - Select the support contract/contact information. This is displayed in the Issue Tracker when opening a Vendor Support issue.</li>
    <li><strong>RMA</strong> - If a component is being replaced, the RMA number is here. Generally this is set in the Issue Tracker but should the RMA number need to be moved to the correct component, you can edit it here.</li>
    <li><strong>Contract Confirmation</strong> - If the Support Company and Response Level information is imported from Contracts, this is set to 'Yes'.</li>
  </ul></li>
  <li><strong>Container/Redundancy Form</strong>
  <ul>
    <li><strong>Main Hardware Container</strong> - This lists all the unassigned hardware for this system. This provides the ability to manually associate hardware with the main device. This should be done automatically by the import scripts however some systems aren't able to be accessed by the service account.</li>
    <li><strong>Hard Disk Redundancy</strong> - This lists all the RAID devices associated with this system. You will need to identify RAIDed devices in the Model drop down for a Hard Disk in order for this list to be populated. I recommend entering a Volume number in the Asset, Serial, or Service Tag fields as they will be displayed along with the RAIDed Device in the menu. Selecting the Main Hardware Container is not required as it will assume the main device.</li>
  </ul></li>
  <li><strong>Life-Cycle Form</strong>
  <ul>
    <li><strong>Purchased</strong> - The date the device was purchased.</li>
    <li><strong>Built</strong> - The date the device was built and ready for use.</li>
    <li><strong>Live</strong> - The date the device went live and into production.</li>
    <li><strong>End of Life</strong> - The date the company expects to retire this device. This is different than the hardware model End of Life which is provided by the vendor.</li>
    <li><strong>Retired</strong> - The date the server was removed from service.</li>
    <li><strong>Reused</strong> - The date the server was repurposed. Use the note field here or under the Detail record to identify what the new server name is.</li>
  </ul></li>
  <li><strong>Notes Form</strong>
  <ul>
    <li><strong>Note</strong> - Enter in any notes for this device. This will be a hover text in the <strong>Show Inventory</strong> pages.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Fields marked with an asterisk (*) are automatically captured where possible.</li>
    <li>Click the <strong>Hardware Management</strong> title bar to toggle the <strong>Hardware Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>

<span id="maintenance_form"><?php print wait_Process("Please Wait"); ?></span>

</div>



<div id="hardware">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('hardware-hide');">Hardware Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('hardware-help');">Help</a></th>
</tr>
</table>

<div id="hardware-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Hardware Listing</strong> - Reloads the Hardware Listing table. At times, especially when removing several items, the table fails to refresh.</li>
    <li><strong>Update Hardware</strong> - After selecting a piece of hardware to edit, click here to save changes.</li>
    <li><strong>Add New Hardware</strong> - Add a new piece of hardware. You can also select an existing piece, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Hardware Table From:</strong> - Select a server from the listing to copy the hardware list from.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Hardware Form</strong>
  <ul>
    <li><strong>Asset Tag</strong> - the company Asset tag located somewhere on the device. Set it to asterisk (*) if you found the device but the Asset tag was inaccessible.</li>
    <li><strong>Serial</strong> - the serial number of the device.</li>
    <li><strong>Service Tag</strong> - used for Dell equipment in place of the serial number.</li>
    <li><strong>Delete?</strong> - This checkbox lets identify a piece of hardware as active again. If you deleted a <strong>Primary Container</strong>, you will need to recheck that checkbox when saving changes.</li>
    <li><strong>Type</strong> - when you select a device type, the Model menu automatically fills in with equipment identified as the selected type</li>
    <li><strong>Model</strong> - select the appropriate model information.</li>
    <li><strong>Size</strong> - Enter in the height of the physical device in Units, or the size of the drive or memory, or the number of cores if it's a CPU.</li>
    <li><strong>Speed</strong> - Enter in the drive speed or CPU speed.</li>
  </ul></li>
  <li><strong>Support Form</strong>
  <ul>
    <li><strong>Support Company</strong> - Select the support contract/contact information. This is displayed in the Issue Tracker when opening a Vendor Support issue.</li>
    <li><strong>Response Level</strong> - Select the support contract/contact information. This is displayed in the Issue Tracker when opening a Vendor Support issue.</li>
    <li><strong>RMA</strong> - If a component is being replaced, the RMA number is here. Generally this is set in the Issue Tracker but should the RMA number need to be moved to the correct component, you can edit it here.</li>
    <li><strong>Contract Confirmation</strong> - If the Support Company and Response Level information is imported from Contracts, this is set to 'Yes'.</li>
  </ul></li>
  <li><strong>Container/Redundancy Form</strong>
  <ul>
    <li><strong>Main Hardware Container</strong> - This lists all the unassigned hardware for this system. This provides the ability to manually associate hardware with the main device. This should be done automatically by the import scripts however some systems aren't able to be accessed by the service account.</li>
    <li><strong>Hard Disk Redundancy</strong> - This lists all the RAID devices associated with this system. You will need to identify RAIDed devices in the Model drop down for a Hard Disk in order for this list to be populated. I recommend entering a Volume number in the Asset, Serial, or Service Tag fields as they will be displayed along with the RAIDed Device in the menu. Selecting the Main Hardware Container is not required as it will assume the main device.</li>
  </ul></li>
  <li><strong>Life-Cycle Form</strong>
  <ul>
    <li><strong>Purchased</strong> - The date the device was purchased.</li>
    <li><strong>Built</strong> - The date the device was built and ready for use.</li>
    <li><strong>Live</strong> - The date the device went live and into production.</li>
    <li><strong>End of Life</strong> - The date the company expects to retire this device. This is different than the hardware model End of Life which is provided by the vendor.</li>
    <li><strong>Retired</strong> - The date the server was removed from service.</li>
    <li><strong>Reused</strong> - The date the server was repurposed. Use the note field here or under the Detail record to identify what the new server name is.</li>
  </ul></li>
  <li><strong>Notes Form</strong>
  <ul>
    <li><strong>Note</strong> - Enter in any notes for this device. This will be a hover text in the <strong>Show Inventory</strong> pages.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Fields marked with an asterisk (*) are automatically captured where possible.</li>
    <li>Click the <strong>Hardware Management</strong> title bar to toggle the <strong>Hardware Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="hardware-hide" style="display: none">

<span id="hardware_form"><?php print wait_Process("Please Wait"); ?></span>

</div>

<span id="hardware_table"><?php print wait_Process("Please Wait"); ?></span>

</div>




<div id="filesystem">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Filesystem Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('filesystem-help');">Help</a></th>
</tr>
</table>

<div id="filesystem-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Filesystem Listing</strong> - Reloads the Filesystem Listing table. At times, especially when removing several items, the table fails to refresh.</li>
    <li><strong>Update Filesystem</strong> - After selecting a filesystem to edit, click here to save changes.</li>
    <li><strong>Add Filesystem</strong> - Add a new filesystem. You can also select an existing piece, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Filesystem Table From:</strong> - Select a server from the listing to duplicate a filesystem list.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Filesystem Form</strong>
  <ul>
    <li><strong>Back up?</strong> - Check this box if this file system is backed up.</li>
    <li><strong>Device</strong> - Enter the filesystem device.</li>
    <li><strong>Mount Point</strong> - Enter the expected mount point for this filesystem.</li>
    <li><strong>Size</strong> - Enter the size of the filesystem.</li>
  </ul></li>
  <li><strong>SAN Form</strong>
  <ul>
    <li><strong>WWID</strong> - </li>
    <li><strong>Subsystem</strong> - </li>
    <li><strong>LUN</strong> - </li>
    <li><strong>Volume</strong> - </li>
    <li><strong>VolID</strong> - </li>
    <li><strong>Path</strong> - </li>
    <li><strong>Switch</strong> - Enter the switch name used to connect this server to the SAN.</li>
    <li><strong>Port</strong> - Enter the switch port the fiber is plugged in to.</li>
    <li><strong>Server Port</strong> - Enter the position on the server where the fiber is plugged in to.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Fields marked with an asterisk (*) are automatically captured where possible.</li>
    <li>Click the <strong>Filesystem Management</strong> title bar to toggle the <strong>Filesystem Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content"><input type="button" id="clickFilesystemCreate" value="Add Filesystem"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
<th class="ui-state-default">Filesystem Listing</th>
<th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('filesystem-listing-help');">Help</a></th>
</tr>
</table>


<div id="filesystem-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Filesystem Listing</strong>
  <ul>
    <li><strong>Highlighted</strong> - Filesystems that are <span class="ui-state-highlight">highlighted</span> are <strong>not</strong> being backed up if the Backup Form "Include all filesystems" checkbox is not checked.</li>
    <li><strong>Remove</strong> - Clicking the <strong>Remove Button</strong> will delete this filesystem from this server.</li>
    <li><strong>Editing</strong> - Click on a filesystem to toggle the form for editing.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Rows marked with a checkmark in the Updated column have been automatically captured where possible.</li>
    <li>Click the <strong>Filesystem Management</strong> title bar to toggle the <strong>Filesystem Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>


<span id="filesystem_table"><?php print wait_Process("Please Wait"); ?></span>

</div>

<?php
  }
?>
<div id="software">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-help');">Help</a></th>
</tr>
</table>

<div id="software-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Insert Software Help Here</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content"><input type="button" id="clickSoftwareCreate" value="Add Software"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Software Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('software-listing-help');">Help</a></th>
</tr>
</table>

<div id="software-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Insert Software Help Here</p>

</div>

</div>


<span id="software_table"><?php print wait_Process("Please Wait"); ?></span>

</div>





<?php
  if (check_grouplevel($db, $a_inventory['inv_manager'])) {
?>
<div id="interface">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Interface Editor</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('interface-help');">Help</a></th>
</tr>
</table>

<div id="interface-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Network Listing</strong> - Reloads the Network Listing table. At times, especially when removing several items, the table fails to refresh.</li>
    <li><strong>Update Interface</strong> - After selecting the interface to edit, click here to save changes.</li>
    <li><strong>Add Interface</strong> - Add a new interface. You can also select an existing item, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Network Table From:</strong> - Select a server from the listing to duplicate it's list of interfaces.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Server Form</strong>
  <ul>
    <li><strong>Interface Name</strong> - The name of the interface. Typically the hostname and one of the interface names will match. The interface that's not a hostname will generally be the management interface.</li>
    <li><strong>Physical Port</strong> - The physical port on the server where the network cable is plugged in.</li>
    <li><strong>MAC</strong> - The MAC Address.</li>
    <li><strong>IP Address</strong> - The IP Address.</li>
    <li><strong>IPv6</strong> - If this is an IPv6 IP, check this box.</li>
    <li><strong>Subnet Mask</strong> - Select the appropriate subnet mask here.</li>
    <li><strong>Logical Interface Name</strong> - The name of the interface as assigned by the operating system (such as eth0, e1000g0, bge0, etc).</li>
    <li><strong>Interface Type</strong> - Identify the purpose of this interface. For several reports to properly work, one interface must be identified as a Management interface even if there's just one IP.</li>
    <li><strong>Virtual Interface?</strong> - If a virtual interface, identify it here. Note that there are virtual interfaces that aren't part of a redundant configuration such as Oracle's Virtual interfaces. Identify all virtual interfaces here.</li>
    <li><strong>Note</strong> - Enter a note here which will be hover text on the inventory display page. Something like special notes for accessing a serial console.</li>
  </ul></li>
  <li><strong>Redundancy Form</strong>
  <ul>
    <li><strong>Redundancy</strong> - If part of a redundant interface configuration such as Bond, IPMP, APA, or Teaming, select the virtual interface type here.</li>
    <li><strong>Group/Teaming Name</strong> - Some operating systems such as Solaris and Windows assign group names to the physical members of a virtual interface. Enter the group or team name for the physical interfaces here.</li>
    <li><strong>Bond/IPMP/APA/Teaming Assignment</strong> - If the physical member of a redundant configuration, select the redundant virtual interface this interface is a member of.</li>
  </ul></li>
  <li><strong>Monitoring Form</strong>
  <ul>
    <li><strong>How is this interface monitored?</strong> - Select the method of interface management. Typically OpenView monitors one interface but we may want to further monitor interfaces in case of a problem with the LOM.</li>
    <li><strong>Services to monitor</strong> - Ping check just performing a ping of the identified interface. SSH check confirms you can ssh to the server and get a header.</li>
    <li><strong>Notify Process</strong> - How does the group that is responsible for the system want to receive notifications for this interface.</li>
    <li><strong>Notification Hours</strong> - When does the group want to be notified. The team may want to be paged but only during working hours.</li>
    <li><strong>Nagios Custom Coordinate Layout</strong> - These numbers are relative to the gateway X, Y, and Z coordinates.</li>
  </ul></li>
  <li><strong>Transport Form</strong>
  <ul>
    <li><strong>Media</strong> - What physical cable type is being used for this system.</li>
    <li><strong>Speed</strong> - Generally systems auto-negotiate however some systems have issues with properly syncing with the switch if it's set to auto-negotiate.</li>
    <li><strong>Duplex</strong> - Same here. Most systems are auto-negotiate but there are some systems with issues.</li>
  </ul></li>
  <li><strong>Switch Form</strong>
  <ul>
    <li><strong>Switch</strong> - The name of the network switch.</li>
    <li><strong>Switch Port</strong> - Switch port being configured.</li>
    <li><strong>VLAN</strong> - Virtual LAN domain.</li>
    <li><strong>Gateway</strong> - Destination for traffic on this interface.</li>
    <li><strong>Default Route</strong> - Is this gateway the default for all traffic unless otherwise defined.</li>
    <li><strong>Zone</strong> - Network zone this IP belongs to. There may be sub-configurations to insure proper traffic shaping.</li>
    <li><strong>Role</strong> - The role of this interface.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Fields marked with an asterisk (*) are automatically captured where possible.</li>
  </ul></li>
</ul>


</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content"><input type="button" id="clickInterfaceCreate" value="Add Interface"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Interface Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('interface-listing-help');">Help</a></th>
</tr>
</table>

<div id="interface-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Interface Listing</strong>
  <ul>
    <li><strong>Highlighted</strong> - This interface is the <span class="ui-state-highlight">Default Route</span>.</li>
    <li><strong>Highlighted</strong> - This hostname either doesn't match the resolved hostname or is simply <span class="ui-state-error">not in DNS</span>. If incorrect or incomplete, the identified DNS entry will be displayed. If no DNS entry, it will show the IP Address. Not all interfaces need to be in DNS but they will be highlighted if not.</li>
    <li><strong>Delete</strong> - Clicking the <strong>Delete</strong> button will delete this interface from this server.</li>
    <li><strong>Bridge</strong> - A bridge interface will be designed with a (b).</li>
    <li><strong>Virtual Memberships</strong> - If a physical interface is a member of a virtual interface, it will be designated with a &gt; to the left of the name and will listed under the virtual interface. The main virtual interface of the group will be designated with (r). If Group or Teaming names are used, they will be listed next to the physical members of the group.
    <ul>
      <li><strong>Solaris</strong> virtual interfaces end in :number (e1000g1:1, e1000g5:1, etc).</li>
      <li><strong>Linux</strong> virtual interfaces begin with bond (bond0, bond0.87, bond1, etc).</li>
      <li><strong>HP-UX</strong> virtual interfaces are in the 900 range (lan900, lan901, etc).</li>
      <li><strong>Windows</strong> virtual interfaces.</li>
    </ul></li>
    <li><strong>Virtual</strong> - A Virtual interface will be identified with a (v) next to the Logical Interface name. Not all Virtual interfaces are part of a Redundancy group.</li>
    <li><strong>Management</strong> - A interface that is designated to pass management traffic will be identified with a (M). There should only be one interface identified as such.</li>
    <li><strong>Backups</strong> - A interface that is designated to pass backup traffic will be identified with a (B). If it's not designated, by default the (M) interface is assumed to pass backup traffic.</li>
    <li><strong>Editing</strong> - Click on an interface to edit it.</li>
  </ul></li>
</ul>

</div>

</div>

<span id="interface_table"><?php print wait_Process("Please Wait"); ?></span>

</div>





<div id="users">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('users-hide');">User Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('users-help');">Help</a></th>
</tr>
</table>

<div id="users-help" style="display: none">

<div class="main-help ui-widget-content">

<p>In general what you're seeing are the list of users, system accounts, and service accounts that are on this server. 
In addition are columns for managing the user such as whether or not to lock the user, updating the type of account, 
and/or updating the GECOS information.</p>

<p>When this information is updated, the following three files are also updated which subsequently change the information 
on all servers during the following server script run.</p>

<ul>
  <li>valid.email - This changes the GECOS field on all servers to match the GECOS settings made here.</li>
  <li>lockuser.dat - This file identifes who needs to be locked on a server due to departure from the company.</li>
  <li>users.exclude - This file lists service accounts that aren't listed in the company.email file used to identify people who are still in the company.</li>
</ul>

<p>While you can fill out the fields and "create" a new user in this listing, adding the user does not add the user 
on the server.</p>

<p>Any user account with a '--' in the Account Type column indicates it is not currently being managed and is likely 
being reported if a service or system account as it's not being found in the company.email file. Click on the user 
to modify it and then click the Add User button to begin managing the account.</p>

</div>

</div>


<div id="users-hide" style="display: none">

<span id="users_form"><?php print wait_Process("Please Wait"); ?></span>

</div>

<span id="users_table"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="routing">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('routing-hide');">Route Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('routing-help');">Help</a></th>
</tr>
</table>

<div id="routing-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Route Listing</strong> - Reloads the Route Listing table. At times, especially when removing several items, the table fails to refresh.</li>
    <li><strong>Update Route</strong> - After selecting the route to edit, click here to save changes.</li>
    <li><strong>Add Route</strong> - Add a new route. You can also select an existing item, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Route Table From:</strong> - Select a server from the listing to duplicate it's list of routes.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Route Form</strong>
  <ul>
    <li><strong>Route</strong> - Enter the route.</li>
    <li><strong>Static Route?</strong> - Is this a static route? A script will extract the static routes and create a Ansible Playbook vars file.</li>
    <li><strong>Gateway</strong> - Enter the gateway traffic for this route will traverse.</li>
    <li><strong>Subnet Mask</strong> - Select the subnet mask for this route.</li>
    <li><strong>Interface</strong> - Select the appropriate interface for the traffic.</li>
    <li><strong>Interface (2)</strong> - Disabled. This provides a free form field for entering interfaces that may not exist on the server but need to be added.</li>
    <li><strong>Source IP</strong> - If you want to ensure traffic is coming from a specific IP when routing traffic out, add the IP here..</li>
    <li><strong>Description</strong> - Add a brief description for this route. You will be prompted to see if you want to copy this description to similar routes on other systems.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Possible Solaris/Linux Route Files</strong> - Reference information. Contains a list of extrapolated route configurations that might be used on the requisite system.</li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Fields marked with an asterisk (*) are automatically captured where possible.</li>
    <li>Click the <strong>Route Management</strong> title bar to toggle the <strong>Route Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="routing-hide" style="display: none">

<span id="routing_form"><?php print wait_Process("Please Wait"); ?></span>

</div>

<span id="routing_table"><?php print wait_Process("Please Wait - DNS Lookups"); ?></span>

</div>


<div id="backup">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Backup Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('backup-help');">Help</a></th>
</tr>
</table>

<div id="backup-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Backups</strong> - After making any changes, click here to save changes.</li>
    <li><strong>Copy Backup Schedule From:</strong> - Select a server from the listing to duplicate it's list of interfaces.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Backup Form</strong>
  <ul>
    <li><strong>Backup Start Date</strong> - When should backups start.</li>
    <li><strong>Include all filesystems?</strong> - The default is to back up every file system. If this is not checked, the Filesystem Listing will identify filesystems that are not to be backed up.</li>
    <li><strong>Retention Length</strong> - How long should backups be kept.</li>
    <li><strong>Schedule</strong> - Set up a backup schedule.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="button ui-widget-content">
<input type="button" name="bu_update" value="Update Backups" onClick="javascript:attach_backups('backups.mysql.php', 1);">
<input type="hidden" name="bu_id" value="0">
  </td>
</tr>
<tr>
  <td class="button ui-widget-content">
<input type="button" name="copyitem"  value="Copy Backup Schedule From: " onClick="javascript:attach_backups('backups.mysql.php', -2);">
<select name="bu_copyfrom">
<option value="0">None</option>
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $_SESSION['group'] . " ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
    $q_string  = "select bu_id ";
    $q_string .= "from backups ";
    $q_string .= "where bu_companyid = " . $a_inventory['inv_id'];
    $q_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_backups) > 0) {
      print "<option value=\"" . $a_inventory['inv_id'] . "\">" . htmlspecialchars($a_inventory['inv_name']) . "</option>\n";
    }
  }
?>
</select></td>
</tr>
</table>

<table class="full ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="14">Backup Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">Backup Start Date <input type="text" name="bu_start" size="10"></td>
  <td class="ui-widget-content" colspan="3"><label>Include all filesystems? <input type="checkbox" name="bu_include"></label></td>
  <td class="ui-widget-content" colspan="7">Retention Length <select name="bu_retention">
<option value="0">None</option>
<option value="1">Less than 6 Months (Details Required)</option>
<option value="2">6 Months</option>
<option value="3">1 Year</option>
<option selected value="4">3 Years (Standard)</option>
<option value="5">7 Years</option>
</select></td>
</tr>
<tr>
  <th class="ui-state-default" colspan="2">Sunday</th>
  <th class="ui-state-default" colspan="2">Monday</th>
  <th class="ui-state-default" colspan="2">Tuesday</th>
  <th class="ui-state-default" colspan="2">Wednesday</th>
  <th class="ui-state-default" colspan="2">Thursday</th>
  <th class="ui-state-default" colspan="2">Friday</th>
  <th class="ui-state-default" colspan="2">Saturday</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_sunday"    checked="true" type="radio"></label><br><label>Incr <input value="1" name="bu_sunday"    type="radio"></label></td>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_monday"    type="radio"></label><br><label>Incr <input value="1" name="bu_monday"    checked="true" type="radio"></label></td>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_tuesday"   type="radio"></label><br><label>Incr <input value="1" name="bu_tuesday"   checked="true" type="radio"></label></td>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_wednesday" type="radio"></label><br><label>Incr <input value="1" name="bu_wednesday" checked="true" type="radio"></label></td>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_thursday"  type="radio"></label><br><label>Incr <input value="1" name="bu_thursday"  checked="true" type="radio"></label></td>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_friday"    type="radio"></label><br><label>Incr <input value="1" name="bu_friday"    checked="true" type="radio"></label></td>
  <td class="ui-widget-content" colspan="2"><label>Full <input value="0" name="bu_saturday"  type="radio"></label><br><label>Incr <input value="1" name="bu_saturday"  checked="true" type="radio"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_suntime" size="4"></td>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_montime" size="4"></td>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_tuetime" size="4"></td>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_wedtime" size="4"></td>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_thutime" size="4"></td>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_fritime" size="4"></td>
  <td class="ui-widget-content">Start:</td>
  <td class="ui-widget-content"><input type="text" value="00:00" name="bu_sattime" size="4"></td>
</tr>
</table>

<table class="full ui-styled-table">
<tr>
  <th class="ui-state-default">Backup Notes</th>
</tr>
<tr>
  <td class="ui-widget-content"><textarea id="bu_notes" name="bu_notes" cols="130" rows="10"
  onKeyDown="textCounter(document.edit.bu_notes, document.edit.remLen, 1024);"
  onKeyUp  ="textCounter(document.edit.bu_notes, document.edit.remLen, 1024);">
</textarea>
<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1024"> characters left</td>
</tr>
</table>

</div>



<div id="association">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('association-hide');">Association Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('association-help');">Help</a></th>
</tr>
</table>

<div id="association-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Refresh Association Listing</strong> - Reloads the Association Listing table. At times, especially when removing several items, the table fails to refresh.</li>
    <li><strong>Update Association</strong> - After selecting the association to edit, click here to save changes.</li>
    <li><strong>Add Association</strong> - Add a new association. You can also select an existing item, make changes if needed, and click this button to add a second item.</li>
    <li><strong>Copy Association Table From:</strong> - Select an association from the listing to duplicate the list of associations.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Association Form</strong>
  <ul>
    <li><strong>Source IP</strong> - Select a source IP from the list of available IPs on this system.</li>
    <li><strong>Destination IP</strong> - Select a destination IP. This is the name, interface name, and IP from every device in the inventory.</li>
    <li><strong>Destination Port</strong> - Enter the network port that the Associated server is listening on.</li>
    <li><strong>Protocol</strong> - Enter the network protocol (such as udp or tcp).</li>
    <li><strong>Notes</strong> - Description of the connection.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="association-hide" style="display: none">

<span id="association_form"><?php print wait_Process("Please Wait"); ?></span>

</div>

<span id="association_table"><?php print wait_Process("Please Wait"); ?></span>

</div>


<div id="comments">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('comments-hide');">Comments</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('comments-help');">Help</a></th>
</tr>
</table>

<div id="comments-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reset</strong> - Reset the data entry form clearing the textarea and resetting the formatting buttons and data entry fields.</li>
    <li><strong>Update Detail</strong> - After selecting a detail record to edit, click here to save any changes.</li>
    <li><strong>Save New Detail</strong> - Add a new detail record. You can also select an existing record, make changes if needed, and click this button to add a second detail.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Problem Form</strong>
  <ul>
    <li><strong>Data Entry</strong> - Enter data about the issue here. The Bold, Italic, Underline, 
and Preserve Formatting buttons let you format the output of the data. The <strong>character count</strong> 
field shows you the limit of the number of characters. This limit is set by the browser.</li>
    <li><strong>Timestamp</strong> - The time the work was done.</li>
    <li><strong>Support Tech</strong> - The person performing the work.</li>
    <li><strong>Generate Wiki Page</strong> - Generates a formated page for insertion into a wiki.</li>
    <li><strong>Generate Text Page</strong> - Generates a formated log suitable for emailing to a support technician.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>Problem Management</strong> title bar to toggle the <strong>Problem Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>



<div id="comments-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td colspan="7" class="ui-widget-content button">
<input type="button"                 name="reset"       value="Reset"           onClick="javascript:reset_detail();">
<input type="button" disabled="true" name="comupdate"   value="Update Comment"   onClick="javascript:attach_comment('comments.mysql.php', 1);hideDiv('comments-hide');">
<input type="hidden" name="com_id" value="0">
<input type="hidden" name="format_bold" value="0">
<input type="hidden" name="format_italic" value="0">
<input type="hidden" name="format_underline" value="0">
<input type="hidden" name="format_preserve" value="0">
<input type="button"                 name="combutton" value="Save New Comment" onClick="javascript:attach_comment('comments.mysql.php', 0);"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="7">Comment Form</th>
</tr>
<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
<tr>
  <td class="ui-widget-content delete"><input type="button" id="show_bold"      value="Bold"                onclick="javascript:formatText('bold');"></td>
  <td class="ui-widget-content delete"><input type="button" id="show_italic"    value="Italic"              onclick="javascript:formatText('italic');"></td>
  <td class="ui-widget-content delete"><input type="button" id="show_underline" value="Underline"           onclick="javascript:formatText('underline');"></td>
  <td class="ui-widget-content delete"><input type="button" id="show_preserve"  value="Preserve Formatting" onclick="javascript:formatText('preserve');"></td>
  <td class="ui-widget-content" colspan="3">&nbsp;</td>
</tr>
<?php
  }
?>
<tr>
  <td class="ui-widget-content" colspan="7">
<textarea id="com_text" name="com_text" cols="130" rows="10" 
  onKeyDown="textCounter(document.edit.com_text, document.edit.remLen, 1800);" 
  onKeyUp  ="textCounter(document.edit.com_text, document.edit.remLen, 1800);">
</textarea>

<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1800"> characters left
</td>
</tr>
<tr>
  <td class="ui-widget-content" title="Leave Timestamp field set to Current Time to use current time, otherwise use YYYY-MM-DD HH:MM:SS." colspan="4">Timestamp: <input type="text" name="com_timestamp" value="Current Time" size=23></td>
  <td class="ui-widget-content">Comment by: <select name="com_user">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . " " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</div>

<span id="comments_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>


</div>

</div>

</div>

</div>

<?php
  }
?>

</div>

</div>

</div>

</form>




<?php include($Editpath . '/tags.dialog.php'); ?>

<?php include($Editpath . '/filesystem.dialog.php'); ?>

<?php include($Editpath . '/interface.dialog.php'); ?>

<?php include($Editpath . '/software.dialog.php'); ?>



<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
