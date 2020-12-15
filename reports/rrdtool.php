<?php
# Script: rrdtool.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "rrdtool.php";

  logaccess($db, $formVars['uid'], $package, "Viewing rrdtool");

  $formVars['product']   = clean($_GET['product'],  10);
  $formVars['group']     = clean($_GET['group'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],   10);
  $formVars['country']   = clean($_GET['country'],  10);
  $formVars['state']     = clean($_GET['state'],    10);
  $formVars['city']      = clean($_GET['city'],     10);
  $formVars['location']  = clean($_GET['location'], 10);

  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = '';
  }

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = " order by inv_name";
    $_SESSION['sort'] = '';
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Performance Review</title>

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

# now build the where clause
  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      $and = " and";
    }
  }

  $group = '';
  if ($formVars['group'] > 0) {
    $group = $and . " inv_manager = " . $formVars['group'] . " ";
    $and = " and";
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '0000-00-00' and hw_primary = 1 and hw_deleted = 0 ";
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
    $and = " and";
  } else {
    if ($formVars['country'] > 0) {
      $location .= $and . " loc_country = " . $formVars['country'] . " ";
      $and = " and";
    }
    if ($formVars['state'] > 0) {
      $location .= $and . " loc_state = " . $formVars['state'] . " ";
      $and = " and";
    }
    if ($formVars['city'] > 0) {
      $location .= $and . " loc_city = " . $formVars['city'] . " ";
      $and = " and";
    }
    if ($formVars['location'] > 0) {
      $location .= $and . " inv_location = " . $formVars['location'] . " ";
      $and = " and";
    }
  }

  if ($formVars['type'] == -1) {
    $type = "";
  } else {
    $type = $and . " inv_status = 0 ";
    $and = " and";
  }

  $where = $product . $group . $inwork . $location . $type;

  $passthrough = 
    "&group="    . $formVars['group']    .
    "&product="  . $formVars['product']  .
    "&inwork="   . $formVars['inwork']   .
    "&type="     . $formVars['type']     .
    "&country="  . $formVars['country']  .
    "&state="    . $formVars['state']    .
    "&city="     . $formVars['city']     .
    "&location=" . $formVars['location'];

  print "<table class=\"ui-styled-table\">\n";

  $q_string  = "select inv_id,IF(INSTR(inv_name,'/'),LEFT(inv_name,LOCATE('/',inv_name)-1),inv_name) as inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= $where;
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_type = 'OS' and sw_companyid = " . $a_inventory['inv_id'];
    $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_software = mysqli_fetch_array($q_software);

    $os = explode(" ", $a_software['sw_software']);

    print "<tr>\n";
    print "  <th class=\"ui-state-default\" style=\"text-align: left;\" colspan=\"4\"><a href=\"/rrdtool/" . $a_inventory['inv_name'] . "\" target=\"_blank\">" . $a_inventory['inv_name'] . " (" . $a_software['sw_software'] . ")</a></th>\n";
    print "</tr>\n";

    if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "/load-day-thumb.png")) {
      $load   = "title=\"Load Average: Should be under 1. Shows how busy the system is. Spikes here should be reflected with spikes in CPU and Memory\"";
      $queue  = "title=\"Run Queues: Should be under 2. Red shows disk I/O blocking. Blue shows CPU blocking.\"";
      $cpu    = "title=\"CPU Usage: All cpus are shown so multiple colors are possible. The more colors, the more cpus are in use.\"";
      $memory = "title=\"Memory Usage: Red=Program usage, Orange=Cached usage, Yellow=Buffers\"";
      $swap   = "title=\"Swap Usage: Generally red is 50% but can go higher. 100% will start Disk Performance alerts.\"";
      print "<tr>\n";
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "/load-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center;\" " . $load   . "><img height=\"95\" src=\"/rrdtool/" . $a_inventory['inv_name'] . "/load-day-thumb.png\" width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center;\">Image not found</td>\n";
      }
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "/mem-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center;\" " . $queue  . "><img height=\"95\" src=\"/rrdtool/" . $a_inventory['inv_name'] . "/mem-day-thumb.png\"  width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center;\">Image not found</td>\n";
      }
      if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "/cpu-day-thumb.png")) {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center;\" " . $cpu    . "><img height=\"95\" src=\"/rrdtool/" . $a_inventory['inv_name'] . "/cpu-day-thumb.png\"  width=\"200\"></td>\n";
      } else {
        print "  <td class=\"ui-widget-content\" style=\"text-align: center;\">Image not found</td>\n";
      }
      if ($os[0] == "Red" || $os[0] == "Oracle") {
        if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "/ram-day-thumb.png")) {
          print "  <td class=\"ui-widget-content\" style=\"text-align: center;\" " . $memory . "><img height=\"95\" src=\"/rrdtool/" . $a_inventory['inv_name'] . "/ram-day-thumb.png\"  width=\"200\"></td>\n";
        } else {
          print "  <td class=\"ui-widget-content\" style=\"text-align: center;\">Image not found</td>\n";
        }
      }
      if ($os[0] == "SunOS") {
        if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "/swap-day-thumb.png")) {
          print "  <td class=\"ui-widget-content\" style=\"text-align: center;\" " . $swap  . "><img height=\"95\" src=\"/rrdtool/" . $a_inventory['inv_name'] . "/swap-day-thumb.png\" width=\"200\"></td>\n";
        } else {
          print "  <td class=\"ui-widget-content\" style=\"text-align: center;\">Image not found</td>\n";
        }
      }
      print "</tr>\n";
    }
  }

?>
</table>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
