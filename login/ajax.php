<?php
  include('functions/dbconn.php');

  if (isset($_GET['search']) && $_GET['search'] != '') {

    $search = trim(addslashes($_GET['search']));

    $q_string  = "select distinct usr_name as suggest, usr_id, usr_first, usr_last ";
    $q_string .= "from inv_users ";
    $q_string .= "where usr_name like '" . $search . "%' or usr_first like '" . $search . "%' or usr_last like '" . $search . "%' ";
    $q_string .= "order by usr_name limit 0, 5";

    $q_inv_users = mysqli_query($db, $q_string);

    $c_inv_users = mysqli_num_rows($q_inv_users);

    if ($c_inv_users == 0) {
      echo "<div class='suggestions' style='color: #08c;'>No suggestions</div>\n";
    } else { // Display suggestions found.
      echo "<div class='suggestions''>Suggestions</div>\n";

      while ($a_inv_users = mysqli_fetch_array($q_inv_users)) {
        echo "<div class='suggest_link'><a href='user_edit.php?uid=" . $a_inv_users['usr_id'] . "'>" . $a_inv_users['suggest'] . "</a></div>\n";
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

      $c_inv_users = mysqli_num_rows(mysqli_query($db, "select * from inv_users where usr_name='" . $username . "'"));

      header('Content-Type: text/xml'); 
      header('Pragma: no-cache'); 
      echo '<?xml version="1.0" encoding="UTF-8"?>';

      echo '<result>'; 
      if ($c_inv_users > 0) {
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

      $c_levels = mysqli_num_rows(mysqli_query($db, "select * from inv_levels where lvl_name='" . $level . "'"));

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
