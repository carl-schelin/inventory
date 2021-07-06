<?php
# Script: ipaddress.php
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

  $package = "ipaddress.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $formVars['net_id'] = 0;
  if (isset($_GET['network'])) {
    $formVars['net_id'] = clean($_GET['network'], 10);
  }

# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>IP Address Editor</title>

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
  var answer = confirm("Delete this IP Address entry?")

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
  var af_form = document.createDialog;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&ip_ipv4="           + encode_URI(af_form.ip_ipv4.value);
  af_url += "&ip_ipv6="           + encode_URI(af_form.ip_ipv6.value);
  af_url += "&ip_hostname="       + encode_URI(af_form.ip_hostname.value);
  af_url += "&ip_domain="         + encode_URI(af_form.ip_domain.value);
  af_url += "&ip_network="        + <?php print $formVars['net_id']; ?>;
  af_url += "&ip_subzone="        + af_form.ip_subzone.value;
  af_url += "&ip_type="           + af_form.ip_type.value;
  af_url += "&network="           + af_form.network.value;
  af_url += "&ip_description="    + encode_URI(af_form.ip_description.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&ip_ipv4="           + encode_URI(uf_form.ip_ipv4.value);
  uf_url += "&ip_ipv6="           + encode_URI(uf_form.ip_ipv6.value);
  uf_url += "&ip_hostname="       + encode_URI(uf_form.ip_hostname.value);
  uf_url += "&ip_domain="         + encode_URI(uf_form.ip_domain.value);
  uf_url += "&ip_network="        + <?php print $formVars['net_id']; ?>;
  uf_url += "&ip_subzone="        + uf_form.ip_subzone.value;
  uf_url += "&ip_type="           + uf_form.ip_type.value;
  uf_url += "&network="           + uf_form.network.value;
  uf_url += "&ip_description="    + encode_URI(uf_form.ip_description.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('ipaddress.mysql.php?update=-1&network=<?php print $formVars['net_id']; ?>');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCreate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('ipaddress.mysql.php?update=-1&network=<?php print $formVars['net_id']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add IP Address",
        click: function() {
          attach_file('ipaddress.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUpdate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('ipaddress.mysql.php?update=-1&network=<?php print $formVars['net_id']; ?>');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update IP Address",
        click: function() {
          update_file('ipaddress.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add IP Address",
        click: function() {
          update_file('ipaddress.mysql.php', 0);
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

<form name="ipaddress">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">IP Address Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('ipaddress-help');">Help</a></th>
</tr>
</table>

<div id="ipaddress-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>An IP Address is generally what's needed to access other servers on the same network, other networks, and even 
the internet. This page provides the ability to identify IP Addresses that will be used in your environment.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add IP Address"></td>
</tr>
</table>

</form>


<span id="table_mysql"></span>

</div>


<div id="dialogCreate" title="Add IP Address">

<form name="createDialog">

<input type="hidden" name="id" value="0">
<input type="hidden" name="network" value="<?php print $formVars['net_id']; ?>">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">IPv4 Address: <input type="text" name="ip_ipv4" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">IPv6 Address: <input type="text" name="ip_ipv6" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Hostname: <input type="text" name="ip_hostname" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Domain: <input type="text" name="ip_domain" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">IP Zone: <select name="ip_subzone">
<?php
# need to know the zone the network is in in order to select the sub_zones
  $q_string  = "select net_zone ";
  $q_string .= "from network ";
  $q_string .= "where net_id = " . $formVars['net_id'] . " ";
  $q_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_network = mysqli_fetch_array($q_network);

  $q_string  = "select sub_id,sub_name ";
  $q_string .= "from sub_zones ";
  $q_string .= "where sub_zone = " . $a_network['net_zone'] . " ";
  $q_string .= "order by sub_name ";
  $q_sub_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_sub_zones = mysqli_fetch_array($q_sub_zones)) {
    print "<option value=\"" . $a_sub_zones['sub_id'] . "\">" . $a_sub_zones['sub_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
</tr>
<tr>
  <td class="ui-widget-content">IP Address Type: <select name="ip_type">
<?php
  $q_string  = "select ip_id,ip_name ";
  $q_string .= "from ip_types ";
  $q_string .= "order by ip_name ";
  $q_ip_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_ip_types = mysqli_fetch_array($q_ip_types)) {
    print "<option value=\"" . $a_ip_types['ip_id'] . "\">" . $a_ip_types['ip_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="ip_description" size="50"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit IP Address">

<form name="updateDialog">

<input type="hidden" name="id" value="0">
<input type="hidden" name="network" value="<?php print $formVars['net_id']; ?>">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">IPv4 Address: <input type="text" name="ip_ipv4" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">IPv6 Address: <input type="text" name="ip_ipv6" size="50"></td>
</tr>
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
  $q_string .= "from network ";
  $q_string .= "where net_id = " . $formVars['net_id'] . " ";
  $q_network = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_network = mysqli_fetch_array($q_network);

  $q_string  = "select sub_id,sub_name ";
  $q_string .= "from sub_zones ";
  $q_string .= "where sub_zone = \"" . $a_network['net_zone'] . "\" ";
  $q_string .= "order by sub_name ";
  $q_sub_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_sub_zones = mysqli_fetch_array($q_sub_zones)) {
    print "<option value=\"" . $a_sub_zones['sub_id'] . "\">" . $a_sub_zones['sub_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">IP Address Type: <select name="ip_type">
<?php
  $q_string  = "select ip_id,ip_name ";
  $q_string .= "from ip_types ";
  $q_string .= "order by ip_name ";
  $q_ip_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_ip_types = mysqli_fetch_array($q_ip_types)) {
    print "<option value=\"" . $a_ip_types['ip_id'] . "\">" . $a_ip_types['ip_name'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="ip_description" size="50"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
