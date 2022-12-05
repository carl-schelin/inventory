<div id="dialogInterfaceCreate" title="Add Interface">

<form name="formInterfaceCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">IP Address* <select name="int_ipaddressid">
<?php
  $q_string  = "select ip_id,ip_ipv4,ip_hostname ";
  $q_string .= "from ipaddress ";
  $q_string .= "order by ip_hostname,ip_ipv4 ";
  $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_ipaddress) > 0) {
    while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {

      $q_string  = "select int_id ";
      $q_string .= "from interface ";
      $q_string .= "where int_ipaddressid = " . $a_ipaddress['ip_id'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_interface) == 0) {
        print "<option value=\"" . $a_ipaddress['ip_id'] . "\">" . $a_ipaddress['ip_hostname'] . " " . $a_ipaddress['ip_ipv4'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_ipaddress['ip_id'] . "\">" . $a_ipaddress['ip_hostname'] . " " . $a_ipaddress['ip_ipv4'] . "*</option>\n";
      }
    }
  } else {
    print "<option value=\"0\">IPAM is not populated</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Virtual Interface? <input type="checkbox" name="int_virtual"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Default Route? <input type="checkbox" name="int_primary"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Logical Interface Name* <input type="text" name="int_face" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Interface Type: <select name="int_type">
<?php
  $q_string  = "select itp_id,itp_name ";
  $q_string .= "from int_types ";
  $q_string .= "order by itp_id";
  $q_int_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_int_types) > 0) {
    while ($a_int_types = mysqli_fetch_array($q_int_types)) {
      print "<option value=\"" . $a_int_types['itp_id'] . "\">" . $a_int_types['itp_name'] . "</option>\n";
    }
  } else {
    print "<option value=\"0\">No Interface Types defined</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">MAC* <input type="text" name="int_eth" value="00:00:00:00:00:00" size="18"></td>
</tr>

<tr>
  <td class="ui-widget-content">Redundancy: <select name="int_redundancy">
<?php
  $q_string  = "select red_id,red_default,red_text ";
  $q_string .= "from inv_int_redundancy ";
  $q_string .= "order by red_default desc,red_text";
  $q_inv_int_redundancy = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_int_redundancy) > 0) {
    while ($a_inv_int_redundancy = mysqli_fetch_array($q_inv_int_redundancy)) {
      if ($a_inv_int_redundancy['red_default']) {
        print "<option selected=\"true\" value=\"" . $a_inv_int_redundancy['red_id'] . "\">" . $a_inv_int_redundancy['red_text'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_inv_int_redundancy['red_id'] . "\">" . $a_inv_int_redundancy['red_text'] . "</option>\n";
      }
    }
  } else {
    print "<option value=\"0\">No Redundant Interfaces identified</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">
<?php
  $os = return_System($db, $formVars['server']);

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
Assignment <select name="int_int_id"></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Name: <input type="text" name="int_groupname" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="int_management"> Used for Management traffic</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="int_backup"> Used for Backup traffic</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="int_login"> Used for Secure Shell traffic</label></td>
</tr>
<tr>
  <td class="ui-widget-content">Note: <input type="text" name="int_note" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Physical Hardware Port <input type="text" name="int_sysport" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Media: <select name="int_media">
<?php
  $q_string  = "select med_id,med_default,med_text ";
  $q_string .= "from inv_int_media ";
  $q_string .= "order by med_default desc,med_text";
  $q_inv_int_media = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_int_media) > 0) {
    while ($a_inv_int_media = mysqli_fetch_array($q_inv_int_media)) {
      if ($a_inv_int_media['med_default']) {
        print "<option selected value=\"" . $a_inv_int_media['med_id'] . "\">" . $a_inv_int_media['med_text'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_inv_int_media['med_id'] . "\">" . $a_inv_int_media['med_text'] . "</option>\n";
      }
    }
  } else {
    print "<option value=\"0\">No Interface Media has been defined</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Speed*: <select name="int_speed">
<?php
  $q_string  = "select spd_id,spd_default,spd_text ";
  $q_string .= "from int_speed ";
  $q_string .= "order by spd_default desc,spd_text";
  $q_int_speed = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_int_speed) > 0) {
    while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {
      if ($a_int_speed['spd_default']) {
        print "<option selected value=\"" . $a_int_speed['spd_id'] . "\">" . $a_int_speed['spd_text'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_int_speed['spd_id'] . "\">" . $a_int_speed['spd_text'] . "</option>\n";
      }
    }
  } else {
    print "<option value=\"0\">No Interface Speeds have been defined</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Duplex*: <select name="int_duplex">
<?php
  $q_string  = "select dup_id,dup_default,dup_text ";
  $q_string .= "from inv_int_duplex ";
  $q_string .= "order by dup_default desc,dup_text";
  $q_inv_int_duplex = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_int_duplex) > 0) {
    while ($a_inv_int_duplex = mysqli_fetch_array($q_inv_int_duplex)) {
      if ($a_inv_int_duplex['dup_default']) {
        print "<option selected value=\"" . $a_inv_int_duplex['dup_id'] . "\">" . $a_inv_int_duplex['dup_text'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_inv_int_duplex['dup_id'] . "\">" . $a_inv_int_duplex['dup_text'] . "</option>\n";
      }
    }
  } else {
    print "<option value=\"0\">No Interface Duplex have been defined</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Switch <input type="text" name="int_switch" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port <input type="text" name="int_port" size="20"></td>
</tr>
</table>

</form>

</div>




<div id="dialogInterfaceUpdate" title="Edit Interface">

<form name="formInterfaceUpdate">

<input type="hidden" name="int_id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">IP Address* <select name="int_ipaddressid">
<?php
  $q_string  = "select ip_id,ip_ipv4,ip_hostname ";
  $q_string .= "from ipaddress ";
  $q_string .= "order by ip_hostname,ip_ipv4 ";
  $q_ipaddress = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_ipaddress) > 0) {
    while ($a_ipaddress = mysqli_fetch_array($q_ipaddress)) {

      $q_string  = "select int_id ";
      $q_string .= "from interface ";
      $q_string .= "where int_ipaddressid = " . $a_ipaddress['ip_id'] . " ";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_interface) == 0) {
        print "<option value=\"" . $a_ipaddress['ip_id'] . "\">" . $a_ipaddress['ip_hostname'] . " " . $a_ipaddress['ip_ipv4'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_ipaddress['ip_id'] . "\">" . $a_ipaddress['ip_hostname'] . " " . $a_ipaddress['ip_ipv4'] . "*</option>\n";
      }
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Virtual Interface? <input type="checkbox" name="int_virtual"></label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label>Default Route? <input type="checkbox" name="int_primary"></label></td>
</tr>
<tr>
  <td class="ui-widget-content">Logical Interface Name* <input type="text" name="int_face" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Interface Type: <select name="int_type">
<?php
  $q_string  = "select itp_id,itp_name ";
  $q_string .= "from int_types ";
  $q_string .= "order by itp_id";
  $q_int_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_int_types = mysqli_fetch_array($q_int_types)) {
    print "<option value=\"" . $a_int_types['itp_id'] . "\">" . $a_int_types['itp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">MAC* <input type="text" name="int_eth" value="00:00:00:00:00:00" size="18"></td>
</tr>

<tr>
  <td class="ui-widget-content">Redundancy: <select name="int_redundancy">
<?php
  $q_string  = "select red_id,red_text ";
  $q_string .= "from inv_int_redundancy ";
  $q_string .= "order by red_default desc,red_text";
  $q_inv_int_redundancy = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_int_redundancy = mysqli_fetch_array($q_inv_int_redundancy)) {
    print "<option value=\"" . $a_inv_int_redundancy['red_id'] . "\">" . $a_inv_int_redundancy['red_text'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">
<?php
  $os = return_System($db, $formVars['server']);

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
Assignment <select name="int_int_id"></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Name: <input type="text" name="int_groupname" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="int_management"> Used for Management traffic</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="int_backup"> Used for Backup traffic</label></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="int_login"> Used for Secure Shell traffic</label></td>
</tr>
<tr>
  <td class="ui-widget-content">Note: <input type="text" name="int_note" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Physical Hardware Port <input type="text" name="int_sysport" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Media: <select name="int_media">
<?php
        $q_string  = "select med_id,med_text ";
        $q_string .= "from inv_int_media ";
        $q_string .= "order by med_default desc,med_text";
        $q_inv_int_media = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inv_int_media = mysqli_fetch_array($q_inv_int_media)) {
          print "<option value=\"" . $a_inv_int_media['med_id'] . "\">" . $a_inv_int_media['med_text'] . "</option>\n";
        }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Speed*: <select name="int_speed">
<?php
        $q_string  = "select spd_id,spd_text ";
        $q_string .= "from int_speed ";
        $q_string .= "order by spd_default desc,spd_text";
        $q_int_speed = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_int_speed = mysqli_fetch_array($q_int_speed)) {
          print "<option value=\"" . $a_int_speed['spd_id'] . "\">" . $a_int_speed['spd_text'] . "</option>\n";
        }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Duplex*: <select name="int_duplex">
<?php
        $q_string  = "select dup_id,dup_text ";
        $q_string .= "from inv_int_duplex ";
        $q_string .= "order by dup_default desc,dup_text";
        $q_inv_int_duplex = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_inv_int_duplex = mysqli_fetch_array($q_inv_int_duplex)) {
          print "<option value=\"" . $a_inv_int_duplex['dup_id'] . "\">" . $a_inv_int_duplex['dup_text'] . "</option>\n";
        }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Switch <input type="text" name="int_switch" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Port <input type="text" name="int_port" size="20"></td>
</tr>
</table>

</form>

</div>

