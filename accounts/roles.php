<?php
# Script: roles.php
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

  $package = "roles.php";

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
<title>Role Editor</title>

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
  var answer = confirm("Delete this Role?")

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

  af_url += "&role_name=" + encode_URI(af_form.role_name.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&role_name=" + encode_URI(uf_form.role_name.value);

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('roles.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 150,
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
          show_file('roles.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Role",
        click: function() {
          attach_file('roles.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 150,
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
          show_file('roles.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Role",
        click: function() {
          update_file('roles.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Role",
        click: function() {
          update_file('roles.mysql.php', 0);
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
  <th class="ui-state-default">Role Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('role-help');">Help</a></th>
</tr>
</table>

<div id="role-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Roles are part of a Role Based Access Control (RBAC) system. It lets you create a user without any permissions 
and a role based on permission levels such as Create, Rename, Update, and Delete (CRUD). Then you can connect a 
user with a role to give them sufficient access to view, edit, or administer the specific item.</p>

<p>At the moment the Inventory doesn't support RBAC however it's a future plan.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Role"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Role Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('role-listing-help');">Help</a></th>
</tr>
</table>

<div id="role-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Role Listing</strong></p>

<p>This page lists all the defined Roles which are used for access control.</p>

<p>To edit a Role, click on the entry in the listing. A dialog box will be displayed where 
you can edit the current entry, or if there's some change you wish to make, you can add 
a new Role.</p>

<p>To add a new Role, click the Add Role button. A dialog box will be displayed where you 
can add the necessary information and then save the new Role.</p>


</div>

</div>


<span id="table_mysql"></span>

</div>

</div>


<div id="dialogCreate" title="Add Role">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Role: <input type="text" name="role_name" size="60"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Role">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Role: <input type="text" name="role_name" size="60"></td>
</tr>
</table>

</form>

</div>




<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
