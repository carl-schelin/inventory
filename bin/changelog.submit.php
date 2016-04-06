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

  $q_string  = "select usr_email,usr_first,usr_last,usr_name,usr_clientid,usr_group,usr_manager ";
  $q_string .= "from users ";
  $q_string .= "where (usr_email = '" . $email . "' or usr_altemail like '%" . $email . "%') and usr_id != 1 and usr_disabled = 0 ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_users = mysql_fetch_array($q_users);

# reset the email if using an alternate email address (like incojs01).
#  $email = $a_users['usr_email'];
  $clientid = $a_users['usr_clientid'];

  $q_string  = "select usr_first,usr_last,usr_name,usr_clientid ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $a_users['usr_manager'] . " and usr_disabled = 0 ";
  $q_manager = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_manager = mysql_fetch_array($q_manager);

  $q_string  = "select grp_changelog,grp_magic ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $a_users['usr_group'];
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_groups = mysql_fetch_array($q_groups);

  $groupid = $a_groups['grp_magic'];
  if ($groupid == '') {
    $groupid = "CORP-UNIX SYSADMIN";
  }

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

# bail if clientid not set. Can't submit to remedy without it.
# this also fails if email is incorrect including alternate emails.
  if ($a_users['usr_clientid'] == '') {
    print "ERROR: RemedyID not set\n";
    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

    $body  = "Your RemedyID has not been set in the Inventory application. In order for changelog messages ";
    $body .= "to be submitted successfully to Remedy, the RemedyID must be set. This value is the same ";
    $body .= "as your Windows Login ID. Please go into your Account Details and add your Remedy/Windows ";
    $body .= "RemedyID to your account and resend the change to the system.\n\n";

    $body .= "This message can also be received if your sending email address doesn't match your ";
    $body .= "Inventory email address.\n\n";

    $body .= "https://incojs01/inventory/accounts/profile.php\n";

    $email .= ",carl.schelin@intrado.com";

    mail($email, "Error: RemedyID Missing", $body, $headers);

    unlink($changelog);
    exit(1);
  }

# bail if manager is not selected. Can't submit to remedy without it.
  if ($a_users['usr_manager'] == 0) {
    print "ERROR: Manager not selected\n";
    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

    $body  = "Remedy requires your manager's login information in order to store your changelog entry. You have not ";
    $body .= "identified your manager in your profile. Log in to your account and select your manager from the list.";

    $body .= "https://incojs01/inventory/accounts/profile.php\n";

    $email .= ",carl.schelin@intrado.com";

    mail($email, "Error: Manager not selected", $body, $headers);

    unlink($changelog);
    exit(1);
  }


