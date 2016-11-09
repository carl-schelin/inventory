<?php
# Script: update.ansible.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

# update inv_ansible on all systems where OS == Red Hat and version is 6 or 7
# future to enable ansible if system == 5

  $q_string  = "select inv_id,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where sw_type = 'OS' ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    if (stripos($a_inventory['sw_software'], "red hat") !== false) {
      if (stripos($a_inventory['sw_software'], " 6") !== false || stripos($a_inventory['sw_software'], " 7") !== false ) {

        $q_string  = "update inventory ";
        $q_string .= "set ";
        $q_string .= "inv_ansible = 1 ";
        $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        }

      }
    }
 
  }

?>
