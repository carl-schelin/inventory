<?php
# Script: index.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "index.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['projectid'])) {
      $formVars['id'] = clean($_GET['projectid'], 10);
    }

    if (check_userlevel(1)) {

      print "document.getElementById('table_mysql').innerHTML = '" . wait_Process("Please Wait") . "';\n";

      $tables = array(
        0 => "rsdp_server",
        1 => "rsdp_applications",
        2 => "rsdp_backups",
        3 => "rsdp_datacenter",
        4 => "rsdp_designed",
        5 => "rsdp_filesystem",
        6 => "rsdp_infosec",
        7 => "rsdp_infrastructure",
        8 => "rsdp_interface",
        9 => "rsdp_osteam",
        10 => "rsdp_platform",
        11 => "rsdp_san",
        12 => "rsdp_status",
        13 => "rsdp_accept",
        14 => "rsdp_check",
        15 => "rsdp_comments"
      );

      $key = array(
        0 => "rsdp_id",
        1 => "app_rsdp",
        2 => "bu_rsdp",
        3 => "dc_rsdp",
        4 => "san_rsdp",
        5 => "fs_rsdp",
        6 => "is_rsdp",
        7 => "if_rsdp",
        8 => "if_rsdp",
        9 => "os_rsdp",
        10 => "pf_rsdp",
        11 => "san_rsdp",
        12 => "st_rsdp",
        13 => "acc_rsdp",
        14 => "chk_rsdp",
        15 => "com_rsdp"
      );

      $q_string  = "select rsdp_id ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_project = " . $formVars['id'] . " ";
      $q_rsdp_server = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_rsdp_server) > 0) {
        while ($a_rsdp_server = mysql_fetch_array($q_rsdp_server)) {

          for ($i = 0; $i < count($tables); $i++) {
            logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from rsdp_server");

            $q_string  = "delete ";
            $q_string .= "from " . $tables[$i] . " ";
            $q_string .= "where " . $key[$i] . " = " . $formVars['id'];

            $delete = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          }

        }
        print "alert('Project and all servers and tasks deleted.');\n";

        print "clear_fields();\n";
      }
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>
