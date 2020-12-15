<?php
# Script: monitoring.toggle.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  include($RSDPpath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "monitoring.toggle.php";
    $formVars['id']       = clean($_GET['id'],       10);
    $formVars['flip']     = clean($_GET['flip'],     50);

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['flip'] == 'openview') {
        $q_string  = "select int_openview ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_openview']) {
          $a_interface['int_openview'] = 0;
          $display = '--';
        } else {
          $a_interface['int_openview'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_openview = " . $a_interface['int_openview'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('ov_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'nagios') {
        $q_string  = "select int_nagios ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_nagios']) {
          $a_interface['int_nagios'] = 0;
          $display = '--';
        } else {
          $a_interface['int_nagios'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_nagios = " . $a_interface['int_nagios'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('nag_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'ping') {
        $q_string  = "select int_ping ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_ping']) {
          $a_interface['int_ping'] = 0;
          $display = '--';
        } else {
          $a_interface['int_ping'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_ping = " . $a_interface['int_ping'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('ping_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'ssh') {
        $q_string  = "select int_ssh ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_ssh']) {
          $a_interface['int_ssh'] = 0;
          $display = '--';
        } else {
          $a_interface['int_ssh'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_ssh = " . $a_interface['int_ssh'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('ssh_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'http') {
        $q_string  = "select int_http ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_http']) {
          $a_interface['int_http'] = 0;
          $display = '--';
        } else {
          $a_interface['int_http'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_http = " . $a_interface['int_http'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('http_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'ftp') {
        $q_string  = "select int_ftp ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_ftp']) {
          $a_interface['int_ftp'] = 0;
          $display = '--';
        } else {
          $a_interface['int_ftp'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_ftp = " . $a_interface['int_ftp'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('ftp_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'smtp') {
        $q_string  = "select int_smtp ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_smtp']) {
          $a_interface['int_smtp'] = 0;
          $display = '--';
        } else {
          $a_interface['int_smtp'] = 1;
          $display = 'Yes';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_smtp = " . $a_interface['int_smtp'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('smtp_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }


      if ($formVars['flip'] == 'cfg2html') {
        $q_string  = "select int_cfg2html ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_interface = mysqli_fetch_array($q_interface);

        if ($a_interface['int_cfg2html']) {
          $a_interface['int_cfg2html'] = 0;
          $display = 'Check';
        } else {
          $a_interface['int_cfg2html'] = 1;
          $display = '--';
        }

        $q_string  = "update interface ";
        $q_string .= "set ";
        $q_string .= "int_smtp = " . $a_interface['int_cfg2html'] . " ";
        $q_string .= "where int_id = " . $formVars['id'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

        print "document.getElementById('cfg_" . $formVars['id'] . "').innerHTML = '<u>" . $display . "</u>';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
