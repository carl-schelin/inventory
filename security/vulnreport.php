<?php
# Script: vulnreport.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "vulnreport.php";

  logaccess($formVars['uid'], $package, "Getting a report on vulnerabilities.");

  $formVars['group']     = clean($_GET['group'],      10);
  $formVars['product']   = clean($_GET['product'],    10);
  $formVars['project']   = clean($_GET['project'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],     10);
  $formVars['country']   = clean($_GET['country'],    10);
  $formVars['state']     = clean($_GET['state'],      10);
  $formVars['city']      = clean($_GET['city'],       10);
  $formVars['location']  = clean($_GET['location'],   10);
  $formVars['csv']       = clean($_GET['csv'],        10);
  $formVars['type']      = clean($_GET['type'],       10);

  if ($formVars['group'] == '') {
    $formVars['group'] = $_SESSION['group'];
  }
  if ($formVars['product'] == '') {
    $formVars['product'] = 0;
  }
  if ($formVars['project'] == '') {
    $formVars['project'] = 0;
  }
  if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['country'] == '') {
    $formVars['country'] = 0;
  }
  if ($formVars['state'] == '') {
    $formVars['state'] = 0;
  }
  if ($formVars['city'] == '') {
    $formVars['city'] = 0;
  }
  if ($formVars['location'] == '') {
    $formVars['location'] = 0;
  }
  if ($formVars['csv'] == '') {
    $formVars['csv'] = 'false';
  }
  if ($formVars['type'] == '' ) {
    $formVars['type'] = '';
  }

  $and = " where";
  $argument = "";
  $ampersand = "?";
  if ($formVars['group'] == 0) {
    $group = '';
  } else {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $argument .= $ampersand . "group=" . $formVars['group'];
    $ampersand = "&";
    $and = " and";
  }

  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";

      $q_string  = "select prod_name ";
      $q_string .= "from products ";
      $q_string .= "where prod_id = " . $formVars['product'] . " ";
      $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_products = mysql_fetch_array($q_products);
      $product_name = $a_products['prod_name'];

      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
        $argument .= $ampersand . "project=" . $formVars['project'];
        $ampersand = "&";

        $q_string  = "select prj_name ";
        $q_string .= "from projects ";
        $q_string .= "where prj_id = " . $formVars['project'] . " ";
        $q_projects = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_projects = mysql_fetch_array($q_projects);
        $project_name = $a_projects['prj_name'];

      }
      $and = " and";
    }
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
    $argument .= $ampersand . "inwork=" . $formVars['inwork'];
    $ampersand = "&";
    $and = " and";
  }

