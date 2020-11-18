<?php

include('settings.php');

date_default_timezone_set('UTC');

# clean and escape the input data

function clean($input, $maxlength) {
  $input = trim($input);
  $input = substr($input, 0, $maxlength);
  return ($input);
}

# log who did what

function logaccess($user, $source, $detail) {
  include('settings.php');

  $query = "insert into log set " .
    "log_id        = NULL, " .
    "log_user      = \"" . $user   . "\", " .
    "log_source    = \"" . $source . "\", " .
    "log_detail    = \"" . $detail . "\"";

  $insert = mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
}

function check_userlevel( $p_level ) {
  if (isset($_SESSION['username'])) {
    include('settings.php');
    $q_string  = "select usr_level ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $_SESSION['uid'];
    $q_user_level = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_user_level = mysqli_fetch_array($q_user_level);

    if ($a_user_level['usr_level'] <= $p_level) {
      return(1);
    } else {
      return(0);
    }
  } else {
    return(0);
  }
}

function last_insert_id() {
  include('settings.php');

  $query = "select last_insert_id()";
  $q_result = mysqli_query($db, $query) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $query . "&mysql=" . mysqli_error($db)));
  $a_result = mysqli_fetch_array($q_result);

  return ($a_result['last_insert_id()']);
}

# west is looking only for the following data to be logged.
# Asset Name
# Manufacturer
# Model
# Serial Number
# State (Active)
# Class
# Location
# clear out any old records for the same system and same table/column when the same one shows up
function changelog( $p_serverid, $p_changed, $p_notes, $p_user, $p_table, $p_column, $p_cleared ) {
  include('settings.php');

# clear previous entries for the same task; so if a server was location changed, only record the last change
# still testing
  $cl_query = "update modified set " . 
    "mod_cleared = 1 " . 
    "where mod_notes  = \"" . $p_notes  . "\" " . 
      "and mod_table  = \"" . $p_table  . "\" " . 
      "and mod_column = \"" . $p_column . "\" " .
      "and mod_companyid =   " . $p_serverid;
#  $result = mysqli_query($db, $cl_query);

  $cl_query  = 
    "mod_companyid    =   " . $p_serverid     . "," . 
    "mod_changed      = \"" . $p_changed      . "\"," . 
    "mod_notes        = \"" . $p_notes        . "\"," . 
    "mod_user         =   " . $p_user         . "," . 
    "mod_date         = \"" . date('Y-m-d')   . "\"," . 
    "mod_table        = \"" . $p_table        . "\"," . 
    "mod_column       = \"" . $p_column       . "\"," .
    "mod_cleared      =   " . $p_cleared;

  $query = "insert into modified set mod_id = null," . $cl_query;

  $result = mysqli_query($db, $query);

}

# pass the group to verify.
# get the user level and group the user belongs to
# if the group matches or the user is an admin
# return yes.
function check_grouplevel( $p_group ) {
# somewhere it's passing a blank value for 'p_group' so for now; if blank set to 0.
  if ($p_group == '') {
    $p_group = 0;
  }
  if (isset($_SESSION['username'])) {
    include('settings.php');

# if primary group, just return
    $q_string  = "select usr_level,usr_group ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $_SESSION['uid'];
    $q_users = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    $a_users = mysqli_fetch_array($q_users);

    if ($p_group == $a_users['usr_group'] || $a_users['usr_level'] == $AL_Admin) {
      return(1);
    }

# check extended groups next...
    $q_string  = "select gpl_id ";
    $q_string .= "from grouplist ";
    $q_string .= "where gpl_user = " . $_SESSION['uid'] . " and gpl_group = " . $p_group . " ";
    $q_grouplist = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=function.php&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    if (mysqli_num_rows($q_grouplist) > 0) {
      return(1);
    }
  }
  return(0);
}

if (!function_exists('hex2bin')) {
  function hex2bin($str) {
    $sbin = "";
    $len = strlen($str);
    for ($i = 0; $i < $len; $i += 2) {
      $sbin .= pack("H*", substr($str, $i, 2));
    }
    return $sbin;
  }
}

function generatePassword ($length = 8) {

// start with a blank password
  $password = "";

// define possible characters - any character in this string can be
// picked for use in the password, so if you want to put vowels back in
// or add special characters such as exclamation marks, this is where
// you should do it
  $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

// we refer to the length of $possible a few times, so let's grab it now
  $maxlength = strlen($possible);

// check for length overflow and truncate if necessary
  if ($length > $maxlength) {
    $length = $maxlength;
  }

// set up a counter for how many characters are in the password so far
  $i = 0;

// add random characters to $password until $length is reached
  while ($i < $length) {

// pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, $maxlength-1), 1);

// have we already used this character in $password?
    if (!strstr($password, $char)) {
// no, so it's OK to add it onto the end of whatever we've already got...
      $password .= $char;
// ... and increase the counter by one
      $i++;
    }

  }

// done!
  return $password;

}

