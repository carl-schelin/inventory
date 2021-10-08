<?php
# Script: state.php
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

  $package = "state.php";

  logaccess($db, $_SESSION['uid'], $package, "Viewing the State table");

# if help has not been seen yet,
  if (show_Help($db, $Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>State Editor</title>

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
  var af_form = document.formUpdate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&st_acronym="    + encode_URI(af_form.st_acronym.value);
  af_url += "&st_state="      + encode_URI(af_form.st_state.value);
  af_url += "&st_country="    + af_form.st_country.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&st_acronym="    + encode_URI(uf_form.st_acronym.value);
  uf_url += "&st_state="      + encode_URI(uf_form.st_state.value);
  uf_url += "&st_country="    + uf_form.st_country.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('state.mysql.php?update=-1');
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
          show_file('state.mysql.php?update=-1');
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
          show_file('state.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update",
        click: function() {
          update_file('state.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add State",
        click: function() {
          update_file('state.mysql.php', 0);
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
  <th class="ui-state-default">State Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('state-help');">Help</a></th>
</tr>
</table>

<div id="state-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>There's not really a lot to describe here. Most folks know what a State is. The idea here is you create a chain 
where you only need to select the City when managing Address Books as the City will have the State and Country 
already associated with it.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add State"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">State Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('state-listing-help');">Help</a></th>
</tr>
</table>

<div id="state-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>State Listing</strong></p>

<p>This page lists all the defined States which are used when editing the address books.</p>

<p>To edit a State, click on the entry in the listing. A dialog box will be displayed where you 
can edit the current entry, or if there's some change you wish to make, you can add a new 
State.</p>

<p>To add a new State, click the Add State button. A dialog box will be displayed where you can 
add the necessary information and then save the new State.</p>

</div>

</div>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add State">

<form name="formUpdate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">State: <input type="text" name="st_state" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="st_acronym" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Country: <select name="st_country">
<?php
  $q_string  = "select cn_id,cn_country ";
  $q_string .= "from country ";
  $q_string .= "order by cn_country ";
  $q_country = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_country = mysqli_fetch_array($q_country)) {
    print "<option value=\"" . $a_country['cn_id'] . "\">" . $a_country['cn_country'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update State">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">State: <input type="text" name="st_state" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Acronym: <input type="text" name="st_acronym" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Country: <select name="st_country">
<?php
  $q_string  = "select cn_id,cn_country ";
  $q_string .= "from country ";
  $q_string .= "order by cn_country ";
  $q_country = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
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
