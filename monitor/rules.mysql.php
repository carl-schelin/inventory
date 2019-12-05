<?php
# Script: rules.mysql.php
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
    $package = "rules.mysql.php";
    $formVars['update']     = clean($_GET['update'],     10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }

    if (isset($_SESSION['sort'])) {
      $orderby = "order by " . clean($_SESSION['sort'], 20) . " ";
    } else {
      $orderby = "order by rule_description ";
    }

    if (check_userlevel($AL_Edit)) {
      if ($formVars['update'] == 0 || $formVars['update'] == 1) {
        $formVars['id']                    = clean($_GET['id'],                  10);
        $formVars['rule_parent']           = clean($_GET['rule_parent'],         10);
        $formVars['rule_description']      = clean($_GET['rule_description'],   255);
        $formVars['rule_annotate']         = clean($_GET['rule_annotate'],      255);
        $formVars['rule_group']            = clean($_GET['rule_group'],          10);
        $formVars['rule_source']           = clean($_GET['rule_source'],         10);
        $formVars['rule_application']      = clean($_GET['rule_application'],    10);
        $formVars['rule_object']           = clean($_GET['rule_object'],         10);
        $formVars['rule_message']          = clean($_GET['rule_message'],        10);
        $formVars['rule_page']             = clean($_GET['rule_page'],           10);
        $formVars['rule_email']            = clean($_GET['rule_email'],          10);
        $formVars['rule_autoack']          = clean($_GET['rule_autoack'],        10);
        $formVars['rule_deleted']          = clean($_GET['rule_deleted'],        10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

        if ($formVars['rule_autoack'] == 'true') {
          $formVars['rule_autoack'] = 1;
        } else {
          $formVars['rule_autoack'] = 0;
        }
        if ($formVars['rule_deleted'] == 'true') {
          $formVars['rule_deleted'] = 1;
        } else {
          $formVars['rule_deleted'] = 0;
        }

        if (strlen($formVars['rule_description']) > 0) {
          logaccess($_SESSION['uid'], $package, "Building the query.");

          $q_string =
            "rule_parent        =   " . $formVars['rule_parent']       . "," .
            "rule_description   = \"" . $formVars['rule_description']  . "\"," .
            "rule_annotate      = \"" . $formVars['rule_annotate']     . "\"," .
            "rule_group         =   " . $formVars['rule_group']        . "," . 
            "rule_source        =   " . $formVars['rule_source']       . "," . 
            "rule_application   =   " . $formVars['rule_application']  . "," .
            "rule_object        =   " . $formVars['rule_object']       . "," .
            "rule_message       =   " . $formVars['rule_message']      . "," .
            "rule_page          =   " . $formVars['rule_page']         . "," .
            "rule_email         =   " . $formVars['rule_email']        . "," .
            "rule_autoack       =   " . $formVars['rule_autoack']      . "," .
            "rule_deleted       =   " . $formVars['rule_deleted'];

          if ($formVars['update'] == 0) {
            $query = "insert into rules set rule_id = NULL, " . $q_string;
          }
          if ($formVars['update'] == 1) {
            $query = "update rules set " . $q_string . " where rule_id = " . $formVars['id'];
          }

          logaccess($_SESSION['uid'], $package, "Saving Changes to: " . $formVars['rule_description']);

          mysql_query($query) or die($query . ": " . mysql_error());
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      logaccess($_SESSION['uid'], $package, "Creating the table for viewing.");

      $output  = "<p></p>\n";
      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>\n";
      $output .= "  <th class=\"ui-state-default\">Rule Listing</th>\n";
      $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('rule-listing-help');\">Help</a></th>\n";
      $output .= "</tr>\n";
      $output .= "</table>\n";

      $output .= "<div id=\"rule-listing-help\" style=\"display: none\">\n";

      $output .= "<div class=\"main-help ui-widget-content\">\n";
      $output .= "<ul>\n";
      $output .= "  <li><strong>Rule Listing</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li><strong>Editing</strong> - Click on a Rule to edit it.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "<ul>\n";
      $output .= "  <li><strong>Notes</strong>\n";
      $output .= "  <ul>\n";
      $output .= "    <li>Click the <strong>Rule Management</strong> title bar to toggle the <strong>Rule Form</strong>.</li>\n";
      $output .= "  </ul></li>\n";
      $output .= "</ul>\n";

      $output .= "</div>\n";

      $output .= "</div>\n";

      $output .= "<table class=\"ui-styled-table\">\n";
      $output .= "<tr>";
      $output .= "  <th class=\"ui-state-default\">Del</th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"rules.php?sort=rule_description\">Description</a></th>";
      $output .= "  <th class=\"ui-state-default\"><a href=\"rules.php?sort=rule_group\">Page Group</a></th>";
      $output .= "  <th class=\"ui-state-default\">Rule</th>";
      $output .= "</tr>";

      $q_string  = "select rule_id,rule_description,key_description,key_page,key_email,key_annotate,key_critical_annotate,src_node,app_description,obj_name,msg_group,";
      $q_string .= "rule_page,rule_email,rule_autoack,rule_deleted,rule_annotate ";
      $q_string .= "from rules ";
      $q_string .= "left join keywords      on keywords.key_id      = rules.rule_group ";
      $q_string .= "left join source_node   on source_node.src_id   = rules.rule_source ";
      $q_string .= "left join application   on application.app_id   = rules.rule_application ";
      $q_string .= "left join objects       on objects.obj_id       = rules.rule_object ";
      $q_string .= "left join message_group on message_group.msg_id = rules.rule_message ";
      $q_string .= $orderby;
      $q_rules = mysql_query($q_string) or die (mysql_error());
      while ($a_rules = mysql_fetch_array($q_rules)) {

        $linkstart = "<a href=\"#\" onclick=\"show_file('rules.fill.php?id="  . $a_rules['rule_id'] . "');jQuery('#dialogRule').dialog('open');\">";
        $linkdel   = "<input type=\"button\" value=\"Delete\" onclick=\"delete_line('rules.del.php?id=" . $a_rules['rule_id'] . "');\">";
        $linkend = "</a>";

        if ($a_rules['rule_page']) {
          $page = "Yes";
        } else {
          $page = "No";
        }
        if ($a_rules['rule_email']) {
          $email = "Yes";
        } else {
          $email = "No";
        }
        if ($a_rules['rule_autoack']) {
          $autoack = "Y";
        } else {
          $autoack = "N";
        }
        if ($a_rules['rule_deleted']) {
          $class = "ui-state-highlight";
        } else {
          $class = "ui-widget-content";
        }
        if (strlen($a_rules['rule_annotate']) != 0) {
          $annotate = $a_rules['rule_annotate'];
        } else {
          $annotate = $a_rules['key_annotate'];
        }
        if ($a_rules['rule_page'] == 0) {
          $rule_page = "";
        } else {
          if ($a_rules['rule_page'] == 1) {
            $rule_page = $a_rules['key_page'];
          } else {
            $rule_page = $a_rules['key_email'];
          }
        }
        if ($a_rules['rule_email'] == 0) {
          $rule_email = "";
        } else {
          if ($a_rules['rule_email'] == 1) {
            $rule_email = $a_rules['key_page'];
          } else {
            $rule_email = $a_rules['key_email'];
          }
        }

        $output .= "<tr>";
        $output .= "  <td class=\"" . $class . " delete\">" . $linkdel                                             . "</td>";
        $output .= "  <td class=\"" . $class . "\">"        . $linkstart . $a_rules['rule_description'] . $linkend . "</td>";
        $output .= "  <td class=\"" . $class . "\">"                     . $a_rules['key_description']             . "</td>";
        $output .= "  <td class=\"" . $class . "\" title=\"ANNOTATE = '" . $annotate . "'\nCRITICAL = '" . $a_rules['key_critical_annotate'] . "'\">";

        $output .= "if [[ \$source_node = \"" . $a_rules['src_node'] . "\" ]] then; ";


        if ($a_rules['app_description'] != '') {
          $output .= "if [[ \$application = \"" . $a_rules['app_description'] . "\" ]]; then ";
        } else {
          if ($a_rules['obj_name'] != '') {
            $output .= "if [[ \$object = \"" . $a_rules['obj_name'] . "\" ]]; then ";
          } else {
            if ($a_rules['msg_group'] != '') {
              $output .= "if [[ \$message_group = \"" . $a_rules['msg_group'] . "\" ]]; then ";
            }
          }
        }

        $output .= "PAGE = \"" . $rule_page . "\"; ";
        $output .= "EMAIL = \"" . $rule_email . "\"; ";
        $output .= "AUTOACK = \"" . $autoack . "\"; ";

        $output .= "fi; ";
        $output .= "fi; ";

        $output .= "</td></tr>";

      }
      $output .= "</table>";

      mysql_free_result($q_rules);

      print "document.getElementById('table_mysql').innerHTML = '" . mysql_real_escape_string($output) . "';\n\n";

      print "document.rules.rule_description.value = '';\n";
      print "document.rules.rule_annotate.value = '';\n";
      print "document.rules.rule_group[0].selected = true;\n";
      print "document.rules.rule_source[0].selected = true;\n";
      print "document.rules.rule_application[0].selected = true;\n";
      print "document.rules.rule_object[0].selected = true;\n";
      print "document.rules.rule_message[0].selected = true;\n";
      print "document.rules.rule_page.checked = false;\n";
      print "document.rules.rule_email.checked = false;\n";
      print "document.rules.rule_autoack.checked = false;\n";
      print "document.rules.rule_deleted.checked = false;\n";

    } else {
      logaccess($_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
