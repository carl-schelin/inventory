<?php
if (isset($_SESSION['username'])) {
  print "<li id=\"tm_account\"><a href=\"" . $Siteroot . "/index.account.php\">Account (" . $_SESSION['username'] . ")</a>\n";
} else {
  print "<li id=\"tm_account\"><a href=\"" . $Siteroot . "/index.account.php\">Account</a>\n";
}
?>
    <ul>
<?php
  if (isset($_SESSION['username'])) {
?>
      <li><a href="<?php print $Usersroot;  ?>/profile.php">Account Profile</a></li>
      <li><a href="<?php print $Usersroot;  ?>/grouplist.php">Group Members</a></li>
      <li><a href="<?php print $Bugroot; ?>/bugs.php">Report a Bug</a></li>
      <li><a href="<?php print $Featureroot; ?>/features.php">Request Enhancement</a></li>
      <li><a href="<?php print $FAQroot; ?>/whatsnew.php">What's New?</a></li>
      <li><a href="<?php print $Articleroot; ?>/index.php">Did You Know?</a></li>
      <li><a href="<?php print $Loginroot; ?>/logout.php">Logout (<?php print $_SESSION['username']; ?>)</a></li>
<?php
    if (check_userlevel($AL_Admin)) {
?>
      <li><a href="">-------------------------</a></li>
      <li><a href="<?php print $Usersroot; ?>/users.php">User Management</a></li>
      <li><a href="<?php print $Usersroot; ?>/groups.php">Group Management</a></li>
      <li><a href="<?php print $Usersroot; ?>/levels.php">Access Level Management</a></li>
      <li><a href="<?php print $Loginroot; ?>/assume.php">Change Credentials</a></li>
      <li><a href="<?php print $Adminroot; ?>/rsdpdup.php">Dedup RSDP Records</a></li>
      <li><a href="<?php print $Reportroot;  ?>/logs.php">View Last 7 Days of Logs</a></li>
<?php
    }
  } else {
?>
      <li><a href="<?php print $Loginroot; ?>/login.php">Login</a></li>
<?php
  }
?>
    </ul>
  </li>
</ul>

</div>

</div>

<p></p>

