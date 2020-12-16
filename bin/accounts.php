#!/usr/local/bin/php
<?php
# Script: accounts.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# This script reads in a colon delimited passwd and group file from each live server
# which are parsed by this script and then imported into the syspwd and sysgrp tables.

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# need 2 command line options.
# p|g - Password file or Group file
# path and file name
#
# accounts.php p /usr/local/admin/servers/[servername]/passwd
# accounts.php g /usr/local/admin/servers/[servername]/group
# 
# ultimately the user name needs to be matched with the group as well
# check the third field for group name and associate that.
# 

  if ($argc == 1) {
    print "ERROR: missing file type flag.\n";
    print "\n";
    print "Usage:\n";
    print "accounts.php [p|g] [serverid] [path]/[filename]\n";
    print "- p - File is a passwd file\n";
    print "- g - File is a group file\n";
    print "- id - Server inventory id\n";
    print "- filename - The path and file name to be imported\n\n";
    exit(1);
  } else {
    if ($argc == 2) {
      print "ERROR: missing server id.\n";
      print "\n";
      print "Usage:\n";
      print "accounts.php [p|g] [serverid] [path]/[filename]\n";
      print "- p - File is a passwd file\n";
      print "- g - File is a group file\n";
      print "- id - Server inventory id\n";
      print "- filename - The path and file name to be imported\n\n";
      exit(1);
    } else {
      if ($argc == 3) {
        print "ERROR: missing file name.\n";
        print "\n";
        print "Usage:\n";
        print "accounts.php [p|g] [serverid] [path]/[filename]\n";
        print "- p - File is a passwd file\n";
        print "- g - File is a group file\n";
        print "- id - Server inventory id\n";
        print "- filename - The path and file name to be imported\n\n";
        exit(1);
      } else {

        $filetype = $argv[1];
        $serverid = $argv[2];
        $filename = $argv[3];

        if ($filetype != 'p' && $filetype != 'g') {
          print "ERROR: invalid file type flag.\n";
          print "\n";
          print "Usage:\n";
          print "accounts.php [p|g] [serverid] [path]/[filename]\n";
          print "- p - File is a passwd file\n";
          print "- g - File is a group file\n";
          print "- id - Server inventory id\n";
          print "- filename - The path and file name to be imported\n\n";
          exit(1);
        }

        if (file_exists($filename) === false) {
          print "ERROR: file name does not exist.\n";
          print "\n";
          print "Usage:\n";
          print "accounts.php [p|g] [serverid] [path]/[filename]\n";
          print "- p - File is a passwd file\n";
          print "- g - File is a group file\n";
          print "- id - Server inventory id\n";
          print "- filename - The path and file name to be imported\n\n";
          exit(1);
        }
      }
    }
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# file names are passed on the command line with a flag as to if it's a password or group file being imported.

  $date = date('Y-m-d');

  $q_string  = "select inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $serverid;
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  print "\n" . $filetype . $a_inventory['inv_name'] . "!";

  $file = fopen($filename, "r") or die;
  while(!feof($file)) {
    $process = trim(fgets($file));
    $value = split(":", $process);

    $value[4] = str_replace('"', "", $value[4]);

    if ($filetype == 'p') {
#+---------------+-----------+------+-----+------------+----------------+
#| Field         | Type      | Null | Key | Default    | Extra          |
#+---------------+-----------+------+-----+------------+----------------+
#| pwd_id        | int(10)   | NO   | PRI | NULL       | auto_increment | 
#| pwd_companyid | int(10)   | NO   |     | 0          |                | 
#| pwd_user      | char(30)  | NO   |     |            |                | 
#| pwd_uid       | int(10)   | NO   |     | 0          |                | 
#| pwd_gid       | int(10)   | NO   |     | 0          |                | 
#| pwd_gecos     | char(100) | NO   |     |            |                | 
#| pwd_home      | char(50)  | NO   |     |            |                | 
#| pwd_shell     | char(50)  | NO   |     |            |                | 
#| pwd_exclude   | int(10)   | NO   |     | 0          |                | 
#| pwd_update    | date      | NO   |     | 0000-00-00 |                | 
#+---------------+-----------+------+-----+------------+----------------+

# need to see if the user exists
      if ($value[0] != '') {
        $q_string  = "select pwd_id ";
        $q_string .= "from syspwd ";
        $q_string .= "where pwd_companyid = " . $serverid . " and pwd_user = \"" . $value[0] . "\" ";
        $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_syspwd = mysqli_fetch_array($q_syspwd);

        $q_string = 
          "pwd_companyid =   " . $serverid . "," . 
          "pwd_user      = \"" . $value[0] . "\"," . 
          "pwd_uid       =   " . $value[2] . "," . 
          "pwd_gid       =   " . $value[3] . "," . 
          "pwd_gecos     = \"" . $value[4] . "\"," . 
          "pwd_home      = \"" . $value[5] . "\"," . 
          "pwd_shell     = \"" . $value[6] . "\"," . 
          "pwd_update    = \"" . $date     . "\"";

        if (mysqli_num_rows($q_syspwd) == 0) {
          $query = "insert into syspwd set pwd_id = null," . $q_string;
          $status = 'i';
        } else {
          $query = "update syspwd set " . $q_string . " where pwd_id = " . $a_syspwd['pwd_id'];
          $status = 'u';
        }

        if ($debug == 'yes') {
          print $query . "\n";
        } else {
          if ($value[2] != '') {
            $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
            print $status;
          }
        }
      }

# need to get the user and group id.
# mem_uid = pwd_id
# mem_gid = grp_id

      if (isset($value[3]) && $value[3] != '') {
# get the pwd_id of the user
        $q_string  = "select pwd_id ";
        $q_string .= "from syspwd ";
        $q_string .= "where pwd_companyid = " . $serverid . " and pwd_user = \"" . $value[0] . "\" ";
        $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_syspwd = mysqli_fetch_array($q_syspwd);

# get the grp_id of the group
        $q_string  = "select grp_id ";
        $q_string .= "from sysgrp ";
        $q_string .= "where grp_companyid = " . $serverid . " and grp_gid = \"" . $value[3] . "\" ";
        $q_sysgrp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_sysgrp = mysqli_fetch_array($q_sysgrp);

        if (mysqli_num_rows($q_syspwd) > 0 && mysqli_num_rows($q_sysgrp) > 0) {
# now see if they're in the members table
          $q_string  = "select mem_id ";
          $q_string .= "from sysgrp_members ";
          $q_string .= "where mem_uid = " . $a_syspwd['pwd_id'] . " and mem_gid = " . $a_sysgrp['grp_id'] . " ";
          $q_sysgrp_members = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          $a_sysgrp_members = mysqli_fetch_array($q_sysgrp_members);

# set up the string
          $q_string = 
            "mem_uid    =   " . $a_syspwd['pwd_id'] . "," . 
            "mem_gid    =   " . $a_sysgrp['grp_id'] . "," . 
            "mem_update = \"" . $date . "\"";

          if (mysqli_num_rows($q_sysgrp_members) == 0) {
            $query = "insert into sysgrp_members set mem_id = null," . $q_string;
            $status = "M";
          } else {
            $query = "update sysgrp_members set " . $q_string . " where mem_id = " . $a_sysgrp_members['mem_id'];
            $status = "m";
          }

          if ($debug == 'yes') {
            print $query . "\n";
          } else {
            $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
            print $status;
          }
        }
      }
    }

    if ($filetype == 'g') {
#+---------------+----------+------+-----+------------+----------------+
#| Field         | Type     | Null | Key | Default    | Extra          |
#+---------------+----------+------+-----+------------+----------------+
#| grp_id        | int(10)  | NO   | PRI | NULL       | auto_increment | 
#| grp_companyid | int(10)  | NO   |     | 0          |                | 
#| grp_name      | char(30) | NO   |     |            |                | 
#| grp_gid       | double   | NO   |     | 0          |                | 
#| grp_update    | date     | NO   |     | 0000-00-00 |                | 
#+---------------+----------+------+-----+------------+----------------+

# need to see if the group exists
      if ($value[0] != '' && $value[2] != '') {
        $q_string  = "select grp_id ";
        $q_string .= "from sysgrp ";
        $q_string .= "where grp_companyid = " . $serverid . " and grp_name = \"" . $value[0] . "\" ";
        $q_sysgrp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_sysgrp = mysqli_fetch_array($q_sysgrp);

        $q_string = 
          "grp_companyid =   " . $serverid . "," . 
          "grp_name      = \"" . $value[0] . "\"," . 
          "grp_gid       =   " . $value[2] . "," . 
          "grp_update    = \"" . $date     . "\"";

        if (mysqli_num_rows($q_sysgrp) == 0) {
          $query = "insert into sysgrp set grp_id = null," . $q_string;
          $status = 'I';
        } else {
          $query = "update sysgrp set " . $q_string . " where grp_id = " . $a_sysgrp['grp_id'];
          $status = 'U';
        }

        if ($debug == 'yes') {
          print $query . "\n";
        } else {
          print $status;
          $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        }

# loop through the user list if it exists and add a link in the sysgrp_members
# mem_uid = pwd_id
# mem_gid = grp_id
# groupname:x:gid:users

        if ($value[3] != '') {
          $users = split(",", $value[3]);

          foreach ($users as &$username) {
# get the pwd_id of the user
            $q_string  = "select pwd_id ";
            $q_string .= "from syspwd ";
            $q_string .= "where pwd_companyid = " . $serverid . " and pwd_user = \"" . $username . "\" ";
            $q_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_syspwd = mysqli_fetch_array($q_syspwd);

# get the grp_id of the group
            $q_string  = "select grp_id ";
            $q_string .= "from sysgrp ";
            $q_string .= "where grp_companyid = " . $serverid . " and grp_name = \"" . $value[0] . "\" ";
            $q_sysgrp = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
            $a_sysgrp = mysqli_fetch_array($q_sysgrp);

            if (mysqli_num_rows($q_syspwd) > 0 && mysqli_num_rows($q_sysgrp) > 0) {
# now see if they're in the members table
              $q_string  = "select mem_id ";
              $q_string .= "from sysgrp_members ";
              $q_string .= "where mem_uid = " . $a_syspwd['pwd_id'] . " and mem_gid = " . $a_sysgrp['grp_id'] . " ";
              $q_sysgrp_members = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
              $a_sysgrp_members = mysqli_fetch_array($q_sysgrp_members);

# set up the string
              $q_string = 
                "mem_uid    =   " . $a_syspwd['pwd_id'] . "," . 
                "mem_gid    =   " . $a_sysgrp['grp_id'] . "," . 
                "mem_update = \"" . $date               . "\"";

              if (mysqli_num_rows($q_sysgrp_members) == 0) {
                $query = "insert into sysgrp_members set mem_id = null," . $q_string;
                $status = "M";
              } else {
                $query = "update sysgrp_members set " . $q_string . " where mem_id = " . $a_sysgrp_members['mem_id'];
                $status = "m";
              }

              if ($debug == 'yes') {
                print $query . "\n";
              } else {
                $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
                print $status;
              }
            }
          }
        }
      }
    }
  }

  mysqli_close($db);

?>
