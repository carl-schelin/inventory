<?php
# Script: country.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "country.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Country table");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Countries</title>

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
  var answer = confirm("Delete this Country?")

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
  var af_form = document.country;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&cn_country="       + encode_URI(af_form.cn_country.value);
  af_url += "&cn_acronym="       + encode_URI(af_form.cn_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('country.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickAddCountry' ).click(function() {
    $( "#dialogCountry" ).dialog('open');
  });

  $( "#dialogCountry" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCountry" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('country.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Country",
        click: function() {
          attach_file('country.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Country",
        click: function() {
          attach_file('country.mysql.php', 0);
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
  <th class="ui-state-default">Country Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('country-help');">Help</a></th>
</tr>
</table>

<div id="country-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Country</strong> - Save any changes to this form.</li>
    <li><strong>Add Country</strong> - Create a new country record. You can copy an existing country by editing it, changing a field and saving it again.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddCountry" value="Add Country"></td>
</tr>
</table>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

<div id="dialogCountry" title="Country Form">

<form name="country">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Country Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="cn_acronym" size="10"></td>
  <td class="ui-widget-content">Country: <input type="text" name="cn_country" size="25"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
