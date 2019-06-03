<?php
  session_start(); 
  include('settings.php');
  include($Loginpath . '/functions/dbconn.php');
  include($Loginpath . '/functions/functions.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Inventory Management</title>

<link rel="stylesheet" href="<?php print $Loginroot; ?>/stylesheet.css" />

</head>
<body>

<div id="header">
    
<div id="title">

<h1>Inventory Login</h1>

</div>

</div>

<div id="main">

<?php
// User is already logged in, they don't need to view this page.

if (isset($_SESSION['username'])) {

  echo '<div class="error_message">Attention! You already have an account.</div>';
  echo "<h2>What to do now?</h2><br />";
  echo "Go <a href='javascript:history.go(-1)'>back</a> to the page you were viewing before this.</li>";

?>
</div>

<div id="footer"><a href="<?php print $Siteroot; ?>">Inventory Management</a></div>

</body>
</html>
<?php
  exit();
}

// Get POST vars.

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$username = $_POST['username'];
$email = $_POST['email'];

if (isset($_POST['new_user'])) {

  $fname     = $_POST['fname'];
  $lname     = $_POST['lname'];
  $username  = $_POST['username'];
  $email     = $_POST['email'];
  $password  = $_POST['password'];
  $password2 = $_POST['password_confirm'];
  $group     = $_POST['group'];

  if (trim($fname) == '') {
    $error = '<div class="error_message">Attention! You must enter your first name.</div>';
  } else if(trim($lname) == '') {
    $error = '<div class="error_message">Attention! You must enter your last name.</div>';
  } else if(trim($username) == '') {
    $error = '<div class="error_message">Attention! You must enter a user name.</div>';
  } else if($group == 0) {
    $error = '<div class="error_message">Attention! You must select a group.</div>';
  } else if(!isEmail($email)) {
    $error = '<div class="error_message">Attention! You have entered an invalid e-mail address, try again.</div>';
  }

  if ($password != $password2) {
    $error = '<div class="error_message">Attention! Your passwords did not match.</div>';
  }

  if (strlen($password) < 5) {
    $error = '<div class="error_message">Attention! Your password must be at least 5 characters.</div>';
  }

  $count = mysql_num_rows(mysql_query("select * from users where usr_name='".$username."'"));

  if ($count > 0) {
    $error = '<div class="error_message">Sorry, username already taken.</div>';
  }

  if ($error == '') {
    $q_string = "insert into users set " . 
      "usr_level    =  " . '4'              . "," . 
      "usr_first    = '" . $fname           . "'," . 
      "usr_last     = '" . $lname           . "'," . 
      "usr_email    = '" . $email           . "'," . 
      "usr_name     = '" . $username        . "'," . 
      "usr_passwd   = '" . MD5($password)   . "'," . 
      "usr_report   =  " . '1';
      
    $q_users = mysql_query($q_string) or die("Fatal error: " . mysql_error());

    echo "<h2>Success!</h2>";	
    echo "<div class='success_message'>Thank you for registering! Go to the <a href='" . $Siteroot . "'>Inventory Management</a> application and log in.</div>";

    echo "<h2>Your login details</h2>";

    echo "<ul class='success-reg'>";
    echo "<li><span class='success-info'><b>Name</b></span>$fname $lname</li>";
    echo "<li><span class='success-info'><b>Username</b></span>$username</li>";
    echo "<li><span class='success-info'><b>E-Mail</b></span>$email</li>";
    echo "<li><span class='success-info'><b>Password</b></span>*hidden*</li>";
    echo "</ul>";

    echo "<h2>What to do now?</h2><br />";
    echo "Go to the <a href='" . $Siteroot . "'>Inventory</a>.</li>";

// Notify the admin that a new member has arrived.
    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $group;
    $q_groups = mysql_query($q_string) or die(mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    $q_string  = "select usr_email ";
    $q_string .= "from users ";
    $q_string .= "where usr_level < 2";
    $q_users = mysql_query($q_string) or die("Fatal error: ".mysql_error());
    while ($a_users = mysql_fetch_array($q_users)) {
      $usermail = $a_users['usr_email'];
      $subject = "New member in Inventory Management";
      $body = "$fname $lname has created an account and requested access to the " . $a_groups['grp_name'] . " group and is currently waiting for confirmation.";
      mail($usermail, $subject, $body);
    }
  }
}

if(!isset($_POST['new_user']) || $error != '') {	

echo $error;

?>

<h2>Sign Up</h2>

<form name="login" action="" method="post">

<label>First / Last Name</label><input type="text" name="fname" value="<?php print $fname; ?>" style="width: 46%;" />&nbsp;
<input type="text" name="lname" value="<?php print $lname; ?>" style="width: 46%;" onchange="populate_email();" /><br />

<script type="text/javascript"> 

function toggle_username(userid) { 
  if (window.XMLHttpRequest) { 
    http = new XMLHttpRequest(); 
  } else if (window.ActiveXObject) { 
    http = new ActiveXObject("Microsoft.XMLHTTP"); 
  } 

  handle = document.getElementById(userid); 
  var url = 'ajax.php?'; 

  if (handle.value.length > 0) { 
    var fullurl = url + 'do=check_username_exists&username=' + encodeURIComponent(handle.value);
    http.open("GET", fullurl, true); 
    http.send(null); 
    http.onreadystatechange = statechange_username; 
  } else { 
    document.getElementById('username').className = ''; 
  } 
} 

function statechange_username() { 
  if (http.readyState == 4) { 
    var xmlObj = http.responseXML; 
    var html = xmlObj.getElementsByTagName('result').item(0).firstChild.data; 
    document.getElementById('username').className = html; 
  } 
} 

function populate_email() {
  var pe_doc = document.login;

  var pe_username = pe_doc.fname.value.substring(0,1) + pe_doc.lname.value;
  var pe_email = pe_doc.fname.value + '.' + pe_doc.lname.value + '@intrado.com';

  document.login.username.value = pe_username.toLowerCase();
  document.login.email.value = pe_email.toLowerCase();
  document.login.password.value = '';
  document.login.password_confirm.value = '';
}

</script> 

<label>Username / Group Membership</label><input id="username" type="text" name="username" style="width: 46%;" value="<?php print $username; ?>" onchange="toggle_username('username')" />&nbsp;
<select style="width: 49%;" name="group">
  <option value="0">Unassigned</option>
<?php 
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die(mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {
    print "  <option value=\"" . $a_groups['grp_id'] . "\">" . $a_groups['grp_name'] . "</option>\n";
  }
?>
</select><br /> 
<label>Email</label><input type="text" name="email" value="<?php print $email; ?>" /><br />
<label>Password</label><input type="password" name="password" value="<?php print $password; ?>" /><br />
<label>Confirm</label><input type="password" name="password_confirm" value="<?php print $password2; ?>" /><br /><br />

<input type="submit" value="Continue" name="new_user" />

</form>

<?php
  }
?>

</div>

<div id="footer"><a href="<?php print $Siteroot; ?>">Inventory Management</a></div>

</body>
</html>
