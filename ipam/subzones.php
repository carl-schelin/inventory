<?php
# Script: subzones.php
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

  $package = "subzones.php";

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
<title>IP Address Zone Editor</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($db, $AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Sub Zone?")

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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&sub_name="              + encode_URI(af_form.sub_name.value);
  af_url += "&sub_zone="              + af_form.sub_zone.value;
  af_url += "&sub_description="       + encode_URI(af_form.sub_description.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&sub_name="              + encode_URI(uf_form.sub_name.value);
  uf_url += "&sub_zone="              + uf_form.sub_zone.value;
  uf_url += "&sub_description="       + encode_URI(uf_form.sub_description.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('subzones.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
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
          show_file('subzones.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add IP Address Zone",
        click: function() {
          attach_file('subzones.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
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
          show_file('subzones.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update IP Address Zone",
        click: function() {
          update_file('subzones.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add IP Address Zone",
        click: function() {
          update_file('subzones.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">IP Address Zone Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('sub-help');">Help</a></th>
</tr>
</table>

<div id="sub-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Within Network Zones are network ranges that might be used for specific types of traffic. These are identified as IP Address Zones. For example you may have an IP Address that's specific to Windows or Linux servers. You might have an IP Address that's specific for Application traffic or Administration traffic.</p>

<p>Click the Help link at the upper right to open and close any Help window.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add IP Address Zone"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">IP Address Zone Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('sub-listing-help');">Help</a></th>
</tr>
</table>

<div id="sub-listing-help" style="display:none">

<div class="main-help ui-widget-content">

<p><strong>IP Address Zone Listing</strong></p>

<p>This page lists all the currently defined IP Address Zones.</p>

<p>To add a new IP Address Zone, click the <strong>Add IP Address Zone</strong> 
button on the upper right. A dialog box will be displayed that will let you 
enter the necessary information to create a new IP Address Zone listing.</p>

<p>If you want to edit an existing IP Address Zone, click the entry in the listing. 
This will bring up a dialog box where you can edit the current listing or, if you 
have an IP Address Zone with just a minor change, you can edit it and save it as a new 
listing.</p>

<p>Note that under the Members column is a number which indicates the number of IP 
addresses that are currently using this IP Address Zone. You cannot remove an IP Address 
Zone until this value is zero. Clicking on the number will take you to the IP Address 
editing screen where you can delete or change the IP Address Zone for that IP address.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add IP Address Zone">

<form name="formCreate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">IP Address Zone <input type="text" name="sub_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Network Zone: <select name="sub_zone">
<?php
  $q_string  = "select zone_id,zone_zone ";
  $q_string .= "from inv_net_zones ";
  $q_string .= "order by zone_zone ";
  $q_inv_net_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_net_zones = mysqli_fetch_array($q_inv_net_zones)) {
    print "<option value=\"" . $a_inv_net_zones['zone_id'] . "\">" . $a_inv_net_zones['zone_zone'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Description <input type="text" name="sub_description" size="50"></td>
</tr>
</table>

</form>

</div>

<div id="dialogUpdate" title="Update IP Address Zone">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">IP Address Zone <input type="text" name="sub_name" size="50"></td>
</tr>
<tr>
  <td class="ui-widget-content">Network Zone: <select name="sub_zone">
<?php
  $q_string  = "select zone_id,zone_zone ";
  $q_string .= "from inv_net_zones ";
  $q_string .= "order by zone_zone ";
  $q_inv_net_zones = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_net_zones = mysqli_fetch_array($q_inv_net_zones)) {
    print "<option value=\"" . $a_inv_net_zones['zone_id'] . "\">" . $a_inv_net_zones['zone_zone'] . "</option>\n";
  }
?></select></td>
</tr>
<tr>
  <td class="ui-widget-content">Description <input type="text" name="sub_description" size="50"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