# bail if manager or manager clientid is not set. Can't submit to remedy without it.
  if ($a_manager['usr_clientid'] == '') {
    print "ERROR: Manager's RemedyID not set\n";
    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

    $body  = "Your Manager's RemedyID has not been set in the Inventory application. In order for changelog messages ";
    $body .= "to be submitted successfully to Remedy, your Manager's RemedyID must be set. This value is the same ";
    $body .= "as their Windows Login ID. Please have your manager go into their Account Details page and add their Remedy/Windows ";
    $body .= "RemedyID to their account and then you can resend the change to the system.\n\n";

    $body .= "An email was also sent to your manager.\n\n";

    $body .= "https://incojs01/inventory/accounts/profile.php\n";

    $email .= ",carl.schelin@intrado.com";

    mail($email, "Error: Manager's RemedyID Missing", $body, $headers);

# send an email to the manager as well.
    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

    $body  = "Your RemedyID has not been set in the Inventory application. In order for members of your ";
    $body .= "group to send changelog messages to Remedy, your RemedyID must be set. This value is the same ";
    $body .= "as your Windows Login ID. Please go into your Account Details and add your Remedy/Windows ";
    $body .= "RemedyID to your account and notify " . $a_users['usr_first'] . " " . $a_users['usr_last'] . " ";
    $body .= "so they can resend the change to the system.\n\n";

    $body .= "https://incojs01/inventory/accounts/profile.php\n";

    $email .= ",carl.schelin@intrado.com";

    mail($email, "Error: RemedyID Missing", $body, $headers);

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

    $email .= ",carl.schelin@intrado.com";

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
# just end when we get to a standard signature on a plain text message.
          if (preg_match("/^--/", $process) && $leave == 0) {
            $leave = 1;
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

# enable for magic
  $magic = 'no';
# enable for remedy
  $remedy = 'yes';

#
# This is the Magic ticket system process.
#
  if ($magic == 'yes') {

    $target = 'local';
    $target = 'dev';
    $target = 'prod';

    if ($target == 'local') {
      $magicemail = "carl.schelin@intrado.com";
    }
    if ($target == 'dev') {
      $magicemail = "svc_MagicAdminDev@intrado.com,carl.schelin@intrado.com";
    }
    if ($target == 'prod') {
      $magicemail = "svc_magicprodemail@intrado.com,carl.schelin@intrado.com";
    }

###############################
###  Format the mail message
###############################

# Template:
# Wrap the specific information in the listed tags

    $headers  = "From: Changelog <changelog@incojs01.scc911.com>\r\n";

#########
### Client ID: -u-/*u*
#########
    $body = "-u-" . $clientid . "*u*\n\n";

#########
### Group ID: -g-/*g*
#########
    $body .= "-g-" . $groupid . "*g*\n\n";

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

    mail($magicemail, "changelog", $body, $headers);
  }

  if ($remedy == 'yes') {

    $target = 'local';
    $target = 'dev';
    $target = 'prod';

# send it to carl for testing
    if ($target == 'local') {
      $remedyemail  = "carl.schelin@intrado.com";
      $remedyserver = "Blank";
    }
# development server information
    if ($target == 'dev') {
      $remedyemail  = "remedy.helpdesk.dev@intrado.com,carl.schelin@intrado.com";
      $remedyserver = "LMV08-REMAPPQA.corp.intrado.pri";
    }
# production server information
    if ($target == 'prod') {
      $remedyemail  = "remedy.helpdesk@intrado.com,carl.schelin@intrado.com";
      $remedyserver = "LMV08-REMAR01.corp.intrado.pri";
    }

# get the user information for the person in the inventory and will be the one opening the ticket plus group information
    $q_string  = "select usr_first,usr_last,usr_name,usr_email,usr_manager,grp_name ";
    $q_string .= "from users ";
    $q_string .= "left join groups on groups.grp_id = users.usr_group ";
    $q_string .= "where (usr_email = '" . $email . "' or usr_altemail like '%" . $email . "%') and usr_id != 1 and usr_disabled = 0 ";
    $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_users = mysql_fetch_array($q_users);

    $headers = "From: " . $a_users['usr_first'] . " " . $a_users['usr_last'] . "<" . $a_users['usr_email'] . ">\r\n";
    $headers .= "CC: carl.schelin@intrado.com\r\n";

#
# begin the email message
#

    $body  = "First Name*+ !1000000019!: " . $a_users['usr_first'] . "\n";
    $body .= "Last Name*+ !1000000018!: " . $a_users['usr_last'] . "\n";
    $body .= "(Change Location) Company*+ !1000000001!: Intrado, Inc.\n";
    $body .= "(Notes) Detailed Description !1000000151!: " . $report . "\n";
    $body .= "Summary* !1000000000!: Changelog Submission\n";
    $body .= "Impact* !1000000163!: 4-Minor/Localized\n";
    $body .= "Urgency* !1000000162!: 4-Low\n";
    $body .= "Priority !1000000164!: High\n";

    $body .= "#Change Coordinator Details\n";
    $body .= "Support Company !1000003228!: Intrado, Inc.\n";
    $body .= "Support Organization !1000003227!: Technical Operations\n";
    $body .= "Support Group Name+ !1000003229!: " . $a_users['grp_name'] . "\n";
    $body .= "Change Coordinator+ !1000003230!: " . $a_users['usr_first'] . " " . $a_users['usr_last'] . "\n";
    $body .= "Change Coordinator Login !1000003231!: " . $clientid . "\n";

    $body .= "#Change Manager Details\n";
    $body .= "Support Company !1000000251!: Intrado, Inc.\n";
    $body .= "Support Organization !1000000014!: Technical Operations\n";
    $body .= "Support Group Name !1000000015!: " . $a_users['grp_name'] . "\n";
    $body .= "Change Manager !1000000403!: " . $a_manager['usr_first'] . " " . $a_manager['usr_last'] . "\n";
    $body .= "Change Manager Login !1000000408!: " . $a_manager['usr_clientid'] . "\n";

    $body .= "# Change Dates in the following format 3/8/2016 1:00:00 AM MST\n";
    $body .= "Actual Start Date+ !1000000348!: " . date('n/j/Y g:i:s A e', strtotime("Yesterday")) . "\n";
    $body .= "Actual End Date+ !1000000364!: " . date('n/j/Y g:i:s A e') . "\n";

    $body .= "#PLEASE DO NOT MODIFY THE BELOW MANDATORY VALUES:\n";
    $body .= "Schema: CHG:ChangeInterface_Create\n";
    $body .= "Server: " . $remedyserver . "\n";
    $body .= "Action: Submit\n";
    $body .= "Status !         7!: Draft\n";
    $body .= "Risk Level* !1000000180!: Risk Level 1\n";
    $body .= "Class !1000000568!: Latent\n";
    $body .= "Change Type* !1000000181!: Change\n\n";

    mail($remedyemail, "Changelog Submission", $body, $headers);

  }

?>
