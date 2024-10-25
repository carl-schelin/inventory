<?php
# Script: image.php
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

  $package = "image.php";

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
<title>Server Image Editor</title>

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
  var answer = confirm("Delete this Server Image?")

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

  af_url += "&img_name="         + encode_URI(af_form.img_name.value);
  af_url += "&img_hypervisor="   + af_form.img_hypervisor.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&img_name="        + encode_URI(uf_form.img_name.value);
  uf_url += "&img_hypervisor="  + uf_form.img_hypervisor.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('image.mysql.php?update=-1');
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
          show_file('image.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Server Image",
        click: function() {
          attach_file('image.mysql.php', 0);
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
          show_file('image.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Server Image",
        click: function() {
          update_file('image.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Server Image",
        click: function() {
          update_file('image.mysql.php', 0);
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
  <th class="ui-state-default">Server Image Manager</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('image-help');">Help</a></th>
</tr>
</table>

<div id="image-help" style="display: none">

<div class="main-help ui-widget-content">


</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Server Image"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Server Image Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('image-listing-help');">Help</a></th>
</tr>
</table>

<div id="image-listing-help" style="display: none">

<div class="main-help ui-widget-content">

<p><strong>Server Image Listing</strong></p>



<p>This page lists details about purchased software licenses.</p>

<p>To add a software license, click the Add License button. This will bring up a dialog box which you can then use to add 
a software license.</p>

<p>To edit a software license, click on an entry in the listing. A dialog box will be displayed where you can edit the 
software license, or if there is a small difference, you can make changes and add a new software license.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process("Please Wait"); ?></span>

</div>

</div>


<div id="dialogCreate" title="Add Server Image">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Server Image: <input type="text" name="img_name" size="65"></td>
</tr>
<tr>
  <td class="ui-widget-content">Destination: <select name="img_hypervisor">
  <option value="0">None</option>
<?php
  $q_string  = "select hv_id,hv_name ";
  $q_string .= "from inv_hypervisor ";
  $q_string .= "order by hv_name ";
  $q_inv_hypervisor = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inv_hypervisor = mysqli_fetch_array($q_inv_hypervisor)) {
    print "<option value=\"" . $a_inv_hypervisor['hv_id'] . "\">" . $a_inv_hypervisor['hv_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Server Image">

<form name="formUpdate">
<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Server Image: <input type="text" name="img_name" size="65"></td>
</tr>
<tr>
  <td class="ui-widget-content">Destination: <select name="img_hypervisor">
  <option value="0">None</option>
<?php
  $q_string  = "select hv_id,hv_name ";
  $q_string .= "from inv_hypervisor ";
  $q_string .= "order by hv_name ";
  $q_inv_hypervisor = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inv_hypervisor = mysqli_fetch_array($q_inv_hypervisor)) {
    print "<option value=\"" . $a_inv_hypervisor['hv_id'] . "\">" . $a_inv_hypervisor['hv_name'] . "</option>\n";
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
