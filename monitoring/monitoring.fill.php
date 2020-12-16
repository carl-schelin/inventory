<?php
# Script: monitoring.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "monitoring.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from organization");

#+-----------------+---------+------+-----+---------+----------------+
#| Field           | Type    | Null | Key | Default | Extra          |
#+-----------------+---------+------+-----+---------+----------------+
#| mon_id          | int(10) | NO   | PRI | NULL    | auto_increment |
#| mon_interfaceid | int(10) | NO   |     | 0       |                |
#| mon_openview    | int(10) | NO   |     | 0       |                |
#| mon_nagios      | int(10) | NO   |     | 0       |                |
#| mon_type        | int(10) | NO   |     | 0       |                |
#| mon_active      | int(10) | NO   |     | 0       |                |
#| mon_group       | int(10) | NO   |     | 0       |                |
#| mon_user        | int(10) | NO   |     | 0       |                |
#| mon_notify      | int(10) | NO   |     | 0       |                |
#| mon_hours       | int(10) | NO   |     | 0       |                |
#+-----------------+---------+------+-----+---------+----------------+

      $q_string  = "select mon_openview,mon_nagios,mon_type,mon_active,mon_group,mon_user,mon_notify,mon_hours ";
      $q_string .= "from monitoring ";
      $q_string .= "where mon_id = " . $formVars['id'];
      $q_monitoring = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_monitoring = mysqli_fetch_array($q_monitoring);
      mysqli_free_result($q_monitoring);

      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";
      print "document.monitoring.mon_openview.value = '" . mysqli_real_escape_string($db, $a_monitoring['mon_openview']) . "';\n";

      print "document.monitoring.id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
