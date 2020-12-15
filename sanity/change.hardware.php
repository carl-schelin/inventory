<?php
# Script: change.hardware.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# Update all the hardware associated with 'id' to match inv_manager and inv_product
# Pass the inv_id for the server.
# Get the inv_manager and inv_product.
# Update all the hardware with inv_manager and inv_product

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  $formVars['id']        = clean($_GET['id'],       10);

# update all the hardware to match the inv_manager of the id
  if (check_userlevel($db, $AL_Admin)) {
    $q_string  = "select inv_manager,inv_product ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_id = " . $formVars['id'] . " ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) > 0) {
      $a_inventory = mysqli_fetch_array($q_inventory);

      $q_string  = "update hardware "; 
      $q_string .= "set hw_group = " . $a_inventory['inv_manager'] . ",hw_product = " . $a_inventory['inv_product'] . " ";
      $q_string .= "where hw_companyid = " . $formVars['id'] . " ";
      $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    }
  }

?>
