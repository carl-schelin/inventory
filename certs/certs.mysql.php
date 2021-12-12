<?php
# Script: certs.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "certs.mysql.php";
    $formVars['update']         = clean($_GET['update'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['cert_desc']      = clean($_GET['cert_desc'],      80);
        $formVars['cert_url']       = clean($_GET['cert_url'],       80);
        $formVars['cert_expire']    = clean($_GET['cert_expire'],    12);
        $formVars['cert_authority'] = clean($_GET['cert_authority'], 60);
        $formVars['cert_group']     = clean($_GET['cert_group'],     10);
        $formVars['cert_ca']        = clean($_GET['cert_ca'],        10);
        $formVars['cert_memo']      = clean($_GET['cert_memo'],    1024);
        $formVars['cert_isca']      = clean($_GET['cert_isca'],      10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        if ($formVars['cert_isca'] == 'true') {
          $formVars['cert_isca'] = 1;
        } else {
          $formVars['cert_isca'] = 0;
        }

        if (strlen($formVars['cert_desc']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "cert_desc      = \"" . $formVars['cert_desc']      . "\"," .
            "cert_url       = \"" . $formVars['cert_url']       . "\"," .
            "cert_expire    = \"" . $formVars['cert_expire']    . "\"," .
            "cert_authority = \"" . $formVars['cert_authority'] . "\"," .
            "cert_group     =   " . $formVars['cert_group']     . "," . 
            "cert_ca        =   " . $formVars['cert_ca']        . "," .
            "cert_memo      = \"" . $formVars['cert_memo']      . "\"," . 
            "cert_isca      =   " . $formVars['cert_isca'];

          if ($formVars['update'] == 0) {
            $query = "insert into certs set cert_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update certs set " . $q_string . " where cert_id = " . $formVars['id'];
          }

          logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['cert_desc']);

          mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));

        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $date = time();
#      $date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
      $warningdate = mktime(0, 0, 0, date('m'), date('d') + $a_users['usr_notify'], date('Y'));

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Certificate</th>\n";
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">CA?</th>\n";
      $output .= "  <th class=\"ui-state-default\">Expiration</th>\n";
      $output .= "  <th class=\"ui-state-default\">Authority</th>\n";
      $output .= "  <th class=\"ui-state-default\">Managed By</th>\n";
      $output .= "</tr>\n";

      $count = 0;
      $q_string  = "select cert_desc,cert_id,cert_url,cert_expire,cert_authority,cert_group,grp_name,cert_isca ";
      $q_string .= "from certs ";
      $q_string .= "left join a_groups on a_groups.grp_id = certs.cert_group ";
      $q_string .= "order by cert_desc,cert_expire";
      $q_certs = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_certs) > 0) {
        while ($a_certs = mysqli_fetch_array($q_certs)) {

          $certtime = strtotime($a_certs['cert_expire']);

          $class = " class=\"ui-widget-content";
          if ($certtime < $date) {
            $class = " class=\"ui-state-error";
          } else {
            if ($certtime < $warningdate) {
              $class = " class=\"ui-state-highlight";
            }
          }

          if ($a_certs['cert_isca']) {
            $isca = "Yes";
          } else {
            $isca = "No";
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('certs.fill.php?id=" . $a_certs['cert_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('certs.del.php?id=" . $a_certs['cert_id'] . "');\">";
          $linkend   = "</a>";

# if a member of the webapps team or an admin, permit editing.
          $output .= "<tr>";
          if (check_grouplevel($db, $GRP_WebApps)) {
            $output .= "  <td" . $class . " delete\">"                                 . $linkdel                                           . "</td>\n";
          } else {
            $output .= "  <td" . $class . "\">--</td>\n";
          }
          $output .= "  <td" . $class . "\" title=\"" . $a_certs['cert_url'] . "\">" . $linkstart . $a_certs['cert_desc']      . $linkend . "</td>\n";
          $output .= "  <td" . $class . "\">"                                        . $linkstart . $isca                      . $linkend . "</td>\n";
          $output .= "  <td" . $class . "\">"                                        . $linkstart . $a_certs['cert_expire']    . $linkend . "</td>\n";
          $output .= "  <td" . $class . "\">"                                        . $linkstart . $a_certs['cert_authority'] . $linkend . "</td>\n";
          $output .= "  <td" . $class . "\">"                                        . $linkstart . $a_certs['grp_name']       . $linkend . "</td>\n";
          $output .= "</tr>\n";
          $count++;
        }

      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"6\">No Certificates Defined</td>\n";
        $output .= "</tr>\n";
      }

      $output .= "</table>\n";
      $output .= "</div>\n";
      $output .= "</table>\n";

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
