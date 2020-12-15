<?php
# Script: manage.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "manage.mysql.php";

    if (check_userlevel($db, $AL_Edit)) {
      $formVars['update']       = clean($_GET['update'],      10);
      $formVars['product']      = clean($_GET['product'],     10);
      $formVars['project']      = clean($_GET['project'],     10);
      $formVars['location']     = clean($_GET['location'],    10);
      $formVars['sort']         = clean($_GET['sort'],        20);

      if ($formVars['update'] == 1) {
        $formVars['id']           = clean($_GET['id'],              10);
        $formVars['chk_userid']   = clean($_GET['chk_userid'],      10);
# because chk_status is more global
        $formVars['pending']      = clean($_GET['chk_status'],      10);
        $formVars['chk_priority'] = clean($_GET['chk_priority'],    10);
        $formVars['chk_closed']   = clean($_GET['chk_closed'],      10);
        $formVars['chk_text']     = clean($_GET['error_text'],    1800);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }
        $formVars['chk_status'] = 0;
        if ($formVars['chk_userid'] > 0) {
          $formVars['chk_status'] = 1;
        }
        if ($formVars['pending'] == 'true') {
          $formVars['chk_status'] = 2;
        }
        if ($formVars['chk_closed'] == 'true') {
          $q_string  = "select chk_id,chk_closed ";
          $q_string .= "from chkserver ";
          $q_string .= "where chk_id = " . $formVars['id'] . " ";
          $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_chkserver = mysqli_fetch_array($q_chkserver);

          if ($a_chkserver['chk_closed'] == '0000-00-00 00:00:00') {
            $formVars['chk_closed'] = date('Y-m-d H:i:s');
          } else {
            $formVars['chk_closed'] = $a_chkserver['chk_closed'];
          }

        } else {
          $formVars['chk_closed'] = '0000-00-00 00:00:00';
        }

        logaccess($db, $_SESSION['uid'], $package, "Building the query.");

        $q_string =
          "chk_userid         =   " . $formVars['chk_userid']     . "," .
          "chk_status         =   " . $formVars['chk_status']     . "," .
          "chk_closed         = \"" . $formVars['chk_closed']     . "\"," .
          "chk_text           = \"" . $formVars['chk_text']       . "\"," .
          "chk_priority       =   " . $formVars['chk_priority'];

        if ($formVars['update'] == 1) {
          $query = "update chkserver set " . $q_string . " where chk_id = " . $formVars['id'];
        }

        logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $a_inventory['inv_name']);

        mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the table for viewing.");

# need to build a while statement based on passed variables.
# priority where's are simple and will always be the first block. Simply add more to the where statement.

      $and = 'and ';
      if ($formVars['product'] > 0) {
        $where .= $and . " inv_product = " . $formVars['product'] . " ";
      }
      if ($formVars['project'] > 0) {
        $where .= $and . " inv_project = " . $formVars['project'] . " ";
      }
      if ($formVars['location'] > 0) {
        $where .= $and . " inv_location = " . $formVars['location'] . " ";
      }
      $passthrough  = "?product="   . $formVars['product'];
      $passthrough .= "&project="   . $formVars['project'];
      $passthrough .= "&location="  . $formVars['location'];

      if (isset($_GET["sort"])) {
        $formVars['sort'] = clean($_GET["sort"], 20);
        if ($formVars['sort'] == 'none') {
          $orderby = "order by inv_class,inv_callpath desc,chk_priority,ce_error,inv_name ";
        } else {
          $orderby = "order by " . $formVars['sort'] . ",inv_class,inv_callpath desc,chk_priority,ce_error,inv_name ";
        }
      } else {
        $orderby = "order by inv_class,inv_callpath desc,chk_priority,ce_error,inv_name ";
      }

# priority 1
      $count = 0;
      $output  = "<form name=\"priority1\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_name\">Server Name</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_class\">Service Class</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_priority\">Priority</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=ce_error\">Error Message</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_opened\">Date Opened</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_id,chk_priority,chk_opened ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "where ce_priority = 1 and chk_status = 0 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= $orderby;
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_priority']            . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('priority1').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('pri1_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }


# priority 2
      $count = 0;
      $output  = "<form name=\"priority2\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_name\">Server Name</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_class\">Service Class</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_priority\">Priority</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=ce_error\">Error Message</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_opened\">Date Opened</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_id,chk_priority,chk_opened ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "where ce_priority = 2 and chk_status = 0 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= $orderby;
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_priority']            . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('priority2').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('pri2_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }


# priority 3
      $count = 0;
      $output  = "<form name=\"priority3\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_name\">Server Name</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_class\">Service Class</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_priority\">Priority</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=ce_error\">Error Message</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_opened\">Date Opened</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_id,chk_priority,chk_opened ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "where ce_priority = 3 and chk_status = 0 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= $orderby;
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_priority']            . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('priority3').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('pri3_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }


