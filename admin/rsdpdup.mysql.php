<?php
# Script: rsdpdup.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "rsdpdup.mysql.php";
    $formVars['update']    = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">RSDP Duplication Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('rsdp-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"rsdp-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Patch Description Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Patch Description to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

# get all duplicate RSDP entries from each of the tables other than the main table and the four tables that will have extra bits

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"16\">rsdp_applications</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">installed</th>";
      $output .= "  <th class=\"ui-state-default\">configured</th>";
      $output .= "  <th class=\"ui-state-default\">mib</th>";
      $output .= "  <th class=\"ui-state-default\">process</th>";
      $output .= "  <th class=\"ui-state-default\">logfile</th>";
      $output .= "  <th class=\"ui-state-default\">inscheck</th>";
      $output .= "  <th class=\"ui-state-default\">tested</th>";
      $output .= "  <th class=\"ui-state-default\">integrated</th>";
      $output .= "  <th class=\"ui-state-default\">failover</th>";
      $output .= "  <th class=\"ui-state-default\">concheck</th>";
      $output .= "  <th class=\"ui-state-default\">monitor</th>";
      $output .= "  <th class=\"ui-state-default\">verified</th>";
      $output .= "  <th class=\"ui-state-default\">moncheck</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "where app_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select app_rsdp,count(app_rsdp) ";
      $q_string .= "from rsdp_applications ";
      $q_string .= "group by app_rsdp ";
      $q_rsdp_applications = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_applications) > 0) {
        while ($a_rsdp_applications = mysqli_fetch_array($q_rsdp_applications)) {

          if ($a_rsdp_applications['count(app_rsdp)'] > 1) {
            $q_string  = "select app_id,app_rsdp,app_installed,app_configured,app_mib,app_process,app_logfile,app_inscheck,app_tested,app_integrated,app_failover,app_concheck,app_monitor,app_verified,app_moncheck ";
            $q_string .= "from rsdp_applications ";
            $q_string .= "where app_rsdp = " . $a_rsdp_applications['app_rsdp'] . " ";
            $q_applications = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_applications = mysqli_fetch_array($q_applications)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_applications['app_id'] . "&select=app_id&table=rsdp_applications');\">";
              $linkend = "</a>";

              $output .= "<tr>";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_id']          . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_rsdp']        . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_installed']   . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_configured']  . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_mib']         . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_process']     . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_logfile']     . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_inscheck']    . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_tested']      . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_integrated']  . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_failover']    . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_concheck']    . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_monitor']     . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_verified']    . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['app_moncheck']    . "</td>";
              $output .= "</tr>";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"16\">Nothing in rsdp_applications.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"20\">rsdp_backups</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">start</th>";
      $output .= "  <th class=\"ui-state-default\">include</th>";
      $output .= "  <th class=\"ui-state-default\">retention</th>";
      $output .= "  <th class=\"ui-state-default\">sunday</th>";
      $output .= "  <th class=\"ui-state-default\">monday</th>";
      $output .= "  <th class=\"ui-state-default\">tuesday</th>";
      $output .= "  <th class=\"ui-state-default\">wednesday</th>";
      $output .= "  <th class=\"ui-state-default\">thursday</th>";
      $output .= "  <th class=\"ui-state-default\">friday</th>";
      $output .= "  <th class=\"ui-state-default\">saturday</th>";
      $output .= "  <th class=\"ui-state-default\">suntime</th>";
      $output .= "  <th class=\"ui-state-default\">montime</th>";
      $output .= "  <th class=\"ui-state-default\">tuetime</th>";
      $output .= "  <th class=\"ui-state-default\">wedtime</th>";
      $output .= "  <th class=\"ui-state-default\">thutime</th>";
      $output .= "  <th class=\"ui-state-default\">fritime</th>";
      $output .= "  <th class=\"ui-state-default\">sattime</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_backups ";
      $q_string .= "where bu_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select bu_rsdp,count(bu_rsdp) ";
      $q_string .= "from rsdp_backups ";
      $q_string .= "group by bu_rsdp ";
      $q_rsdp_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_backups) > 0) {
        while ($a_rsdp_backups = mysqli_fetch_array($q_rsdp_backups)) {

          if ($a_rsdp_backups['count(bu_rsdp)'] > 1) {
            $q_string  = "select bu_id, bu_rsdp, bu_start, bu_include, bu_retention, bu_sunday, bu_monday, bu_tuesday, bu_wednesday, bu_thursday, bu_friday, bu_saturday, bu_suntime, bu_montime, bu_tuetime, bu_wedtime, bu_thutime, bu_fritime, bu_sattime ";
            $q_string .= "from rsdp_backups ";
            $q_string .= "where bu_rsdp = " . $a_rsdp_backups['bu_rsdp'] . " ";
            $q_backups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_backups = mysqli_fetch_array($q_backups)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_backups['bu_id'] . "&select=bu_id&table=rsdp_backups');\">";
              $linkend = "</a>";

              $output .= "<tr>";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                 . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_id']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_rsdp']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_start']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_include']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_retention']  . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_sunday']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_monday']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_tuesday']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_wednesday']  . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_thursday']   . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_friday']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_saturday']   . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_suntime']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_montime']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_tuetime']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_wedtime']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_thutime']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_fritime']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_backups['bu_sattime']    . "</td>\n";
              $output .= "</tr>";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"20\">Nothing in rsdp_backups.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"10\">rsdp_datacenter</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">power</th>";
      $output .= "  <th class=\"ui-state-default\">cables</th>";
      $output .= "  <th class=\"ui-state-default\">infra</th>";
      $output .= "  <th class=\"ui-state-default\">received</th>";
      $output .= "  <th class=\"ui-state-default\">installed</th>";
      $output .= "  <th class=\"ui-state-default\">checklist</th>";
      $output .= "  <th class=\"ui-state-default\">path</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_datacenter ";
      $q_string .= "where dc_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select dc_rsdp,count(dc_rsdp) ";
      $q_string .= "from rsdp_datacenter ";
      $q_string .= "group by dc_rsdp ";
      $q_rsdp_datacenter = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_datacenter) > 0) {
        while ($a_rsdp_datacenter = mysqli_fetch_array($q_rsdp_datacenter)) {

          if ($a_rsdp_datacenter['count(dc_rsdp)'] > 1) {
            $q_string  = "select dc_id,dc_rsdp,dc_power,dc_cables,dc_infra,dc_received,dc_installed,dc_checklist,dc_path ";
            $q_string .= "from rsdp_datacenter ";
            $q_string .= "where dc_rsdp = " . $a_rsdp_datacenter['dc_rsdp'] . " ";
            $q_datacenter = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_datacenter = mysqli_fetch_array($q_datacenter)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_datacenter['dc_id'] . "&select=dc_id&table=rsdp_datacenter');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_id']           . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_rsdp']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_power']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_cables']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_infra']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_received']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_installed']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_checklist']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_datacenter['dc_path']         . "</td>\n";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"10\">Nothing in rsdp_datacenter.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"5\">rsdp_designed</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">complete</th>";
      $output .= "  <th class=\"ui-state-default\">checklist</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_designed ";
      $q_string .= "where san_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select san_rsdp,count(san_rsdp) ";
      $q_string .= "from rsdp_designed ";
      $q_string .= "group by san_rsdp ";
      $q_rsdp_designed = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_designed) > 0) {
        while ($a_rsdp_designed = mysqli_fetch_array($q_rsdp_designed)) {

          if ($a_rsdp_designed['count(san_rsdp)'] > 1) {
            $q_string  = "select san_id,san_rsdp,san_complete,san_checklist ";
            $q_string .= "from rsdp_designed ";
            $q_string .= "where san_rsdp = " . $a_rsdp_designed['san_rsdp'] . " ";
            $q_designed = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_designed = mysqli_fetch_array($q_designed)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_designed['san_id'] . "&select=san_id&table=rsdp_designed');\">";
              $linkend = "</a>";

              $output .= "<tr>";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel   . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['san_id']          . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['san_rsdp']        . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['san_complete']    . "</td>";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_applications['san_checklist']   . "</td>";
              $output .= "</tr>";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">Nothing in rsdp_designed.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"7\">rsdp_infosec</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">checklist</th>";
      $output .= "  <th class=\"ui-state-default\">ticket</th>";
      $output .= "  <th class=\"ui-state-default\">scan</th>";
      $output .= "  <th class=\"ui-state-default\">verified</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_infosec ";
      $q_string .= "where is_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select is_rsdp,count(is_rsdp) ";
      $q_string .= "from rsdp_infosec ";
      $q_string .= "group by is_rsdp ";
      $q_rsdp_infosec = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_infosec) > 0) {
        while ($a_rsdp_infosec = mysqli_fetch_array($q_rsdp_infosec)) {

          if ($a_rsdp_infosec['count(is_rsdp)'] > 1) {
            $q_string  = "select is_id,is_rsdp,is_checklist,is_ticket,is_scan,is_verified ";
            $q_string .= "from rsdp_infosec ";
            $q_string .= "where is_rsdp = " . $a_rsdp_infosec['is_rsdp'] . " ";
            $q_infosec = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_infosec = mysqli_fetch_array($q_infosec)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_infosec['is_id'] . "&select=is_id&table=rsdp_infosec');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                 . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infosec['is_id']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infosec['is_rsdp']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infosec['is_checklist']  . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infosec['is_ticket']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infosec['is_scan']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infosec['is_verified']   . "</td>\n";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">Nothing in rsdp_infosec.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";


      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"32\">rsdp_infrastructure</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">net check</th>";
      $output .= "  <th class=\"ui-state-default\">magic</th>";
      $output .= "  <th class=\"ui-state-default\">dc rack</th>";
      $output .= "  <th class=\"ui-state-default\">dc cabled</th>";
      $output .= "  <th class=\"ui-state-default\">wiki</th>";
      $output .= "  <th class=\"ui-state-default\">svr mgt</th>";
      $output .= "  <th class=\"ui-state-default\">config</th>";
      $output .= "  <th class=\"ui-state-default\">built</th>";
      $output .= "  <th class=\"ui-state-default\">net work</th>";
      $output .= "  <th class=\"ui-state-default\">dns</th>";
      $output .= "  <th class=\"ui-state-default\">ins check</th>";
      $output .= "  <th class=\"ui-state-default\">san fs</th>";
      $output .= "  <th class=\"ui-state-default\">verified</th>";
      $output .= "  <th class=\"ui-state-default\">check list</th>";
      $output .= "  <th class=\"ui-state-default\">backups</th>";
      $output .= "  <th class=\"ui-state-default\">bu verified</th>";
      $output .= "  <th class=\"ui-state-default\">bu check</th>";
      $output .= "  <th class=\"ui-state-default\">monitor</th>";
      $output .= "  <th class=\"ui-state-default\">mon verified</th>";
      $output .= "  <th class=\"ui-state-default\">mon check</th>";
      $output .= "  <th class=\"ui-state-default\">san conf</th>";
      $output .= "  <th class=\"ui-state-default\">provisioned</th>";
      $output .= "  <th class=\"ui-state-default\">pro check</th>";
      $output .= "  <th class=\"ui-state-default\">vm check</th>";
      $output .= "  <th class=\"ui-state-default\">net prov</th>";
      $output .= "  <th class=\"ui-state-default\">san prov</th>";
      $output .= "  <th class=\"ui-state-default\">vm prov</th>";
      $output .= "  <th class=\"ui-state-default\">vm note</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "where if_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select if_rsdp,count(if_rsdp) ";
      $q_string .= "from rsdp_infrastructure ";
      $q_string .= "group by if_rsdp ";
      $q_rsdp_infrastructure = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_infrastructure) > 0) {
        while ($a_rsdp_infrastructure = mysqli_fetch_array($q_rsdp_infrastructure)) {

          if ($a_rsdp_infrastructure['count(if_rsdp)'] > 1) {
            $q_string  = "select if_id, if_rsdp, if_netcheck, if_magic, if_dcrack, if_dccabled, if_wiki, if_svrmgt, if_config, if_built, if_network, if_dns, if_inscheck, if_sanfs, if_verified,";
            $q_string .= "if_checklist, if_backups, if_buverified, if_bucheck, if_monitor, if_monverified, if_moncheck, if_sanconf, if_provisioned, if_procheck, if_vmcheck, if_netprov,";
            $q_string .= "if_sanprov, if_vmprov, if_vmnote ";
            $q_string .= "from rsdp_infrastructure ";
            $q_string .= "where if_rsdp = " . $a_rsdp_infrastructure['if_rsdp'] . " ";
            $q_infrastructure = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_infrastructure = mysqli_fetch_array($q_infrastructure)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_infrastructure['if_id'] . "&select=if_id&table=rsdp_infrastructure');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_id']          . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_rsdp']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_netcheck']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_magic']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_dcrack']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_dccabled']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_wiki']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_svrmgt']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_config']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_built']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_network']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_dns']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_inscheck']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_sanfs']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_verified']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_checklist']   . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_backups']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_buverified']  . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_bucheck']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_monitor']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_monverified'] . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_moncheck']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_sanconf']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_provisioned'] . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_procheck']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_vmcheck']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_netprov']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_sanprov']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_vmprov']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_infrastructure['if_vmnote']      . "</td>\n";
              $output .= "</tr>\n";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"32\">Nothing in rsdp_infrastructure.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"7\">rsdp_osteam</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">sysname</th>";
      $output .= "  <th class=\"ui-state-default\">fqdn</th>";
      $output .= "  <th class=\"ui-state-default\">software</th>";
      $output .= "  <th class=\"ui-state-default\">complete</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_osteam ";
      $q_string .= "where os_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select os_rsdp,count(os_rsdp) ";
      $q_string .= "from rsdp_osteam ";
      $q_string .= "group by os_rsdp ";
      $q_rsdp_osteam = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_osteam) > 0) {
        while ($a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam)) {

          if ($a_rsdp_osteam['count(os_rsdp)'] > 1) {
            $q_string  = "select os_id,os_rsdp,os_sysname,os_fqdn,os_software,os_complete ";
            $q_string .= "from rsdp_osteam ";
            $q_string .= "where os_rsdp = " . $a_rsdp_osteam['os_rsdp'] . " ";
            $q_osteam = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_osteam = mysqli_fetch_array($q_osteam)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_osteam['os_id'] . "&select=os_id&table=rsdp_osteam');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_osteam['os_id']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_osteam['os_rsdp']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_osteam['os_sysname']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_osteam['os_fqdn']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_osteam['os_software']   . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_osteam['os_complete']   . "</td>\n";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"7\">Nothing in rsdp_osteam.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"16\">rsdp_platform</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>";
      $output .= "  <th class=\"ui-state-default\">model</th>";
      $output .= "  <th class=\"ui-state-default\">asset</th>";
      $output .= "  <th class=\"ui-state-default\">serial</th>";
      $output .= "  <th class=\"ui-state-default\">hba</th>";
      $output .= "  <th class=\"ui-state-default\">redundant</th>";
      $output .= "  <th class=\"ui-state-default\">row</th>";
      $output .= "  <th class=\"ui-state-default\">rack</th>";
      $output .= "  <th class=\"ui-state-default\">unit</th>";
      $output .= "  <th class=\"ui-state-default\">special</th>";
      $output .= "  <th class=\"ui-state-default\">circuita</th>";
      $output .= "  <th class=\"ui-state-default\">circuitb</th>";
      $output .= "  <th class=\"ui-state-default\">complete</th>";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_platform ";
      $q_string .= "where pf_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select pf_rsdp,count(pf_rsdp) ";
      $q_string .= "from rsdp_platform ";
      $q_string .= "group by pf_rsdp ";
      $q_rsdp_platform = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_platform) > 0) {
        while ($a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform)) {

          if ($a_rsdp_platform['count(pf_rsdp)'] > 1) {
            $q_string  = "select pf_id, pf_rsdp, pf_model, pf_asset, pf_serial, pf_hba, pf_redundant, pf_row, pf_rack, pf_unit, pf_special, pf_circuita, pf_circuitb, pf_complete ";
            $q_string .= "from rsdp_platform ";
            $q_string .= "where pf_rsdp = " . $a_rsdp_platform['pf_rsdp'] . " ";
            $q_platform = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_platform = mysqli_fetch_array($q_platform)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_platform['pf_id'] . "&select=pf_id&table=rsdp_platform');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_id']           . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_rsdp']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_model']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_asset']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_serial']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_hba']          . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_redundant']    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_row']          . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_rack']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_unit']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_special']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_circuita']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_circuitb']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_platform['pf_complete']     . "</td>\n";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"16\">Nothing in rsdp_platform.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\" colspan=\"19\">rsdp_tickets</th>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\">id</th>\n";
      $output .= "  <th class=\"ui-state-default\">rsdp</th>\n";
      $output .= "  <th class=\"ui-state-default\">build</th>\n";
      $output .= "  <th class=\"ui-state-default\">san</th>\n";
      $output .= "  <th class=\"ui-state-default\">network</th>\n";
      $output .= "  <th class=\"ui-state-default\">datacenter</th>\n";
      $output .= "  <th class=\"ui-state-default\">virtual</th>\n";
      $output .= "  <th class=\"ui-state-default\">sysins</th>\n";
      $output .= "  <th class=\"ui-state-default\">sysdns</th>\n";
      $output .= "  <th class=\"ui-state-default\">storage</th>\n";
      $output .= "  <th class=\"ui-state-default\">syscnf</th>\n";
      $output .= "  <th class=\"ui-state-default\">backups</th>\n";
      $output .= "  <th class=\"ui-state-default\">monitor</th>\n";
      $output .= "  <th class=\"ui-state-default\">appins</th>\n";
      $output .= "  <th class=\"ui-state-default\">appmon</th>\n";
      $output .= "  <th class=\"ui-state-default\">appcnf</th>\n";
      $output .= "  <th class=\"ui-state-default\">infosec</th>\n";
      $output .= "  <th class=\"ui-state-default\">sysscan</th>\n";
      $output .= "</tr>";

      $count = 0;

      $q_string  = "delete ";
      $q_string .= "from rsdp_tickets ";
      $q_string .= "where tkt_rsdp = 0";
      $result = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      $q_string  = "select tkt_rsdp,count(tkt_rsdp) ";
      $q_string .= "from rsdp_tickets ";
      $q_string .= "group by tkt_rsdp ";
      $q_rsdp_tickets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_rsdp_tickets) > 0) {
        while ($a_rsdp_tickets = mysqli_fetch_array($q_rsdp_tickets)) {

          if ($a_rsdp_tickets['count(tkt_rsdp)'] > 1) {
            $q_string  = "select tkt_id, tkt_rsdp, tkt_build, tkt_san, tkt_network, tkt_datacenter, tkt_virtual, tkt_sysins, tkt_sysdns, tkt_storage, tkt_syscnf, tkt_backups, tkt_monitor, tkt_appins, tkt_appmon, tkt_appcnf, tkt_infosec, tkt_sysscan ";
            $q_string .= "from rsdp_tickets ";
            $q_string .= "where tkt_rsdp = " . $a_rsdp_tickets['tkt_rsdp'] . " ";
            $q_tickets = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            while ($a_tickets = mysqli_fetch_array($q_tickets)) {

              $linkdel = "<input type=\"button\" value=\"Remove\" onclick=\"delete_item('rsdpdup.del.php?id=" . $a_tickets['tkt_id'] . "&select=tkt_id&table=rsdp_tickets');\">";
              $linkend = "</a>";

              $output .= "<tr>\n";
              $output .= "  <td class=\"ui-widget-content delete\">" . $linkdel                                    . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_id']          . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_rsdp']        . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_build']       . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_san']         . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_network']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_datacenter']  . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_virtual']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_sysins']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_sysdns']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_storage']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_syscnf']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_backups']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_monitor']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_appins']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_appmon']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_appcnf']      . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_infosec']     . "</td>\n";
              $output .= "  <td class=\"ui-widget-content\">"                     . $a_tickets['tkt_sysscan']     . "</td>\n";
              $count++;
            }
          }
        }
      }
      if ($count == 0) {
        $output .= "<tr>";
        $output .= "  <td class=\"ui-widget-content\" colspan=\"19\">Nothing in rsdp_tickets.</td>";
        $output .= "</tr>";
      }

      $output .= "</table>";


      print "document.getElementById('table_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
