<?php
# Script: organization.php
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

  $package = "organization.php";

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
<title>Organization Editor</title>

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
  var answer = confirm("Delete this Organization?")

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

  af_url += "&org_name="       + encode_URI(af_form.org_name.value);
  af_url += "&org_manager="    + af_form.org_manager.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&org_name="      + encode_URI(uf_form.org_name.value);
  uf_url += "&org_manager="   + uf_form.org_manager.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('organization.mysql.php?update=-1');
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
          show_file('organization.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Organization",
        click: function() {
          attach_file('organization.mysql.php', 0);
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
          show_file('organization.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Organization",
        click: function() {
          update_file('organization.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Organization",
        click: function() {
          update_file('organization.mysql.php', 0);
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
  <th class="ui-state-default">Organization Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('organization-help');">Help</a></th>
</tr>
</table>

<div id="organization-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>An Organization is the top level description of the entire business. It might be a simply company name 
or a corporation. The next level down is Business.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Organization"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Organization Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('organization-listing-help');">Help</a></th>
</tr>
</table>

<div id="organization-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Organization Listing</strong></p>

<p>This page lists all the defined Organizations which are used when defining the company structure.</p>

<p>To edit an Organization, click on the entry in the listing. A dialog box will be displayed where you 
can edit the current entry, or if there's some change you wish to make, you can add a new Organization.</p>

<p>To add a new Organization, click the Add Organization button. A dialog box will be displayed where 
you can add the necessary information and then save the new Organization.</p>

</div>

</div>


<span id="table_mysql"></span>

</div>

</div>


<div id="dialogCreate" title="Add Organization">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Organization Name: <input type="text" name="org_name" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="org_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>

<div id="dialogUpdate" title="Edit Organization">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Organization Name: <input type="text" name="org_name" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="org_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
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
