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
<title>City/County Editor</title>

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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&ct_city="       + encode_URI(af_form.ct_city.value);
  af_url += "&ct_state="      + af_form.ct_state.value;
  af_url += "&ct_clli="       + encode_URI(af_form.ct_clli.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&ct_city="       + encode_URI(uf_form.ct_city.value);
  uf_url += "&ct_state="      + uf_form.ct_state.value;
  uf_url += "&ct_clli="       + encode_URI(uf_form.ct_clli.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('city.mysql.php?update=-1');
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
          show_file('city.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add City/County",
        click: function() {
          attach_file('city.mysql.php', 0);
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
          show_file('city.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update City",
        click: function() {
          update_file('city.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add City/County",
        click: function() {
          update_file('city.mysql.php', 0);
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
  <th class="ui-state-default">City/County Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('city-help');">Help</a></th>
</tr>
</table>

<div id="city-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>There isn't much need to describe what a city or county is. The CLLI code is something 
telecoms have used to have a short, unique code to identify a city or county. </p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add City/County"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">City/County Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('city-listing-help');">Help</a></th>
</tr>
</table>

<div id="city-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>City/County Listing</strong></p>

<p>This page lists all the defined cities and counties which are used when editing the address books.</p>

<p>To edit a City or County, click on the entry in the listing. A dialog box will be displayed where you
can edit the current entry, or if there's some change you wish to make, you can add a new
City or County.</p>

<p>To add a new City or County, click the Add City/County button. A dialog box will be displayed where you can
add the necessary information and then save the new City or County.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add City/County">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">City/County: <input type="text" name="ct_city" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">State: <select name="ct_state">
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
</tr>
<tr>
  <td class="ui-widget-content">CLLI Code: <input type="text" name="ct_clli" size="10"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update City/County">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">City/County: <input type="text" name="ct_city" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">State: <select name="ct_state">
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
</tr>
<tr>
  <td class="ui-widget-content">CLLI Code: <input type="text" name="ct_clli" size="10"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
