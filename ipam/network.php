<?php
# Script: network.php
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

  $package = "network.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

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
<title>Network Editor</title>

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
  var answer = confirm("Delete this Network entry?")

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

  af_url += "&net_ipv4="        + encode_URI(af_form.net_ipv4.value);
  af_url += "&net_ipv6="        + encode_URI(af_form.net_ipv6.value);
  af_url += "&net_mask="        + af_form.net_mask.value;
  af_url += "&net_zone="        + af_form.net_zone.value;
  af_url += "&net_location="    + af_form.net_location.value;
  af_url += "&net_vlan="        + encode_URI(af_form.net_vlan.value);
  af_url += "&net_description=" + encode_URI(af_form.net_description.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.updateDialog;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&net_ipv4="        + encode_URI(uf_form.net_ipv4.value);
  uf_url += "&net_ipv6="        + encode_URI(uf_form.net_ipv6.value);
  uf_url += "&net_mask="        + uf_form.net_mask.value;
  uf_url += "&net_zone="        + uf_form.net_zone.value;
  uf_url += "&net_location="    + uf_form.net_location.value;
  uf_url += "&net_vlan="        + encode_URI(uf_form.net_vlan.value);
  uf_url += "&net_description=" + encode_URI(uf_form.net_description.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('network.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });


  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 640,
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
          show_file('network.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Network",
        click: function() {
          attach_file('network.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 640,
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
          show_file('network.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Network",
        click: function() {
          update_file('network.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Network",
        click: function() {
          update_file('network.mysql.php', 0);
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

<form name="network">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Network Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('network-help');">Help</a></th>
</tr>
</table>

<div id="network-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A network defined here provides overall information about the network such as the network mask, location, 
vlan, and what network zone the network belongs to. This information will be used to define some elements of 
an IP address that is then assigned to a device.</p>

<p>Click the Help link to the upper right to open and close any help screen.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Network"></td>
</tr>
</table>

</form>


<span id="table_mysql"></span>

</div>


<div id="dialogCreate" title="Add Network">

<form name="createDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
<tr>
  <td class="ui-widget-content">IPv4 Network: <input type="text" name="net_ipv4" size="18"></td>
</tr>
<tr>
  <td class="ui-widget-content">IPv6 Network: <input type="text" name="net_ipv6" size="48"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Description: <input type="text" name="net_description" size="70"></td>
</tr>
<tr>
  <td class="ui-widget-content">Netmask: <select name="net_mask">
<?php
    for ($i = 0; $i < 129; $i++) {
      if ($i > 32) {
        print "<option value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
      } else {
        if ($i == 24) {
          print  "<option selected value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
        } else {
          print "<option value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
        }
      }
    }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Network Zone: <select name="net_zone">
<?php
  $q_string  = "select zone_id,zone_zone ";
  $q_string .= "from net_zones ";
  $q_string .= "order by zone_zone ";
  $q_net_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_net_zones = mysqli_fetch_array($q_net_zones)) {
    print "<option value=\"" . $a_net_zones['zone_id'] . "\">" . $a_net_zones['zone_zone'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Location: <select name="net_location">
<?php
  $q_string  = "select loc_id,loc_name ";
  $q_string .= "from locations ";
  $q_string .= "order by loc_name ";
  $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_locations = mysqli_fetch_array($q_locations)) {
    print "<option value=\"" . $a_locations['loc_id'] . "\">" . $a_locations['loc_name'] . "</option>\n";
  }

?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">VLAN: <input type="text" name="net_vlan" size="10"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Network">

<form name="updateDialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
<tr>
  <td class="ui-widget-content">IPv4 Network: <input type="text" name="net_ipv4" size="18"></td>
</tr>
<tr>
  <td class="ui-widget-content">IPv6 Network: <input type="text" name="net_ipv6" size="48"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Description: <input type="text" name="net_description" size="70"></td>
</tr>
<tr>
  <td class="ui-widget-content">Netmask: <select name="net_mask">
<?php
    for ($i = 0; $i < 129; $i++) {
      if ($i > 32) {
        print "<option value=\"" . $i . "\">IPv6/" . $i . "</option>\n";
      } else {
        if ($i == 24) {
          print  "<option selected value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
        } else {
          print "<option value=\"" . $i . "\">" . createNetmaskAddr($i) . "/" . $i . "</option>\n";
        }
      }
    }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Network Zone: <select name="net_zone">
<?php
  $q_string  = "select zone_id,zone_zone ";
  $q_string .= "from net_zones ";
  $q_string .= "order by zone_zone ";
  $q_net_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_net_zones = mysqli_fetch_array($q_net_zones)) {
    print "<option value=\"" . $a_net_zones['zone_id'] . "\">" . $a_net_zones['zone_zone'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Location: <select name="net_location">
<?php
  $q_string  = "select loc_id,loc_name ";
  $q_string .= "from locations ";
  $q_string .= "order by loc_name ";
  $q_locations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_locations = mysqli_fetch_array($q_locations)) {
    print "<option value=\"" . $a_locations['loc_id'] . "\">" . $a_locations['loc_name'] . "</option>\n";
  }

?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">VLAN: <input type="text" name="net_vlan" size="10"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
