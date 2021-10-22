#!/usr/local/bin/php
<?php
# Script: package.import.php
# By: Carl Schelin
# This script reads the output of the chkserver.output file and stores the information in a table

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# import doesn't change the entry, just adds if it's not there already.

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the import file name.\n";
    exit(1);
  } else {
    $server = $argv[1];
  }

  $date = date('Y-m-d');

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server id from the inventory table
  $q_string  = "select inv_id,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where inv_name = \"" . $server . "\" and inv_status = 0 and sw_type = 'OS' ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n");
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {
    $inv_id = $a_inventory['inv_id'];
    $sw_software = $a_inventory['sw_software'];
  }

# return the basic system type; Linux, SunOS, HP-UX, etc
  $uname = return_System($db, $inv_id);

  if ($inv_id == '') {
    print "Error: No server found (" . $server . ")\n";
    exit(1);
  }

  print $server . ":";

  $servername = '/usr/local/admin/servers/' . $server . "/chkpackages.output";

  $file = fopen($servername, "r") or die;
  while(!feof($file)) {

# solaris is a multiline capture
# looking for package: PKGINST:  SUNWxorg-devel-docs
# and version:         VERSION:  6.8.2.5.10.0110,REV=0.2005.06.21
# combine into a single line SUNWxorg-devel-docs 6.8.2.5.10.0110,REV=0.2005.06.21
    if ($uname == "SunOS") {

      $process = trim(fgets($file));
      $value = explode(" ", $process);

      if ($value[0] == 'PKGINST:') {
        $package = $value[2] . " ";
      }


      if ($value[0] == 'VERSION:') {
        $package .= $value[2];

# locate the package. If it exists, update the date only. If it doesn't exist, create a new entry
        $q_string  = "select pkg_id ";
        $q_string .= "from packages ";
        $q_string .= "where pkg_inv_id = " . $inv_id . " and pkg_name = \"" . $package . "\" ";
        $q_packages = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_packages) == 0) {

          $q_string  = "insert into packages set ";
          $q_string .= "pkg_inv_id   =   " . $inv_id       . ",";
          $q_string .= "pkg_name     = \"" . $package      . "\",";
          $q_string .= "pkg_update   = \"" . $date         . "\",";
          $q_string .= "pkg_usr_id   =   " . "1"           . ",";
          $q_string .= "pkg_grp_id   =   " . "1"           . ",";
          $q_string .= "pkg_os       = \"" . $sw_software  . "\"";

          $status = "i";
        } else {
          $a_packages = mysqli_fetch_array($q_packages);

          $q_string  = "update packages set ";
          $q_string .= "pkg_update = \"" . $date . "\" ";
          $q_string .= "where pkg_id = " . $a_packages['pkg_id'] . " ";

          $status = "u";
        }

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          print $status;
          $q_result = mysqli_query($db, $q_string) or die($q_string . ":  " . mysqli_error($db));
        }

        $package = '';
      }
    } else {

      if ($uname == "Linux" || $uname == "HP-UX") {

        $process = trim(fgets($file));

        $q_string  = "select pkg_id ";
        $q_string .= "from packages ";
        $q_string .= "where pkg_inv_id = " . $inv_id . " and pkg_name = \"" . $process . "\" ";
        $q_packages = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_packages) == 0) {
          $q_string  = "insert into packages set ";
          $q_string .= "pkg_inv_id   =   " . $inv_id       . ",";
          $q_string .= "pkg_name     = \"" . $process      . "\",";
          $q_string .= "pkg_update   = \"" . $date         . "\",";
          $q_string .= "pkg_usr_id   =   " . "1"           . ",";
          $q_string .= "pkg_grp_id   =   " . "1"           . ",";
          $q_string .= "pkg_os       = \"" . $sw_software  . "\"";

          $status = "i";
        } else {
          $a_packages = mysqli_fetch_array($q_packages);

          $q_string  = "update packages set ";
          $q_string .= "pkg_update = \"" . $date . "\" ";
          $q_string .= "where pkg_id = " . $a_packages['pkg_id'] . " ";

          $status = "u";
        }

        if ($debug == 'yes') {
          print $q_string . "\n";
        } else {
          print $status;
          $q_result = mysqli_query($db, $q_string) or die($q_string . ":  " . mysqli_error($db));
        }
      } else {
        $process = trim(fgets($file));
        print "i";
      }
    }
  }

  print "\n";

  mysqli_close($db);

?>
