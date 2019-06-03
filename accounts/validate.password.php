<?php
# Script: validate.password.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $formVars['password'] = clean($_GET['password'],  32);
  $formVars['reenter']  = clean($_GET['reenter'],   32);

  $class = "ui-widget-content";
  print "document.user.update.disabled = false;";
  if ($formVars['password'] != $formVars['reenter']) {
    $class = "ui-state-error";
    print "document.user.update.disabled = true;";
  }

  if (strlen($formVars['password']) < 5) {
    $class = "ui-state-error";
    print "document.user.update.disabled = true;";
  }
  
  if (strlen($formVars['password']) == 0 && strlen($formVars['reenter']) == 0) {
    $class = "ui-widget-content";
    print "document.user.update.disabled = false;";
  }
  
?>

if (navigator.appName == "Microsoft Internet Explorer") {
  document.getElementById('password').className = "<?php print $class; ?>";
} else {
  document.getElementById('password').setAttribute("class","<?php print $class; ?>");
}
if (navigator.appName == "Microsoft Internet Explorer") {
  document.getElementById('reenter').className = "<?php print $class; ?>";
} else {
  document.getElementById('reenter').setAttribute("class","<?php print $class; ?>");
}

