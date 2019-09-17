<?php
# Script: users.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "users.php";

  logaccess($formVars['uid'], $package, "Managing Data files.");

# if help has not been seen yet,
  if (show_Help('manageerrors')) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Managing Users</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_users( p_script_url ) {
  var answer = confirm("Delete this User?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_users( p_script_url, update ) {
  var au_form = document.users;
  var au_url;

  au_url  = "?update="        + update;
  au_url += "&id="            + au_form.id.value;

  au_url += "&mu_username="  + encode_URI(au_form.mu_username.value);
  au_url += "&mu_name="      + encode_URI(au_form.mu_name.value);
  au_url += "&mu_email="     + encode_URI(au_form.mu_email.value);
  au_url += "&mu_account="   + radio_Loop(au_form.mu_account, 3);
  au_url += "&mu_comment="   + encode_URI(au_form.mu_comment.value);
  au_url += "&mu_locked="    + au_form.mu_locked.checked;
  au_url += "&mu_ticket="    + encode_URI(au_form.mu_ticket.value);

  script = document.createElement('script');
  script.src = p_script_url + au_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('users.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddUsers' ).click(function() {
    $( "#dialogUsers" ).dialog('open');
  });

  $( "#dialogUsers" ).dialog({
    autoOpen: false,
    modal: true,
    height: 220,
    width: 1000,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUsers" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('users.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Users",
        click: function() {
          attach_users('users.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Users",
        click: function() {
          attach_users('users.mysql.php', 0);
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

<form name="dialog">


<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">User Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('help');">Help</a></th>
</tr>
</table>

<div id="help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<h2>Instructions</h2>

<p>This page has multiple tabs for use in managing various pieces of information for the users on systems.</p>

<ul>
  <li><strong>GECOS</strong> - This tab lists all the entries for the <strong>valid.email</strong> file which converts GECOS entries for users to the listed value.</li>
  <li><strong>Lockuser</strong> - This tab lists all the entries for the <strong>lockuser.dat</strong> file which automatically locks the listed users.</li>
  <li><strong>System Account Excludes</strong> - This tab lists all the system based entries for the <strong>users.exclude</strong> file which prevents the audit check.</li>
  <li><strong>Service Account Excludes</strong> - This tab lists all the service account based entries for the <strong>users.exclude</strong> file which prevents the audit check.</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddUsers" value="Add Users"></td>
</tr>
</table>

<div id="tabs">

<ul>
  <li><a href="#gecos">GECOS</a></li>
  <li><a href="#lockuser">Lockuser</a></li>
  <li><a href="#system">System Account Excludes</a></li>
  <li><a href="#service">Service Account Excludes</a></li>
</ul>

<div id="gecos">

<span id="gecos_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="lockuser">

<span id="lockuser_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="system">

<span id="system_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


<div id="service">

<span id="service_mysql"><?php print wait_Process('Waiting...'); ?></span>

</div>


</div>

</form>

</div>


<div id="dialogUsers" title="Manage Users">

<form name="users">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="2">Manage Users</th>
</tr>
<tr>
  <td class="ui-widget-content">Username: <input type="text" name="mu_username" size="20"></td>
  <td class="ui-widget-content">Lock User?: <input type="checkbox" name="mu_locked"></td>
</tr>
<tr>
  <td class="ui-widget-content">User's Name: <input type="text" name="mu_name" size="40"></td>
  <td class="ui-widget-content">User's Email: <input type="text" name="mu_email" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Account Type: <input type="radio" name="mu_account" value="0"> User <input type="radio" name="mu_account" value="1"> System <input type="radio" name="mu_account" value="2"> Service</td>
</tr>
<tr>
  <td class="ui-widget-content">Comment: <input type="text" name="mu_comment" size="60"></td>
  <td class="ui-widget-content">Ticket Info: <input type="text" name="mu_ticket" size="60"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
