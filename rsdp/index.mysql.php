<?php
# Script: index.mysql.php
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
    $package = "index.mysql.php";
    $formVars['update']   = clean($_GET['update'],   10);
    $formVars['myrsdp']   = clean($_GET['myrsdp'],   10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['myrsdp'] == '') {
      $formVars['myrsdp'] = 'yes';
    }

    if (check_userlevel($db, $AL_Edit)) {

      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Project Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('project-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"project-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Delete Project</strong> - Admins Only: This parses <strong>every server</strong> and <strong>task</strong> associated with ";
      $output .= "this Project and removes it from the system. <strong>Note!</strong> folks don't always create a new project entry. Make <strong>sure</strong> ";
      $output .= "you are deleting only the servers you manage. Better to go to the server page and just delete the servers you are creating.</li>\n";
      $output .= "  <li><strong>Project</strong> - The name of the Project. Clicking here will bring up the next page which shows the servers associated with this specific project.</li>\n";
      $output .= "  <li><strong>Product</strong> - The primary Product this Project is building servers for.</li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Project</th>\n";
      $output .= "  <th class=\"ui-state-default\">Product</th>\n";
      if (check_userlevel($db, $AL_Admin)) {
        $output .= "  <th class=\"ui-state-default\">Delete</th>\n";
      }
      $output .= "</tr>\n";

# pseudocode for knowing which project to display:
#
# need to loop through all the projects and get a unique list of rsdp_projects.
# then loop through all the rsdp entries with that rsdp_project id
# then exclude any that have a completed task 18
# if myrsdp then parse the tasks, 
#   if requestor, platform builder, or on the task
#     set myserver = yes
# else
#   set myserver = yes
# if myserver and array[server] = 0 then
#   display project and link
#   set array[server] = 1

# get one instance of each project in use by rsdp
      $q_string  = "select rsdp_id,rsdp_product,prod_name,rsdp_project,prj_name,prj_code ";
      $q_string .= "from rsdp_server ";
      $q_string .= "left join products on products.prod_id = rsdp_server.rsdp_product ";
      $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
      $q_string .= "group by rsdp_project ";
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_rsdp_server = mysqli_fetch_array($q_rsdp_server)) {

# set the "there can be only one" flag
        $onlyone[$a_rsdp_server['rsdp_project']] = 0;

# check _every_ instance of a server being build for this project to see if it's built 
# by this user (or if not 'myrsdp' then all servers associated with this project)
# this is to display the project if there is at least one.
        $q_string  = "select rsdp_id,rsdp_requestor,rsdp_platformspoc,rsdp_sanpoc,rsdp_networkpoc,";
        $q_string .= "rsdp_virtpoc,rsdp_dcpoc,rsdp_dcpoc,rsdp_srpoc,rsdp_monitorpoc,rsdp_apppoc,rsdp_backuppoc ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_project = " . $a_rsdp_server['rsdp_project'];
        $q_rsdp_server_2 = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_rsdp_server_2 = mysqli_fetch_array($q_rsdp_server_2)) {

# if viewing only my tasks, select the last completed or skipped task from the rsdp_status table
          $mystep = 0;  # by default, it's not my step
          if ($formVars['myrsdp'] == 'yes') {

# if I'm the requestor of the rsdp task I want to see the output
            if ($a_rsdp_server_2['rsdp_requestor'] == $_SESSION['uid']) {
              $mystep = 1;
            }

# if I'm the platform owner itself, it's likely I want to keep track even if I'm not the Requestor.
            if ($a_rsdp_server_2['rsdp_platformspoc'] == $_SESSION['uid']) {
                $mystep = 1;
            }

# start with step 3
            for ($i = 3; $i < 19; $i++) {

              $q_string  = "select st_step ";
              $q_string .= "from rsdp_status ";
              $q_string .= "where st_rsdp = " . $a_rsdp_server_2['rsdp_id'] . " and st_step = " . $i;
              $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
              $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

# let's see if this is a virtual machine or a physical machine
              $virtual = rsdp_Virtual($db, "$a_rsdp_server_2['rsdp_id']);

# if the step exists, then it's either done or skipped. so not $mystep
              if ($a_rsdp_status['st_step'] == '') {

# rsdp_sanpoc = step 3 and step 11
                if (($i == 3 || $i == 11) && $a_rsdp_server_2['rsdp_sanpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }

# rsdp_networkpoc = step 4
                if ($i == 4 && $a_rsdp_server_2['rsdp_networkpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }

# rsdp_virtpoc = step 5vm
# rsdp_dcpoc = step 5, step 6, step 7, and step 9
                if ($i == 5) {
# if it's virtual check the virtpoc info, otherwise check the dcpoc info
                  if ($virtual) {
                    if ($a_rsdp_server_2['rsdp_virtpoc'] == $_SESSION['uid']) {
                      $mystep = 1;
                    }
                  } else {
                    if ($a_rsdp_server_2['rsdp_dcpoc'] == $_SESSION['uid']) {
                      $mystep = 1;
                    }
                  }
                }

# rsdp_dcpoc = step 5 (done above), step 6, step 7, and step 9
                if (($i == 6 || $i == 7 || $i == 9) && $a_rsdp_server_2['rsdp_dcpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }

# rsdp_srpoc = step 8
                if ($i == 8 && $a_rsdp_server_2['rsdp_srpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }

# rsdp_monitorpoc = step 14 and step 16
                if (($i == 14 || $i == 16) && $a_rsdp_server_2['rsdp_monitorpoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }

# rsdp_backuppoc = step 13
                if ($i == 13 && $a_rsdp_server_2['rsdp_backuppoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }

# rsdp_apppoc = step 15
                if ($i == 15 && $a_rsdp_server_2['rsdp_apppoc'] == $_SESSION['uid']) {
                  $mystep = 1;
                }
              }
            }
          } else {
            $mystep = 1;   # if we're not looking at 'myrsdp', then every entry will be shown.
          }

# see if _any_ servers are still incomplete
          $q_string  = "select st_step ";
          $q_string .= "from rsdp_status ";
          $q_string .= "where st_rsdp = " . $a_rsdp_server_2['rsdp_id'] . " and st_step = 18";
          $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# we have at least _one_ incomplete server
          if (mysqli_num_rows($q_rsdp_status) == 0 && $onlyone[$a_rsdp_server['rsdp_project']] == 0 && $mystep == 1) {
            $onlyone[$a_rsdp_server['rsdp_project']] = 1;

            $projectstart = "<a href=\"servers.php?projectid=" . $a_rsdp_server['rsdp_project'] . "&myrsdp=" . $formVars['myrsdp'] . "\" target=\"_blank\">";
            $productstart = "<a href=\"servers.php?productid=" . $a_rsdp_server['rsdp_product'] . "&myrsdp=" . $formVars['myrsdp'] . "\" target=\"_blank\">";
            $linkdel = '';
            $linktext = '--';
            $linkend   = "</a>";
            if (check_userlevel($db, $AL_Admin)) {
              $linkdel   = "<a href=\"#\" onclick=\"delete_line('index.del.php?projectid=" . $a_rsdp_server['rsdp_project'] . "');\">";
              $linktext = 'Delete Project';
            }

            if ($a_rsdp_server['prj_name'] == '') {
              $a_rsdp_server['prj_name'] = 'Unknown';
            } else {
              $a_rsdp_server['prj_name'] .= ' (' . $a_rsdp_server['prj_code'] . ')';
            }
            if ($a_rsdp_server['prod_name'] == '') {
              $a_rsdp_server['prod_name'] = 'Unknown';
            }

            $output .= "  <tr>\n";
            $output .= "    <td class=\"ui-widget-content\">"        . $projectstart . $a_rsdp_server['prj_name']  . $linkend . "</td>\n";
            $output .= "    <td class=\"ui-widget-content\">"        . $productstart . $a_rsdp_server['prod_name'] . $linkend . "</td>\n";
            if (check_userlevel($db, $AL_Admin)) {
              $output .= "    <td class=\"ui-widget-content delete\">" . $linkdel   . $linktext                   . $linkend . "</td>\n";
            }
            $output .= "  </tr>\n";
          }
        }
      }

      $output .= "</table>";

      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