function createNetmaskAddr($bitcount) {
  $netmask = str_split(str_pad(str_pad('', $bitcount, '1'), 32, '0'), 8);
  foreach ($netmask as &$element) $element = bindec($element);
  return join('.', $netmask);
}

# return the network portion of a passed address and cidr (eg 24)
function return_Network( $p_addr, $p_cidr ) {
  $binary = decbin(ip2long(long2ip(-1 << (32 - (int)$p_cidr))));
  return long2ip(bindec(decbin(ip2long($p_addr)) & $binary));
}

/* our simple php ping function */
function ping($host) {
  $sysos = php_uname('s');

  if ($sysos == "Linux") {
    exec(sprintf('/bin/ping -c 1 -w 1 %s', $host), $res, $rval);
  }
  if ($sysos == "SunOS") {
    exec(sprintf('/usr/sbin/ping %s 1', $host), $res, $rval);
  }
  return $rval === 0;
}

function return_Index($p_check, $p_string) {
  include('settings.php');

  $r_index = 0;
  $count = 1;
  $q_table = mysqli_query($db, $p_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_table = mysqli_fetch_row($q_table)) {
    if ($p_check == $a_table[0]) {
      $r_index = $count;
    }
    $count++;
  }
  return $r_index;
}

function wait_Process($p_string) {
# includeing in order to use path information
  include('settings.php');

  $randgif = rand(0,1);

  $output  = "<center>";
  switch ($randgif) {
    case 0: $output .= "<img src=\"" . $Siteroot . "/imgs/3MA_processingbar.gif\">";
            $output .= "<br class=\"iu-widget-content\">" . $p_string;
            break;
    case 1: $output .= "<img src=\"" . $Siteroot . "/imgs/progress_bar.gif\">";
            $output .= "<br class=\"iu-widget-content\">" . $p_string;
            break;
    case 2: $output .= "<img src=\"" . $Siteroot . "/imgs/chasingspheres.gif\">";
            $output .= $p_string;
            $output .= "<img src=\"" . $Siteroot . "/imgs/chasingspheres.gif\">";
            break;
    case 3: $output .= "<img src=\"" . $Siteroot . "/imgs/gears.gif\">";
            $output .= $p_string;
            $output .= "<img src=\"" . $Siteroot . "/imgs/gears.gif\">";
            break;
    case 4: $output .= "<img src=\"" . $Siteroot . "/imgs/recycling.gif\">";
            $output .= $p_string;
            $output .= "<img src=\"" . $Siteroot . "/imgs/recycling.gif\">";
            break;
  }
  $output .= "</center>";

  return $output;
}

function return_ServerID( $p_string ) {
  include('settings.php');

  $output = 1109;

# need to split fqdn if passed.
  $p_hostname = explode(".", $p_string);

# first check production systems.
  $q_string  = "select inv_id ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_name = '" . $p_hostname[0] . "' ";
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

  if (mysqli_num_rows($q_inventory) == 0) {
    $q_string  = "select inv_id ";
    $q_string .= "from inventory ";
    $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
    $q_string .= "where inv_status = 0 and int_server = '" . $p_hostname[0] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

    if (mysqli_num_rows($q_inventory) > 0) {
      $a_inventory = mysqli_fetch_array($q_inventory);
      $output = $a_inventory['inv_id'];
    }
  } else {
    if (mysqli_num_rows($q_inventory) == 1) {
      $a_inventory = mysqli_fetch_array($q_inventory);
      $output = $a_inventory['inv_id'];
    }
  }

# can't find it in production, check retired servers.
  if ($output == 1109) {
    $q_string  = "select inv_id ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $p_hostname[0] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

    if (mysqli_num_rows($q_inventory) == 0) {
      $q_string  = "select inv_id ";
      $q_string .= "from inventory ";
      $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
      $q_string .= "where int_server = '" . $p_hostname[0] . "' ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      if (mysqli_num_rows($q_inventory) > 0) {
        $a_inventory = mysqli_fetch_array($q_inventory);
        $output = $a_inventory['inv_id'];
      }
    } else {
      if (mysqli_num_rows($q_inventory) == 1) {
        $a_inventory = mysqli_fetch_array($q_inventory);
        $output = $a_inventory['inv_id'];
      }
    }
  }

  return $output;
}

function return_Virtual( $p_string ) {
  include('settings.php');

  $output = 0;

  $q_string  = "select hw_id,mod_virtual ";
  $q_string .= "from hardware ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where hw_companyid = " . $p_string . " and mod_primary = 1 and mod_virtual = 1 ";
  $q_hardware = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# if there are any rows, then the server is a virtual machine.
  if (mysqli_num_rows($q_hardware) > 0) {
    $output = 1;
  }

  return $output;
}

