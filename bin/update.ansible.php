<?php
# Script: update.ansible.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

# root.cron: # update all RH 6 and 7 ansible setting to 1
# root.cron: 0 1 * * * /usr/local/bin/php /usr/local/httpd/bin/update.ansible.php > /dev/null 2>&1

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

# update inv_ansible on all systems where OS == Red Hat and version is 6 or 7
# future to enable ansible if system == 5
#        stripos($a_inventory['sw_software'], " 5.") !== false || 
# turns out even if we add the rpm, rh5 isn't working everywhere. Make it a manual update if someone wants to add it in.

  $q_string  = "select inv_id,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where sw_type = 'OS' ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if (stripos($a_inventory['sw_software'], "red hat") !== false || stripos($a_inventory['sw_software'], "centos") !== false ) {
      if (
        stripos($a_inventory['sw_software'], " 6.") !== false || 
        stripos($a_inventory['sw_software'], " 7.") !== false
      ) {

        $q_string  = "update inventory ";
        $q_string .= "set ";
        $q_string .= "inv_ansible = 1 ";
        $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " ";

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        }

      }
    }
 
  }

  mysqli_free_request($db);

?>
