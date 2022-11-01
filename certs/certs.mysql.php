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
    $formVars['top']            = clean($_GET['top'],            10);
    $andtop = '';

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['top'] == '') {
      $formVars['top'] = 0;
    }
    if ($formVars['top'] > 0) {
      $andtop = "and cert_id = " . $formVars['top'];
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']             = clean($_GET['id'],             10);
        $formVars['cert_desc']      = clean($_GET['cert_desc'],      80);
        $formVars['cert_url']       = clean($_GET['cert_url'],       80);
        $formVars['cert_filename']  = clean($_GET['cert_filename'],  80);
        $formVars['cert_expire']    = clean($_GET['cert_expire'],    12);
        $formVars['cert_authority'] = clean($_GET['cert_authority'], 60);
        $formVars['cert_subject']   = clean($_GET['cert_subject'],   60);
        $formVars['cert_group']     = clean($_GET['cert_group'],     10);
        $formVars['cert_top']       = clean($_GET['cert_top'],       10);
        $formVars['cert_memo']      = clean($_GET['cert_memo'],    1024);
        $formVars['cert_isca']      = clean($_GET['cert_isca'],      10);
        $formVars['cert_ca']        = clean($_GET['cert_ca'],        10);

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
            "cert_filename  = \"" . $formVars['cert_filename']  . "\"," .
            "cert_expire    = \"" . $formVars['cert_expire']    . "\"," .
            "cert_authority = \"" . $formVars['cert_authority'] . "\"," .
            "cert_subject   = \"" . $formVars['cert_subject']   . "\"," .
            "cert_group     =   " . $formVars['cert_group']     . "," . 
            "cert_ca        =   " . $formVars['cert_ca']        . "," .
            "cert_memo      = \"" . $formVars['cert_memo']      . "\"," . 
            "cert_isca      =   " . $formVars['cert_isca']      . "," . 
            "cert_top       =   " . $formVars['cert_top'];

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
      $warningdate = mktime(0, 0, 0, date('m'), date('d') + $a_inv_users['usr_notify'], date('Y'));

      $output  = "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      if (check_grouplevel($db, $GRP_WebApps)) {
        $output .= "  <th class=\"ui-state-default\" width=\"160\">Delete Certificate</th>\n";
      }
      $output .= "  <th class=\"ui-state-default\">Description</th>\n";
      $output .= "  <th class=\"ui-state-default\">Filename</th>\n";
      $output .= "  <th class=\"ui-state-default\">CA?</th>\n";
      $output .= "  <th class=\"ui-state-default\">Not After</th>\n";
      $output .= "  <th class=\"ui-state-default\">Issuer</th>\n";
      $output .= "  <th class=\"ui-state-default\">Subject</th>\n";
      $output .= "  <th class=\"ui-state-default\">Managed By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Members</th>\n";
      $output .= "</tr>\n";

      $count = 0;
      $q_string  = "select cert_desc,cert_id,cert_url,cert_expire,cert_authority,";
      $q_string .= "cert_filename,cert_subject,cert_group,grp_name,cert_isca,cert_top ";
      $q_string .= "from certs ";
      $q_string .= "left join inv_groups on inv_groups.grp_id = certs.cert_group ";
      $q_string .= "where cert_ca = 0 " . $andtop . " ";
      $q_string .= "order by cert_desc,cert_expire";
      $q_certs = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_certs) > 0) {
        while ($a_certs = mysqli_fetch_array($q_certs)) {

# reset if andtop succeeds here
          $andtop = '';

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

# show as top if a top level
          if ($a_certs['cert_top']) {
            $isca = "<a href=\"certs.php?top=" . $a_certs['cert_id'] . "\" target=\"_blank\">Top</a>";
          }

          $linkstart = "<a href=\"#\" onclick=\"show_file('certs.fill.php?id=" . $a_certs['cert_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
          $certstart = "<a href=\"servers.php?id=" . $a_certs['cert_id'] . "\" target=\"_blank\">";
          $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('certs.del.php?id=" . $a_certs['cert_id'] . "');\">";
          $linkend   = "</a>";

          $total = 0;
          $q_string  = "select svr_certid ";
          $q_string .= "from svr_software ";
          $q_string .= "where svr_certid = " . $a_certs['cert_id'] . " ";
          $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_svr_software) > 0) {
            while ($a_svr_software = mysqli_fetch_array($q_svr_software)) {
              $total++;
            }
          }

# if a member of the webapps team or an admin, permit editing.
          $output .= "<tr>";
          if (check_grouplevel($db, $GRP_WebApps)) {
            if ($total == 0) {
              $output .= "  <td" . $class . " delete\">" . $linkdel . "</td>\n";
            } else {
              $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
            }
          }
          $output .= "  <td" . $class . "\" title=\"" . $a_certs['cert_url'] . "\">" . $linkstart . $a_certs['cert_desc']      . $linkend . "</td>\n";
          $output .= "  <td" . $class . "\">"                                                     . $a_certs['cert_filename']             . "</td>\n";
          $output .= "  <td" . $class . " delete\">"                                              . $isca                                 . "</td>\n";
          $output .= "  <td" . $class . " delete\">"                                              . $a_certs['cert_expire']               . "</td>\n";
          $output .= "  <td" . $class . "\">"                                                     . $a_certs['cert_authority']            . "</td>\n";
          $output .= "  <td" . $class . "\">"                                                     . $a_certs['cert_subject']              . "</td>\n";
          $output .= "  <td" . $class . "\">"                                                     . $a_certs['grp_name']                  . "</td>\n";
          $output .= "  <td" . $class . " delete\">"                                 . $certstart . $total                     . $linkend . "</td>\n";
          $output .= "</tr>\n";
          $count++;


          $q_string  = "select cert_desc,cert_id,cert_url,cert_expire,cert_authority,";
          $q_string .= "cert_filename,cert_subject,cert_group,grp_name,cert_isca,cert_top ";
          $q_string .= "from certs ";
          $q_string .= "left join inv_groups on inv_groups.grp_id = certs.cert_group ";
          $q_string .= "where cert_ca = " . $a_certs['cert_id'] . " " . $andtop . " ";
          $q_string .= "order by cert_desc,cert_expire";
          $q_child = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_child) > 0) {
            while ($a_child = mysqli_fetch_array($q_child)) {

# reset if andtop succeeds here
              $andtop = '';

              $certtime = strtotime($a_child['cert_expire']);

              $class = " class=\"ui-widget-content";
              if ($certtime < $date) {
                $class = " class=\"ui-state-error";
              } else {
                if ($certtime < $warningdate) {
                  $class = " class=\"ui-state-highlight";
                }
              }

              if ($a_child['cert_isca']) {
                $isca = "Yes";
              } else {
                $isca = "No";
              }

              if ($a_child['cert_top']) {
                $iscaa = "<a href=\"certs.php?top=" . $a_child['cert_id'] . "\" target=\"_blank\">Top</a>";
              }

              $linkstart = "<a href=\"#\" onclick=\"show_file('certs.fill.php?id=" . $a_child['cert_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
              $certstart = "<a href=\"servers.php?id=" . $a_child['cert_id'] . "\" target=\"_blank\">";
              $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('certs.del.php?id=" . $a_child['cert_id'] . "');\">";
              $linkend   = "</a>";

              $total = 0;
              $q_string  = "select svr_certid ";
              $q_string .= "from svr_software ";
              $q_string .= "where svr_certid = " . $a_child['cert_id'] . " ";
              $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
              if (mysqli_num_rows($q_svr_software) > 0) {
                while ($a_svr_software = mysqli_fetch_array($q_svr_software)) {
                  $total++;
                }
              }

# if a member of the webapps team or an admin, permit editing.
              $output .= "<tr>";
              if (check_grouplevel($db, $GRP_WebApps)) {
                if ($total == 0) {
                  $output .= "  <td" . $class . " delete\">" . $linkdel . "</td>\n";
                } else {
                  $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
                }
              }
              $output .= "  <td" . $class . "\" title=\"" . $a_child['cert_url'] . "\">&gt; " . $linkstart . $a_child['cert_desc']      . $linkend . "</td>\n";
              $output .= "  <td" . $class . "\">"                                                          . $a_child['cert_filename']             . "</td>\n";
              $output .= "  <td" . $class . " delete\">"                                                   . $isca                                 . "</td>\n";
              $output .= "  <td" . $class . " delete\">"                                                   . $a_child['cert_expire']               . "</td>\n";
              $output .= "  <td" . $class . "\">"                                                          . $a_child['cert_authority']            . "</td>\n";
              $output .= "  <td" . $class . "\">"                                                          . $a_child['cert_subject']              . "</td>\n";
              $output .= "  <td" . $class . "\">"                                                          . $a_child['grp_name']                  . "</td>\n";
              $output .= "  <td" . $class . " delete\">"                                      . $certstart . $total                     . $linkend . "</td>\n";
              $output .= "</tr>\n";



              $q_string  = "select cert_desc,cert_id,cert_url,cert_expire,cert_authority,";
              $q_string .= "cert_filename,cert_subject,cert_group,grp_name,cert_isca,cert_top ";
              $q_string .= "from certs ";
              $q_string .= "left join inv_groups on inv_groups.grp_id = certs.cert_group ";
              $q_string .= "where cert_ca = " . $a_child['cert_id'] . " " . $andtop . " ";
              $q_string .= "order by cert_desc,cert_expire";
              $q_grandchild = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
              if (mysqli_num_rows($q_grandchild) > 0) {
                while ($a_grandchild = mysqli_fetch_array($q_grandchild)) {

# reset if andtop succeeds here
                  $andtop = '';

                  $certtime = strtotime($a_grandchild['cert_expire']);

                  $class = " class=\"ui-widget-content";
                  if ($certtime < $date) {
                    $class = " class=\"ui-state-error";
                  } else {
                    if ($certtime < $warningdate) {
                      $class = " class=\"ui-state-highlight";
                    }
                  }

                  if ($a_grandchild['cert_isca']) {
                    $isca = "Yes";
                  } else {
                    $isca = "No";
                  }

                  if ($a_grandchild['cert_top']) {
                    $isca = "<a href=\"certs.php?top=" . $a_grandchild['cert_id'] . "\" target=\"_blank\">Top</a>";
                  }

                  $linkstart = "<a href=\"#\" onclick=\"show_file('certs.fill.php?id=" . $a_grandchild['cert_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
                  $certstart = "<a href=\"servers.php?id=" . $a_grandchild['cert_id'] . "\" target=\"_blank\">";
                  $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('certs.del.php?id=" . $a_grandchild['cert_id'] . "');\">";
                  $linkend   = "</a>";

                  $total = 0;
                  $q_string  = "select svr_certid ";
                  $q_string .= "from svr_software ";
                  $q_string .= "where svr_certid = " . $a_grandchild['cert_id'] . " ";
                  $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                  if (mysqli_num_rows($q_svr_software) > 0) {
                    while ($a_svr_software = mysqli_fetch_array($q_svr_software)) {
                      $total++;
                    }
                  }

# if a member of the webapps team or an admin, permit editing.
                  $output .= "<tr>";
                  if (check_grouplevel($db, $GRP_WebApps)) {
                    if ($total == 0) {
                      $output .= "  <td" . $class . " delete\">" . $linkdel . "</td>\n";
                    } else {
                      $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
                    }
                  }
                  $output .= "  <td" . $class . "\" title=\"" . $a_grandchild['cert_url'] . "\">&gt;&gt; " . $linkstart . $a_grandchild['cert_desc']      . $linkend . "</td>\n";
                  $output .= "  <td" . $class . "\">"                                                                   . $a_grandchild['cert_filename']             . "</td>\n";
                  $output .= "  <td" . $class . " delete\">"                                                            . $isca                                      . "</td>\n";
                  $output .= "  <td" . $class . " delete\">"                                                            . $a_grandchild['cert_expire']               . "</td>\n";
                  $output .= "  <td" . $class . "\">"                                                                   . $a_grandchild['cert_authority']            . "</td>\n";
                  $output .= "  <td" . $class . "\">"                                                                   . $a_grandchild['cert_subject']              . "</td>\n";
                  $output .= "  <td" . $class . "\">"                                                                   . $a_grandchild['grp_name']                  . "</td>\n";
                  $output .= "  <td" . $class . " delete\">"                                               . $certstart . $total                          . $linkend . "</td>\n";
                  $output .= "</tr>\n";



                  $q_string  = "select cert_desc,cert_id,cert_url,cert_expire,cert_authority,";
                  $q_string .= "cert_filename,cert_subject,cert_group,grp_name,cert_isca,cert_top ";
                  $q_string .= "from certs ";
                  $q_string .= "left join inv_groups on inv_groups.grp_id = certs.cert_group ";
                  $q_string .= "where cert_ca = " . $a_grandchild['cert_id'] . " " . $andtop . " ";
                  $q_string .= "order by cert_desc,cert_expire";
                  $q_greatgrandchild = mysqli_query($db, $q_string) or die ($q_string . ": " . mysqli_error($db));
                  if (mysqli_num_rows($q_greatgrandchild) > 0) {
                    while ($a_greatgrandchild = mysqli_fetch_array($q_greatgrandchild)) {

# reset if andtop succeeds here
                      $andtop = '';

                      $certtime = strtotime($a_greatgrandchild['cert_expire']);

                      $class = " class=\"ui-widget-content";
                      if ($certtime < $date) {
                        $class = " class=\"ui-state-error";
                      } else {
                        if ($certtime < $warningdate) {
                          $class = " class=\"ui-state-highlight";
                        }
                      }

                      if ($a_greatgrandchild['cert_isca']) {
                        $isca = "Yes";
                      } else {
                        $isca = "No";
                      }

                      if ($a_greatgrandchild['cert_top']) {
                        $isca = "<a href=\"certs.php?top=" . $a_greatgrandchild['cert_id'] . "\" target=\"_blank\">Top</a>";
                      }

                      $linkstart = "<a href=\"#\" onclick=\"show_file('certs.fill.php?id=" . $a_greatgrandchild['cert_id'] . "');jQuery('#dialogUpdate').dialog('open');return false;\">";
                      $certstart = "<a href=\"servers.php?id=" . $a_greatgrandchild['cert_id'] . "\" target=\"_blank\">";
                      $linkdel   = "<input type=\"button\" value=\"Remove\" onclick=\"delete_line('certs.del.php?id=" . $a_greatgrandchild['cert_id'] . "');\">";
                      $linkend   = "</a>";

                      $total = 0;
                      $q_string  = "select svr_certid ";
                      $q_string .= "from svr_software ";
                      $q_string .= "where svr_certid = " . $a_greatgrandchild['cert_id'] . " ";
                      $q_svr_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                      if (mysqli_num_rows($q_svr_software) > 0) {
                        while ($a_svr_software = mysqli_fetch_array($q_svr_software)) {
                          $total++;
                        }
                      }

# if a member of the webapps team or an admin, permit editing.
                      $output .= "<tr>";
                          if (check_grouplevel($db, $GRP_WebApps)) {
                        if ($total == 0) {
                          $output .= "  <td" . $class . " delete\">" . $linkdel . "</td>\n";
                        } else {
                          $output .= "  <td class=\"ui-widget-content delete\">Members &gt; 0</td>";
                        }
                      }
                      $output .= "  <td" . $class . "\" title=\"" . $a_greatgrandchild['cert_url'] . "\">&gt;&gt;&gt; " . $linkstart . $a_greatgrandchild['cert_desc']      . $linkend . "</td>\n";
                      $output .= "  <td" . $class . "\">"                                                                            . $a_greatgrandchild['cert_filename']             . "</td>\n";
                      $output .= "  <td" . $class . " delete\">"                                                                     . $isca                                           . "</td>\n";
                      $output .= "  <td" . $class . " delete\">"                                                                     . $a_greatgrandchild['cert_expire']               . "</td>\n";
                      $output .= "  <td" . $class . "\">"                                                                            . $a_greatgrandchild['cert_authority']            . "</td>\n";
                      $output .= "  <td" . $class . "\">"                                                                            . $a_greatgrandchild['cert_subject']              . "</td>\n";
                      $output .= "  <td" . $class . "\">"                                                                            . $a_greatgrandchild['grp_name']                  . "</td>\n";
                      $output .= "  <td" . $class . " delete\">"                                                        . $certstart . $total                               . $linkend . "</td>\n";
                      $output .= "</tr>\n";
                    }
                  }




                }
              }
            }
          }
        }

      } else {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"9\">No Certificates Defined</td>\n";
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
