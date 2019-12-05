<?php
# Script: support.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "support.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Support Contracts</title>

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
  var answer = confirm("Delete this Support Contract?")

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
  var af_form = document.support;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&sup_company="      + encode_URI(af_form.sup_company.value);
  af_url += "&sup_phone="        + encode_URI(af_form.sup_phone.value);
  af_url += "&sup_email="        + encode_URI(af_form.sup_email.value);
  af_url += "&sup_web="          + encode_URI(af_form.sup_web.value);
  af_url += "&sup_contract="     + encode_URI(af_form.sup_contract.value);
  af_url += "&sup_wiki="         + encode_URI(af_form.sup_wiki.value);
  af_url += "&sup_hwresponse="   + af_form.sup_hwresponse.value;
  af_url += "&sup_swresponse="   + af_form.sup_swresponse.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('support.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddSupport' ).click(function() {
    $( "#dialogSupport" ).dialog('open');
  });

  $( "#dialogSupport" ).dialog({
    autoOpen: false,
    modal: true,
    height: 250,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogSupport" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('support.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Support Contract",
        click: function() {
          attach_file('support.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Support Contract",
        click: function() {
          attach_file('support.mysql.php', 0);
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

<div id="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Support Contract Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('support-help');">Help</a></th>
</tr>
</table>

<div id="support-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Support Contract Record</strong> - Save any changes to this form.</li>
    <li><strong>Add Support Contract</strong> - Add new Support Contract details.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddSupport" value="Add Support"></td>
</tr>
</table>

</form>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>

<div id="dialogSupport" title="Support Contract Form">

<form name="support">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Support Contract Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Company: <input type="text" name="sup_company" size="20"></td>
  <td class="ui-widget-content">Hardware Response: <select name="sup_hwresponse">
<option value="0">Unsupported</option>
<?php
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from supportlevel ";
  $q_string .= "order by slv_value";
  $q_supportlevel = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_supportlevel = mysql_fetch_array($q_supportlevel)) {
    print "<option value=\"" . $a_supportlevel['slv_id'] . "\">" . htmlspecialchars($a_supportlevel['slv_value']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Software Response: <select name="sup_swresponse">
<option value="0">Unsupported</option>
<?php
  $q_string  = "select slv_id,slv_value ";
  $q_string .= "from supportlevel ";
  $q_string .= "order by slv_value";
  $q_supportlevel = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));
  while ($a_supportlevel = mysql_fetch_array($q_supportlevel)) {
    print "<option value=\"" . $a_supportlevel['slv_id'] . "\">" . htmlspecialchars($a_supportlevel['slv_value']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Phone: <input type="text" name="sup_phone" size="20"></td>
  <td class="ui-widget-content">E-Mail: <input type="email" name="sup_email" size="20"></td>
  <td class="ui-widget-content">Contract #: <input type="text" name="sup_contract" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Company Web Site: <input type="text" name="sup_web" size="70"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Wiki Site: <input type="text" name="sup_wiki" size="70"></td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
