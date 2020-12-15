<?php
# Script: rrdtool.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "rrdtool.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

  $q_string  = "select int_server ";
  $q_string .= "from interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "where inv_id = " . $formVars['id'] . " and int_management = 1 ";
  $q_interface = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_interface = mysqli_fetch_array($q_interface);

  $load_day    = "/rrdtool/" . $a_interface['int_server'] . "/load-day-thumb.png";
  $mem_day     = "/rrdtool/" . $a_interface['int_server'] . "/mem-day-thumb.png";
  $cpu_day     = "/rrdtool/" . $a_interface['int_server'] . "/cpu-day-thumb.png";
  $ramswap_day = "/rrdtool/" . $a_interface['int_server'] . "/ram-day-thumb.png";
  $ramswap_title = "Memory Usage";

  $os = return_System($db, $formVars['id']);

  if ($os == "Linux") {
    $ramswap_day  = "/rrdtool/" . $a_interface['int_server'] . "/ram-day-thumb.png";
    $ramswap_title = "Memory Usage";
  }
  if ($os == "SunOS") {
    $ramswap_day  = "/rrdtool/" . $a_interface['int_server'] . "/swap-day-thumb.png";
    $ramswap_title = "Swap Usage";
  }

  $numpics = 0;
  if (file_exists($Sitedir . $load_day)) {
    $load_display = "<img src=\"" . $load_day . "\">";
    $numpics++;
  } else {
    $load_display = "Chart Not Found";
  }
  if (file_exists($Sitedir . $mem_day)) {
    $mem_display = "<img src=\"" . $mem_day . "\">";
    $numpics++;
  } else {
    $mem_display = "Chart Not Found";
  }
  if (file_exists($Sitedir . $cpu_day)) {
    $cpu_display = "<img src=\"" . $cpu_day . "\">";
    $numpics++;
  } else {
    $cpu_display = "Chart Not Found";
  }
  if (file_exists($Sitedir . $ramswap_day)) {
    $ramswap_display = "<img src=\"" . $ramswap_day . "\">";
    $numpics++;
  } else {
    $ramswap_display = "Chart Not Found";
  }

  $output  = "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">Performance Graphs</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('performance-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  if ($numpics > 0) {
    $output .= "<div id=\"performance-help\" style=\"display: none\">";

    $output .= "<div class=\"main-help ui-widget-content\">";

    $output .= "<p>This page displays the thumbnail graphs for the most commonly reviewed charts for a server. ";
    $output .= "Click the full report link at the bottom of the page to get more details and views of the other available reports.</p>";
    $output .= "<ul>";
    $output .= "  <li><strong>Load Average</strong> - Generally should be low and indicates how busy the system is. If this is trending high, it could indicate a system experiencing heavy usage. The red line is the load average over the past minute, darker yellow is over the last 5 minutes, and lighter yellow at the bottom the average over the past 15 minutes.</li>";
    $output .= "  <li><strong>Run/Blocked Queues</strong> - The Run Queue (red line) indicates a problem with Disk I/O. The Blocked Queue (blue line) indicates a problem with CPU.</li>";
    $output .= "  <li><strong>CPU Usage</strong> - This chart shows how busy every CPU is. This can be a pretty busy chart with systems that have numerous cores.</li>";
    $output .= "  <li><strong>Memory Usage</strong> - This is displayed for Linux systems and shows the current memory usage.";
    $output .= "  <ul>";
    $output .= "    <li><strong>Red</strong> - Program usage. This is how much memory all running programs are using on this system. Too much memory being taken will reduce the performance of the system.</li>";
    $output .= "    <li><strong>Orange</strong> - Memory Caching. Memory is cached to speed up access to commonly requested data. Too much memory caching will reduce the disk caching which will slow the system depending on the speed of the drives (memory access is much faster than disk access).</li>";
    $output .= "    <li><strong>Yellow</strong> - Disk Buffer Caching. All remaining memory is allocated to disk caching. This also improves the performance of the system.</li>";
    $output .= "  </ul></li>";
    $output .= "  <li><strong>Swap Usage</strong> - On Solaris systems, this shows the swap utilization.</li>";
    $output .= "</ul>";

    $output .= "</div>";

    $output .= "</div>";

    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\">Load Average</th>";
    $output .= "<th class=\"ui-state-default\">Run/Blocked Queues</th>";
    $output .= "</tr>";
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\" style=\"text-align: center;\">" . $load_display . "</td>";
    $output .= "<td class=\"ui-widget-content\" style=\"text-align: center;\">" . $mem_display . "</td>";
    $output .= "</tr>";
    $output .= "<tr>";
    $output .= "<th class=\"ui-state-default\">CPU Usage</th>";
    $output .= "<th class=\"ui-state-default\">" . $ramswap_title . "</th>";
    $output .= "</tr>";
    $output .= "<td class=\"ui-widget-content\" style=\"text-align: center;\">" . $cpu_display . "</td>";
    $output .= "<td class=\"ui-widget-content\" style=\"text-align: center;\">" . $ramswap_display . "</td>";
    $output .= "</tr>";

    $output .= "</table>";
    $output .= "<p><a href=\"" . $Siteurl . "/rrdtool/" . $a_interface['int_server'] . "\" target=\"_blank\">Go to the full performance report for " . $a_interface['int_server'] . "</a></p>";
    $output .= "<p><strong>Disclaimer</strong>: This information is for the Unix team’s usage for ad hoc review and is not the Official location of performance metrics. The Monitoring team has the ";
    $output .= "mandate to provide this information for Official requests.</p>\n";
    $output .= "<p>You are of course welcome to review the pages. Please realize that if there are questions regarding these charts, the Unix team may not be immediately available ";
    $output .= "to make corrections or research the reason for errors such as missing charts.</p>\n";
  } else {
    $output .= "<div id=\"performance-help\" style=\"display: none\">";

    $output .= "<div class=\"main-help ui-widget-content\">";

    $output .= "<p>Performance graphs are available for any system running rrdtool. In this case, none of the thumbnail ";
    $output .= "charts were discovered indicating the scripts aren't running on this system. Currently the HP-UX systems ";
    $output .= "have not been configured to run the charts. Red Hat/CentOS Linux and Solaris have an install package. If ";
    $output .= "this is a Linux or Solaris install, it's likely the software isn't configured to run at this time.</p>";

    $output .= "</div>";

    $output .= "</div>";

    $output .= "<table class=\"ui-styled-table\">";
    $output .= "<tr>";
    $output .= "<td class=\"ui-widget-content\">No graphs available for this system.</td>";
    $output .= "</tr>";
    $output .= "</table>";
  }
?>

document.getElementById('performance_mysql').innerHTML = '<?php print mysqli_real_escape_string($output); ?>';

