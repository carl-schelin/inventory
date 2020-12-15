<?php
# Script: validate.hostname.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $formVars['server'] = clean($_GET['server'],       60);

  $q_string  = "select inv_id ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_name = \"" . $formVars['server'] . "\" and inv_status = 0";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_inventory = mysqli_fetch_array($q_inventory);

# search for the name in the database of live servers
# if the server is found then leave it at the default
# colors but don't permit the server to be added.

# if just the current server is found, change the color to show the 
# change is approved and activate the button that 
# permits the creation of the new server

  if (mysqli_num_rows($q_inventory) > 0) {
    print "document.getElementById('gohere').innerHTML = ' Server by that name already exists: <a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">" . $formVars['server'] . "</a>'\n";
?>
    document.edit.addnew.disabled = true;
    if (navigator.appName == "Microsoft Internet Explorer") {
      document.getElementById('edit_hn').className = "ui-widget-content";
    } else {
      document.getElementById('edit_hn').setAttribute("class","ui-widget-content");
    }
<?php
  } else {
    print "document.getElementById('gohere').innerHTML = '';\n";
?>
    document.edit.addnew.disabled = false;
    if (navigator.appName == "Microsoft Internet Explorer") {
      document.getElementById('edit_hn').className = "ui-state-highlight";
    } else {
      document.getElementById('edit_hn').setAttribute("class","ui-state-highlight");
    }
<?php
  }
?>
