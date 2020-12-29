<?php
# Script: eolreport.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "eolreport.php";

  logaccess($db, $formVars['uid'], $package, "Getting a report on vulnerabilities.");

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
      $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_products = mysqli_fetch_array($q_products);
      $product_name = $a_products['prod_name'];

      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
        $argument .= $ampersand . "project=" . $formVars['project'];
        $ampersand = "&";

        $q_string  = "select prj_name ";
        $q_string .= "from projects ";
        $q_string .= "where prj_id = " . $formVars['project'] . " ";
        $q_projects = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        $a_projects = mysqli_fetch_array($q_projects);
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
  if (show_Help($db, $Reportpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Hardware/Software End of Life Report</title>

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
  print "  <th class=\"ui-state-default\">Hardware/Software Report Listing</th>\n";
  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<p>This report uses the filter system to let you generate a specific report. By default you get a product listing for the selected group, ";
  print "the number of servers located in that product, then the count of software that has reached end of support/life for all the servers associated ";
  print "with that product. The next column lists all software where an end of support/life date has not been entered.</p>\n";

  print "<p>Since many of the servers or devices are virtual and not physical, the next column provides a count of physical devices associated with ";
  print "this product. In this way, you have an accurate view of the end of support/life status of the servers for this product. The next two columns ";
  print "count the number of servers which have reached end of support/life and the number of servers where a date hasn't been entered.</p>\n";

  print "<p>Note that Dell equipment end of support/life dates are 5 years after the date of purchase.</p>\n";

  print "<p>Note that the software count includes all software installed on the server where the physical count only counts the main device and none ";
  print "of the components. It is assumed that the components are end of support/life as well so there's no need to include them in the overall count.</p>\n";

  print "<p>Clicking on a product will redisplay the report and list all the servers in that product with the listing of hardware and software.</p>\n";

  print "<p>Finally, clicking on a server in the product listing, will take you to that server's main tab. You'll need to select the Software or Hardware ";
  print "tab in order to view those listings.</p>\n";

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
      print "  <th class=\"ui-state-default\">" . "Type" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Vendor" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Software" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "EOL Date" . "</th>\n";
      print "</tr>\n";
    } else {
      print "<tr>\n";
      print "  <th class=\"ui-state-default\">" . "Product"      . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Number of Servers" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Software" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Software Undated" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Physical Devices" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Hardware" . "</th>\n";
      print "  <th class=\"ui-state-default\">" . "Hardware Undated" . "</th>\n";
      print "</tr>\n";
    }
  } else {

    print "<textarea rows=\"100\" cols=\"200\">";

    if ($formVars['product'] > 0) {
      if ($formVars['project'] > 0) {
        print "\"" . $product_name . ": " . $project_name . "\n";
      } else {
        print "\"" . $product_name . "\n";
      }
      print "\"" . "Server Name"      . "\",";
      print "\"Type\",";
      print "\"Vendor\",";
      print "\"Software\",";
      print "\"EOL Date\"\n";
    } else {
      print "\"" . "Product"      . "\",";
      print "\"" . "Number of Servers" . "\",";
      print "\"Software\",";
      print "\"Software Undated\",";
      print "\"Physical Devices\",";
      print "\"Hardware\",";
      print "\"Hardware Undated\"\n";
    }
  }

  $totalsweol = 0;
  $totalswuncounted = 0;
  $totalhweol = 0;
  $totalhwuncounted = 0;
  $totalservers = 0;
  $totalhardware = 0;
  $saved_name = '';
  $today = date('Y-m-d');

  $q_string  = "select prod_id,prod_name,COUNT(inv_id) ";
  $q_string .= "from products ";
  $q_string .= "left join inventory on inventory.inv_product = products.prod_id ";
  $q_string .= "left join hardware  on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join locations on locations.loc_id      = inventory.inv_location ";
  $q_string .= "left join cities    on cities.ct_id          = locations.loc_city ";
  $q_string .= "left join zones     on zones.zone_id         = inventory.inv_zone ";
  $q_string .= "left join models    on models.mod_id         = hardware.hw_vendorid ";
  $q_string .= "left join a_groups    on a_groups.grp_id         = inventory.inv_manager ";
  $q_string .= $group . $product . $inwork . $location . $type . " ";
  $q_string .= "group by prod_name ";
  $q_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_products = mysqli_fetch_array($q_products)) {

    $sweol = 0;
    $swuncounted = 0;
    $hweol = 0;
    $hwuncounted = 0;
    $hwtotal = 0;
    $hardware = 0;
    $totalservers += $a_products['COUNT(inv_id)'];

    if ($formVars['product'] > 0) {
      $q_string  = "select inv_id,inv_name,inv_virtual ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_status = 0 and inv_product = " . $formVars['product'] . " ";
      if ($formVars['project'] > 0) {
        $q_string .= "and inv_project = " . $formVars['project'] . " ";
      }
      if ($formVars['group'] > 0) {
        $q_string .= "and inv_manager = " . $formVars['group'] . " ";
      }
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_inventory = mysqli_fetch_array($q_inventory)) {

        $q_string  = "select mod_vendor,mod_name,mod_eol,mod_virtual,hw_purchased ";
        $q_string .= "from models ";
        $q_string .= "left join hardware on hardware.hw_vendorid = models.mod_id ";
        $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1 and hw_deleted = 0 ";
        $q_models = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_models = mysqli_fetch_array($q_models)) {
          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#hardware\" target=\"_blank\">";
          $linkend = "</a>";

          $newdate = '0000-00-00';
          if ($a_models['mod_virtual'] == 0) {
            if ($a_models['mod_vendor'] == 'Dell') {
# For Dell, the end of support is 5 years after the purchase date
              $date = explode("-", $a_models['hw_purchased']);
              $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
              $newdate = date("Y-m-d",$support);
            } else {
              if ($a_models['mod_eol'] == '') {
                $a_models['mod_eol'] = '0000-00-00';
              }
# just commonalizing the newdate value for the check
              $newdate = $a_models['mod_eol'];
            }

            if ($newdate == '0000-00-00') {
              $hwuncounted++;
            } else {
              if ($newdate < $today) {
                $hweol++;
              }
            }
            $hardware++;
          }

          $class = "ui-widget-content";
          if ($newdate < $today && $newdate != '0000-00-00') {
            $class = "ui-state-error";
          }

          if ($formVars['csv'] == 'false') {
            print "<tr>\n";
            print "  <td class=\"" . $class . "\">"        . $linkstart . $a_inventory['inv_name'] . $linkend . "</td>\n";
            print "  <td class=\"" . $class . " delete\">" . "Hardware"                                       . "</td>\n";
            print "  <td class=\"" . $class . "\">"        . $a_models['mod_vendor']                          . "</td>\n";
            print "  <td class=\"" . $class . "\">"        . $a_models['mod_name']                            . "</td>\n";
            print "  <td class=\"" . $class . " delete\">" . $newdate                                         . "</td>\n";
            print "</tr>\n";
          } else {
            print "\"" . $a_inventory['inv_name'] . "\",";
            print "\"" . "Hardware"               . "\",";
            print "\"" . $a_models['mod_vendor']  . "\",";
            print "\"" . $a_models['mod_name']    . "\",";
            print "\"" . $newdate                 . "\"\n";
          }

        }

        $q_string  = "select sw_software,sw_vendor,sw_type,sw_eol ";
        $q_string .= "from software ";
        $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
        $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " ";
        $q_string .= "order by sw_software ";
        $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        while ($a_software = mysqli_fetch_array($q_software)) {

          $inv_name = '';

          $linkstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#sofware\" target=\"_blank\">";
          $linkend = "</a>";

          if ($a_software['sw_eol'] == '0000-00-00') {
            $swuncounted++;
          } else {
            if ($a_software['sw_eol'] < $today) {
              $sweol++;
            }
          }

          $class = "ui-widget-content";
          if ($a_software['sw_eol'] < $today && $a_software['sw_eol'] != '0000-00-00') {
            $class = "ui-state-error";
          }

          if ($formVars['csv'] == 'false') {
            print "<tr>\n";
            print "  <td class=\"" . $class . "\">"        . $linkstart . $inv_name . $linkend . "</td>\n";
            print "  <td class=\"" . $class . " delete\">" . "Software"                        . "</td>\n";
            print "  <td class=\"" . $class . "\">"        . $a_software['sw_vendor']          . "</td>\n";
            print "  <td class=\"" . $class . "\">"        . $a_software['sw_software']        . "</td>\n";
            print "  <td class=\"" . $class . " delete\">" . $a_software['sw_eol']             . "</td>\n";
            print "</tr>\n";
          } else {
            print "\"" . $a_inventory['inv_name']     . "\",";
            print "\"" . "Software"                   . "\",";
            print "\"" . $a_software['sw_vendor']     . "\",";
            print "\"" . $a_software['sw_software']   . "\",";
            print "\"" . $a_software['sw_eol']        . "\"\n";
          }
        }
      }
    } else {

      $q_string  = "select sw_eol ";
      $q_string .= "from software ";
      $q_string .= "left join inventory       on inventory.inv_id               = software.sw_companyid ";
      $q_string .= "where inv_product = " . $a_products['prod_id'] . " and inv_status = 0 ";
      if ($formVars['group'] > 0) {
        $q_string .= "and inv_manager = " . $formVars['group'] . " ";
      }
      $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_software = mysqli_fetch_array($q_software)) {
        if ($a_software['sw_eol'] == '0000-00-00') {
          $swuncounted++;
          $totalswuncounted++;
        } else {
          if ($a_software['sw_eol'] < $today) {
            $sweol++;
            $totalsweol++;
          }
        }
      }
      $q_string  = "select mod_vendor,hw_purchased,mod_eol ";
      $q_string .= "from hardware ";
      $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
      $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
      $q_string .= "where inv_product = " . $a_products['prod_id'] . " and inv_status = 0 and hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 ";
      if ($formVars['group'] > 0) {
        $q_string .= "and inv_manager = " . $formVars['group'] . " ";
      }
      $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_hardware = mysqli_fetch_array($q_hardware)) {

        $hwtotal++;
        $totalhardware++;

        if ($a_hardware['mod_vendor'] == 'Dell') {
# For Dell, the end of support is 5 years after the purchase date
          $date = explode("-", $a_hardware['hw_purchased']);
          $support = mktime(0,0,0,$date[1],$date[2],$date[0] + 5);
          $newdate = date("Y-m-d",$support);
        } else {
          if ($a_hardware['mod_eol'] == '') {
            $a_hardware['mod_eol'] = '0000-00-00';
          }
# just commonalizing the newdate value for the check
          $newdate = $a_hardware['mod_eol'];
        }

        if ($newdate == '0000-00-00') {
          $hwuncounted++;
          $totalhwuncounted++;
        } else {
          if ($newdate < $today) {
            $hweol++;
            $totalhweol++;
          }
        }
      }

      $linkstart = "<a href=\"" . $Reportroot . "/eolreport.php" . $argument . $ampersand . "product=" . $a_products['prod_id'] . "\" target=\"_blank\">";
      $linkend = "</a>";

      $class = "ui-widget-content";
      if (($sweol + $totalhweol + $hweol + $hweoltotal) == 0) {
        $class = "ui-state-highlight";
      }
      if (($sweol + $totalsweol + $hweol + $hweoltotal) == 0) {
        $class = "ui-state-error";
      }

      if ($formVars['csv'] == 'false') {
        print "<tr>\n";
        print "  <td class=\"" . $class . "\">"        . $linkstart . $a_products['prod_name'] . $linkend   . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $a_products['COUNT(inv_id)']          . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $sweol                                . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $swuncounted                          . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $hwtotal                              . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $hweol                                . "</td>\n";
        print "  <td class=\"" . $class . " delete\">"              . $hwuncounted                          . "</td>\n";
        print "</tr>\n";
      } else {
        print "\"" . $a_products['prod_name']       . "\",";
        print "\"" . $a_products['COUNT(inv_id)']   . "\",";
        print "\"" . $sweol                         . "\",";
        print "\"" . $swuncounted                   . "\",";
        print "\"" . $hwtotal                       . "\",";
        print "\"" . $hweol                         . "\",";
        print "\"" . $hwuncounted                   . "\"\n";
      }
    }
  }

  $class = "ui-widget-content";
  if ($formVars['csv'] == 'false') {
    if ($formVars['product'] == 0) {
      print "<tr>\n";
      print "  <td class=\"" . $class . "\">"        . "Total"           . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $totalservers     . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $totalsweol       . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $totalswuncounted . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $totalhardware    . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $totalhweol       . "</td>\n";
      print "  <td class=\"" . $class . " delete\">" . $totalhwuncounted . "</td>\n";
      print "</tr>\n";
    } else {
      print "<tr>\n";
      print "  <td class=\"" . $class . "\" colspan=\"5\">" . "Total";
      print " <strong>Servers</strong>: "           . $totalservers;
      print ", <strong>End of Life</strong>: "      . $sweol;
      print ", <strong>Uncounted</strong>: "        . $swuncounted;
      print ", <strong>Physical Servers</strong>: " . $hardware;
      print ", <strong>End of Life</strong>: "      . $hweol;
      print ", <strong>Uncounted</strong>: "        . $hwuncounted . "</td>\n";
    }
    print "</table>\n";
  } else {
    print "</textarea>\n";
  }
?>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
