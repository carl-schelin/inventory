#!/usr/local/bin/php
<?php
# Script: core.exclude.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "#\n";
  print "# messages.exclude file - by Carl Schelin\n";
  print "#  This file contains a list of common message entries to be excluded from the raw\n";
  print "#  messages files so that sysadmins can properly troubleshoot problems.\n";
  print "#\n";
  print "#  Commented lines are ignored by the chkmessages script so be verbose in explaining\n";
  print "#  why a specific block of lines are in the exclude file.\n";
  print "#\n";
  print "#  At the end is a block of temporary excludes. This is used to remove data from the\n";
  print "#  raw file when you're working on it or the problem won't be resolved in a few days\n";
  print "#  or weeks.\n";
  print "#\n";
  print "#  Lines can be regex'd. If you are searching for a bracket or parenthese, you'll\n";
  print "#  need to escape it \( \) \[ \] as they are used by regex to mean something.\n";
  print "#\n";
  print "#  Current regex's I'm using in this file:\n";
  print "#   .*    Everything between the two search strings.\n";
  print "#   [12]  Search for the number 1 or the number 2\n";
  print "#   [0-9] Search for all numbers from 0 to 9\n";
  print "#\n";
  print "#\n";
  print "# There is a 'messages.exclude' file in /usr/local/admin/install/unixsuite/etc\n";
  print "# which contains server specific messages that need to be removed.\n";
  print "# This file should be used for messages that occur across multiple servers\n";
  print "# and smaller groupings of messages or temporary exclusions (please date the\n";
  print "# entries and put a subject above it so we know when they can be removed).\n";

  $comment = '';
  $q_string  = "select ex_text,ex_comments ";
  $q_string .= "from excludes ";
  $q_string .= "where ex_companyid = 0 and ex_deleted = 0 and ex_expiration >= \"" . date('Y-m-d') . "\" ";
  $q_string .= "order by ex_text ";
  $q_excludes = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_excludes = mysqli_fetch_array($q_excludes)) {

    if ($comment != $a_excludes['ex_comments']) {
      print "#\n# " . $a_excludes['ex_comments'] . "\n";
      $comment = $a_excludes['ex_comments'];
    }
    print $a_excludes['ex_text'] . "\n";

  }

  mysqli_close($db);

?>
