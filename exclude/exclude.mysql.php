<?php
# Script: exclude.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package            = "exclude.mysql.php";
    $formVars['update'] = clean($_GET['update'], 10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                 = clean($_GET['id'],             10);
        $formVars['ex_companyid']       = clean($_GET['ex_companyid'],   10);
        $formVars['ex_text']            = clean($_GET['ex_text'],       255);
        $formVars['ex_comments']        = clean($_GET['ex_comments'],   255);
        $formVars['ex_expiration']      = clean($_GET['ex_expiration'],  15);
        $formVars['ex_deleted']         = clean($_GET['ex_deleted'],     10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['ex_expiration'] == '') {
          $formVars['ex_expiration'] = "2038-01-01";
        }
        if ($formVars['ex_deleted'] == 'true') {
          $formVars['ex_deleted'] = $_SESSION['uid'];
        } else {
          $formVars['ex_deleted'] = 0;
        }

        if (strlen($formVars['ex_text']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "ex_companyid   =   " . $formVars['ex_companyid']   . "," .
            "ex_text        = \"" . $formVars['ex_text']        . "\"," .
            "ex_comments    = \"" . $formVars['ex_comments']    . "\"," .
            "ex_expiration  = \"" . $formVars['ex_expiration']  . "\"," .
            "ex_userid      =   " . $_SESSION['uid']            . "," .
            "ex_deleted     =   " . $formVars['ex_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into excludes set ex_id = NULL, " . $q_string;
            $message = "Exclude Message added.";
          }
          if ($formVars['update'] == 1) {
            $query = "update excludes set " . $q_string . " where ex_id = " . $formVars['id'];
            $message = "Exclude Message updated.";
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['ex_text']);

          mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));

          print "alert('" . $message . "');\n";
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }

      $output  = "<pre style=\"text-align: left;\" class=\"ui-widget-content\">\n";
      $output .= "#\n";
      $output .= "# messages.exclude file - by Carl Schelin\n";
      $output .= "#  This file contains a list of common message entries to be excluded from the raw\n";
      $output .= "#  messages files so that sysadmins can properly troubleshoot problems.\n";
      $output .= "#\n";
      $output .= "#  Commented lines are ignored by the chkmessages script so be verbose in explaining\n";
      $output .= "#  why a specific block of lines are in the exclude file.\n";
      $output .= "#\n";
      $output .= "#  At the end is a block of temporary excludes. This is used to remove data from the\n";
      $output .= "#  raw file when you're working on it or the problem won't be resolved in a few days\n";
      $output .= "#  or weeks.\n";
      $output .= "#\n";
      $output .= "#  Lines can be regex'd. If you are searching for a bracket or parenthese, you'll\n";
      $output .= "#  need to escape it \( \) \[ \] as they are used by regex to mean something.\n";
      $output .= "#\n";
      $output .= "#  Current regex's I'm using in this file:\n";
      $output .= "#   .*    Everything between the two search strings.\n";
      $output .= "#   [12]  Search for the number 1 or the number 2\n";
      $output .= "#   [0-9] Search for all numbers from 0 to 9\n";
      $output .= "#\n";
      $output .= "#\n";
      $output .= "# There is a 'messages.exclude' file in /usr/local/admin/install/intrado/etc\n";
      $output .= "# which contains server specific messages that need to be removed.\n";
      $output .= "# This file should be used for messages that occur across multiple servers\n";
      $output .= "# and smaller groupings of messages or temporary exclusions (please date the\n";
      $output .= "# entries and put a subject above it so we know when they can be removed).\n";

      $comment = '';
      $comment_test = '';
      $q_string  = "select ex_id,ex_companyid,inv_name,ex_text,ex_comments,ex_expiration,usr_name,ex_deleted ";
      $q_string .= "from excludes ";
      $q_string .= "left join inventory on inventory.inv_id = excludes.ex_companyid ";
      $q_string .= "left join users on users.usr_id = excludes.ex_userid ";
      $q_string .= "order by ex_comments,inv_name,ex_text ";
      $q_excludes = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_excludes = mysqli_fetch_array($q_excludes)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('exclude.fill.php?id=" . $a_excludes['ex_id'] . "');jQuery('#dialogExclude').dialog('open');return false;\">";
        $linkdel = "<input type=\"button\" value=\"Mark As Deleted\" onClick=\"javascript:delete_line('exclude.del.php?id=" . $a_excludes['ex_id'] . "');\">";
        $linkremove = "<input type=\"button\" value=\"Remove Rule\" onClick=\"javascript:delete_line('exclude.del.php?id=" . $a_excludes['ex_id'] . "');\">";
        $linkend = "</a>";

        $q_string  = "select usr_name ";
        $q_string .= "from users ";
        $q_string .= "where usr_id = " . $a_excludes['ex_deleted'] . " ";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_users = mysqli_fetch_array($q_users);

        if ($a_excludes['ex_comments'] != $comment_test) {
          $comment = $a_excludes['ex_comments'];
          $comment_test = $comment;
        } else {
          $comment = '';
        }

        if ($a_excludes['ex_companyid'] > 0) {

          $q_string  = "select int_server ";
          $q_string .= "from interface ";
          $q_string .= "where int_companyid = " . $a_excludes['ex_companyid'] . " and (int_type = 1 or int_type = 2) ";
          $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          while ($a_interface = mysqli_fetch_array($q_interface)) {
            if ($comment != '') {
              $output .= "#\n# " . $comment . "\n";
            }
            if ($a_excludes['ex_expiration'] == date('Y-m-d')) {
              $output .= "<span class=\"ui-state-highlight\">";
            }
            if ($a_excludes['ex_expiration'] < date('Y-m-d')) {
              $output .= "<span class=\"ui-state-error\">";
            }
            $output .= $linkstart;
            $output .= $a_interface['int_server'] . " ";
            $output .= $a_excludes['ex_text'] . $linkend;
            if ($a_excludes['ex_expiration'] < date('Y-m-d')) {
              $output .= "</span>";
            }
            if ($a_excludes['ex_expiration'] == '2038-01-01') {
              $output .= " (Never Expires)";
            } else {
              $output .= " (Expires: " . $a_excludes['ex_expiration'] . ")";
            }
            $output .= " (Entered by: " . $a_excludes['usr_name'] . ")";
            $output .= " (" . $linkdel . ")\n";
          }
        } else {
          if ($comment != '') {
            $output .= "#\n# " . $comment . "\n";
          }
          if ($a_excludes['ex_expiration'] == date('Y-m-d')) {
            $output .= "<span class=\"ui-state-highlight\">";
          }
          if ($a_excludes['ex_expiration'] < date('Y-m-d')) {
            $output .= "<span class=\"ui-state-error\">";
          }
          $output .= $linkstart;
          $output .= $a_excludes['ex_text'] . $linkend;
          if ($a_excludes['ex_expiration'] < date('Y-m-d')) {
            $output .= "</span>";
          }
          if ($a_excludes['ex_expiration'] == '2038-01-01') {
            $output .= " (Never Expires)";
          } else {
            $output .= " (Expires: " . $a_excludes['ex_expiration'] . ")";
          }
          $output .= " (Entered by: " . $a_excludes['usr_name'] . ")";
          if ($a_excludes['ex_deleted'] > 0) {
            $output .= " (Deleted by: " . $a_users['usr_name'] . " " . $linkremove . ")\n";
          } else {
            $output .= " (" . $linkdel . ")\n";
          }
        }
      }

      $output .= "</pre>\n";

      print "document.exclude.ex_companyid['0'].selected = true;\n";
      print "document.exclude.ex_text.value = '';\n";
      print "document.exclude.ex_comments.value = '';\n";
      print "document.exclude.ex_expiration.value = '';\n";
      print "document.exclude.noexpire.checked = false;\n";

      print "document.getElementById('exclude_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

    }

  } else {
    logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
  }
?>
