
<div id="dialogFilesystemCreate" title="Add Filesystem">

<form name="formFilesystemCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content"><label>Back up? <input type="checkbox" name="fs_backup"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Device:* <input type="text" name="fs_device" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Mount Point:* <input type="text" name="fs_mount" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Size:* <input type="text" name="fs_size" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Managed by: <select name="fs_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
<tr>
  <td class="ui-widget-content">WWID: <input type="text" name="fs_wwid" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Subsystem: <input type="text" name="fs_subsystem" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">LUN: <input type="text" name="fs_lun" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Volume: <input type="text" name="fs_volume" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">VolID: <input type="text" name="fs_volid" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Path: <input type="text" name="fs_path" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Switch: <input type="text" name="fs_switch" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port: <input type="text" name="fs_port" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Server Port: <input type="text" name="fs_sysport" size="30"></td>
</tr>
</table>

</form>

</div>


<div id="dialogFilesystemUpdate" title="Edit Filesystem">

<form name="formFilesystemUpdate">

<input type="hidden" name="fs_id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content"><label>Back up? <input type="checkbox" name="fs_backup"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Device:* <input type="text" name="fs_device" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Mount Point:* <input type="text" name="fs_mount" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Size:* <input type="text" name="fs_size" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Managed by: <select name="fs_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
<tr>
  <td class="ui-widget-content">WWID: <input type="text" name="fs_wwid" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Subsystem: <input type="text" name="fs_subsystem" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">LUN: <input type="text" name="fs_lun" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Volume: <input type="text" name="fs_volume" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">VolID: <input type="text" name="fs_volid" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Path: <input type="text" name="fs_path" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Switch: <input type="text" name="fs_switch" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port: <input type="text" name="fs_port" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Server Port: <input type="text" name="fs_sysport" size="30"></td>
</tr>
</table>

</form>

</div>