function return_ShortOS( $p_string ) {
  include('settings.php');

# this should be the official string returned from the OS and not 
# found strings.
  $ret_string = $p_string;
  if ($p_string == "Solaris 9 9/04 s9s_u7wos_09 SPARC") {
    $ret_string = "Solaris 9 9/04";
  }
  if ($p_string == "Solaris 10 6/06 s10s_u2wos_09a SPARC") {
    $ret_string = "Solaris 10 6/06";
  }
  if ($p_string == "Solaris 10 11/06 s10s_u3wos_10 SPARC") {
    $ret_string = "Solaris 10 11/06";
  }
  if ($p_string == "Solaris 10 5/08 s10x_u5wos_10 X86") {
    $ret_string = "Solaris 10 5/08";
  }
  if ($p_string == "Solaris 10 10/08 s10s_u6wos_07b SPARC") {
    $ret_string = "Solaris 10 10/08";
  }
  if ($p_string == "Solaris 10 10/08 s10x_u6wos_07b X86") {
    $ret_string = "Solaris 10 10/08";
  }
  if ($p_string == "Oracle Solaris 10 9/10 s10x_u9wos_14a X86") {
    $ret_string = "Solaris 10 9/10";
  }
  if ($p_string == "Oracle Solaris 10 8/11 s10s_u10wos_17b SPARC") {
    $ret_string = "Solaris 10 8/11";
  }
  if ($p_string == "Oracle Enterprise Linux Enterprise Linux AS release 4 (October Update 7)") {
    $ret_string = "Oracle EL AS 4.7";
  }
  if ($p_string == "Red Hat Enterprise Linux ES release 4 (Nahant Update 4)") {
    $ret_string = "RHEL ES 4.4";
  }
  if ($p_string == "Red Hat Enterprise Linux AS release 4 (Nahant Update 8)") {
    $ret_string = "RHEL AS 4.8";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 5.1 (Tikanga)") {
    $ret_string = "RHEL5.1";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 5.2 (Tikanga)") {
    $ret_string = "RHEL5.2";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 5.3 (Tikanga)") {
    $ret_string = "RHEL5.3";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 5.4 (Tikanga)") {
    $ret_string = "RHEL5.4";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 5.11 (Tikanga)") {
    $ret_string = "RHEL5.11";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.1 (Santiago)") {
    $ret_string = "RHEL6.1";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.2 (Santiago)") {
    $ret_string = "RHEL6.2";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.3 (Santiago)") {
    $ret_string = "RHEL6.3";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.4 (Santiago)") {
    $ret_string = "RHEL6.4";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.5 (Santiago)") {
    $ret_string = "RHEL6.5";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.6 (Santiago)") {
    $ret_string = "RHEL6.6";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.7 (Santiago)") {
    $ret_string = "RHEL6.7";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.8 (Santiago)") {
    $ret_string = "RHEL6.8";
  }
  if ($p_string == "CentOS release 6.8 (Final)") {
    $ret_string = "COS6.8";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.9 (Santiago)") {
    $ret_string = "RHEL6.9";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 6.10 (Santiago)") {
    $ret_string = "RHEL6.10";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.0 (Maipo)") {
    $ret_string = "RHEL7.0";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.1 (Maipo)") {
    $ret_string = "RHEL7.1";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.2 (Maipo)") {
    $ret_string = "RHEL7.2";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.3 (Maipo)") {
    $ret_string = "RHEL7.3";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.4 (Maipo)") {
    $ret_string = "RHEL7.4";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.5 (Maipo)") {
    $ret_string = "RHEL7.5";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.6 (Maipo)") {
    $ret_string = "RHEL7.6";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.7 (Maipo)") {
    $ret_string = "RHEL7.7";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.8 (Maipo)") {
    $ret_string = "RHEL7.8";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 7.9 (Maipo)") {
    $ret_string = "RHEL7.9";
  }
  if ($p_string == "Red Hat Enterprise Linux Server release 8.0 (Ootpa)") {
    $ret_string = "RHEL8.0";
  }

  return $ret_string;
}

