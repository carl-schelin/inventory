<?php
# Script: tags.mysql.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "tags.mysql.php";
    $formVars['update']         = clean($_GET['update'],            10);
    $formVars['tag_companyid']  = clean($_GET['tag_companyid'],     10);
    $formVars['tag_name']       = str_replace(' ', '_', clean($_GET['tag_name'], 255));
    $formVars['tag_owner']      = clean($_SESSION['uid'],           10);
    $formVars['tag_group']      = clean($_SESSION['group'],         10);

    if ($formVars['update'] == '') {
      $formVars['update'] = -1;
    }
    if ($formVars['tag_companyid'] == '') {
      $formVars['tag_companyid'] = 0;
    }

    if (check_userlevel($db, $AL_Edit)) {
      if ($formVars['update'] == 0) {
        $formVars['id']             = clean($_GET['id'],            10);

        if ($formVars['id'] == '') {
          $formVars['id'] = 0;
        }

# check to see if the tag exists for this server and either add the new tag
# or pass it on to the add/delete toggle that follows (at -2)
        if (strlen($formVars['tag_name']) > 0) {
          logaccess($db, $_SESSION['uid'], $package, "Building the query.");

          $q_string  = "select tag_name ";
          $q_string .= "from tags ";
          $q_string .= "where tag_name = \"" . $formVars['tag_name'] . "\" and tag_companyid = " . $formVars['tag_companyid'] . " ";
          $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_tags) == 0) {

            $q_string =
              "tag_companyid =   " . $formVars['tag_companyid'] . "," .
              "tag_name      = \"" . $formVars['tag_name']      . "\"," .
              "tag_type      =   " . "1"                        . "," .
              "tag_owner     =   " . $formVars['tag_owner']     . "," .
              "tag_group     =   " . $formVars['tag_group'];

            $q_string = "insert into tags set tag_id = NULL," . $q_string;

            logaccess($db, $_SESSION['uid'], $package, "Saving Changes to: " . $formVars['tag_name']);
            mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
         }
        } else {
          print "alert('You must input data before saving changes.');\n";
        }
      }


      if ($formVars['update'] == -2) {
        logaccess($db, $_SESSION['uid'], $package, "Add or delete the tag.");

        $q_string  = "select tag_id ";
        $q_string .= "from tags ";
        $q_string .= "where tag_name = '" . $formVars['tag_name'] . "' and tag_companyid = " . $formVars['tag_companyid'] . " ";
        $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_tags) > 0) {
          $a_tags = mysqli_fetch_array($q_tags);

# this is a delete task. We found the tag_name that's associated with the server so delete it
          $q_string = "delete from tags where tag_id = " . $a_tags['tag_id'] . " ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        } else {
# this is an add task. The tag_name for this server wasn't found so add it in for this server.
          $q_string  =
            "tag_companyid =   " . $formVars['tag_companyid'] . "," . 
            "tag_name      = \"" . $formVars['tag_name']      . "\"," .
            "tag_type      =   " . "1"                        . "," .
            "tag_owner     =   " . $formVars['tag_owner']     . "," .
            "tag_group     =   " . $formVars['tag_group'];

          $q_string  = "insert into tags set tag_id = null," . $q_string;
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        }
      }


      logaccess($db, $_SESSION['uid'], $package, "Creating the form for viewing.");

      $output = "<t4>Server Tags</t4>\n";

      $output .= "<p>\n";

      if (new_Mysql($db)) {
        $q_string  = "select ANY_VALUE(tag_id) as tagid,tag_name ";
      } else {
        $q_string  = "select tag_id as tagid,tag_name ";
      }
      $q_string .= "from tags ";
      $q_string .= "where tag_type = 1 ";
      $q_string .= "group by tag_name ";
      $q_string .= "order by tag_name ";
      $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_tags) > 0) {
        while ($a_tags = mysqli_fetch_array($q_tags)) {
          $linkstart = "<a href=\"#\" onclick=\"javascript:show_file('tags.mysql.php?update=-2&tag_companyid="  . $formVars['tag_companyid'] . "&tag_name=" . $a_tags['tag_name'] . "');\">";
          $linkend   = "</a>";

          $q_string  = "select tag_id ";
          $q_string .= "from tags ";
          $q_string .= "where tag_name = \"" . $a_tags['tag_name'] . "\" and tag_companyid = " . $formVars['tag_companyid'] . " ";
          $q_identity = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
          if (mysqli_num_rows($q_identity) > 0) {
            $output .= "<strong>[";
          }

          $output .= $linkstart . $a_tags['tag_name'] . $linkend;

          if (mysqli_num_rows($q_identity) > 0) {
            $output .= "]</strong>";
          }

          $output .= " ";
        }
      }

      $output .= "</p>\n";

      print "document.getElementById('Server_tags').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";


      if ($formVars['update'] == -3) {

        $q_string  = "select type_id,type_name ";
        $q_string .= "from tag_types ";
        $q_string .= "where type_id > 1 ";   # 1 is Servers which is above. This is showing all the other tags that might be attached.
        $q_string .= "order by type_name ";
        $q_tag_types = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
        if (mysqli_num_rows($q_tag_types) > 0) {
          while ($a_tag_types = mysqli_fetch_array($q_tag_types)) {

            $output = "<t4>" . $a_tag_types['type_name'] . " Tags</t4>\n";

            $output .= "<p>\n";

            if (new_Mysql($db)) {
              $q_string  = "select ANY_VALUE(tag_id) as tagid,tag_name ";
            } else {
              $q_string  = "select tag_id as tagid,tag_name ";
            }
            $q_string .= "from tags ";
            $q_string .= "where tag_type = " . $a_tag_types['type_id'] . " ";
            $q_string .= "group by tag_name ";
            $q_string .= "order by tag_name ";
            $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
            if (mysqli_num_rows($q_tags) > 0) {
              while ($a_tags = mysqli_fetch_array($q_tags)) {

                if ($a_tags['tag_name'] == '') {
                  $a_tags['tag_name'] = 'blank';
                }

                $q_string  = "select tag_id ";
                $q_string .= "from tags ";
                $q_string .= "where tag_name = \"" . $a_tags['tag_name'] . "\" and tag_companyid = " . $formVars['tag_companyid'] . " ";
                $q_identity = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
                if (mysqli_num_rows($q_identity) > 0) {
                  $output .= "<strong>[";
                }

                $output .= $a_tags['tag_name'];

                if (mysqli_num_rows($q_identity) > 0) {
                  $output .= "]</strong>";
                }

                $output .= " ";
              }
            }

            $output .= "</p>\n";

            print "document.getElementById('" . $a_tag_types['type_name'] . "_tags').innerHTML = '" . mysqli_real_escape_string($db, $output) . "';\n\n";
          }
        }
      }
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
