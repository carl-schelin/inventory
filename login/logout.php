<?php
session_start(); 

// For re-direction after logout. Because the form self-submits we need to keep the referal to the form.
$ref = getenv('HTTP_REFERER');

//check to make sure the session variable is registered 
if (isset($_SESSION['username'])){ 

//session variable is registered, the user is ready to logout 

  session_unset(); 
  session_destroy();

  header("Location: /inventory"); 

} else {
  header("Location: /inventory");
}

?>