# Location management. With Country, State, City, and Data Center selectable, this needs to
# expand to permit the viewing of systems in larger areas
# two ways here.
# country > 0, state > 0, city > 0, location > 0
# or country == 0 and location >  0

  $location = '';
  if ($formVars['country'] == 0 && $formVars['location'] > 0) {
    $location = $and . " inv_location = " . $formVars['location'] . " ";
    $argument .= $ampersand . "location=" . $formVars['location'];
    $ampersand = "&";
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $argument .= $ampersand . "country=" . $formVars['country'];
      $ampersand = "&";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $argument .= $ampersand . "state=" . $formVars['state'];
      $ampersand = "&";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $argument .= $ampersand . "city=" . $formVars['city'];
      $ampersand = "&";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $argument .= $ampersand . "location=" . $formVars['location'];
      $ampersand = "&";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
    $argument .= $ampersand . "type=" . $formVars['type'];
    $ampersand = "&";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

# if help has not been seen yet,
  if (show_Help($Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Vulnerability Report</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<?php

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">Vulnerability Report Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report uses the filter system to let you generate a specific report. By default you get a product listing for the selected group, ";
  print "the number of servers located in that product, and then the number of each type of vulnerability associated with that product.</p>\n";

  print "<p>Clicking on a product will redisplay the report and list all the servers in that product with the breakdown of vulnerabilities.</p>\n";

  print "<p>Finally, clicking on a server in the product listing, will take you to that server's vulnerability tab so you see what the vulnerabilities are.</p>\n";

  print "</div>\n\n";

  print "</div>\n\n";

  if ($formVars['csv'] == 'false') {
    print "<table class=\"ui-styled-table\">\n";

    if ($formVars['product'] > 0) {
      if ($formVars['project'] > 0) {
        print "<tr>\n";
        print "  <th class=\"ui-state-default\" colspan=\"6\">" . $product_name . ": " . $project_name . "</th>\n";
        print "</tr>\n";
      } else {
        print "<tr>\n";
        print "  <th class=\"ui-state-default\" colspan=\"6\">" . $product_name . "</th>\n";
        print "</tr>\n";
      }
      print "<tr>\n";
      print "  <th class=\"ui-state-default\">" . "Server Name"      . "</th>\n";
    } else {
      print "<tr>\n";
      print "  <th class=\"ui-state-default\">" . "Product"      . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Number of Servers" . "</th>\n";
    }

    print "  <th class=\"ui-state-default\">" . "Critical" . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "High" . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Medium" . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Low"   . "</th>\n";
    print "  <th class=\"ui-state-default\">" . "Information"   . "</th>\n";
    print "</tr>\n";
  } else {

    print "<textarea rows=\"100\" cols=\"200\">";

    if ($formVars['product'] > 0) {
      if ($formVars['project'] > 0) {
        print "\"" . $product_name . ": " . $project_name . "\n";
      } else {
        print "\"" . $product_name . "\n";
      }
      print "\"" . "Server Name"      . "\",";
    } else {
      print "\"" . "Product"      . "\",";
      print "\"" . "Number of Servers" . "\",";
    }

    print "\"Critical\",";
    print "\"High\",";
    print "\"Medium\",";
    print "\"Low\",";
    print "\"Information\"\n";
  }

  $class = "ui-widget-content";

  $q_string  = "select prod_id,prod_name,COUNT(inv_id) ";
  $q_string .= "from products ";
  $q_string .= "left join inventory on inventory.inv_product = products.prod_id ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities    on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join zones     on zones.zone_id         = inventory.inv_zone ";
  $q_string .= "left join models    on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join groups    on groups.grp_id         = inventory.inv_manager ";
  $q_string .= $group . $product . $inwork . $location . $type . " ";
  $q_string .= "group by prod_name ";
  $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_products = mysql_fetch_array($q_products)) {

    $critical = 0;
    $high = 0;
    $medium = 0;
    $low = 0;
    $information = 0;
    $crithist = 0;
    $highhist = 0;
    $medhist = 0;
    $lowhist = 0;
    $infohist = 0;

    if ($formVars['product'] > 0) {
      $q_string  = "select inv_id,inv_name ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_product = " . $formVars['product'] . " ";
      if ($formVars['project'] > 0) {
        $q_string .= "and inv_project = " . $formVars['project'] . " ";
      }
      if ($formVars['group'] > 0) {
        $q_string .= "and inv_manager = " . $formVars['group'] . " ";
      }
      $q_string .= "order by inv_name ";
      $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_inventory = mysql_fetch_array($q_inventory)) {

        $critical = 0;
        $high = 0;
        $medium = 0;
        $low = 0;
        $information = 0;
        $crithist = 0;
        $highhist = 0;
        $medhist = 0;
        $lowhist = 0;
        $infohist = 0;

        $q_string  = "select sev_name,vuln_delete ";
        $q_string .= "from interface ";
        $q_string .= "left join vulnerabilities on vulnerabilities.vuln_interface = interface.int_id ";
        $q_string .= "left join security        on security.sec_id                = vulnerabilities.vuln_securityid ";
        $q_string .= "left join severity        on severity.sev_id                = security.sec_severity ";
        $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " ";
        $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        while ($a_interface = mysql_fetch_array($q_interface)) {
          if ($a_interface['sev_name'] == 'Critical') {
            if ($a_interface['vuln_delete']) {
              $crithist++;
            } else {
              $critical++;
            }
          }
          if ($a_interface['sev_name'] == 'High') {
            if ($a_interface['vuln_delete']) {
              $highhist++;
            } else {
              $high++;
            }
          }
          if ($a_interface['sev_name'] == 'Medium') {
            if ($a_interface['vuln_delete']) {
              $medhist++;
            } else {
              $medium++;
            }
          }
          if ($a_interface['sev_name'] == 'Low') {
            if ($a_interface['vuln_delete']) {
              $lowhist++;
            } else {
              $low++;
            }
          }
          if ($a_interface['sev_name'] == 'Info') {
            if ($a_interface['vuln_delete']) {
              $infohist++;
            } else {
              $information++;
            }
          }
        }

        $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#vulnerabilities\" target=\"_blank\">";
        $linkend = "</a>";

        if ($formVars['csv'] == 'false') {
          print "<tr>\n";
          print "  <td class=\"" . $class . "\">"        . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";
          print "  <td class=\"" . $class . " delete\">" . $critical                                        . "</td>\n";
          print "  <td class=\"" . $class . " delete\">" . $high                                            . "</td>\n";
          print "  <td class=\"" . $class . " delete\">" . $medium                                          . "</td>\n";
          print "  <td class=\"" . $class . " delete\">" . $low                                             . "</td>\n";
          print "  <td class=\"" . $class . " delete\">" . $information                                     . "</td>\n";
          print "</tr>\n";
        } else {
          print "\"" . $a_inventory['inv_name'] . "\",";
          print "\"" . $critical                . "\",";
          print "\"" . $high                    . "\",";
          print "\"" . $medium                  . "\",";
          print "\"" . $low                     . "\",";
          print "\"" . $information             . "\"/n";
        }
      }
    } else {

      $q_string  = "select sev_name,vuln_delete ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory       on inventory.inv_id               = interface.int_companyid ";
      $q_string .= "left join vulnerabilities on vulnerabilities.vuln_interface = interface.int_id ";
      $q_string .= "left join security        on security.sec_id                = vulnerabilities.vuln_securityid ";
      $q_string .= "left join severity        on severity.sev_id                = security.sec_severity ";
      $q_string .= "where inventory.inv_product = " . $a_products['prod_id'] . " and inv_status = 0 ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        if ($a_interface['sev_name'] == 'Critical') {
          if ($a_interface['vuln_delete']) {
            $crithist++;
          } else {
            $critical++;
          }
        }
        if ($a_interface['sev_name'] == 'High') {
          if ($a_interface['vuln_delete']) {
            $highhist++;
          } else {
            $high++;
          }
        }
        if ($a_interface['sev_name'] == 'Medium') {
          if ($a_interface['vuln_delete']) {
            $medhist++;
          } else {
            $medium++;
          }
        }
        if ($a_interface['sev_name'] == 'Low') {
          if ($a_interface['vuln_delete']) {
            $lowhist++;
          } else {
            $low++;
          }
        }
        if ($a_interface['sev_name'] == 'Info') {
          if ($a_interface['vuln_delete']) {
            $infohist++;
          } else {
            $information++;
          }
        }
      }

      $linkstart = "<a href=\"" . $Securityroot . "/vulnreport.php" . $argument . $ampersand . "product=" . $a_products['prod_id'] . "\" target=\"_blank\">";
      $linkend = "</a>";

      if ($formVars['csv'] == 'false') {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_name'] . $linkend . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $a_products['COUNT(inv_id)']        . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $critical                           . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $high                               . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $medium                             . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $low                                . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $information                        . "</td>\n";
        print "</tr>\n";
      } else {
        print "\"" . $a_products['prod_name']       . "\",";
        print "\"" . $a_products['COUNT(inv_id)']   . "\",";
        print "\"" . $critical                      . "\",";
        print "\"" . $high                          . "\",";
        print "\"" . $medium                        . "\",";
        print "\"" . $low                           . "\",";
        print "\"" . $information                   . "\"\n";
      }
    }
  }

  if ($formVars['csv'] == 'false') {
    print "</table>\n";
  } else {
    print "</textarea>\n";
  }
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
