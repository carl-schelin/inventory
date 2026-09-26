<table class="ui-styled-table">
<?php
  if ($ipv4) {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">IPv4 Address: <input type=\"text\" name=\"ip_ipv4\" size=\"20\"></td>\n";
    print "</tr>\n";
  } else {
    print "<input type=\"hidden\" name=\"ip_ipv4\" value=\"\">\n";
  }
  if ($ipv6) {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\">IPv6 Address: <input type=\"text\" name=\"ip_ipv6\" size=\"50\"></td>\n";
    print "</tr>\n";
  } else {
    print "<input type=\"hidden\" name=\"ip_ipv6\" value=\"\">\n";
  }
?>
<tr>
  <td class="ui-widget-content">Hostname: <input type="text" name="ip_hostname" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Domain: <input type="text" name="ip_domain" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">IP Zone: <select name="ip_subzone">
<?php
  $q_string  = "select net_zone ";
  $q_string .= "from inv_network ";
  $q_string .= "where net_id = " . $formVars['net_id'] . " ";
  $q_inv_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_inv_network = mysqli_fetch_array($q_inv_network);

  $q_string  = "select sub_id,sub_name ";
  $q_string .= "from inv_sub_zones ";
  $q_string .= "where sub_zone = \"" . $a_inv_network['net_zone'] . "\" ";
  $q_string .= "order by sub_name ";
  $q_inv_sub_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_sub_zones = mysqli_fetch_array($q_inv_sub_zones)) {
    print "<option value=\"" . $a_inv_sub_zones['sub_id'] . "\">" . $a_inv_sub_zones['sub_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">IP Address Type: <select name="ip_type">
<?php
  $q_string  = "select ip_id,ip_name ";
  $q_string .= "from inv_ip_types ";
  $q_string .= "order by ip_name ";
  $q_inv_ip_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_ip_types = mysqli_fetch_array($q_inv_ip_types)) {
    print "<option value=\"" . $a_inv_ip_types['ip_id'] . "\">" . $a_inv_ip_types['ip_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="ip_description" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Notes:<br><textarea name="ip_notes" rows="3" cols="80"></textarea></td>
</tr>
</table>
