<?php
# Script: image.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . "/check.php");
  include($Sitepath . "/function.php");
  check_login(2);

  $package = "image.php";

  logaccess($formVars['uid'], $package, "Managing Image files");

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
    $q_string .= "from images ";
    $q_string .= "where img_id = " . $formVars['id'];
    $q_images = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_images = mysql_fetch_array($q_images);

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
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($AL_Admin)) {
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
  var af_form = document.images;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&img_title="     + encode_URI(af_form.img_title.value);
  af_url += "&img_file="      + encode_URI(af_form.img_file.value);
  af_url += "&img_date="      + encode_URI(af_form.img_date.value);
  af_url += "&img_facing="    + radio_Loop(af_form.img_facing, 2);
  af_url += "&img_owner="     + af_form.img_owner.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('image.mysql.php?update=-1&id=<?php print $formVars['id']; ?>');
}

$(document).ready( function() {
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="images">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('image-hide');">Image Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('image-help');">Help</a></th>
</tr>
</table>

<div id="image-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Image</strong> - Save any changes to this form.</li>
    <li><strong>Add Image</strong> - Create a new project record. You can copy an existing project by editing it, changing a field and saving it again.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="image-hide" style="<?php print $display; ?>">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="button" <?php print $update; ?> name="update" value="Update Image" onClick="javascript:attach_file('image.mysql.php', 1);hideDiv('image-hide');">
<input type="hidden" name="id" value="0">
<input type="button"                         name="addbtn" value="Add Image"    onClick="javascript:attach_file('image.mysql.php', 0);"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Image Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="img_title" value="<?php print $formVars['img_title']; ?>" size="60"></td>
  <td class="ui-widget-content">Image Name: <input type="text" name="img_file" value="<?php print $formVars['img_file']; ?>" size="40"></td>
  <td class="ui-widget-content">Image Date: <input type="text" name="img_date" value="<?php print $formVars['img_date']; ?>" size="20"></td>
  <td class="ui-widget-content">Image Owner: <select name="img_owner">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    if ($a_users['usr_id'] == $formVars['img_owner']) {
      print "<option selected value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
    } else {
      print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
    }
  }

?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">Image Facing: <label><input type="radio" value="0" checked name="img_facing"> Rear</label> <label><input type="radio" value="1" name="img_facing"> Front<label></td>
</tr>
</table>

<span id="image_mysql"></span>

</form>

</div>


<form name="manager" enctype="multipart/form-data" action="image.upload.php" method="POST">

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('file-hide');">File Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('file-help');">Help</a></th>
</tr>
</table>

<div id="file-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Upload Image</strong> - Upload the selected image. The image must be 100k or less in size and can be a .jpg, .gif, or .png image type.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="file-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="submit" name="addbtn" value="Upload Image"></td>
</tr>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">File Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Select a file to upload: <input type="hidden" name="MAX_FILE_SIZE" value="100000"> <input type="file" name="upload"></td>
</tr>
</table>

</div>

</form>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
