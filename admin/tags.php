<?php
# Script: tags.php
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

  $package = "tags.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

  $_SESSION['p_product']   = clean($_GET['product'],  10);
  $_SESSION['p_project']   = clean($_GET['project'],  10);
  $_SESSION['p_group']     = clean($_GET['group'],    10);
  $_SESSION['p_inwork']    = clean($_GET['inwork'],   10);
  $_SESSION['p_country']   = clean($_GET['country'],  10);
  $_SESSION['p_state']     = clean($_GET['state'],    10);
  $_SESSION['p_city']      = clean($_GET['city'],     10);
  $_SESSION['p_location']  = clean($_GET['location'], 10);
  $_SESSION['p_csv']       = clean($_GET['csv'],      10);

  if (isset($_GET['type'])) {
    $_SESSION['p_type'] = clean($_GET['type'], 10);
  } else {
    $_SESSION['p_type'] = '';
  }

  if ($_SESSION['p_product'] == '') {
    $_SESSION['p_product'] = 0;
  }
  if ($_SESSION['p_project'] == '') {
    $_SESSION['p_project'] = 0;
  }
  if ($_SESSION['p_group'] == '') {
    $_SESSION['p_group'] = 1;
  }
  if ($_SESSION['p_inwork'] == '') {
    $_SESSION['p_inwork'] = 'false';
  }
  if ($_SESSION['p_country'] == '') {
    $_SESSION['p_country'] = 0;
  }
  if ($_SESSION['p_state'] == '') {
    $_SESSION['p_state'] = 0;
  }
  if ($_SESSION['p_city'] == '') {
    $_SESSION['p_city'] = 0;
  }
  if ($_SESSION['p_location'] == '') {
    $_SESSION['p_location'] = 0;
  }
  if ($_SESSION['p_csv'] == '') {
    $_SESSION['p_csv'] = 'false';
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Tags</title>

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
  var answer = confirm("Delete this Tag?")

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
  var af_form = document.tags;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&tag_name="       + encode_URI(af_form.tag_name.value);
  af_url += "&tag_companyid="  + af_form.tag_companyid.value;
  af_url += "&tag_view="       + af_form.tag_view.value;
  af_url += "&tag_owner="      + af_form.tag_owner.value;
  af_url += "&tag_group="      + af_form.tag_group.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('tags.mysql.php?update=-1');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );

  $( '#clickAddTags' ).click(function() {
    $( "#dialogTags" ).dialog('open');
  });

  $( "#dialogTags" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width: 900,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogTags" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          show_file('tags.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Tags",
        click: function() {
          attach_file('tags.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Tags",
        click: function() {
          attach_file('tags.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });


  $( "#dialog-confirm" ).dialog({
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
      "Delete this tag": function() {
        $( this ).dialog( "close" );
      },
      "Delete all tags": function() {
        $( this ).dialog( "close" );
      },
      Cancel: function() {
        $( this ).dialog( "close" );
      }
    }
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
  <th class="ui-state-default">Tag Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('tag-help');">Help</a></th>
</tr>
</table>

<div id="tag-help" style="display: none">

<div class="main-help ui-widget-content">


Tag Management

This page is intended to provide a view into a list of servers based on the selection via filters both to show all the tags and to let you verify that all servers for the selection have received the necessary tags.

In the Tag View page, you can select a tag and it will show you all the servers that have that tag assigned to it but unless you know all the servers that should be 


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
  <td class="ui-widget-content button"><input type="button" id="clickAddTags" value="Add Tags"></td>
</tr>
</table>

</form>


<div id="tabs">

<ul>
  <li><a href="#private_tag">Private Tags</a></li>
  <li><a href="#group_tag">Group Tags</a></li>
  <li><a href="#public_tag">Public Tags</a></li>
</ul>


<div id="private_tag">

<span id="view_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="group_tag">

<span id="group_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="public_tag">

<span id="public_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>


</div>

</div>

<div id="dialogTags" title="Tag Form">

<form name="tags">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Tag Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Tag: <input type="text" name="tag_name" size="40"></td>
  <td class="ui-widget-content">Server: <select name="tag_companyid">
<option value="0">All Servers</option>
<?php
  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 ";
  if ($_SESSION['p_group'] > 0) {
    $q_string .= "and inv_manager = " . $_SESSION['p_group'] . " ";
  }
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
    print "<option value=\"" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . "</option>\n";
  }
?>
</select> Select All Servers to create a Master Tag.</td>
</tr>
<tr>
  <td class="ui-widget-content">View: <select name="tag_view">
<option value="0">Private Tag</option>
<option value="1">Group Tag</option>
<option value="2">Public Tag</option>
</select></td>
  <td class="ui-widget-content">Owner: <select name="tag_owner">
<option value="0">None</option>
<?php
  $q_string  = "select usr_id,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Group: <select name="tag_group">
<option value="0">None</option>
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3"><input type="checkbox" name="applytoall"> Add this Tag definition to all servers in this listing?</td>
</tr>
</table>

</form>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
