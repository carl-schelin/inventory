#!/usr/local/bin/php
<?php
# File: changelog.submit.php
# Owner: Carl Schelin
# Description: Accepts email input in order to forward e-mail to Magic (or Remedy later)
#

  include('/usr/local/httpd/htsecure/inventory/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');


#########################
### validate parameter list
#########################

# passing user email and server or application name (with _ for spaces)
# $argc = the number of items in the $argv array
# argv[0] == app
# argv[1] == email
# argv[2] == server/application
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

#########################
### validate parameter list
#########################

  $q_string  = "select usr_email,usr_clientid,usr_group ";
  $q_string .= "from users ";
  $q_string .= "where (usr_email = '" . $email . "' or usr_altemail like '%" . $email . "%') and usr_id != 1 ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_users = mysql_fetch_array($q_users);

# reset the email if using an alternate email address (like incojs01).
#  $email = $a_users['usr_email'];
  $clientid = $a_users['usr_clientid'];

  $q_string  = "select grp_changelog ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $a_users['usr_group'];
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_groups = mysql_fetch_array($q_groups);

# added because Josh is in a different group (Mobility) that has their own changelog file.
# Josh sends changelogs for the Unix group on incojs01 and changelogs for Mobility via Outlook.
  if ($email == 'jjohnson@incojs01.scc911.com') {
    $a_groups['grp_changelog'] = 'changelog';
  }

  $changelog = "/export/home/" . $a_groups['grp_changelog'] . "/Mail/" . $email . ".report." . $random;

# received an "Out of Office:" message; just exit the script
# don't forget to delete the .report file or the next report will be whack.
  if ($subject == "Out") {
    print "ERROR: Out of Office message received\n";
    unlink($changelog);
    exit(1);
  }

# bail if clientid not set. Can't submit to magic without it.
# this also fails if email is incorrect including alternate emails.
  if ($clientid == '') {
    print "ERROR: ClientID not set\n";
    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

    $body  = "Your ClientID has not been set in the Inventory application. In order for changelog messages ";
    $body .= "to be submitted successfully to Magic, the ClientID must be set. Please go into your ";
    $body .= "Account Details and add your Magic ClientID to your account and resend the change to ";
    $body .= "the system.\n\n";

    $body .= "This message can also be received if your sending email address doesn't match your ";
    $body .= "Inventory email address.\n\n";

    $body .= "https://incojs01/inventory/login/user_admin.php\n";

    $email .= ",carl.schelin@intrado.com";

    mail($email, "Error: ClientID Missing", $body, $headers);

    unlink($changelog);
    exit(1);
  }


###################
### Now retrieve the server name and application name
### $subject will equal one or the other
###################
  $q_string  = "select inv_product ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_name = '" . $subject . "' ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_inventory = mysql_fetch_array($q_inventory);

  if ($a_inventory['inv_product'] == '') {
    $server = "Multiple";
    $application = $subject;
  } else {
    $q_string = "select prod_name ";
    $q_string .= "from products ";
    $q_string .= "where prod_id = " . $a_inventory['inv_product'];
    $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_products = mysql_fetch_array($q_products);

    $server = $subject;
    $application = $a_products['prod_name'];
  }


####################
# need to parse out the e-mail then delete it;
####################

# get the border value
#Content-Type: multipart/mixed;
#	boundary="_000_CBAD9FE331D55CarlSchelinintradocom_"
# read until: ^--------------
# read until --boundary value
#--_000_CBAD9FE331D55CarlSchelinintradocom_
# read content type for text/
#Content-Type: text/plain; charset="us-ascii"
#
#--_000_4AF497FC81FF2244B4B567536C8D627E011EC33478LMV08MX04corp_
#Content-Type: text/plain; charset="us-ascii"
#Content-Transfer-Encoding: quoted-printable
#
# looking for base64 and need to convert before processing.
#--_000_51581CA9D0965243B6931E4CDEDF3E45033E271B4Clmv08mx02corp_
#Content-Type: text/plain; charset="utf-8"
#Content-Transfer-Encoding: base64
#
# save details
# until '-- ' or '--=20' or until --border value
# ignore the rest
# delete the file when done

