<?php
# Script: tag.view.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "tag.view.php";

  logaccess($db, $formVars['uid'], $package, "Reports tag view.");

  if (isset($_GET['tag'])) {
    $formVars['tag'] = clean($_GET['tag'],      20);
  } else {
    $formVars['tag'] = 'prod';
  }
  if (isset($_GET['product'])) {
    $formVars['product']   = clean($_GET['product'],  10);
  } else {
    $formVars['product']   = 0;
  }
  if (isset($_GET['inwork'])) {
    $formVars['inwork']    = clean($_GET['inwork'],   10);
  } else {
    $formVars['inwork']    = 'false';
  }
  if (isset($_GET['country'])) {
    $formVars['country']   = clean($_GET['country'],  10);
  } else {
    $formVars['country']   = 0;
  }
  if (isset($_GET['state'])) {
    $formVars['state']     = clean($_GET['state'],    10);
  } else {
    $formVars['state']     = 0;
  }
  if (isset($_GET['city'])) {
    $formVars['city']      = clean($_GET['city'],     10);
  } else {
    $formVars['city']      = 0;
  }
  if (isset($_GET['location'])) {
    $formVars['location']  = clean($_GET['location'], 10);
  } else {
    $formVars['location']  = 0;
  }
  if (isset($_GET['type'])) {
    $formVars['type'] = clean($_GET['type'], 10);
  } else {
    $formVars['type'] = 1;
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
<title>Tag View: <?php print $formVars['tag']; ?></title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

$(document).ready( function () {
});

</script>

</head>
<body class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<form name="tags">

<div class="main">

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

  $title = '';
# if private
  if ($formVars['type'] == 0) {
    $tag = $and . " tag_name = '" . $formVars['tag'] . "' and tag_owner = " . $_SESSION['user'] . " ";
    $and = " and";
    $title = "Private Tag View: " . $formVars['tag'];
  }
# if group
  if ($formVars['type'] == 1) {
    $tag = $and . " tag_name = '" . $formVars['tag'] . "' and tag_group = " . $_SESSION['group'] . " ";
    $and = " and";
    $title = "Group Tag View: " . $formVars['tag'];
  }
# if all
  if ($formVars['type'] == 2) {
    $tag = $and . " tag_name = '" . $formVars['tag'] . "' ";
    $and = " and";
    $title = "Public Tag View: " . $formVars['tag'];
  }

  $where = $product . $inwork . $location . $type . $tag;

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">" . $title . "</th>\n";
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
  print "  <li><strong>IP Address</strong> Shows the assigned interface and IPs for servers.\n";
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

  print "<table class=\"ui-styled-table\">\n";
  print "<tr>\n";
  print "  <th class=\"ui-state-default\">System Name</th>\n";
  print "  <th class=\"ui-state-default\">Function</th>\n";
  print "  <th class=\"ui-state-default\">Device Owner</th>\n";
  print "  <th class=\"ui-state-default\">Model</th>\n";
  print "  <th class=\"ui-state-default\">Operating System</th>\n";
  print "  <th class=\"ui-state-default\">Location (TZ)</th>\n";
  print "  <th class=\"ui-state-default\">IP Address</th>\n";
  print "</tr>\n";

  $q_string = "select inv_id,inv_name,inv_function,inv_document,inv_manager,grp_name,ct_city,"
            . "mod_vendor,mod_name,zone_name,inv_ssh,hw_active,hw_retired,hw_reused "
            . "from inventory "
            . "left join hardware  on hardware.hw_companyid = inventory.inv_id "
            . "left join models    on models.mod_id         = hardware.hw_vendorid "
            . "left join locations on locations.loc_id      = inventory.inv_location "
            . "left join cities    on cities.ct_id          = locations.loc_city "
            . "left join a_groups    on a_groups.grp_id         = inventory.inv_manager "
            . "left join zones     on zones.zone_id         = inventory.inv_zone "
            . "left join tags      on tags.tag_companyid    = inventory.inv_id "
            . $where
            . "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

#####
# Get interface information
#####
    $interface = "";
    $console = "";
    $q_string = "select int_face,int_addr,int_type,itp_acronym,int_ip6 "
              . "from interface "
              . "left join inttype on inttype.itp_id = interface.int_type "
              . "where int_companyid = \"" . $a_inventory['inv_id'] . "\" and int_type != 7 and int_addr != '' and int_ip6 = 0 "
              . "order by int_face";
    $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_interface = mysqli_fetch_array($q_interface)) {

# if a console or LOM interface type
      if ($a_interface['int_type'] == 4 || $a_interface['int_type'] == 6) {
        $console .= $a_interface['int_face'] . "=" . "<a href=\"http://" . $a_interface['int_addr'] . "\" target=\"_blank\">" . $a_interface['int_addr'] . "</a> ";
      } else {
        $interface .= $a_interface['itp_acronym'] . "=" . $a_interface['int_addr'] . " ";
      }
    }

