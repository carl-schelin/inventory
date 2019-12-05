<?php
# Script: image.upload.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "image.upload.php";

    if (check_userlevel($AL_Edit)) {
      $formVars['id'] = clean($_SESSION['uid'], 10);
      $errorString = "";

      if (!isset($_FILES['upload']['error']) || is_array($_FILES['upload']['error'])) {
        $errorString .= "Invalid parameters.";
      }

      switch ($_FILES['upload']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            $errorString .= "No file sent.";
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $errorString .= "Exceeded filesize limit.";
            break;
        default:
            $errorString .= "Unknown errors.";
            break;
      }

      if ($_FILES['upload']['size'] > 100000) {
          $errorString .= "Exceeded filesize limit.";
      }

      $finfo = new finfo(FILEINFO_MIME_TYPE);
      if (false === $ext = array_search(
        $finfo->file($_FILES['upload']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ),
        true
      )) {
          $errorString .= "Invalid file format.";
      }

      if (file_exists($Pictureroot . "/" . $_FILES['upload']['name'])) {
        $errorString .= $_FILES['upload']['name'] . " already exists.";
      }

      if (!empty($errorString)) {
?>
<!DOCTYPE HTML>
<html>
<head>

<title>Image Upload Fail</title>

</head>
<body bgcolor="white">

<h3>Image Upload Errors</h3>

<?php print $errorString; ?>

<br><a href="image.php">Return to the Image Management page</a>
<br><a href="<?php print $Sitepath; ?>">Return Home</a>

</body>
</html>
<?php
        exit;
      }

      $target = $Picturepath . "/" . $_FILES['upload']['name'];

      if (move_uploaded_file($_FILES['upload']['tmp_name'], $target) ) {

        $lastid = 0;
        $q_string = 
          "img_title   = \"" . "Unknown" . "\"," . 
          "img_file    = \"" . $_FILES['upload']['name'] . "\"," . 
          "img_date    = \"" . date('Y-m-d') . "\"," . 
          "img_owner   =   " . $formVars['id'];
        $query = "insert into images set img_id = NULL," . $q_string;
        mysql_query($query) or die($query . ": " . mysql_error());

        $lastid = last_insert_id();

        header("Location: image.php?id=" . $lastid);
      } else {
?>
<!DOCTYPE HTML>
<html>
<head>

<title>Image Upload Fail</title>

</head>
<body bgcolor="white">

<h3>Image Upload Errors</h3>

<p>Sorry, there was a problem uploading your file.</p>

<br><a href="image.php">Return to the Image Management page</a>
<br><a href="<?php print $Sitepath; ?>">Return Home</a>

</body>
</html>
<?php
      }
    }
  }

?>
