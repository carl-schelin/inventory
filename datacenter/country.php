<?php
# Script: country.php
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

  $package = "country.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the Country table");

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
<title>Manage Countries</title>

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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&cn_country="       + encode_URI(af_form.cn_country.value);
  af_url += "&cn_acronym="       + encode_URI(af_form.cn_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&cn_country="       + encode_URI(uf_form.cn_country.value);
  uf_url += "&cn_acronym="       + encode_URI(uf_form.cn_acronym.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('country.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
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
          show_file('country.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add",
        click: function() {
          attach_file('country.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
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
          show_file('country.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update",
        click: function() {
          update_file('country.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add",
        click: function() {
          update_file('country.mysql.php', 0);
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
  <th class="ui-state-default">Country Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('country-help');">Help</a></th>
</tr>
</table>

<div id="country-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Countries are pretty self explanatory. In this case they're used in the Address Book to identify where a state or 
province is located.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Country"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Country Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('country-listing-help');">Help</a></th>
</tr>
</table>

<div id="country-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">


<p><strong>Country Listing</strong></p>

<p>This page lists all the defined countries which are used when editing 
the address books.</p>

<p>To edit a Country, click on the entry in the listing. A dialog box will 
be displayed where you can edit the current entry, or if there's some change you 
wish to make, you can add a new Country.</p>

<p>To add a new Country, click the Add Country button. A dialog box will be 
displayed where you can add the necessary information and then save the new 
Country.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Country Form">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Country: <input type="text" name="cn_country" size="25"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="cn_acronym" size="10"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Country Form">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Country: <input type="text" name="cn_country" size="25"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="cn_acronym" size="10"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
