<?php
/**
 * Script: tags.php
 * Owner: Carl Schelin
 * Coding Standard 3.0 Applied
 * Description:
 */

require 'settings.php';
$called = 'no';
require $Loginpath . '/check.php';
require $Sitepath . '/function.php';

// connect to the database
$db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

check_login($db, $AL_Edit);

$package = "tags.php";

logaccess($db, $_SESSION['uid'], $package, "Accessing script");

$_SESSION['p_product']   = clean($_GET['product'],  10);
$_SESSION['p_group']     = clean($_GET['group'],    10);

if (isset($_GET['type'])) {
    $_SESSION['p_type'] = clean($_GET['type'], 10);
} else {
    $_SESSION['p_type'] = '';
}

if ($_SESSION['p_product'] == '') {
    $_SESSION['p_product'] = 0;
}
if ($_SESSION['p_group'] == '') {
    $_SESSION['p_group'] = 1;
}

// if help has not been seen yet,
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
<title>Edit Tags</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php require $Sitepath . "/mobile.php"; ?>
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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&tag_name="       + encode_URI(af_form.tag_name.value);
  af_url += "&tag_companyid="  + af_form.tag_companyid.value;
  af_url += "&tag_owner="      + af_form.tag_owner.value;
  af_url += "&tag_group="      + af_form.tag_group.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&tag_name="       + encode_URI(uf_form.tag_name.value);
  uf_url += "&tag_companyid="  + uf_form.tag_companyid.value;
  uf_url += "&tag_owner="      + uf_form.tag_owner.value;
  uf_url += "&tag_group="      + uf_form.tag_group.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('tags.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 250,
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
          show_file('tags.mysql.php?update=-1');
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

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 250,
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
          show_file('tags.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Tags",
        click: function() {
          update_file('tags.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Tags",
        click: function() {
          update_file('tags.mysql.php', 0);
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

<?php require $Sitepath . '/topmenu.start.php'; ?>
<?php require $Sitepath . '/topmenu.end.php'; ?>

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Tag Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('tag-help');">Help</a></th>
</tr>
</table>

<div id="tag-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Tag Management</p>

<p>This page is intended to provide a view into a list of servers based on the selection via filters 
both to show all the tags and to let you verify that all servers for the selection have received the 
necessary tags.</p>

<p>In the Tag View page, you can select a tag and it will show you all the servers that have that 
tag assigned to it but unless you know all the servers that should be </p>


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
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Tags"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Public Tag Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('public-listing-help');">Help</a></th>
</tr>
</table>

<div id="public-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">
<ul>
  <li><strong>Public Tag Listing</strong>
  <ul>
    <li><strong>Editing</strong> - Click on a contract to edit it.</li>
  </ul></li>
</ul>

</div>

</div>


<span id="public_mysql"><?php print wait_Process('Waiting...')?></span>

</div>

</div>




<div id="dialogCreate" title="Add Tag">

<form name="formCreate">

<?php include('tags.dialog.php'); ?>

</form>

</div>


<div id="dialogUpdate" title="EditTag">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<?php include('tags.dialog.php'); ?>

</form>

</div>



<?php require $Sitepath . '/footer.php'; ?>

</body>
</html>
