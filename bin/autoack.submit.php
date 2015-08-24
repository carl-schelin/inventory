#!/usr/local/bin/php
<?php
# File: autoack.submit.php
# Owner: Carl Schelin
# Description: Accepts email input
#

# the only thing we're doing here is creating a server.autoack file
# if the file exists, it's deleted

#########################
### validate parameter list
#########################

# passing user email and server or application name (with _ for spaces)
# $argc = the number of items in the $argv array
# argv[0] == app
# argv[1] == email
# argv[2] == server
# argv[3] == random number associated with the report email

  if ($argc == 1) {
# you'll never get here if it's coming from the procmail script so you can't 'unlink' the file.
    print "ERROR: invalid command line parameters\n";
    exit(1);
  } else {
    $email = trim($argv[1]);
  }

  if ($argc == 2) {
# you'll never get here if it's coming from the procmail script so you can't 'unlink' the file.
    print "ERROR: missing server/application\n";
    exit(1);
  } else {
    $subject = trim($argv[2]);
    $subject = strtolower($subject);
# in case it's an application, convert _ to spaces
    $subject = str_replace('_', ' ', $subject);
  }

  if ($argc == 3) {
    print "ERROR: missing random number\n";
    exit(1);
  } else {
    $random = trim($argv[3]);
  }

# received an "Out of Office:" message; just exit the script
# don't forget to delete the .report file or the next report will be whack.
  if ($subject == "Out") {
    print "ERROR: Out of Office message received\n";
    unlink($autoack);
    exit(1);
  }

#########################
### Set variables
#########################

  $autoack = "/export/home/autoack/Mail/" . $email . ".report." . $random;
  $server = "/export/home/autoack/Mail/" . $subject . ".autoack";

  if (file_exists($server)) {
    unlink($server);
  } else {
    $write = fopen($server, 'w');
    fclose($write);
  }

  unlink($autoack);

?>
