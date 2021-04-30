<?php
# Script: inventory.tags.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve data and update the database with the new info. Prepare and display the table

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "inventory.tags.php";
    $formVars['id']       = clean($_GET['id'],       10);
    $formVars['function'] = clean($_GET['function'], 10);
    $formVars['status']   = clean($_GET['status'],   10);
    $formVars['select']   = clean($_GET['select'],   500);

    if (check_userlevel($db, $AL_Edit)) {

# check to see if the person editing is a member of a group that can edit this information; if not, zero out 'type' so no changes can be made.
      $q_string  = "select inv_manager ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $formVars['id'] . " ";
      $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_inventory = mysqli_fetch_array($q_inventory);

      $formVars['group'] = $a_inventory['inv_manager'];

# if not a member of the group that can edit this server, default to zero which bypasses all the edit functions.
      if (check_grouplevel($db, $a_inventory['inv_manager']) == 0) {
        $formVars['function'] = '';
      }

      $cellid = $formVars['function'] . $formVars['id'];

####
# NOTE: Passed value is the Inventory Server ID and not the id of the specific item.
# as such, we'll need to split it into values 
####


# Process when clicked to change:
# 1. change trigger status 1 (show) to 0 (clear) so clicking again will clear it.
# 2. clear field
# 3. Build new element (input, select, etc) with onselect; trigger
# 4. Present element
# 5. when selected;
# 6. clear element
# 7. update with new text

# private tags
# this is a text edit field
      if ($formVars['function'] == 'tagv') {
        if ($formVars['status'] == 1) {
# give me the cell pointer you just clicked on.
          print "var cell = document.getElementById('" . $cellid . "');\n";
# give me the text in that cell
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

# remove the underscores
          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

# remove the commas
          print "celltext = celltext.replaceAll(\",\", \"\");\n";
          print "celltext = celltext.replaceAll(\",\", \"\");\n";

# clear if no tags
          print "if (celltext = 'No Private Tags') {\n";
          print "  celltext = ''\n";
          print "}\n";

# blank the cell
          print "cell.innerHTML = '&nbsp;';\n";
# remove the function call
          print "cell.setAttribute(\"onclick\", \"\");\n";

# create an input field so the data can be edited
          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"tags_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"40\");\n";

# put the new input field into the cell
          print "cell.appendChild(infield);\n";

# put the cursor into the new cell
          print "document.getElementById('edit_data').focus();\n";
# highlight the text
          print "document.getElementById('edit_data').select();\n";
# and ready for editing
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

# give me the cell pointer you just finished
          print "var cell = document.getElementById('" . $cellid . "');\n";

# update the function so it can be clicked again; matches the info in the original
          print "cell.setAttribute(\"onclick\", \"edit_Tags(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# return to blanks
          if ($formVars['select'] == '') {
            $formVars['select'] = "No Private Tags";
          }

# replace the input field with the updated data.
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }


# group tags
# this is a text edit field
      if ($formVars['function'] == 'tagg') {
        if ($formVars['status'] == 1) {
# give me the cell pointer you just clicked on.
          print "var cell = document.getElementById('" . $cellid . "');\n";
# give me the text in that cell
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

# remove the underscores
          print "celltext = celltext.replace(\"<u>\", \"\");\n";
          print "celltext = celltext.replace(\"</u>\", \"\");\n";

# remove the commas
          print "celltext = celltext.replaceAll(\",\", \"\");\n";
          print "celltext = celltext.replaceAll(\",\", \"\");\n";

# clear if no tags
          print "if (celltext = 'No Group Tags') {\n";
          print "  celltext = ''\n";
          print "}\n";

# blank the cell
          print "cell.innerHTML = '&nbsp;';\n";
# remove the function call
          print "cell.setAttribute(\"onclick\", \"\");\n";

# create an input field so the data can be edited
          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"tags_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"40\");\n";

# put the new input field into the cell
          print "cell.appendChild(infield);\n";

# put the cursor into the new cell
          print "document.getElementById('edit_data').focus();\n";
# highlight the text
          print "document.getElementById('edit_data').select();\n";
# and ready for editing
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

# give me the cell pointer you just finished
          print "var cell = document.getElementById('" . $cellid . "');\n";

# update the function so it can be clicked again; matches the info in the original
          print "cell.setAttribute(\"onclick\", \"edit_Tags(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# return to blanks
          if ($formVars['select'] == '') {
            $formVars['select'] = "No Group Tags";
          }

# replace the input field with the updated data.
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }


# public tags
# this is a text edit field
      if ($formVars['function'] == 'tagu') {
        if ($formVars['status'] == 1) {
# give me the cell pointer you just clicked on.
          print "var cell = document.getElementById('" . $cellid . "');\n";
# give me the text in that cell
          print "var celltext = document.getElementById('" . $cellid . "').innerHTML;\n";

# remove the underscores
          print "celltext = celltext.replaceAll(\"<u>\", \"\");\n";
          print "celltext = celltext.replaceAll(\"</u>\", \"\");\n";

# remove the commas
          print "celltext = celltext.replaceAll(\",\", \"\");\n";
          print "celltext = celltext.replaceAll(\",\", \"\");\n";

# clear if no tags
          print "if (celltext == 'No Public Tags') {\n";
          print "  celltext = ''\n";
          print "}\n";

# blank the cell
          print "cell.innerHTML = '&nbsp;';\n";
# remove the function call
          print "cell.setAttribute(\"onclick\", \"\");\n";

# create an input field so the data can be edited
          print "var infield = document.createElement('input');\n";

          print "infield.setAttribute(\"id\",\"edit_data\");\n";
          print "infield.setAttribute(\"name\",\"edit_data\");\n";
          print "infield.setAttribute(\"onblur\",\"tags_Completed(" . $formVars['id'] . ",'" . $formVars['function'] . "');\");\n";
          print "infield.setAttribute(\"type\",\"text\");\n";
          print "infield.setAttribute(\"value\",celltext);\n";
          print "infield.setAttribute(\"size\",\"100\");\n";

# put the new input field into the cell
          print "cell.appendChild(infield);\n";

# put the cursor into the new cell
          print "document.getElementById('edit_data').focus();\n";
# highlight the text
          print "document.getElementById('edit_data').select();\n";
# and ready for editing
        }
# close down the cell and put the text in 
        if ($formVars['status'] == 0) {

# give me the cell pointer you just finished
          print "var cell = document.getElementById('" . $cellid . "');\n";

# update the function so it can be clicked again; matches the info in the original
          print "cell.setAttribute(\"onclick\", \"edit_Tags(" . $formVars['id'] . ",'" . $formVars['function'] . "');" . "\");\n";

# update tags with the updated information
# the question is to delete all public tags, then add in the "new" ones?
# that might be the easiest method.

          $q_string  = "delete ";
          $q_string .= "from tags ";
          $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_view = 2 and tag_type = 1 ";
          $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# replace commas if accidentally added
          $formVars['select'] = str_replace(",", " ", $formVars['select']);
# and just in case it's comma+space, remove doubles
          $formVars['select'] = str_replace("  ", " ", $formVars['select']);

          $list = explode(" ", $formVars['select']);

          foreach ($list as $index) {

            $q_string  = "insert ";
            $q_string .= "into tags ";
            $q_string .= "set ";
            $q_string .= "tag_id          =   " . "null"             . ",";
            $q_string .= "tag_companyid   =   " . $formVars['id']    . ",";
            $q_string .= "tag_name        = \"" . $index             . "\",";
            $q_string .= "tag_type        =   " . 1                  . ",";
            $q_string .= "tag_view        =   " . 2                  . ",";
            $q_string .= "tag_owner       =   " . $_SESSION['uid']   . ",";
            $q_string .= "tag_group       =   " . $formVars['group'];

            $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          }

# rebuild the string, just to be sure
          $str_output = "";
          $comma = "";
          $q_string  = "select tag_name ";
          $q_string .= "from tags ";
          $q_string .= "where tag_companyid = " . $formVars['id'] . " and tag_type = 1 and tag_view = 2 ";
          $q_string .= "order by tag_name ";
          $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_tags) > 0) {
            while ($a_tags = mysqli_fetch_array($q_tags)) {
              $str_output .= $comma . $a_tags['tag_name'];
              $comma = ", ";
            }
          }

# want to rebuild the string
          $formVars['select'] = $str_output;

# return to blanks
          if ($formVars['select'] == '') {
            $formVars['select'] = "No Public Tags";
          }

# replace the input field with the updated data.
          print "cell.innerHTML = '<u>" . $formVars['select'] . "</u>';\n";
        }
      }


    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