# Return the main OS type vs the unique OS version string.
# passed value is the numeric inv_id of the server
function return_System( $p_string ) {
  include('settings.php');

  $output = '';
  $q_string = "select sw_software ";
  $q_string .= "from software ";
  $q_string .= "where sw_type = 'OS' and sw_companyid = " . $p_string;
  $q_software = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  $a_software = mysqli_fetch_array($q_software);

  $output = $a_software['sw_software'];
  if (stripos($a_software['sw_software'], "linux") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "red hat") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "debian") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "ubuntu") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "centos") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "suse") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "fedora") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_software['sw_software'], "solaris") !== false) {
    $output = 'SunOS';
  }
  if (stripos($a_software['sw_software'], "hp-ux") !== false) {
    $output = 'HP-UX';
  }
  if (stripos($a_software['sw_software'], "tru64") !== false) {
    $output = 'OSF1';
  }
  if (stripos($a_software['sw_software'], "osf1") !== false) {
    $output = 'OSF1';
  }
  if (stripos($a_software['sw_software'], "freebsd") !== false) {
    $output = 'FreeBSD';
  }
  if (stripos($a_software['sw_software'], "windows") !== false) {
    $output = 'Windows';
  }
  if (stripos($a_software['sw_software'], "esx") !== false) {
    $output = 'VMWare';
  }
  if (stripos($a_software['sw_software'], "vmware") !== false) {
    $output = 'VMware';
  }
  if (stripos($a_software['sw_software'], "cisco ios") !== false) {
    $output = 'Cisco';
  }
  if (stripos($a_software['sw_software'], "appliance") !== false) {
    $output = 'Appliance';
  }

  return $output;
}

function return_Pagination( $p_script, $p_current, $p_total, $p_count ) {

  $rp_output = '';
# determine the number of pages, $p_count entries per page.
  $rp_numpages = ceil($p_total / $p_count);

# limit the numbers being displayed to 10. So 1 to 10 or 400 to 410
# 
  $rp_page_start = $p_current - 5;
  $rp_page_end   = $p_current + 5;
  if ($rp_page_start <= 0) {
    $rp_page_start = 0;
    $rp_page_end   = 10;
  }
  if ($rp_page_end >= $rp_numpages) {
    $rp_page_end   = $rp_numpages;
    $rp_page_start = $rp_numpages - 11;
  }

  if ($rp_numpages > 1) {

    $rp_output .= "<table class=\"ui-styled-table\">\n";
    $rp_output .= "<tr>\n";
    $rp_output .= "  <td class=\"ui-widget-content button\">Page: ";

    $rp_output .= "&nbsp;<a href=\"#\" alt=\"Go to the first page\" onclick=\"show_file('" . $p_script . "?update=-1');\">First</a>";
    $rp_output .= "&nbsp;<a href=\"#\" alt=\"Jump back 100 pages\" onclick=\"show_file('" . $p_script . "?update=-1&pagination=" . ($p_current - 100) . "');\">Jump</a>";
    $rp_output .= "&nbsp;<a href=\"#\" alt=\"Go back one page\" onclick=\"show_file('" . $p_script . "?update=-1&pagination=" . ($p_current - 1) . "');\">Previous</a>";

    $comma = ' ';
    for ($i = $rp_page_start; $i < $rp_page_end; $i++) {
      if ($i == $p_current || ($i == $rp_page_start && $p_current == 0)) {
        $rp_output .= $comma . "<strong>" . ($i + 1) . "</strong>";
      } else {
        $rp_output .= $comma . "<a href=\"#\" onclick=\"show_file('" . $p_script . "?update=-1&pagination=" . $i . "');\">" . ($i + 1) . "</a>";
      }
        $comma = ", ";
    }

    $rp_output .= "&nbsp;<a href=\"#\" alt=\"Go forward one page\" onclick=\"show_file('" . $p_script . "?update=-1&pagination=" . ($p_current + 1) . "');\">Next</a>";
    $rp_output .= "&nbsp;<a href=\"#\" alt=\"Jump forward 100 pages\" onclick=\"show_file('" . $p_script . "?update=-1&pagination=" . ($p_current + 100) . "');\">Jump</a>";
    $rp_output .= "&nbsp;<a href=\"#\" alt=\"Go to the last page\" onclick=\"show_file('" . $p_script . "?update=-1&pagination=" . ($rp_numpages - 1) . "');\">Last</a>";

    $rp_output .= "&nbsp;</td>\n";
    $rp_output .= "</tr>\n";
    $rp_output .= "</table>\n";
  }

  return $rp_output;
}

# if the passed script name for this user isn't here yet, then the user hasn't viewed the help screen yet.
function show_Help( $p_script ) {

  $q_string  = "select help_id ";
  $q_string .= "from help ";
  $q_string .= "where help_user = " . $_SESSION['uid'] . " and help_screen = '" . $p_script . "' ";
  $q_help = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_help) == 0) {
    $q_string  = "insert ";
    $q_string .= "into help ";
    $q_string .= "set ";
    $q_string .= "help_user = " . $_SESSION['uid'] . ",";
    $q_string .= "help_screen = '" . $p_script . "' ";

    $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    return 1;
  } else {
    return 0;
  }
}

?>
