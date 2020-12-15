<?php
# Script: changelog.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  $called = 'yes';
  include($Sitepath . '/guest.php');

  $package = "changelog.mysql.php";

  logaccess($db, $formVars['uid'], $package, "Accessing the script.");

  header('Content-Type: text/javascript');

  $formVars['id'] = clean($_GET['id'], 10);

# get the name of the application here
  $q_string = "select cl_name "
            . "from changelog "
            . "where cl_id = " . $formVars['id'];
  $q_changelog = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_changelog = mysqli_fetch_array($q_changelog);

  $grpcount = 0;
  $q_string = "select grp_changelog,grp_clfile "
            . "from groups "
            . "where grp_changelog != ''";
  $q_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    $grouplist[$grpcount] = $a_groups['grp_changelog'];
    $filename[$grpcount++] = "." . $a_groups['grp_clfile'];
  }

  $allservers = array();
  $count = 0;
  $debug = '';
  for ($i = 0; $i < count($grouplist); $i++) {
    if (file_exists( $Sitedir . "/" . $grouplist[$i] . "/" . $a_changelog['cl_name'] . $filename[$i])) {
      $svrlist = file($Sitedir . "/" . $grouplist[$i] . "/" . $a_changelog['cl_name'] . $filename[$i]);
      for ($j = 0; $j < count($svrlist); $j++) {
        $list = explode(" ", $svrlist[$j]);
# create the data field
        if ($list[0] == "Date:") {
          switch($list[3]) {
            case "Jan" : $month = "01"; break;
            case "Feb" : $month = "02"; break;
            case "Mar" : $month = "03"; break;
            case "Apr" : $month = "04"; break;
            case "May" : $month = "05"; break;
            case "Jun" : $month = "06"; break;
            case "Jul" : $month = "07"; break;
            case "Aug" : $month = "08"; break;
            case "Sep" : $month = "09"; break;
            case "Oct" : $month = "10"; break;
            case "Nov" : $month = "11"; break;
            case "Dec" : $month = "12"; break;
          }
          if ($list[2] < 10) {
            $zero = "0";
          } else {
            $zero = "";
          }
          $finaldate = $list[4] . $month . $zero . $list[2] . "&nbsp;" . $list[5] . "</td>";
          $finalserver = "<a href=\"/" . $grouplist[$i] . "/server.php?server=" . $a_changelog['cl_name'] . "\">";
        }
# create the from field
        if ($list[0] == "From:") {
          if ($list[1][0] == '"') {
            $from = substr($list[1], 1, strlen($list[1]) - 2);
          } else {
# if it's <first.last@intrado.com>
            $fromtmp = str_replace("<", "", str_replace(">", "", $list[1]));
            $from = substr($fromtmp, 0, strpos($fromtmp, "@"));
          }
          if ($from == '') {
            $from = $list[1];
          }
        }
# process the text file itself
        if ($svrlist[$j] == "--------------\n") {
          $add = 0;
          if ($svrlist[$j + 4] == "--------------\n" || $svrlist[$j + 4] == "Content-Transfer-Encoding: quoted-printable\n") {
            $add = 4;
          }
          $finalname = $from . "</td>";
          if ($svrlist[$j + $add + 1] != "\n") {
            $finaltext = mysqli_real_escape_string($db, rtrim($svrlist[$j + $add + 1])) . "</td>";
          } else {
            if ($svrlist[$j + $add + 2] != "\n") {
              $finaltext = mysqli_real_escape_string($db, rtrim($svrlist[$j + $add + 2])) . "</td>";
            } else {
              $finaltext = mysqli_real_escape_string($db, rtrim($svrlist[$j + $add + 3])) . "</td>";
            }
          }
          $allservers[$count++] = $finaldate . "<td class=\"ui-widget-content\">" . $finalserver . $finalname . "</a><td class=\"ui-widget-content\">" . $finaltext . "</tr>";
        }
      }
    }
  }

  sort($allservers);
  $newarray = array();
  $newarray = array_reverse($allservers);

# done with formatting. Now populate the table.
  $output  = "<p></p>";
  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "  <th class=\"ui-state-default\">Changelog</th>";
  $output .= "  <th class=\"ui-state-default\" width=\"20\"><a href=\"javascript:;\" onmousedown=\"toggleDiv('changelog-help');\">Help</a></th>";
  $output .= "</tr>";
  $output .= "</table>";

  $output .= "<div id=\"changelog-help\" style=\"" . $display . "\">";

  $output .= "<div class=\"main-help ui-widget-content\">";

  $output .= "<p>This page shows changes made to this system by any group permitted to make changes.</p>";
  $output .= "<ul>";
  $output .= "  <li><strong>Change Date</strong> - The date the email was sent to the group's changelog mailing list. It's in reverse order so the newest change is at the top of the list.</li>";
  $output .= "  <li><strong>Changed By</strong> - The person who submitted the change.</li>";
  $output .= "  <li><strong>First Line of Change</strong> - The first line of the changelog submission is extracted from the email.</li>";
  $output .= "</ul>";

  $output .= "</div>";

  $output .= "</div>";

  $output .= "<table class=\"ui-styled-table\">";
  $output .= "<tr>";
  $output .= "<th class=\"ui-state-default\">Change Date</th>";
  $output .= "<th class=\"ui-state-default\">Changed By</th>";
  $output .= "<th class=\"ui-state-default\">First Line of Change</th>";
  $output .= "</tr>";
  if (count($newarray) > 0) {
    for ($i = 0; $i < count($newarray); $i++) {
      $output .= "<tr>";
      $output .= "<td class=\"ui-widget-content\">" . $newarray[$i] . "</td>";
      $output .= "</tr>";
    }
  } else {
    $output .= "<tr>";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"3\">No entries.</td>";
    $output .= "</tr>";
  }
  $output .= "</table>";
?>

document.getElementById('changelog_mysql').innerHTML = '<?php print mysqli_real_escape_string($db, $output); ?>';

