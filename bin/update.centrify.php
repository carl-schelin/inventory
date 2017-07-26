<?php
# Script: update.centrify.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

# root.cron: # update all rsdp entries to check 'centrify'
# root.cron: 0 1 * * * /usr/local/bin/php /usr/local/httpd/bin/update.centrify.php > /dev/null 2>&1

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  $debug = 'yes';

# update rsdp_centrify on all systems where inv_domain != ''

  $q_string  = "select inv_id,inv_rsdp ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_domain != '' ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    if ($a_inventory['inv_rsdp'] > 0) {
      $q_string  = "update rsdp_server ";
      $q_string .= "set ";
      $q_string .= "rsdp_centrify = 1 ";
      $q_string .= "where rsdp_id = " . $a_inventory['inv_rsdp'] . " ";

      if ($debug == 'yes') {
        print $q_string . "\n";
      } else {
        $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      }
    }
  }

?>