# priority 4
      $count = 0;
      $output  = "<form name=\"priority4\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_name\">Server Name</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_class\">Service Class</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_priority\">Priority</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=ce_error\">Error Message</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_opened\">Date Opened</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_id,chk_priority,chk_opened ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "where ce_priority = 4 and chk_status = 0 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= $orderby;
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_priority']            . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('priority4').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('pri4_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }


# priority 5
      $count = 0;
      $output  = "<form name=\"priority5\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_name\">Server Name</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=inv_class\">Service Class</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_priority\">Priority</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=ce_error\">Error Message</a></th>\n";
      $output .= "  <th class=\"ui-state-default\"><a href=\"manage.php" . $passthrough . "&sort=chk_opened\">Date Opened</a></th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_id,chk_priority,chk_opened ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "where ce_priority = 5 and chk_status = 0 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= $orderby;
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_priority']            . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('priority5').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('pri5_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }


# closed errors
# adding an if to block out this search. there are so many, it's taking forever to load.
    if ($formVars['id'] == 'bob') {
      $count = 0;
      $output  = "<form name=\"closed\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Service Class</th>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date Opened</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date Closed</th>\n";
      $output .= "  <th class=\"ui-state-default\">Closed By</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_id,chk_opened,chk_closed,chk_userid,usr_name ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "left join users on users.usr_id = chkserver.chk_userid ";
      $q_string .= "where chk_closed != '0000-00-00 00:00:00' " . $where;
      $q_string .= "order by inv_class,ce_error,inv_name ";
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        if ($a_chkserver['chk_userid'] == 0) {
          $closedby = 'Automatic';
        } else {
          $closedby = $a_chkserver['usr_name'];
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_closed']              . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $closedby                               . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('is_closed').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('closed_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }
    }
# adding closing brace to block closed; a lot and it's killing the page



# claimed errors
      $count = 0;
      $output  = "<form name=\"claimed\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Service Class</th>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Claimed By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Message Text</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date Opened</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_text,chk_id,chk_opened,usr_name ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "left join users on users.usr_id = chkserver.chk_userid ";
      $q_string .= "where chk_status = 1 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= "order by inv_class,ce_error,inv_name ";
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['usr_name']                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_text']                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('is_claimed').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('claimed_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }



# pending errors
      $count = 0;
      $output  = "<form name=\"pending\">\n";
      $output .= "<table id=\"details-table\" class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Server Name</th>\n";
      $output .= "  <th class=\"ui-state-default\">Service Class</th>\n";
      $output .= "  <th class=\"ui-state-default\">Error Message</th>\n";
      $output .= "  <th class=\"ui-state-default\">Claimed By</th>\n";
      $output .= "  <th class=\"ui-state-default\">Message Text</th>\n";
      $output .= "  <th class=\"ui-state-default\">Date Opened</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select inv_id,inv_name,inv_callpath,svc_name,ce_error,chk_text,chk_id,chk_opened,usr_name ";
      $q_string .= "from chkserver ";
      $q_string .= "left join inventory on inventory.inv_id = chkserver.chk_companyid ";
      $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
      $q_string .= "left join products on products.prod_id = inventory.inv_product ";
      $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
      $q_string .= "left join chkerrors on chkerrors.ce_id = chkserver.chk_errorid ";
      $q_string .= "left join service on service.svc_id = inventory.inv_class ";
      $q_string .= "left join users on users.usr_id = chkserver.chk_userid ";
      $q_string .= "where chk_status = 2 and chk_closed = '0000-00-00 00:00:00' " . $where;
      $q_string .= "order by inv_class,ce_error,inv_name ";
      $q_chkserver = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_chkserver = mysqli_fetch_array($q_chkserver)) {

# want to open the dialog box
        if (check_grouplevel($db, $GRP_Unix)) {
          $linkstart = "<a href=\"" . $Editroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = "<a href=\"#\" onclick=\"show_file('manage.fill.php?id="  . $a_chkserver['chk_id'] . "');jQuery('#dialogError').dialog('open');return false;\">";
        } else {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_chkserver['inv_id'] . "\" target=\"_blank\">";
          $linkerror = $linkstart;
        }
        $linkend   = "</a>";

        if ($a_chkserver['svc_name'] == '') {
          $service = 'No Service Class Assigned';
        } else {
          $service = $a_chkserver['svc_name'];
        }

        if ($a_chkserver['inv_callpath']) {
          $class = 'ui-state-error';
        } else {
          $class = 'ui-widget-content';
        }

        $count++;
        $output .= "<tr>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkstart . $a_chkserver['inv_name']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $service                                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">" . $linkerror . $a_chkserver['ce_error']     . $linkend . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['usr_name']                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_text']                . "</td>\n";
        $output .= "<td class=\"" . $class . "\">"              . $a_chkserver['chk_opened']              . "</td>\n";
        $output .= "</tr>\n";

      }

      $output .= "</table>\n";
      $output .= "</form>\n";

      if ($count > 0) {
        print "document.getElementById('is_pending').innerHTML = '" . mysqli_real_escape_string($count) . "';\n";
        print "document.getElementById('pending_mysql').innerHTML = '" . mysqli_real_escape_string($output) . "';\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
