/!/usr/local/bin/php
<?php
# File: changelog.submit.php
# Owner: Carl Schelin
# Description: Accepts email input in order to forward e-mail to Magic (or Remedy later)
#

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# are tickets created?
# yes for remedy
  $remedy = 'no';

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

# NOTE: There are now automated processes where changelogs come from root@ or unixsvc@.
# these two should bypass the validation steps as they're going to belong to the Unix team's 'changelog'

  $service = explode("@", $email);

  if ($service[0] == 'root' || $service[0] == 'unixsvc') {

# really just looking for this so the automatic serviceaccounts can apply changelogs
    $groupchangelog = 'changelog';

  } else {
    $q_string  = "select usr_id,usr_email,usr_first,usr_last,usr_name,usr_group,usr_manager ";
    $q_string .= "from users ";
    $q_string .= "where (usr_email = '" . $email . "' or usr_altemail like '%" . $email . "%') and usr_id != 1 and usr_disabled = 0 ";
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_users = mysqli_fetch_array($q_users);

# reset the email if using an alternate email address.
#  $email = $a_users['usr_email'];

# users can be members of multiple groups.
# run through the grouplist table for this user
# and try to locate the saved file.

    $q_string  = "select gpl_group,grp_changelog ";
    $q_string .= "from grouplist ";
    $q_string .= "left join a_groups on a_groups.grp_id = grouplist.gpl_group ";
    $q_string .= "where gpl_user = " . $a_users['usr_id'] . " ";
    $q_grouplist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_grouplist = mysqli_fetch_array($q_grouplist)) {

# looking for $Changehome/$groupchangelog/Mail/user@domain.report.random_number
      if (file_exists($Changehome . "/" . $a_grouplist['grp_changelog'] . '/Mail/' . $email . '.report.' . $random)) {
        $groupchangelog = $a_grouplist['grp_changelog'];
      }
    }

# get the manager information for the user.
    $q_string  = "select usr_first,usr_last,usr_name ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_users['usr_manager'] . " and usr_disabled = 0 ";
    $q_manager = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_manager = mysqli_fetch_array($q_manager);
  }

  $changelog = $Changehome . "/" . $groupchangelog . "/Mail/" . $email . ".report." . $random;

# received an "Out of Office:" message; just exit the script
# don't forget to delete the .report file or the next report will be whack.
  if ($subject == "Out") {
    print "ERROR: Out of Office message received\n";
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
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  if ($a_inventory['inv_product'] == '') {
    $server = "Multiple";
    $application = $subject;
  } else {
    $q_string = "select prod_name ";
    $q_string .= "from products ";
    $q_string .= "where prod_id = " . $a_inventory['inv_product'];
    $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_products = mysqli_fetch_array($q_products);

    $server = $subject;
    $application = $a_products['prod_name'];
  }


####################
# need to parse out the e-mail then delete it;
####################

# get the border value
#Content-Type: multipart/mixed;
#	boundary="_000_CBAD9FE331D55CarlSchelininteralpri_"
# read until: ^--------------
# read until --boundary value
#--_000_CBAD9FE331D55CarlSchelininteralpri_
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
  $firstline = '';
  $boundary = '';
  $leave = 0;
  $report = '';
  $file = fopen($changelog, "r");
  if ($file === FALSE) {
    print "ERROR: Unable to open file.\n";

    $headers  = "From: Changelog <changelog@" . $hostname . ">\r\n";
    $headers .= "CC: " . $Sitedev . "\r\n";

    $body  = "ERROR: Unable to open " . $changelog . ".\n\n";
    $body .= "Email: " . $email . ".\n";
    $body .= "Server: " . $server . ".\n";
    $body .= "Application: " . $application . ".\n";
    $body .= "Random Number: " . $random . ".\n\n";

    mail($email, "Error: Unable to open changelog report", $body, $headers);

    exit(1);
  } else {
    while(!feof($file)) {
      $process = trim(fgets($file));

      if (preg_match("/boundary/", $process) && $leave == 0) {
        $value = explode("\"", $process);
        $boundary = $value[1];
      }

# this is added by the procmail script. lets us skip all the header information
      if (preg_match("/^--------------/", $process) && $leave == 0) {
        print "Loop: Past the header lines, reached the separator between header and body\n";
        while (!feof($file)) {
          $process = trim(fgets($file));

# again, if a blackberry (bb uses '__' as signature sep), we're done
          if (preg_match("/Company Wireless Information Network/", $process) || preg_match("/__/", $process) && $leave == 0) {
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
# save the first line as the 'summary line' for Remedy tickets. Ignore for Magic
            if ($firstline == '') {
              $firstline = clean($process, 95);
            }
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
# save the first line as the 'summary line' for Remedy tickets. Ignore for Magic
                if ($firstline == '') {
                  $firstline = preg_replace("/=$/", '', clean($process, 95));
                }
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
# save the first line as the 'summary line' for Remedy tickets. Ignore for Magic
                    if ($firstline == '') {
                      $firstline = clean($parse[$i], 95);
                    }
                  }
                }
                $leave = 1;
                break;
              }
              if ($process != '') {
                $report .= $process . "\n";
# save the first line as the 'summary line' for Remedy tickets. Ignore for Magic
                if ($firstline == '') {
                  $firstline = clean($process, 95);
                }
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

  mysqli_close($db);

?>
