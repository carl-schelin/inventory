#!/usr/local/bin/php
<?php
# File: autoack.mail.php
# Owner: Carl Schelin
# Description: Accepts email input
#

# read incoming messages to the ovmc account and send out an autoack email if the subject line matches the server name.

# To: 
# $WHOCARES
# cc:
# $WHOCARES
# Subject: Only needs 'Re: number'
# Re: 6201:[server] [06:52:41] [major] [ping] [OS] [NodeDown]

#########################
### validate parameter list
#########################

# passing user email and server or application name (with _ for spaces)
# $argc = the number of items in the $argv array
# argv[0] == app
# argv[1] == noc yes/no
# argv[2] == subjectline

  if ($argc == 1) {
# you'll never get here if it's coming from the procmail script so you can't 'unlink' the file.
    print "ERROR: invalid command line parameters\n";
    exit(1);
  } else {
    $noc = trim($argv[1]);
  }

  $subject = '';
  if ($argc == 2) {
# you'll never get here if it's coming from the procmail script so you can't 'unlink' the file.
    print "ERROR: invalid command line parameters\n";
    exit(1);
  } else {
    $outofoffice = trim($argv[2]);
    for ($i = 2; $i <= $argc; $i++) {
      $subject .= trim($argv[$i]) . " ";
    }
  }

# received an "Out of Office:" message; just exit the script
# don't forget to delete the .report file or the next report will be whack.
  if ($outofoffice == "Out") {
    print "ERROR: Out of Office message received\n";
    exit(1);
  }

#########################
### Set variables
#########################

# server is code:server.fqdn
# split on :
# split on .

  $code = explode(":", $subject);
  $server = explode(".", $code[1]);

  $autoack = "/export/home/autoack/Mail/" . $server[0] . ".autoack";

  if (file_exists($autoack)) {
    $headers      = "From: OVMC <ovmc@" . $hostname . ">\r\n";
    $headers     .= "CC: " . $WHOCARES . "\r\n";
    $headers     .= "MIME-Version: 1.0\r\n";
    $headers     .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    if ($noc == 'yes') {
      $destination  = $NOCCARES;
    } else {
      $destination  = $WHOCARES;
    }

    $subject      = "Re: $subject";
    $body         = "Ack\n";
    
    mail($destination, $subject, $body, $headers);
  }

?>
