<?php
# Script: department.php
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

  $package = "department.php";

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
<title>Department Editor</title>

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
  var answer = confirm("Delete this Department?")

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

  af_url += "&dep_name="       + encode_URI(af_form.dep_name.value);
  af_url += "&dep_business="   + af_form.dep_business.value;
  af_url += "&dep_manager="    + af_form.dep_manager.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&dep_name="        + encode_URI(uf_form.dep_name.value);
  uf_url += "&dep_business="    + uf_form.dep_business.value;
  uf_url += "&dep_manager="     + uf_form.dep_manager.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('department.mysql.php?update=-1');
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
          show_file('department.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Department",
        click: function() {
          attach_file('department.mysql.php', 0);
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
          show_file('department.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Department",
        click: function() {
          update_file('department.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Department",
        click: function() {
          update_file('department.mysql.php', 0);
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
  <th class="ui-state-default">Department Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('department-help');">Help</a></th>
</tr>
</table>

<div id="department-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A Department is a subset of a Business consisting of groups like HR, Operations, Engineering, and Development.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Department"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
  <th class="ui-state-default">Department Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('department-listing-help');">Help</a></th>
</tr>
</table>

<div id="department-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Department Listing</strong></p>

<p>This page lists all the currently defined Departments.</p>

<p>To add a new Department, click the Add Department button on the upper right. A dialog box will be displayed that will let 
you enter the necessary information to create a new Department.

<p>If you want to edit an existing Department, click the entry in the listing. This will bring up a dialog box where you can 
edit the current listing or, if you have a Department with just a minor change, you can edit it and save it as a new Department.</p>

<p>The Members column tells you how many Groups are associated with that Department. Before deleting a Department, all 
Groups will need to be removed or moved to another Department.</p>


</div>

</div>


<span id="table_mysql"></span>

</div>

</div>


<div id="dialogCreate" title="Add Department">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Department Name: <input type="text" name="dep_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business: <select name="dep_business">
<?php
  $q_string  = "select bus_id,bus_name ";
  $q_string .= "from inv_business ";
  $q_string .= "order by bus_name ";
  $q_inv_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_business)) {
    while ($a_inv_business = mysqli_fetch_array($q_inv_business)) {
      print "<option value=\"" . $a_inv_business['bus_id'] . "\">" . $a_inv_business['bus_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="bus_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Department">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Department Name: <input type="text" name="dep_name" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Business: <select name="dep_business">
<?php
  $q_string  = "select bus_id,bus_name ";
  $q_string .= "from inv_business ";
  $q_string .= "order by bus_name ";
  $q_inv_business = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_inv_business)) {
    while ($a_inv_business = mysqli_fetch_array($q_inv_business)) {
      print "<option value=\"" . $a_inv_business['bus_id'] . "\">" . $a_inv_business['bus_name'] . "</option>\n";
    }
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Manager: <select name="bus_manager">
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
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