# for blackberry messages, there is no mime encoding and the signature is actually an ***This message ...
# so save the lines of text (if not blank) and bail if the "This message" signature pops up.
#
# $boundary = not used at this time
# $leave = flag to indicate the signature or other end of message has occurred. Means don't process or add more lines to the morning report message
# $process = the lines read from the email message
# $report = 
# $savedlines = 

  $savedlines = '';
  $boundary = '';
  $leave = 0;
  $report = '';
  $file = fopen($changelog, "r");
  if ($file === FALSE) {
    print "ERROR: Unable to open file.\n";

    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

    $body  = "ERROR: Unable to open " . $changelog . ".\n\n";
    $body .= "Email: " . $email . ".\n";
    $body .= "ClientID: " . $clientid . ".\n";
    $body .= "Server: " . $server . ".\n";
    $body .= "Application: " . $application . ".\n";
    $body .= "Random Number: " . $random . ".\n\n";

    $email .= "carl.schelin@intrado.com";

    mail($email, "Error: Unable to open changelog report", $body, $headers);

    exit(1);
  } else {
  while(!feof($file)) {
    $process = trim(fgets($file));

    if (preg_match("/boundary/", $process) && $leave == 0) {
      $value = split("\"", $process);
      $boundary = $value[1];
    }

# this is added by the procmail script. lets us skip all the header information
    if (preg_match("/^--------------/", $process) && $leave == 0) {
      print "Loop: Past the header lines, reached the separator between header and body\n";
      while (!feof($file)) {
        $process = trim(fgets($file));

# again, if a blackberry (bb uses '__' as signature sep), we're done
        if (preg_match("/Intrado Wireless Information Network/", $process) || preg_match("/__/", $process) && $leave == 0) {
          print "Loop-Wireless: Found Blackberry signature line. Saving text, exiting loop.\n";
          $report .= $savedlines . "\n";
          $leave = 1;
          break;
        }
# save the lines in case it's a plain text message from the blackberry; save after the exit due to the "Wireless" message.
        if ((preg_match("/^--/", $process) == 0 && 
             preg_match("/Content-Type: multipart\/alternative/", $process) == 0 && 
             preg_match("/boundary=\"_/", $process) == 0 && 
             preg_match("/Content-Type: text\/plain/", $process) == 0 && 
             preg_match("/Content-Transfer-Encoding:/", $process) == 0) && 
             $leave == 0
           ) {
          print "Loop-Blank: Non-blank line; saving.\n";
          $savedlines .= $process . "\n";
        }

# on the other hand, if it's an outlook message, parse out the mime encoding.
# assume the text/plain is the first block of data with the text/html as the second block
# each block starts with '^--' so after the '-----' line added by procmail, the next line to look for is
# the Content-Transfer line (the last line of the separator block). Once that's reached, begin parsing each line 
# until the next '^--' is reached which is either the signature block or the text/html block.
        if (preg_match("/Content-Transfer-Encoding: quoted-printable/", $process) && $leave == 0) {
          print "Loop-Content: quoted-printable found.\n";
          while (!feof($file)) {
            $process = trim(fgets($file));
            if (preg_match("/^--/", $process)) {
              print "Loop-Content: '^--' found. Exiting loop.\n";
              $leave = 1;
              break;
            }
            if ($process != '') {
              print "Loop-Content: Non-blank line; removing equals sign and saving.\n";
              $report .= preg_replace("/=$/", '', $process);
            }
          }
        }
# need to read it in, then convert it, _then_ loop through the resultent output looking for the *this message has been sent... or -- /r/n lines to save any encoded information
        if (preg_match("/Content-Transfer-Encoding: base64/", $process) && $leave == 0) {
          print "Loop-Content: base64 found.\n";
          while (!feof($file)) {
            $process = trim(fgets($file));
            if (preg_match("/^--/", $process)) {
              print "Loop-Content: '^--' found. Exiting loop.\n";
              $parse = explode("\n", base64_decode($report));
              $report = '';
              for ($i = 0; $i < count($parse); $i++) {
                if (preg_match("/_______________________/", $parse[$i]) || preg_match("/^--/", $parse[$i])) {
                  $leave = 1;
                  break;
                } else {
                  $report .= $parse[$i] . "\n";
                }
              }
              $leave = 1;
              break;
            }
            if ($process != '') {
              $report .= $process . "\n";
            }
          }
        }
      }
    }
  }
  fclose($file);
  }
  unlink($changelog);


#####################
# we have a readable message.
#####################

  if ($report == '' and strlen($savedlines) > 0) {
    $report = $savedlines;
  }


###############################
###  Format the mail message
###############################

# Template:
# Wrap the specific information in the listed tags

  $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";
#  $magic = "carl.schelin@intrado.com";
#  $magic = "svc_MagicAdminDev@intrado.com,carl.schelin@intrado.com";
  $magic = "svc_magicprodemail@intrado.com,carl.schelin@intrado.com";

#########
### Client ID: -u-/*u*
#########
  $body = "-u-" . $clientid . "*u*\n\n";

#########
### Server: -s-/*s*
#########
  $body .= "-s-" . $server . "*s*\n\n";
  
#########
### Application: -a-/*a*
#########
  $body .= "-a-" . $application . "*a*\n\n";

#########
### Description: -d-/*d*
#########
  $body .= "-d-\n" . $report . "*d*\n\n";

#########
### Resolution: -r-/*r*
#########
  $body .= "-r-" . "Completed" . "*r*\n\n";


###############################
###  Send the mail to magic
###############################

  mail($magic, "changelog", $body, $headers);

?>
