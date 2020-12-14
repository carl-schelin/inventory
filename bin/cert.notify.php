<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# Set debug to 1 in order to debug the code
  $debug = 0;

# get today's date
# loop through the certs in the database.
# build the localtime value (seconds from epoch)
# read the cert_group value to get the list of users who want to be notified
# loop through the users 
# build the warning date
# compare it with the expiration date

  $date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

  $q_string  = "select cert_url,cert_expire,cert_group ";
  $q_string .= "from certs";
  $q_certs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_certs = mysqli_fetch_array($q_certs)) {

    if ($debug) {
      print "\n  cert url: " . $a_certs['cert_url'] . " cert expire:" . $a_certs['cert_expire'] . " cert group:" . $a_certs['cert_group'] . "\n";
    }

# convert the cert expiration date to a numeric value for comparisons
    $certtime = strtotime($a_certs['cert_expire']);

# default is 90 days for all affected groups then users settings go into effect
    $warningdate = mktime(0, 0, 0, date('m'), date('d') + 90, date('Y'));
    $groupemail = 0;
    $webappsemail = 0;

# first check the affected group
    $q_string  = "select grp_name,grp_email ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_certs['cert_group'];
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);
    if ($debug) {
      print "  email: " . $a_groups['grp_email'] . "\n";
    }
    if (preg_match("/@intrado.com$/i", $a_groups['grp_email'])) {
      if ($a_certs['cert_group'] == 25) {
        $webappsemail = 1;
      } else {
        $groupemail = 1;
      }
    }

    if ($debug) {
      print "  webappsemail: " . $webappsemail . " groupemail: " . $groupemail . "\n";
      print "  date: " . $date . " " . date('Y-m-d', $date) . " cert expire:" . $certtime . " " . date('Y-m-d', $certtime) . " warning date:" . $warningdate . " " . date('Y-m-d', $warningdate) . "\n";
    }
# if it's a good e-mail address and it's right on the expiration date, send an e-mail to the group
    if ($certtime == $warningdate && (($webappsemail + $groupemail) > 0)) {
      if ($debug) {
        print "  " . $a_groups['grp_email'] . " Certificate is expiring" . " The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".\n";
      } else {
        mail($a_groups['grp_email'], "Certificate is expiring", "The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".");
      }
    }

# then check the WebApps group assuming the previous group check wasn't the webapps folks
    if ($webappsemail == 0) {
      $q_string  = "select grp_name,grp_email ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $GRP_WebApps;
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);
      if (preg_match("/@intrado.com$/i", $a_groups['grp_email'])) {
        $webappsemail = 1;
      }
    }
    if ($debug) {
      print "  webappsemail: " . $webappsemail . " groupemail: " . $groupemail . "\n";
      print "  date: " . $date . " cert expire:" . $certtime . " warning date:" . $warningdate . "\n";
    }

# now send it to the webapps email assuming it's a good address
    if ($certtime == $warningdate && $webappsemail == 1) {
      if ($debug) {
        print "  " . $a_groups['grp_email'] . " Certificate is expiring" . " The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".\n";
      } else {
        mail($a_groups['grp_email'], "Certificate is expiring", "The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".");
      }
    }

    $q_string  = "select usr_id,usr_name,usr_email,usr_notify,usr_freq,usr_countdown ";
    $q_string .= "from users ";
    $q_string .= "where (usr_group = " . $a_certs['cert_group'] . " or usr_group = " . $GRP_WebApps . ") and usr_disabled = 0";
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_users = mysqli_fetch_array($q_users)) {
      $email = $a_users['usr_email'];
      $subject = "Certificate is expiring";
      $body = "The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".";

      if ($debug) {
        print "    user name: " . $a_users['usr_name'] . " user countdown:" . $a_users['usr_countdown'] . " user notify:" . $a_users['usr_notify'] . "\n";
      }

# default notification of 90 days
      if ($a_users['usr_notify'] < 1) {
        $a_users['usr_notify'] = 90;
      }
      if ($debug) {
        print "    user notify: " . $a_users['usr_notify'] . "\n";
      }

# now get the number of days prior to expiration so as to be able to correctly ping the groups who manage the certs
      $warningdate = mktime(0, 0, 0, date('m'), date('d') + $a_users['usr_notify'], date('Y'));
      if ($debug) {
        print "    date: " . $date . " cert expire:" . $certtime . " warning date:" . $warningdate . "\n";
      }

# on the first day of expiration, all members of the cert management group get an e-mail assuming the group mail failed
      if ($certtime == $warningdate && $groupemail == 0 && $a_certs['cert_group'] != 25) {
        if ($debug) {
          print "    " . $email . " " . $subject . " " . $body . "\n";
        } else {
          mail($email, $subject, $body);
        }
        $a_users['usr_countdown'] = $a_users['usr_freq'];
      }

# on the first day of expiration, all members of the Web apps group get an e-mail assuming the group mail failed
      if ($certtime == $warningdate && $webappsemail = 0) {
        if ($debug) {
          print "    " . $email . " " . $subject . " " . $body;
        } else {
          mail($email, $subject, $body);
        }
        $a_users['usr_countdown'] = $a_users['usr_freq'];
      }

# if the frequency is greater than 0 (so 10 for example) and the countdown has reached 0
#   if the cert has expired
#     note expiration
#     clear countdown so they don't get any more e-mails once the cert has expired
#   or else
#     if the cert is within the expiration window as configured by the user or by using the default value of 90 days
#       note pending expiration
#       reset countdown to the designated user frequency. It has to be greater than zero to be set though per the initial conditional test
# or else
#   decrement the countdown

      if ($debug) {
        print "    user countdown: " . $a_users['usr_countdown'] . "\n";
      }
      if ($a_users['usr_freq'] > 0 && $a_users['usr_countdown'] == 0) {
        if ($certtime < $date) {
          if ($debug) {
            print "    " . $a_users['usr_email'] . " Certificate has expired" . " The certificate for \"" . $a_certs['cert_url'] . "\" expired on " . $a_certs['cert_expire'] . ".\n";
          } else {
            mail($a_users['usr_email'], "Certificate has expired", "The certificate for \"" . $a_certs['cert_url'] . "\" expired on " . $a_certs['cert_expire'] . ".");
          }
          $a_users['usr_countdown'] = -1;
        } else {
          if ($certtime < $warningdate) {
            if ($debug) {
              print "    " . $a_users['usr_email'] . " Certificate is expiring" . " The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".\n";
            } else {
              mail($a_users['usr_email'], "Certificate is expiring", "The certificate for \"" . $a_certs['cert_url'] . "\" is expiring on " . $a_certs['cert_expire'] . ".");
            }
            $a_users['usr_countdown'] = $a_users['usr_freq'];
          }
        }
      } else {
        $a_users['usr_countdown']--;
      }
      if ($debug) {
        print "    user countdown: " . $a_users['usr_countdown'] . "\n";
      }
      if ($a_users['usr_countdown'] < 0) {
        $a_users['usr_countdown'] = 0;
      }

# now update the user record with the new countdown;
      $q_string = "update users set usr_countdown = " . $a_users['usr_countdown'] . " where usr_id = " . $a_users['usr_id'];
      $r_users = mysqli_query($db, $q_string) or die($q_string . "\n" . mysqli_error($db) . "\n");
    }
  }

  mysqli_free_request($db);

?>
