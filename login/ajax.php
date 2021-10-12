<?php
  include('functions/dbconn.php');

  if (isset($_GET['search']) && $_GET['search'] != '') {

    $search = trim(addslashes($_GET['search']));

    $q_string  = "select distinct usr_name as suggest, usr_id, usr_first, usr_last ";
    $q_string .= "from users ";
    $q_string .= "where usr_name like '" . $search . "%' or usr_first like '" . $search . "%' or usr_last like '" . $search . "%' ";
    $q_string .= "order by usr_name limit 0, 5";

    $q_users = mysqli_query($db, $q_string);

    $c_users = mysqli_num_rows($q_users);

    if ($c_users == 0) {
      echo "<div class='suggestions' style='color: #08c;'>No suggestions</div>\n";
    } else { // Display suggestions found.
      echo "<div class='suggestions''>Suggestions</div>\n";

      while ($a_users = mysqli_fetch_array($q_users)) {
        echo "<div class='suggest_link'><a href='user_edit.php?uid=" . $a_users['usr_id'] . "'>" . $a_users['suggest'] . "</a></div>\n";
      }
    }
  }

  $do = $_GET['do'];
 
  switch($do) {

    case 'check_username_exists': 

      if (get_magic_quotes_gpc()) { 
        $username = $_GET['usr_name']; 
      } else { 
        $username = addslashes($_GET['usr_name']); 
      } 

      $c_users = mysqli_num_rows(mysqli_query($db, "select * from users where usr_name='" . $username . "'"));

      header('Content-Type: text/xml'); 
      header('Pragma: no-cache'); 
      echo '<?xml version="1.0" encoding="UTF-8"?>';

      echo '<result>'; 
      if ($c_users > 0) {
        echo 'exists';
      } else {
        echo 'avail';
      } 
      echo '</result>'; 

      break; 

    default:
      echo 'Error, invalid action'; 
      break;

    case 'check_level_exists': 

      if (get_magic_quotes_gpc()) { 
        $level = $_GET['level']; 
      } else { 
        $level = addslashes($_GET['level']); 
      } 

      $c_levels = mysqli_num_rows(mysqli_query($db, "select * from levels where lvl_name='" . $level . "'"));

      header('Content-Type: text/xml'); 
      header('Pragma: no-cache'); 
      echo '<?xml version="1.0" encoding="UTF-8"?>';

      echo '<result>'; 
      if ($c_levels == 0) {
        echo 'avail';
      } else {
        echo 'exists';
      } 
      echo '</result>'; 

      break; 

    default:
      echo 'Error, invalid action'; 
      break; 
  } 

?>
