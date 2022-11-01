<?php
# Script: business.php
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

  $package = "business.php";

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
<title>Business Editor</title>

<style type="text/css" title="currentStyle" media="screen">
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
  var answer = confirm("Delete this Business?")

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

  af_url += "&bus_name="          + encode_URI(af_form.bus_name.value);
  af_url += "&bus_organization="  + af_form.bus_organization.value;
  af_url += "&bus_manager="       + af_form.bus_manager.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&bus_name="          + encode_URI(uf_form.bus_name.value);
  uf_url += "&bus_organization="  + uf_form.bus_organization.value;
  uf_url += "&bus_manager="       + uf_form.bus_manager.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('business.mysql.php?update=-1');
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
          show_file('business.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Business",
        click: function() {
          attach_file('business.mysql.php', 0);
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
          show_file('business.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Business",
        click: function() {
          update_file('business.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Business",
        click: function() {
          update_file('business.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Business Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('business-help');">Help</a></th>
</tr>
</table>

<div id="business-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A <strong>Business</strong> is an entity in an Organization that has a specialized function. It develops its own 
strategy within the organization that aligns with company objectives.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Business"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Business Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('business-listing-help');">Help</a></th>
</tr>
</table>

<div id="business-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


<p><strong>Business Listing</strong></p>

<p>This page lists all the currently defined <strong>Business</strong> within this organization.</p>

<p>To add a new Business, click the <strong>Add Business</strong> button. This will bring up a dialog box which you can then 
use to create a new Business.</p>

<p>To editing an existing Business, click on an entry in the listing. A dialog box will be displayed where you can edit the current 
Business, or if there's a small difference, you can make changes and add a new Business.</p>

</div>

</div>


<span id="table_mysql"></span>

</div>


<div id="dialogCreate" title="Add Business">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Business Name: <input type="text" name="bus_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Organization: <select name="bus_organization">
<?php
  $q_string  = "select org_id,org_name ";
  $q_string .= "from inv_organizations ";
  $q_string .= "order by org_name ";
  $q_inv_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_organizations)) {
    while ($a_inv_organizations = mysqli_fetch_array($q_inv_organizations)) {
      print "<option value=\"" . $a_inv_organizations['org_id'] . "\">" . $a_inv_organizations['org_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="bus_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Business">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Business Name: <input type="text" name="bus_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Organization: <select name="bus_organization">
<?php
  $q_string  = "select org_id,org_name ";
  $q_string .= "from inv_organizations ";
  $q_string .= "order by org_name ";
  $q_inv_organizations = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_organizations)) {
    while ($a_inv_organizations = mysqli_fetch_array($q_inv_organizations)) {
      print "<option value=\"" . $a_inv_organizations['org_id'] . "\">" . $a_inv_organizations['org_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="bus_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
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
