<?php
# Script: inventory.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "inventory.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script");

  $formVars['group']     = clean($_GET['group'],      10);
  $formVars['product']   = clean($_GET['product'],    10);
  $formVars['project']   = clean($_GET['project'],    10);
  $formVars['inwork']    = clean($_GET['inwork'],     10);
  $formVars['country']   = clean($_GET['country'],    10);
  $formVars['state']     = clean($_GET['state'],      10);
  $formVars['city']      = clean($_GET['city'],       10);
  $formVars['location']  = clean($_GET['location'],   10);
  $formVars['csv']       = clean($_GET['csv'],        10);

  if (isset($_GET["sort"])) {
    $formVars['sort'] = clean($_GET["sort"], 20);
    $orderby = "order by " . $formVars['sort'] . $_SESSION['sort'];
    if ($_SESSION['sort'] == ' desc') {
      $_SESSION['sort'] = '';
    } else {
      $_SESSION['sort'] = ' desc';
    }
  } else {
    $orderby = "order by inv_name";
    $_SESSION['sort'] = '';
  }

  $formVars['type'] = '';
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  }

  if ($formVars['inwork'] == '') {
    $formVars['inwork'] = 'false';
  }
  if ($formVars['project'] == '') {
    $formVars['project'] = 0;
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

# set up filters. Being passed:
# group = The group that's looking at the listing
# product = Restrict the view to just one Intrado Product
# inwork = Restrict the view again to just the products that are being built
# location = Restrict the view again to just where the servers are located
# type = are we looking at all servers or just the ones that are live/in work (inv_status = 0)

  if ($formVars['type'] == -1) {
    $title = "Complete Server Listing";
  } else {
    $title = "Active Server Listing";
  }

# the others are ok blank, but we have to have a group listed
  if (!isset($_GET['group'])) {
    $formVars['group'] = $_SESSION['group'];
  }

# reduce the data down a little to just the selected products if not all.
  $hwand = " and";
  $swand = " where";
  if ($formVars['product'] == 0) {
    $product = '';
    $hwproduct = '';
    $swproduct = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = " where inv_product = 0";
    } else {
      $product = " where inv_product = " . $formVars['product'];
      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
      }
      $hwproduct = $hwand . " hw_product = " . $formVars['product'];
      $swproduct = $swand . " sw_product = " . $formVars['product'];
      $hwand = " and";
      $swand = " and";
    }
  }

# The inventory index (invindex) variable should be set for all systems in the inventory.
# this part activates the inventory item so it'll show.
# if all groups (-1)
  if ($formVars['group'] == -1) {
    $hwgroup = '';
    $swgroup = '';
  } else {
    $hwgroup = $hwand . ' hw_group = ' . $formVars['group'];
    $swgroup = $swand . ' sw_group = ' . $formVars['group'];
    $hwand = " and";
    $swand = " and";
  }

  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= $product;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
# set up the index of systems
    $invindex[$a_inventory['inv_id']] = false;
    $invname[$a_inventory['inv_id']] = $a_inventory['inv_name'];
  }

