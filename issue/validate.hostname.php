<?php
# Script: validate.hostname.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include ('settings.php');
  $called = 'yes';
  include ($Loginpath . '/check.php');
  include ($Sitepath . '/function.php');

  $formVars['server'] = clean($_GET['server'], 60);

  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_name = \"" . $formVars['server'] . "\" and inv_status = 0";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  if ($a_inventory['inv_name'] == $formVars['server'] && $formVars['server'] != '') {
?>
    document.newissue.clone.disabled = false;
    if (navigator.appName == "Microsoft Internet Explorer") {
      document.getElementById('system').className = "ui-widget-content";
    } else {
      document.getElementById('system').setAttribute("class","ui-widget-content");
    }
    document.newissue.id.value = '<?php print $a_inventory['inv_id']; ?>';
<?php
  } else {
?>
    document.newissue.clone.disabled = true;
    if (navigator.appName == "Microsoft Internet Explorer") {
      document.getElementById('system').className = "ui-state-error";
    } else {
      document.getElementById('system').setAttribute("class","ui-state-error");
    }
    document.newissue.id.value = '0';
<?php
  }
?>
