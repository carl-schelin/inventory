<?php
# Script: state.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "state.php";

  logaccess($_SESSION['uid'], $package, "Viewing the State table");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage States</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($AL_Admin)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this State?")

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
  var af_form = document.states;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&st_acronym="    + encode_URI(af_form.st_acronym.value);
  af_url += "&st_state="      + encode_URI(af_form.st_state.value);
  af_url += "&st_country="    + af_form.st_country.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('state.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddState' ).click(function() {
    $( "#dialogState" ).dialog('open');
  });

  $( "#dialogState" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogState" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('state.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update State",
        click: function() {
          attach_file('state.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add State",
        click: function() {
          attach_file('state.mysql.php', 0);
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
  <th class="ui-state-default">State Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('state-help');">Help</a></th>
</tr>
</table>

<div id="state-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update State</strong> - Save any changes to this form.</li>
    <li><strong>Add State</strong> - Create a new state record. You can copy an existing state by editing it, changing a field and saving it again.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddState" value="Add State"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<div id="dialogState" title="State Form">

<form name="states">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">State Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="st_acronym" size="10"></td>
  <td class="ui-widget-content">State: <input type="text" name="st_state" size="30"></td>
  <td class="ui-widget-content">Country: <select name="st_country">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select cn_id,cn_country ";
  $q_string .= "from country ";
  $q_string .= "order by cn_country ";
  $q_country = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_country = mysqli_fetch_array($q_country)) {
    print "<option value=\"" . $a_country['cn_id'] . "\">" . $a_country['cn_country'] . "</option>\n";
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