#####
# Get software information
#####
    $q_string = "select sw_software "
              . "from software "
              . "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_software = mysqli_fetch_array($q_software);
    
#####
# Set visuals
#####
    $title="This system is live.";
    $class = " class=\"ui-widget-content\"";
    if ($a_inventory['hw_active'] == '0000-00-00') {
      $title="This system is not live yet.";
      $class = " class=\"ui-state-highlight\"";
    }
    if ($a_inventory['hw_retired'] != '0000-00-00') {
      $title="This system is retired.";
      $class = " class=\"ui-state-error\"";
    }
    if ($a_inventory['hw_reused'] != '0000-00-00') {
      $title="This system has been reused.";
      $class = " class=\"ui-state-error\"";
    }

    if ($a_inventory['inv_ssh'] == 1) {
      $sshaccess = "*";
    } else {
      $sshaccess = "";
    }

#####
# Set edit options
#####
    if (check_userlevel($db, $AL_Edit)) {
      $editpencil = "<img class=\"ui-icon-edit\" src=\"" . $Imgsroot . "/pencil.gif\"></a>";
      if (check_grouplevel($db, $a_inventory['inv_manager'])) {
        $editmain     = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\" target=\"_blank\">" . $editpencil;
        $edithwstart  = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#hardware\" target=\"_blank\">" . $editpencil;
        $editintstart = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#network\" target=\"_blank\">" . $editpencil;
      }
# all groups can edit the software; that way they can identify systems to be associated with their group.
      $editswstart  = "<a href=\"" . $Editroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "#software\" target=\"_blank\">" . $editpencil;
# used only for the server to view the inventory data;
      $editend = "</a>";
      $showstart    = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "\" target=\"_blank\">";
      $showhwstart  = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#hardware\" target=\"_blank\">";
      $showswstart  = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#\" target=\"_blank\">";
      $showlocstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "\" target=\"_blank\">";
      $shownetstart = "<a href=\"" . $Showroot . "/inventory.php?server="  . $a_inventory['inv_id'] . "#network\" target=\"_blank\">";
      $showend = "</a>";
    }

    if (strlen($a_inventory['inv_document']) > 0) {
      $showdoc = "<a href=\"" . $a_inventory['inv_document'] . "\" target=\"_blank\">";
    } else {
      $showdoc = '';
    }

#####
# Print the table
#####
    print "<tr>\n";
    print "  <td title=\"" . $title . "\"" . $class . ">" . $editmain     . $showstart    . $a_inventory['inv_name']                            . $sshaccess . $showend . "</td>\n";
    print "  <td " . $class . ">"                                         . $showdoc      . $a_inventory['inv_function']                                     . $showend . "</td>\n";
    print "  <td " . $class . ">"                                                         . $a_inventory['grp_name']                                                    . "</td>\n";
    print "  <td " . $class . ">"                         . $edithwstart  . $showhwstart  . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name']      . $showend . "</td>\n";
    print "  <td " . $class . ">"                         . $editswstart  . $showswstart  . $a_software['sw_software']                                       . $showend . "</td>\n";
    print "  <td " . $class . ">"                                         . $showlocstart . $a_inventory['ct_city'] . " (" . $a_inventory['zone_name'] . ")" . $showend . "</td>\n";
    print "  <td " . $class . ">"                         . $editintstart . $shownetstart . $interface . $showend . "<br>"                        . $console            . "</td>\n";
    print "</tr>\n";
  }

?>
</table>

</div>

</form>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
