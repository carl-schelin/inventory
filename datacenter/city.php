<?php
# Script: city.php
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

  $package = "city.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the City table");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Data Center Locations</title>

<style type='text/css' title='currentStyle' media='screen'>
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
  var answer = confirm("Delete this city?")

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
  var af_form = document.cities;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&ct_city="       + encode_URI(af_form.ct_city.value);
  af_url += "&ct_state="      + af_form.ct_state.value;
  af_url += "&ct_clli="       + encode_URI(af_form.ct_clli.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('city.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddCity' ).click(function() {
    $( "#dialogCity" ).dialog('open');
  });

  $( "#dialogCity" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCity" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('city.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update City",
        click: function() {
          attach_file('city.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add City",
        click: function() {
          attach_file('city.mysql.php', 0);
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

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">City/County Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('city-help');">Help</a></th>
</tr>
</table>

<div id="city-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update City/County</strong> - Save any changes to this form.</li>
    <li><strong>Add City/County</strong> - Create a new city record. You can copy an existing city by editing it, changing a field and saving it again.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddCity" value="Add City"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<div id="dialogCity" title="City/County Form">

<form name="cities">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">City/County Form</th>
</tr>
<tr>
  <td class="ui-widget-content">City/County: <input type="text" name="ct_city" size="30"></td>
  <td class="ui-widget-content">State: <select name="ct_state">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select st_id,st_state ";
  $q_string .= "from states ";
  $q_string .= "order by st_state ";
  $q_states = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_states = mysqli_fetch_array($q_states)) {
    print "<option value=\"" . $a_states['st_id'] . "\">" . $a_states['st_state'] . "</option>\n";
  }

?>
</select></td>
  <td class="ui-widget-content">CLLI Code: <input type="text" name="ct_clli" size="10"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
