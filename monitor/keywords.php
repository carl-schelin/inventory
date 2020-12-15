<?php
# Script: keywords.php
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

  $package = "keywords.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  if (isset($_GET['sort'])) {
    $_SESSION['sort'] = clean($_GET['sort'], 20);
  } else {
    unset($_SESSION['sort']);
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Page Group</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Mark this Keyword Definition as Deleted?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    clear_fields();
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.keywords;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&key_description="       + encode_URI(af_form.key_description.value);
  af_url += "&key_page="              + encode_URI(af_form.key_page.value);
  af_url += "&key_email="             + encode_URI(af_form.key_email.value);
  af_url += "&key_annotate="          + encode_URI(af_form.key_annotate.value);
  af_url += "&key_critical_annotate=" + encode_URI(af_form.key_critical_annotate.value);
  af_url += "&key_deleted="           + af_form.key_deleted.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('keywords.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddKeyword' ).click(function() {
    $( "#dialogKeyword" ).dialog('open');
  });

  $( "#dialogKeyword" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogKeyword" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('keywords.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Keyword",
        click: function() {
          attach_file('keywords.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Keyword",
        click: function() {
          attach_file('keywords.mysql.php', 0);
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

<form name="dialog">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Keyword Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('keyword-help');">Help</a></th>
</tr>
</table>

<div id="keyword-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Keyword</strong> - Save any changes to this form.</li>
    <li><strong>Add Keyword</strong> - Add a new Keyword.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddKeyword" value="Add Keyword"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</form>


<div id="dialogKeyword" title="Keywords Form">

<form name="keywords">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="8">Keywords Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="key_description" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">Page: <input type="text" name="key_page" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">E-Mail: <input type="text" name="key_email" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">Annotation: <input type="text" name="key_annotate" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">Critical Annotation: <input type="text" name="key_critical_annotate" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content">Delete? <input type="checkbox" name="key_deleted"></td>
</tr>
</table>

</form>

</div>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
