<?php
# Script: image.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . "/check.php");
  include($Sitepath . "/function.php");

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "image.php";

  logaccess($db, $_SESSION['uid'], $package, "Managing Image files");

  $formVars['id'] = 0;
  $formVars['img_title'] = '';
  $formVars['img_file'] = '';
  $formVars['img_date'] = '';
  $formVars['img_owner'] = 0;
  $display = "display: none";
  $update = "disabled";

  if (isset($_GET['id'])) {
    $formVars['id'] = clean($_GET['id'], 10);

    $q_string  = "select img_title,img_file,img_date,img_owner ";
    $q_string .= "from inv_images ";
    $q_string .= "where img_id = " . $formVars['id'];
    $q_images = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_images = mysqli_fetch_array($q_images);

    $formVars['img_title'] = $a_images['img_title'];
    $formVars['img_file']  = $a_images['img_file'];
    $formVars['img_date']  = $a_images['img_date'];
    $formVars['img_owner'] = $a_images['img_owner'];

    $display = "display: block";
    $update = '';
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage Image Files</title>

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
  var answer = confirm("This step deletes the image file and all associated information.\n\nDelete this Image?")

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

  af_url += "&img_title="     + encode_URI(af_form.img_title.value);
  af_url += "&img_file="      + encode_URI(af_form.img_file.value);
  af_url += "&img_date="      + encode_URI(af_form.img_date.value);
  af_url += "&img_facing="    + radio_Loop(af_form.img_facing, 2);
  af_url += "&img_owner="     + af_form.img_owner.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&img_title="     + encode_URI(uf_form.img_title.value);
  uf_url += "&img_file="      + encode_URI(uf_form.img_file.value);
  uf_url += "&img_date="      + encode_URI(uf_form.img_date.value);
  uf_url += "&img_facing="    + radio_Loop(uf_form.img_facing, 2);
  uf_url += "&img_owner="     + uf_form.img_owner.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}


function clear_fields() {
  show_file('image.mysql.php?update=-1&id=<?php print $formVars['id']; ?>');
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
          show_file('image.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Image",
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
    height: 600,
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
        text: "Update Image",
        click: function() {
          update_file('image.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Image",
        click: function() {
          update_file('image.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( '#clickUpload' ).click(function() {
    $( "#dialogUpload" ).dialog('open');
  });

  $( "#dialogUpload" ).dialog({
    autoOpen: false,
    modal: true,
    height: 150,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUpload" ).hide();
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
        text: "Upload Image",
        click: function() {
          attach_file('image.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogServers" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogServers" ).hide();
    },
    buttons: [
      {
        text: "Close",
        click: function() {
          show_file('image.mysql.php?update=-1');
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

<div id="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Image Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('image-help');">Help</a></th>
</tr>
</table>


<div id="image-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Images are associated with servers to assist with properly identifying the location in the racks to reduce the chance 
of an error when remote hands are working on devices.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickUpload" value="Upload Image"> <input type="button" id="clickCreate" value="Add Image"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Image Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('image-listing-help');">Help</a></th>
</tr>
</table>

<div id="image-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Image Listing</strong></p>

<p>This page lists all the images that have been uploaded to the Inventory.</p>

<p>To add a new Image, you need to upload the image itself using the Upload Image button. Once an image is uploaded, 
click the Add Image button. This will bring up a dialog box which you can then use to create a new Image record.</p>

<p>To edit an existing Image, click on the entry in the listing. A dialog box will be displayed where you can 
edit the current entry, or if there is a small difference, you can make changes and add a new Image record. In 
addition, the picture you selected will be displayed to help with describing the image. If you need to see a 
larger image, you can right click and View Image.</p>

<p>Note that under the Members column is a number which indicates the number of servers that have identified 
this image as describing the server. You cannot remove a server until clear the selection from the server. 
Clicking on the link will bring up a dialog box that displays the servers that are associated with the image.</p>

<p>An image that is <span class="ui-state-highlight">highlighted</span> indicates the image name doesn't exist 
on the server. It can of course be uploaded after the fact.</p>

</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>



<div id="dialogServers" title="List Servers">

<p class="delete"><textarea id="image_memo" cols="80" rows="7"></textarea></p>

</div>


<div id="dialogUpload" title="Upload Image">

<form name="manager" enctype="multipart/form-data" action="image.upload.php" method="POST">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Select a file to upload: <input type="hidden" name="MAX_FILE_SIZE" value="10000000"> <input type="file" name="upload"></td>
</tr>
</table>

</form>

</div>



<div id="dialogCreate" title="Add Image">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="img_title" value="<?php print $formVars['img_title']; ?>" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Name: <input type="text" name="img_file" value="<?php print $formVars['img_file']; ?>" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Date: <input type="date" name="img_date" value="<?php print $formVars['img_date']; ?>" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Owner: <select name="img_owner">
<?php
  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    if ($a_inv_users['usr_id'] == $formVars['img_owner']) {
      print "<option selected value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
    }
  }

?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Facing: <label><input type="radio" value="0" checked name="img_facing"> Rear</label> <label><input type="radio" value="1" name="img_facing"> Front<label></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Image">

<form name="formUpdate">
<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="img_title" value="<?php print $formVars['img_title']; ?>" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Name: <input type="text" name="img_file" value="<?php print $formVars['img_file']; ?>" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Date: <input type="date" name="img_date" value="<?php print $formVars['img_date']; ?>" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Owner: <select name="img_owner">
<?php
  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from inv_users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
    if ($a_inv_users['usr_id'] == $formVars['img_owner']) {
      print "<option selected value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_inv_users['usr_id'] . "\">" . $a_inv_users['usr_last'] . ", " . $a_inv_users['usr_first'] . "</option>\n";
    }
  }

?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Image Facing: <label><input type="radio" value="0" checked name="img_facing"> Rear</label> <label><input type="radio" value="1" name="img_facing"> Front<label></td>
</tr>
<tr>
<td class="ui-widget-content delete" id="image_name"></td>
</tr>
</table>

</form>

</div>










<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