# this builds the index of inventory items that will be displayed;
# activate the entry for this item
  $q_string  = "select hw_companyid ";
  $q_string .= "from hardware ";
  $q_string .= "where hw_deleted = 0 and hw_primary = 1 " . $hwgroup . $hwproduct;
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_hardware = mysqli_fetch_array($q_hardware)) {
    $invindex[$a_hardware['hw_companyid']] = true;
  }

  $q_string  = "select sw_companyid ";
  $q_string .= "from software" . $swproduct . $swgroup;
  $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_software = mysqli_fetch_array($q_software)) {
    $invindex[$a_software['sw_companyid']] = true;
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
<title><?php print $title; ?></title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.min.js"></script>

<script type="text/javascript">

$(document).ready( function () {
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form id="inventory" name="inventory" method="GET">

<?php
# save the passed variables
  print "<input type=\"hidden\" name=\"group\"   value=\"" . $formVars['group']   . "\">";
  print "<input type=\"hidden\" name=\"product\" value=\"" . $formVars['product'] . "\">";
  print "<input type=\"hidden\" name=\"inwork\"  value=\"" . $formVars['inwork']  . "\">\n";
?>

<div class="main">

<table class="ui-styled-table">
<tr>
<?php

# print the group if selected

  if ($formVars['group'] != -1) {
    $q_string  = "select grp_name ";
    $q_string .= "from a_groups ";
    $q_string .= "where grp_id = " . $formVars['group'] . " ";
    $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_groups = mysqli_fetch_array($q_groups);

    print "  <th class=\"ui-state-default\">" . $a_groups['grp_name'] . "</th>";
  } else {
    print "  <th class=\"ui-state-default\">Inventory Table</th>";
  }

  print "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('help');\">Help</a></th>\n";
  print "</tr>\n";
  print "</table>\n";

  print "<div id=\"help\" style=\"" . $display . "\">\n\n";

  print "<div class=\"main-help ui-widget-content\">\n\n";

  print "<h1>Instructions</h1>\n\n";

  print "<p>The Active Server display uses two colors to clearly identify what the current status is of that device:</p>\n\n";

  print "<ul>\n";
  print "  <li><span class=\"ui-state-highlight\">This color indicates the server is currently being built in preparation to being brought into production.</span></li>\n";
  print "  <li>This color indicates the server is live and in production.</li>\n";
  print "</ul>\n\n";

  print "<p>In addition, when viewing the Complete Listing, you'll see the folowing colors.</p>\n\n";

  print "<ul>\n";
  print "  <li><span class=\"ui-state-error\">This color indicates the server has been retired and removed from service. The server may have been destroyed \n";
  print "or has been determined to still be useful and is waiting on a new project to make use of it.</span></li>\n";
  print "  <li><span class=\"ui-state-error\">This color is used to identify a server that had been retired and was reused for a different project.</span></li>\n";
  print "</ul>\n\n";

  print "<p><strong>Note:</strong> The colors are assigned based on the various set dates in the server hardware section. See that section \n";
  print "for more details.</p>\n\n";

  print "<p><strong>Column Details:</strong></p>\n\n";

  print "<p>A pencil in a column means you are able to edit that information. The Platform Owner will see a pencil in every column. All teams have \n";
  print "access to manage Software on a system. See the Software page for more details. Clicking on the Pencil icon brings up the edit page for that server. \n";
  print "Clicking on the main description in the column brings you to the data viewing page for that column unless otherwise noted below.</p>\n\n";

  print "<ul>\n";
  print "  <li><strong>System Name</strong> This is generally the maintenance IP assigned to the server as it's used to log in remotely. An asterisk (*) \n";
  print "next to the server name indicates it's accessible by the Unix Team's service account.</li>\n";
  print "  <li><strong>Function</strong> The link here if it exists takes you to the server's documentation as supplied by the appropriate team.</li>\n";
  print "  <li><strong>Model</strong> Shows the server hardware.</li>\n";
  print "  <li><strong>Operating System</strong> Shows the server operating system.</li>\n";
  print "  <li><strong>Location (TZ)</strong> Shows the server location and time zone (TZ).</li>\n";
  print "  <li><strong>West</strong> Displays the 5 character data center code used by West.</li>\n";
  print "  <li><strong>IP Address</strong> Shows the assigned interface and IPs for servers. A Default Route is identified with an asterisk (*)\n";
  print "  <ul>\n";
  print "    <li><strong>Mgt</strong> Management IP used for remote access plus maintenance traffic such as backups and monitoring</li>\n";
  print "    <li><strong>App</strong> Application IP used for Application traffic only</li>\n";
  print "    <li><strong>Sig</strong> Signaling IP used for heartbeat or other inter-clustered server communication</li>\n";
  print "    <li><strong>Int</strong> Interconnect IP used for heartbeat or other inter-clustered server communication</li>\n";
  print "    <li><strong>LOM</strong> Lights Out Management for remote console access. This will be identified as a 'netmgt' interface. Clicking on the \n";
  print "link will take you to the server LOM for console access if the server provides such access.</li>\n";
  print "  </ul></li>\n";
  print "</ul>\n\n";

  print "</div>\n\n";

  print "</div>\n\n";

  $passed  = "&group="    . $formVars['group'];
  if ($formVars['inwork'] == 'true') {
    $passed .= "&inwork="   . $formVars['inwork'];
  }
  if ($formVars['product'] > 0) {
    $passed .= "&product="  . $formVars['product'];
  }
  if ($formVars['country'] > 0) {
    $passed .= "&country="  . $formVars['country'];
  }
  if ($formVars['state'] > 0) {
    $passed .= "&state="    . $formVars['state'];
  }
  if ($formVars['city'] > 0) {
    $passed .= "&city="     . $formVars['city'];
  }
  if ($formVars['location'] > 0) {
    $passed .= "&location=" . $formVars['location'];
  }
  if ($formVars['csv'] == 'true') {
    $passed .= "&csv="      . $formVars['csv'];
  }

  if ($formVars['csv'] == 'false') {
    print "<table class=\"ui-styled-table\">\n";
    print "<tr>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_name" . $passed . "\">System Name</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_function" . $passed . "\">Function</a></th>\n";
    if ($formVars['group'] == -1) {
      print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=grp_name" . $passed . "\">Platform Owner</a></th>\n";
    }
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=inv_appadmin" . $passed . "\">App Owner</a></th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=mod_vendor,mod_name" . $passed . "\">Model</a></th>\n";
    print "  <th class=\"ui-state-default\">Operating System</th>\n";
    print "  <th class=\"ui-state-default\"><a href=\"" . $package . "?sort=ct_city" . $passed . "\">Location (TZ)</a></th>\n";
    print "  <th class=\"ui-state-default\">West</th>\n";
    print "  <th class=\"ui-state-default\">IP Address</th>\n";
    print "</tr>\n";
  } else {
    print "<p>\"System Name\",";
    print "\"Function\",";
    if ($formVars['group'] == -1) {
      print "\"Device Owner\",";
    }
    print "\"App Owner\",";
    print "\"Model\",";
    print "\"Operating System\",";
    print "\"Location (TZ)\",";
    print "\"West\"";
    print "\"IP Address\"";
    print "</br>\n";
  }

  $date = date("Ym");

  $and = " where";
  if ($formVars['product'] == 0) {
    $product = '';
  } else {
    if ($formVars['product'] == -1) {
      $product = $and . " inv_product = 0 ";
      $and = " and";
    } else {
      $product = $and . " inv_product = " . $formVars['product'] . " ";
      if ($formVars['project'] > 0) {
        $product .= " and inv_project = " . $formVars['project'];
      }
      $and = " and";
    }
  }

  if ($formVars['inwork'] == 'false') {
    $inwork = $and . ' hw_primary = 1 and hw_deleted = 0 ';
    $and = " and";
  } else {
    $inwork = $and . " hw_active = '1971-01-01' and hw_primary = 1 and hw_deleted = 0 ";
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

# rather then run an if each time, just create an array.
  $sshaccess[0] = '';
  $sshaccess[1] = '*';

# wanted to do a dns lookup but it was taking forever so disable for now
  $dnsdead = 'yes';

  $total_servers = 0;
  $q_string = "select inv_id,inv_name,inv_function,inv_document,inv_manager,inv_appadmin,grp_name,"
            . "ct_city,loc_identity,zone_name,inv_ssh,hw_active,hw_retired,hw_reused,mod_vendor,mod_name,inv_status "
            . "from inventory "
            . "left join hardware  on hardware.hw_companyid = inventory.inv_id "
            . "left join locations on locations.loc_id      = inventory.inv_location "
            . "left join cities    on cities.ct_id          = locations.loc_city "
            . "left join zones     on zones.zone_id         = inventory.inv_zone "
            . "left join models    on models.mod_id         = hardware.hw_vendorid "
            . "left join a_groups    on a_groups.grp_id         = inventory.inv_manager "
            . $product . $inwork . $location . $type . " "
            . $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($invindex[$a_inventory['inv_id']] === true) {

      $total_servers++;
      $q_string = "select grp_name "
                . "from a_groups "
                . "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
      $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_groups = mysqli_fetch_array($q_groups);

      $interface = "";
      $console = "";
      $q_string = "select int_face,int_addr,int_type,itp_acronym,int_ip6,int_primary "
                . "from interface "
                . "left join inttype on inttype.itp_id = interface.int_type "
                . "where int_companyid = \"" . $a_inventory['inv_id'] . "\" and int_type != 7 and int_addr != '' and int_ip6 = 0 "
                . "order by int_face";
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      while ($a_interface = mysqli_fetch_array($q_interface)) {

# if a console or LOM interface type
        if ($a_interface['int_primary']) {
          $primary = "*";
        } else {
          $primary = '';
        }
        if ($a_interface['int_type'] == 4 || $a_interface['int_type'] == 6) {
          if ($formVars['csv'] == 'false') {
            $console .= $a_interface['int_face'] . "=" . "<a href=\"http://" . $a_interface['int_addr'] . "\" target=\"_blank\">" . $a_interface['int_addr'] . "</a>" . $primary . " ";
          } else {
            $console .= $a_interface['int_face'] . "=" . $a_interface['int_addr'] . $primary . " ";
          }
        } else {
          $interface .= $a_interface['itp_acronym'] . "=" . $a_interface['int_addr'] . $primary . " ";
        }
      }

      $q_string = "select sw_software "
                . "from software "
                . "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
      $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_software = mysqli_fetch_array($q_software);
    
      $title="This system is live.";
      $class = " class=\"ui-widget-content\"";
      if ($a_inventory['hw_active'] == '1971-01-01') {
        $title="This system is not live yet.";
        $class = " class=\"ui-state-highlight\"";
      }
      if ($a_inventory['hw_retired'] != '1971-01-01' || $a_inventory['inv_status'] == 1) {
        $title="This system is retired.";
        $class = " class=\"ui-state-error\"";
      }
      if ($a_inventory['hw_reused'] != '1971-01-01') {
        $title="This system has been reused.";
        $class = " class=\"ui-state-error\"";
      }

      $editstart = '';
      $edhwstart = '';
      $edipstart = '';
      $edswstart = '';
      if (check_userlevel($db, $AL_Edit)) {
        $editpencil = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\" height=\"10\"></a>";
        if (check_grouplevel($db, $a_inventory['inv_manager'])) {
          $editstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\"          target=\"_blank\">" . $editpencil;
          $edhwstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#hardware\" target=\"_blank\">" . $editpencil;
          $edipstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#network\"  target=\"_blank\">" . $editpencil;
          $edswstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#software\" target=\"_blank\">" . $editpencil;
        }
# all groups can edit the software; that way they can identify systems to be associated with their group.
        $edaastart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#software\" target=\"_blank\">" . $editpencil;
      }

# used only for the server to view the inventory data;
      $editend = "</a>";
      $showstart    = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "\"          target=\"_blank\">";
      $shhwstart    = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#hardware\" target=\"_blank\">";
      $shswstart    = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#software\" target=\"_blank\">";
      $shipstart    = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#network\"  target=\"_blank\">";
      $showend = "</a>";

      if (strlen($a_inventory['inv_document']) > 0) {
        $showdoc = "<a href=\"" . $a_inventory['inv_document'] . "\" target=\"_blank\">";
      } else {
        $showdoc = '';
      }

# print the actual data row
      if ($formVars['csv'] == 'false') {
        print "<tr>\n";
        print "  <td title=\"" . $title . "\"" . $class . ">" . $editstart . $showstart . $a_inventory['inv_name'] . $showend . $sshaccess[$a_inventory['inv_ssh']] . "</td>\n";
        print "  <td " . $class . "><nobr>" . $showdoc . $a_inventory['inv_function'] . $showend . "</nobr></td>\n";
        if ($formVars['group'] == -1) {
          print "  <td " . $class . "><nobr>" . $a_inventory['grp_name'] . "</nobr></td>\n";
        }
        print "  <td " . $class . "><nobr>" . $edaastart . $shswstart . $a_groups['grp_name']                                               . $showend                     . "</nobr></td>\n";
        print "  <td " . $class . "><nobr>" . $edhwstart . $shhwstart . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name']         . $showend                     . "</nobr></td>\n";
        print "  <td " . $class . "><nobr>" . $edswstart . $shswstart . return_ShortOS($a_software['sw_software'])                          . $showend                     . "</nobr></td>\n";
        print "  <td " . $class . "><nobr>"              . $showstart . $a_inventory['ct_city']    . " (" . $a_inventory['zone_name'] . ")" . $showend                     . "</nobr></td>\n";
        print "  <td " . $class . "><nobr>"              . $showstart . $a_inventory['loc_identity']                                        . $showend                     . "</nobr></td>\n";
        print "  <td " . $class . ">" . $edipstart . $shipstart . $interface                                                          . $showend . "<br>" . $console . "</td>\n";
        print "</tr>\n";
      } else {
        print "\"" . $a_inventory['inv_name'] . "\",";
        print "\"" . $a_inventory['inv_function'] . "\",";
        if ($formVars['group'] == -1) {
          print "\"" . $a_inventory['grp_name'] . "\",";
        }
        print "\"" . $a_groups['grp_name'] . "\",";
        print "\"" . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name'] . "\",";
        print "\"" . $a_software['sw_software'] . "\",";
        print "\"" . $a_inventory['ct_city']    . " (" . $a_inventory['zone_name'] . ")\",";
        print "\"" . $a_inventory['loc_identity'] . "\",";
        print "\"" . $interface . " " . $console . "\"";
        print "</br>\n";
      }
    }
  }

?>
</tbody>
</table>

<p>Total Servers: <?php print $total_servers; ?></p>

</div>

</form>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
