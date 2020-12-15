<?php

function setstatus($rsdp,$completed,$step) {
  $q_status =
    "st_rsdp      =   " . $rsdp            . "," .
    "st_completed =   " . $completed       . "," .
    "st_user      =   " . $_SESSION['uid'] . "," .
    "st_step      =   " . $step;

  $q_string  = "select st_id ";
  $q_string .= "from rsdp_status ";
  $q_string .= "where st_rsdp = " . $rsdp . " and st_step = " . $step;
  $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

  if (mysqli_num_rows($q_rsdp_status) == 0) {
    $query = "insert into rsdp_status set st_id = NULL," . $q_status;
  } else {
    $query = "update rsdp_status set " . $q_status . " where st_id = " . $a_rsdp_status['st_id'];
  }

  mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
}

# for RSDP; the e-mail function.

function generateEmail($rsdp, $text1, $text2, $text3, $who, $group) {

  include('settings.php');

  $q_string  = "select " . $who . ",rsdp_requestor,rsdp_project,rsdp_function ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $rsdp;
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $q_string  = "select usr_email,usr_last,usr_first ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $a_rsdp_server['rsdp_requestor'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_requestor = mysqli_fetch_array($q_users);

  $q_string  = "select usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);
  $myuserid = $a_users['usr_email'];

  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $rsdp;
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  $q_string  = "select prj_name,prj_code ";
  $q_string .= "from projects ";
  $q_string .= "where prj_id = " . $a_rsdp_server['rsdp_project'];
  $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_projects = mysqli_fetch_array($q_projects);

  $headers  = "From: RSDP <root@" . $Sitehttp . ">\r\n";
  if ( $Siteenv == "PROD" ) {
    $headers .= "CC: " . $a_requestor['usr_email'] . "," . $Siteadmins . "\r\n";
  }
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
  $headers .= "Reply-To: " . $a_requestor['usr_email'] . "\r\n";
  if ($_SESSION['disposition']) {
    $headers .= "Disposition-Notification-To: " . $a_requestor['usr_email'] . "\r\n";
  }

  $subject = $text3 . " (" . $a_projects['prj_code'] . ")";

  $body  = $text1;

  $body .= $text2;

# get the user for the next step in the process (physical).
# if not set, get the e-mail address for the identified platforms group
# if no group e-mail set, get the e-mail address for the person opening the request.
  $email = '';

  if ($a_rsdp_server[$who] == '' || $a_rsdp_server[$who] == 0) {
    $q_string  = "select grp_email ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $group;
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    if ($a_groups['grp_email'] == '') {
      $body .= "<p>A group e-mail doesn't exist for the selected group and no POC was identified. ";
      $body .= "You'll need to contact someone in the designated platforms group outside of RSDP.</p>\n";

      $email = $myuserid;
    } else {
      $email = $a_groups['grp_email'];
      $body .= "<p>A POC for your group was not selected for this work.</p>\n";
    }

  } else {
    $q_string  = "select usr_email ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_rsdp_server[$who];
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_users = mysqli_fetch_array($q_users);

    $email = $a_users['usr_email'];
  }

  $body .= "<html>\n";
  $body .= "<head>\n";
  $body .= "</head>\n";
  $body .= "<style type=\"text/css\" title=\"currentStyle\" media=\"screen\">\n";
  $body .= "table { \n";
  $body .= "  width: 80%;\n";
  $body .= "  margin-left: 50px;\n";
  $body .= "  margin-right: 50px;\n";
  $body .= "  margin-bottom: 5px;\n";
  $body .= "  margin-top: 5px;\n";
  $body .= "  border-collapse: collapse;\n";
  $body .= "}\n";
  $body .= "th {\n";
  $body .= "  background-color: #99ccff;\n";
  $body .= "  border: 1px solid #000000;\n";
  $body .= "  font-size: 75%;\n";
  $body .= "}\n";
  $body .= "td {\n";
  $body .= "  background-color: #ffffcc;\n";
  $body .= "  border: 1px solid #000000;\n";
  $body .= "  font-size: 75%;\n";
  $body .= "  font-family: sans-serif;\n";
  $body .= "  text-align: left;\n";
  $body .= "  vertical-align: baseline;\n";
  $body .= "}\n";
  $body .= "</style>\n";
  $body .= "<body>\n";

# these are the individual groups
  switch ($group) {
# Virtualization
    case  4: $body .= request_Header($rsdp);
             $body .= virtual_Request($rsdp);
             break;
# SAN
    case  9: $body .= request_Header($rsdp);
             $body .= storage_Request($rsdp);
             break;
# Networking
    case 12: $body .= request_Header($rsdp);
             $body .= network_Request($rsdp);
             break;
    default: $body .= default_Request($rsdp);
             $body .= request_Header($rsdp);
             break;
  }


  $body .= "</body>\n";
  $body .= "</html>\n";

  $body .= "<p>This message is from the <a href=\"" . $RSDProot . "/index.php\">Rapid Server Deployment Process</a> application.\n";
  $body .= "<br>This mail box is not monitored, replies will be ignored.</p>\n\n";

  if ($email == '') {
    print "alert('No e-mail address can be located so no person or group will be notified about this server request.')";
  } else {
    if ($Siteenv == 'PROD') {
      $mailto = $email;
    } else {
      if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
        $mailto = $Sitedev . "," . $_SESSION['email'];
      } else {
        $mailto = $Sitedev;
      }
    }
    mail($mailto, $subject, $body, $headers);
  }
}

function default_Request( $rsdp ) {
  $q_string  = "select rsdp_function,rsdp_project ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $rsdp;
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);


  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $rsdp;
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);
  $output  = "<p>Server: " . $a_rsdp_osteam['os_sysname'] . "\n";

  $output .= "<br>Function: " . $a_rsdp_server['rsdp_function'] . "\n";

  $q_string  = "select prj_name ";
  $q_string .= "from projects ";
  $q_string .= "where prj_id = " . $a_rsdp_server['rsdp_project'];
  $q_projects = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_projects = mysqli_fetch_array($q_projects);
  $output .= "<br>Project: " . $a_projects['prj_name'] . "</p>\n";

  return $output;
}

function request_Header( $rsdp ) {

  $q_string  = "select usr_last,usr_first,dep_name,bus_name,loc_name,rsdp_location,";
  $q_string .= "rsdp_completion,prj_name,prj_code,rsdp_application,grp_name,svc_name ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "left join department on department.dep_id = users.usr_deptname ";
  $q_string .= "left join business_unit on business_unit.bus_id = department.dep_unit ";
  $q_string .= "left join groups on groups.grp_id = rsdp_server.rsdp_platform ";
  $q_string .= "left join service on service.svc_id = rsdp_server.rsdp_service ";
  $q_string .= "left join projects on projects.prj_id = rsdp_server.rsdp_project ";
  $q_string .= "left join locations on locations.loc_id = rsdp_server.rsdp_location ";
  $q_string .= "where rsdp_id = " . $rsdp;
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $q_string  = "select grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $a_rsdp_server['rsdp_application'];
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_groups = mysqli_fetch_array($q_groups);

  $q_string  = "select os_sysname,operatingsystem.os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "left join operatingsystem on operatingsystem.os_id = rsdp_osteam.os_software ";
  $q_string .= "where os_rsdp = " . $rsdp;
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  $output  = "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"2\">Requestor Information</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Requestor's Name</strong>: "    . $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Business Unit/Group</strong>: " . $a_rsdp_server['bus_name'] . "/"  . $a_rsdp_server['dep_name']  . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"3\">Support Information</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>System Administrator(s)</strong>: "      . $a_rsdp_server['grp_name'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Application Administrator(s)</strong>: " . $a_groups['grp_name']      . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Service Class Definition</strong>: "     . $a_rsdp_server['svc_name'] . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"2\">Project Information</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Project Name</strong>: "   . $a_rsdp_server['prj_name'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Project number</strong>: " . $a_rsdp_server['prj_code'] . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"3\">System Information</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Hostname</strong>: "    . $a_rsdp_osteam['os_sysname']  . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Host OS</strong>: "     . $a_rsdp_osteam['os_software'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Data Center</strong>: " . $a_rsdp_server['loc_name']    . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  return $output;
}


function request_Server( $rsdp ) {

  $q_string  = "select rsdp_processors,rsdp_memory,rsdp_ossize,rsdp_function ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $rsdp;
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $output  = "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"4\">System Information</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select os_sysname,os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $rsdp;
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  $q_string  = "select os_software ";
  $q_string .= "from operatingsystem ";
  $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'];
  $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);

  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Hostname</strong>: " . $a_rsdp_osteam['os_sysname'] . "<input type=\"hidden\" name=\"rsdp_virtual\" id=\"rsdp_virtual\" value=\"" . rsdp_Virtual($rsdp) . "\"></td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Function</strong>: " . $a_rsdp_server['rsdp_function'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Operating System</strong>: " . $a_operatingsystem['os_software'] . "</td>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong># CPUs</strong>: "          . $a_rsdp_server['rsdp_processors'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>RAM</strong>: "             . $a_rsdp_server['rsdp_memory']     . " GB</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>OS Disk Storage</strong>: " . $a_rsdp_server['rsdp_ossize']     . " GB</td>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";

  $output .= "<td class=\"ui-widget-content\" colspan=\"4\"><strong>Name Resolution</strong>: " . $a_rsdp_osteam['os_sysname'];
  $ip = gethostbyname($a_rsdp_osteam['os_sysname']);
  if ($ip != $a_rsdp_osteam['os_sysname']) {
    $host = gethostbyaddr($ip);
    $output .= " was found in DNS as " . $host . " and reverse lookup is " . $ip;
  } else {
    $output .= " was not found in DNS.";
  }
  $output .= "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $q_string  = "select pf_special,mod_vendor,mod_name,part_name,volt_text,mod_plugs,pf_redundant,plug_text,mod_draw,mod_start,pf_rack,pf_row,pf_unit,mod_size ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
  $q_string .= "left join int_volts on int_volts.volt_id = models.mod_volts ";
  $q_string .= "left join int_plugtype on int_plugtype.plug_id = models.mod_plugtype ";
  $q_string .= "left join parts on parts.part_id = models.mod_type ";
  $q_string .= "where pf_rsdp = " . $rsdp;
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

  if (strlen($a_rsdp_platform['pf_special']) == 0) {
    $special = "No additional instructions";
  } else {
    $special = $a_rsdp_platform['pf_special'];
  }

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"5\">Equipment Information</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Manufacturer</strong>: " . $a_rsdp_platform['mod_vendor'] . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Device Type</strong>: "  . $a_rsdp_platform['part_name']  . "</td>\n";
  $output .= "  <td class=\"ui-widget-content\"><strong>Model</strong>: "        . $a_rsdp_platform['mod_name']   . "</td>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content\" colspan=\"3\"><strong>Special Instructions</strong>: " . $special . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  if (rsdp_Virtual($rsdp) == 0) {
    if ($a_rsdp_platform['pf_redundant']) {
      $redundant = 'Yes';
    } else {
      $redundant = 'No';
    }

    $output .= "<table class=\"ui-styled-table\">\n";
    $output .= "<tr>\n";
    $output .= "  <th class=\"ui-state-default\" colspan=\"3\">Power Requirements</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Power</strong>: "        . $a_rsdp_platform['volt_text']      . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Power Draw</strong>: "   . $a_rsdp_platform['mod_draw']  . " Amps</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Startup Draw</strong>: " . $a_rsdp_platform['mod_start'] . " Amps</td>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong># of Power Supplies</strong>: "         . $a_rsdp_platform['mod_plugs'] . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Redundant Power Requirement</strong>: " . $redundant                    . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Plug Type</strong>: "                   . $a_rsdp_platform['plug_text'] . "</td>\n";
    $output .= "</tr>\n";
    $output .= "</table>\n";

    $output .= "<table class=\"ui-styled-table\">\n";
    $output .= "<tr>\n";
    $output .= "  <th class=\"ui-state-default\" colspan=\"4\">Location Information</th>\n";
    $output .= "</tr>\n";

    if ($a_rsdp_platform['pf_row'] == '') {
      $a_rsdp_platform['pf_row'] = 'No Preference';
    }
    if ($a_rsdp_platform['pf_rack'] == '') {
      $a_rsdp_platform['pf_rack'] = 'No Preference';
    }

    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Requested Row</strong>: "             . $a_rsdp_platform['pf_row']   . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Requested Rack</strong>: "            . $a_rsdp_platform['pf_rack']  . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong>Requested Low Rack Unit #</strong>: " . $a_rsdp_platform['pf_unit']  . "</td>\n";
    $output .= "  <td class=\"ui-widget-content\"><strong># of RU's</strong>: "                 . $a_rsdp_platform['mod_size'] . "</td>\n";
    $output .= "</tr>\n";
    $output .= "</table>\n";
  }

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"2\">Filesystem Listing</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\">Volume/Mount Point</th>\n";
  $output .= "  <th class=\"ui-state-default\">Size</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $rsdp;
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
    while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_filesystem['fs_volume'] . "</td>\n";
      $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_filesystem['fs_size'] . " GB</td>\n";
      $output .= "</tr>\n";
    }
  } else {
    $output .= "<tr>\n";
    $output .= "<td class=\"ui-widget-content\" colspan=\"2\">No additional disk storage required</td>";
    $output .= "</tr>\n";
  }
  $output .= "</table>\n";

  $output .= "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\" colspan=\"11\">Interface Listing</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <th class=\"ui-state-default\">Interface Name</th>\n";
  $output .= "  <th class=\"ui-state-default\">Type</th>\n";
  $output .= "  <th class=\"ui-state-default\">Logical Interface</th>\n";
  $output .= "  <th class=\"ui-state-default\">MAC Address</th>\n";
  $output .= "  <th class=\"ui-state-default\">IP Address</th>\n";
  $output .= "  <th class=\"ui-state-default\">Zone</th>\n";
  $output .= "  <th class=\"ui-state-default\">Gateway</th>\n";
  $output .= "  <th class=\"ui-state-default\">VLan</th>\n";
  if (rsdp_Virtual($rsdp) == 0) {
    $output .= "  <th class=\"ui-state-default\">Physical Port</th>\n";
    $output .= "  <th class=\"ui-state-default\">Media</th>\n";
    $output .= "  <th class=\"ui-state-default\">Switch</th>\n";
    $output .= "  <th class=\"ui-state-default\">Port</th>\n";
  }
  $output .= "</tr>\n";

  $q_string  = "select if_id,if_name,if_interface,if_sysport,if_mac,if_ip,if_mask,zone_name,if_gate,if_switch,if_port,itp_acronym,if_virtual,med_text,if_vlan ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
  $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
  $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
  $q_string .= "where if_rsdp = " . $rsdp . " and if_if_id = 0 ";
  $q_string .= "order by if_interface";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_interface) > 0) {
    while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

      $class = " class=\"ui-widget-content\"";
      $virtual = '';
      if ($a_rsdp_interface['if_virtual']) {
        $class = " class=\"ui-state-highlight\"";
        $virtual = ' (v)';
      }

      if ($a_rsdp_interface['if_ip'] == '') {
        $ipaddress = '';
      } else {
        $ipaddress = $a_rsdp_interface['if_ip'] . "/" . $a_rsdp_interface['if_mask'];
      }

      $output .= "<tr>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_name'] . $virtual . "</td>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['itp_acronym']      . "</td>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_interface']     . "</td>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_mac']           . "</td>\n";
      $output .= "  <td" . $class . ">" . $ipaddress                            . "</td>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['zone_name']        . "</td>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_gate']          . "</td>\n";
      $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_vlan']          . "</td>\n";
      if (rsdp_Virtual($rsdp) == 0) {
        $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_sysport']     . "</td>\n";
        $output .= "  <td" . $class . ">" . $a_rsdp_interface['med_text']         . "</td>\n";
        $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_switch']        . "</td>\n";
        $output .= "  <td" . $class . ">" . $a_rsdp_interface['if_port']          . "</td>\n";
      }
      $output .= "</tr>\n";

      $q_string  = "select if_name,if_interface,if_sysport,if_mac,if_ip,if_mask,zone_name,if_gate,if_switch,if_port,itp_acronym,if_virtual,med_text,if_vlan ";
      $q_string .= "from rsdp_interface ";
      $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
      $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
      $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
      $q_string .= "where if_rsdp = " . $rsdp . " and if_if_id = " . $a_rsdp_interface['if_id'] . " ";
      $q_string .= "order by if_interface";
      $q_rsdp_child = mysqli_query($db, $q_string);
      if (mysqli_num_rows($q_rsdp_child) > 0) {
        while ($a_rsdp_child = mysqli_fetch_array($q_rsdp_child)) {

          $class = " class=\"ui-widget-content\"";
          $virtual = '';
          if ($a_rsdp_child['if_virtual']) {
            $class = " class=\"ui-state-highlight\"";
            $virtual = ' (v)';
          }

          if ($a_rsdp_child['if_ip'] == '') {
            $ipaddress = '';
          } else {
            $ipaddress = $a_rsdp_child['if_ip'] . "/" . $a_rsdp_child['if_mask'];
          }

          $output .= "<tr>\n";
          $output .= "  <td" . $class . ">&gt; " . $a_rsdp_child['if_name'] . $virtual . "</td>\n";
          $output .= "  <td" . $class . ">" . $a_rsdp_child['itp_acronym']      . "</td>\n";
          $output .= "  <td" . $class . ">" . $a_rsdp_child['if_interface']     . "</td>\n";
          $output .= "  <td" . $class . ">" . $a_rsdp_child['if_mac']           . "</td>\n";
          $output .= "  <td" . $class . ">" . $ipaddress                        . "</td>\n";
          $output .= "  <td" . $class . ">" . $a_rsdp_child['zone_name']        . "</td>\n";
          $output .= "  <td" . $class . ">" . $a_rsdp_child['if_gate']          . "</td>\n";
          $output .= "  <td" . $class . ">" . $a_rsdp_child['if_vlan']          . "</td>\n";
          if (rsdp_Virtual($rsdp) == 0) {
            $output .= "  <td" . $class . ">" . $a_rsdp_child['if_sysport']       . "</td>\n";
            $output .= "  <td" . $class . ">" . $a_rsdp_child['med_text']         . "</td>\n";
            $output .= "  <td" . $class . ">" . $a_rsdp_child['if_switch']        . "</td>\n";
            $output .= "  <td" . $class . ">" . $a_rsdp_child['if_port']          . "</td>\n";
          }
          $output .= "</tr>\n";
        }
      }
    }
  } else {
    $output .= "<tr>\n";
    $output .= "  <td class=\"ui-widget-content\" colspan=\"11\">No records found.</td>\n";
    $output .= "</tr>\n";
  }
  $output .= "</table>\n";

  if (rsdp_Virtual($rsdp) == 0) {
    $output .= "<table class=\"ui-styled-table\">\n";
    $output .= "<tr>\n";
    $output .= "  <th class=\"ui-state-default\" colspan=\"5\">SAN Listing</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .= "  <th class=\"ui-state-default\">System</th>\n";
    $output .= "  <th class=\"ui-state-default\">Switch</th>\n";
    $output .= "  <th class=\"ui-state-default\">Port</th>\n";
    $output .= "  <th class=\"ui-state-default\">Media</th>\n";
    $output .= "  <th class=\"ui-state-default\">WWNN Zone</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select san_id,san_sysport,san_switch,san_port,med_text,san_wwnnzone ";
    $q_string .= "from rsdp_san ";
    $q_string .= "left join int_media on int_media.med_id = rsdp_san.san_media ";
    $q_string .= "where san_rsdp = " . $rsdp . " ";
    $q_string .= "order by san_sysport";
    $q_rsdp_san = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_rsdp_san) > 0) {
      while ($a_rsdp_san = mysqli_fetch_array($q_rsdp_san)) {
        $output .= "<tr>\n";
        $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_sysport']  . "</td>\n";
        $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_switch']   . "</td>\n";
        $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_port']     . "</td>\n";
        $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['med_text']     . "</td>\n";
        $output .= "  <td class=\"ui-widget-content\">" . $a_rsdp_san['san_wwnnzone'] . "</td>\n";
        $output .= "</tr>\n";
      }
    } else {
      $output .= "<tr>\n";
      $output .= "  <td class=\"ui-widget-content\" colspan=\"5\">No records found.</td>\n";
      $output .= "</tr>\n";
    }

    $output .= "</table>\n";
  }

  return $output;
}

# Network request information; looking for interface information.
function network_Request( $rsdp ) {

  $output  = "</div>\n";

  $output .= "<div id=\"main\">\n";
  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th colspan=\"9\">Interface Details</th>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <th>Name</th>\n";
  $output .= "  <th>Slot/Port</th>\n";
  $output .= "  <th>Face</th>\n";
  $output .= "  <th>Zone</th>\n";
  $output .= "  <th>Media</th>\n";
  $output .= "  <th>Speed</th>\n";
  $output .= "  <th>Duplex</th>\n";
  $output .= "  <th>Redundant</th>\n";
  $output .= "  <th>Type</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select if_id,if_name,if_sysport,if_interface,zone_name,med_text,spd_text,dup_text,red_text,itp_acronym,if_description ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
  $q_string .= "left join int_media on int_media.med_id = rsdp_interface.if_media ";
  $q_string .= "left join inttype on inttype.itp_id = rsdp_interface.if_type ";
  $q_string .= "left join int_speed on int_speed.spd_id = rsdp_interface.if_speed ";
  $q_string .= "left join int_duplex on int_duplex.dup_id = rsdp_interface.if_duplex ";
  $q_string .= "left join int_redundancy on int_redundancy.red_id = rsdp_interface.if_redundant ";
  $q_string .= "where if_rsdp = " . $rsdp . " ";
  $q_string .= "order by if_name";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

    $output .= "<tr>\n";
    $output .= "  <td>" . $a_rsdp_interface['if_name']      . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['if_sysport']   . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['if_interface'] . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['zone_name']    . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['med_text']     . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['spd_text']     . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['dup_text']     . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['red_text']     . "</td>\n";
    $output .= "  <td>" . $a_rsdp_interface['itp_acronym']  . "</td>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .= "  <td colspan=\"9\"><strong>Interface Description</strong>: " . $a_rsdp_interface['if_description'] . "</td>\n";
    $output .= "</tr>\n";
  }
  $output .= "</table>\n";
  $output .= "</div>\n";

  return $output;

}

function storage_Request($rsdp) {
# create and return the the san task for display on the rsdp3 page or in the email to the san team

  $output  = "  <table>\n";
  $output .= "  <tr>\n";
  $output .= "    <th colspan=\"4\">Location Information</th>\n";
  $output .= "  </tr>\n";

  $q_string  = "select pf_row,pf_rack,pf_unit,mod_size ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
  $q_string .= "where pf_rsdp = " . $rsdp;
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

  $output .= "<tr>\n";
  $output .= "  <td><strong>Requested Row</strong>: "             . $a_rsdp_platform['pf_row']   . "</td>\n";
  $output .= "  <td><strong>Requested Rack</strong>: "            . $a_rsdp_platform['pf_rack']  . "</td>\n";
  $output .= "  <td><strong>Requested Low Rack Unit #</strong>: " . $a_rsdp_platform['pf_unit']  . "</td>\n";
  $output .= "  <td><strong># of RU's</strong>: "       . $a_rsdp_platform['mod_size'] . "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";
  $output .= "</div>\n";


  $output .= "<div id=\"main\">\n";
  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th colspan=\"6\">Filesystem Information</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select fs_volume,fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $rsdp;
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {

    $output .= "<tr>\n";
    $output .= "  <td><strong>Mountpoint</strong>: "     . $a_rsdp_filesystem['fs_volume']         . "</td>\n";
    $output .= "  <td><strong>Volume Size</strong>: "    . $a_rsdp_filesystem['fs_size']   . " GB" . "</td>\n";
    $output .= "  <td><strong>Volume Purpose</strong>: " . "???"                                   . "</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>\n";
  $output .= "</div>\n";

  return $output;
}


function virtual_Request($rsdp) {

  $formVars['rsdp'] = $rsdp;

  $q_string  = "select rsdp_processors,rsdp_memory,rsdp_ossize,rsdp_function ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $rsdp;
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $output  = "</div>\n";
  $output .= "<div id=\"main\">\n";

  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th colspan=\"4\">Virtual Machine Server Information</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select os_sysname,os_software ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $rsdp;
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

  $q_string  = "select os_software ";
  $q_string .= "from operatingsystem ";
  $q_string .= "where os_id = " . $a_rsdp_osteam['os_software'];
  $q_operatingsystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);

  $output .= "<tr>\n";
  $output .= "  <td colspan=\"2\"><strong>Hostname</strong>: " . $a_rsdp_osteam['os_sysname'] . "</td>\n";
  $output .= "  <td colspan=\"2\"><strong>Function</strong>: " . $a_rsdp_server['rsdp_function'] . "</td>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";
  $output .= "  <td><strong># Virtual CPUs</strong>: "   . $a_rsdp_server['rsdp_processors'] . "</td>\n";
  $output .= "  <td><strong>RAM</strong>: "              . $a_rsdp_server['rsdp_memory']     . "</td>\n";
  $output .= "  <td><strong>Operating System</strong>: " . $a_operatingsystem['os_software'] . "</td>\n";
  $output .= "  <td><strong>OS Disk Storage</strong>: "  . $a_rsdp_server['rsdp_ossize']     . " GB</td>\n";
  $output .= "</tr>\n";
  $output .= "<tr>\n";

  $output .= "<td colspan=\"4\">" . $a_rsdp_osteam['os_sysname'];
  $ip = gethostbyname($a_rsdp_osteam['os_sysname']);
  if ($ip != $a_rsdp_osteam['os_sysname']) {
    $host = gethostbyaddr($ip);
    $output .= " was found in DNS as " . $host . " and reverse lookup is " . $ip;
  } else {
    $output .= " was not found in DNS.";
  }
  $output .= "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th>Special Instructions</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select pf_special ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "where pf_rsdp = " . $rsdp;
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);

  $output .= "<tr>\n";
  $output .= "  <td>";
  if (strlen($a_rsdp_platform['pf_special']) == 0) {
    $output .= "No additional instructions";
  } else {
    $output .= $a_rsdp_platform['pf_special'];
  }
  $output .= "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n";

  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th>Filesystem Details</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select fs_size ";
  $q_string .= "from rsdp_filesystem ";
  $q_string .= "where fs_rsdp = " . $rsdp;
  $q_rsdp_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  if (mysqli_num_rows($q_rsdp_filesystem) > 0) {
    while ($a_rsdp_filesystem = mysqli_fetch_array($q_rsdp_filesystem)) {
      $output .= "<tr>\n";
      $output .= "  <td><strong>Additional Disk Storage</strong>: " . $a_rsdp_filesystem['fs_size'] . "</li>";
      $output .= "</tr>\n";
    }
  } else {
    $output .= "<tr>\n";
    $output .= "<td>No additional disk storage required</td>";
    $output .= "</tr>\n";
  }

  $output .= "</table>\n";

  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th colspan=\"5\">Interface Details</th>\n";
  $output .= "</tr>\n";

  $q_string  = "select if_ip,if_interface,if_mask,if_gate,if_vlan ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "where if_rsdp = " . $formVars['rsdp'] . " ";
  $q_string .= "order by if_interface";
  $q_rsdp_interface = mysqli_query($db, $q_string);
  while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {
    $output .= "<tr>\n";
    $output .= "  <td><strong>IP Address</strong>: " . $a_rsdp_interface['if_ip'] . "</td>\n";
    $output .= "  <td><strong>Interface</strong>: " . $a_rsdp_interface['if_interface'] . "</td>\n";
    $output .= "  <td><strong>Subnet</strong>: " . $a_rsdp_interface['if_mask'] . "</td>\n";
    $output .= "  <td><strong>Default Gateway</strong>: " . $a_rsdp_interface['if_gate'] . "</td>\n";
    $output .= "  <td><strong>VLAN</strong>: " . $a_rsdp_interface['if_vlan'] . "</td>\n";
    $output .= "</tr>\n";
  }

  $output .= "</table>\n";

  $output .= "</div>\n";

  return $output;
}

function submit_RSDP( $rsdp, $task, $script, $poc, $group, $grpnum ) {

  $q_string  = "select st_completed,st_timestamp,st_user ";
  $q_string .= "from rsdp_status ";
  $q_string .= "where st_step = " . $task . " and st_rsdp = " . $rsdp;
  $q_rsdp_status = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_status = mysqli_fetch_array($q_rsdp_status);

  if (strlen($group) > 0) {
    $q_string  = "select " . $group . "," . $poc . " ";
  } else {
    $q_string  = "select " . $poc . " ";
  }
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $rsdp;
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);
  if (strlen($group) > 0) {
    $group_select = $a_rsdp_server[$group];
  } else {
    $group_select = $grpnum;
  }

  $output  = "<table class=\"ui-styled-table\">\n";
  $output .= "<tr>\n";
  $output .= "  <td class=\"ui-widget-content button\">";

  if ($a_rsdp_status['st_completed'] == '' || $a_rsdp_status['st_completed'] == 0) {
    $output .= "<input type=\"button\" name=\"reminder\" value=\"Reminder E-Mail\" onClick=\"javascript:attach_file('" . $script . "', -1);\">\n";
    if (check_grouplevel($db, $group_select) || $_SESSION['uid'] == $a_rsdp_server[$poc]) {
      $output .= "<input type=\"button\" name=\"save\"   value=\"Save Changes\"   onClick=\"javascript:attach_file('" . $script . "', 0);\">\n";
      $output .= "<input type=\"button\" name=\"exit\"   value=\"Save And Exit\"  onClick=\"javascript:attach_file('" . $script . "', 2);\">\n";
      $output .= "<input type=\"button\" name=\"addbtn\" value=\"Task Completed\" onClick=\"javascript:attach_file('" . $script . "', 1);\">\n";
    } else {
      $q_string  = "select grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $group_select;
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);

      $output .= "<a href=\"index.php\">Task waiting to be completed by " . $a_groups['grp_name'] . ".</a>\n";
      $output .= "<input type=\"hidden\" name=\"addbtn\">\n";
    }
  } else {
    $output .= "<input type=\"button\" name=\"save\"   value=\"Save Changes\"   onClick=\"javascript:attach_file('" . $script . "', 0);\">\n";
    $output .= "<input type=\"button\" name=\"exit\"   value=\"Save And Exit\"  onClick=\"javascript:attach_file('" . $script . "', 2);\">\n";

    $q_string  = "select usr_last,usr_first ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_rsdp_status['st_user'];
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_users = mysqli_fetch_array($q_users);

    $output .= "<br><a href=\"index.php\">Task was completed by " . $a_users['usr_first'] . " " . $a_users['usr_last'] . " on " . $a_rsdp_status['st_timestamp'] . ".</a>";
    $output .= "<input type=\"hidden\" name=\"addbtn\">\n";
  }

  $output .= "<input type=\"hidden\" name=\"id\" value=\"0\">\n";
  $output .= "<input type=\"hidden\" name=\"rsdp\" value=\"" . $rsdp . "\">\n";

  $output .= "</td>\n";
  $output .= "</tr>\n";
  $output .= "</table>\n\n";

  return $output;
}


# this function takes the rsdp number and group and sends an email ticket
function submit_Ticket( $rsdp, $script, $field, $group ) {
  include('settings.php');

# enable for magic
  $magic = 'no';
# enable for remedy
  $remedy = 'yes';

#
# This is the Magic ticket system process.
#
  if ($magic == 'yes') {

######
# NOTE: This is the same code for the original email function.
######

# the requestor opens all tickets
    $q_string  = "select usr_name,usr_first,usr_last,usr_clientid,usr_email ";
    $q_string .= "from rsdp_server ";
    $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
    $q_string .= "where rsdp_id = " . $rsdp . " ";
    $q_requestor = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_requestor = mysqli_fetch_array($q_requestor);

    $target = 'prod';   # send to the production ticketing servers
    $target = 'dev';    # send to the development ticketing servers
    $target = 'local';  # send to the developer

    if ($target == 'local') {
      $magicemail = $Sitedev;
    }
    if ($target == 'dev') {
      $magicemail = "svc_MagicAdminDev@intrado.com";
    }
    if ($target == 'prod') {
      $magicemail = "svc_magicprodemail@intrado.com";
    }

###############################
###  Format the mail message
###############################

    $headers  = "From: RSDP <rsdp@incojs01.scc911.com>\r\n";

# Template:
# Wrap the specific information in the listed tags

#########
### Opened by Client ID: -u-/*u*
#########
    if ($a_requestor['usr_clientid'] != '') {
      $body = "-u-" . $a_requestor['usr_clientid'] . "*u*\n\n";

#########
### User Assigned to: -a-/*a*
#########
      $q_string  = "select " . $field . " ";
      $q_string .= "from rsdp_server ";
      $q_string .= "where rsdp_id = " . $rsdp;
      $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

      if ($a_rsdp_server[$field] > 0) {
        $q_string  = "select usr_clientid,usr_group ";
        $q_string .= "from users ";
        $q_string .= "where usr_id = " . $a_rsdp_server[$field];
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_users = mysqli_fetch_array($q_users);

        $grppoc = $a_users['usr_group'];
        $body .= "-a-" . $a_users['usr_clientid'] . "*a*\n\n";
      } else {
        $grppoc = $group;
      }
  
#########
### Group Assigned to: -a-/*a*
#########
      $q_string  = "select grp_magic,grp_category,grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $grppoc;
      $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_groups = mysqli_fetch_array($q_groups);

      if ($a_groups['grp_magic'] != '') {
        $body .= "-g-" . $a_groups['grp_magic'] . "*g*\n\n";
  
#########
### Category: -c-/*c*
#########
        if ($a_groups['grp_category'] != '') {
          $body .= "-c-" . $a_groups['grp_category'] . "*c*\n\n";

#########
### Description: -d-/*d*
#########

          $body .= "-d-\n";
          $body .= "RSDP " . $rsdp . " is waiting on work done by you or your group.\n";
          $body .= "URL: " . $script . "?rsdp=" . $rsdp . "\n\n";
          if ($target == 'local') {
            $body .= "Note: Ticket not being generated yet.\n\n";
          }
          if ($target == 'dev') {
            $body .= "Note: Ticket generated in Dev Magic.\n\n";
          }
          $body .= "*d*\n\n";

###############################
###  Send the mail to magic
###############################

          if ($Siteenv == 'PROD') {
            $mailto = $magicemail . "," . $Siteadmins;
          } else {
            if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
              $mailto = $Sitedev . "," . $_SESSION['email'];
            } else {
              $mailto = $Sitedev;
            }
          }
          mail($mailto, "RSDPOpen", $body, $headers);

          print "alert('Ticket generation email sent.');\n";
        } else {
          print "alert(\"ERROR: Group Ticket Category not set for " . $a_groups['grp_name'] . ".\\nTicket not generated.\");\n";
        }
      } else {
        print "alert(\"ERROR: Group Ticket value not set for " . $a_groups['grp_name'] . ".\\nTicket not generated.\");\n";
      }
    } else {
      print "alert(\"ERROR: Ticket Client ID hasn't been set for " . $a_requestor['usr_first'] . " " . $a_requestor['usr_last'] . ".\\nA Ticket will need to be created manually.\");\n";

      $body  = "Ticket creation failed for RSDP " . $rsdp . " due to " . $a_requestor['usr_name'] . " not having a Client ID set.\n\n";
      $body .= "Your Ticket Client ID field has not been set in your profile.\n\n";
      $body .= "When this field is blank, a Ticket will not be ";
      $body .= "created and you'll need to manually create the IP request.";

      if ($Siteenv == 'PROD') {
        $mailto = $a_users['usr_email'] . "," . $Siteadmins;
      } else {
        if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
          $mailto = $Sitedev . "," . $_SESSION['email'];
        } else {
          $mailto = $Sitedev;
        }
      }
      mail($mailto, "ERROR: RSDP Ticket Failure", $body, $headers);
    }
  }


######
# NOTE: This is the new Remedy specific code
######

# Passed: $rsdp, $script, $field, $group
# the requester opens all tickets.
# ticket is opened from the requestor, requestor's group, and requestor's manager
# ticket is opened for the listed group plus user if known

  if ($remedy == 'yes') {

    $local       = 'yes';
    $development = 'no';
    $sqa         = 'no';
    $production  = 'no';
    $remedy8     = 'no';        # gone away 8/25/2016
    $remedy9     = 'yes';

# the requestor opens all tickets
    $q_string  = "select usr_first,usr_last,usr_name,usr_email,usr_manager,usr_clientid,grp_name ";
    $q_string .= "from rsdp_server ";
    $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
    $q_string .= "left join groups on groups.grp_id = users.usr_group ";
    $q_string .= "where rsdp_id = " . $rsdp . " ";
    $q_requestor = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_requestor = mysqli_fetch_array($q_requestor);

# the person doing the work
    $q_string  = "select usr_first,usr_last,usr_name,usr_email,usr_manager,usr_clientid,grp_name ";
    $q_string .= "from rsdp_server ";
    $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
    $q_string .= "left join groups on groups.grp_id = users.usr_group ";
    $q_string .= "where rsdp_id = " . $rsdp . " ";
    $q_user = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_user = mysqli_fetch_array($q_user);

# get the user information for the person in the inventory and will be the one opening the ticket plus group information
    $q_string  = "select usr_first,usr_last,usr_clientid ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_requestor['usr_manager'] . " ";
    $q_manager = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_manager = mysqli_fetch_array($q_manager);

    $headers = "From: " . $a_requestor['usr_first'] . " " . $a_requestor['usr_last'] . "<" . $a_requestor['usr_email'] . ">\r\n";
    $headers .= "CC: " . $Sitedev . "\r\n";

# need to add the server name and application to the changelog ticket.

    $report = "Server: " . $server . "\nApplication: " . $application . "\n\n" . $report;

#
# begin the email message
#

    $bodyhead  = "First Name*+ !1000000019!: " . $a_requestor['usr_first'] . "\n";
    $bodyhead .= "Last Name*+ !1000000018!: " . $a_requestor['usr_last'] . "\n";
    $bodyhead .= "(Change Location) Company*+ !1000000001!: Intrado, Inc.\n";
    $bodyhead .= "(Notes) Detailed Description !1000000151!: " . $report . "\n";
    $bodyhead .= "Summary* !1000000000!: " . $firstline . "\n";
    $bodyhead .= "Impact* !1000000163!: 4-Minor/Localized\n";
    $bodyhead .= "Urgency* !1000000162!: 4-Low\n";
    $bodyhead .= "Priority !1000000164!: High\n";

    $bodyhead .= "#Change Coordinator Details\n";
    $bodyhead .= "Support Company !1000003228!: Intrado, Inc.\n";
    $bodyhead .= "Support Organization !1000003227!: Technical Operations\n";
    $bodyhead .= "Support Group Name+ !1000003229!: " . $a_user['grp_name'] . "\n";
    $bodyhead .= "Change Coordinator+ !1000003230!: " . $a_user['usr_first'] . " " . $a_user['usr_last'] . "\n";
    $bodyhead .= "Change Coordinator Login !1000003231!: " . $a_user['usr_clientid'] . "\n";

    $bodyhead .= "#Change Manager Details\n";
    $bodyhead .= "Support Company !1000000251!: Intrado, Inc.\n";
    $bodyhead .= "Support Organization !1000000014!: Technical Operations\n";
    $bodyhead .= "Support Group Name !1000000015!: " . $a_requestor['grp_name'] . "\n";
    $bodyhead .= "Change Manager !1000000403!: " . $a_manager['usr_first'] . " " . $a_manager['usr_last'] . "\n";
    $bodyhead .= "Change Manager Login !1000000408!: " . $a_manager['usr_clientid'] . "\n";

    $bodyhead .= "# Change Dates in the following format 3/8/2016 1:00:00 AM MST\n";
    $bodyhead .= "Actual Start Date+ !1000000348!: " . date('n/j/Y g:i:s A e', strtotime("Yesterday")) . "\n";
    $bodyhead .= "Actual End Date+ !1000000364!: " . date('n/j/Y g:i:s A e') . "\n";

    $bodyhead .= "#PLEASE DO NOT MODIFY THE BELOW MANDATORY VALUES:\n";
    $bodyhead .= "Schema: CHG:ChangeInterface_Create\n";

# tail of the email
    $bodytail  = "Action: Submit\n";
    $bodytail .= "Status !         7!: Draft\n";
    $bodytail .= "Risk Level* !1000000180!: Risk Level 1\n";
    $bodytail .= "Class !1000000568!: Latent\n";
    $bodytail .= "Change Type* !1000000181!: Change\n\n";


# send it to the developer for testing
    if ($local == 'yes') {
      $remedyemail  = $Sitedev;
      $remedyserver = "Blank";

      $body = $bodyhead . "Server: " . $remedyserver . "\n" . $bodytail;
      mail($remedyemail, "Changelog Submission", $body, $headers);

    }

# development server information
    if ($development == 'yes') {
      if ($remedy8 == 'yes') {
        $remedyemail  = "remedy.helpdesk.dev@intrado.com";
        $remedyserver = "LMV08-REMAPPQA.corp.intrado.pri";

        $body = $bodyhead . "Server: " . $remedyserver . "\n" . $bodytail;
        mail($remedyemail, "Changelog Submission", $body, $headers);
      }

      if ($remedy9 == 'yes') {
        $remedyemail = "remedy.helpdesk.dev.safetyservices@regmail.west.com";
        $remedyserver = "LNMT0CWASRMAP00";

        $body = $bodyhead . "Server: " . $remedyserver . "\n" . $bodytail;
        mail($remedyemail, "Changelog Submission", $body, $headers);
      }
    }
# production server information
    if ($production == 'yes') {
      if ($remedy8 == 'yes') {
        $remedyemail  = "remedy.helpdesk@intrado.com";
        $remedyserver = "LMV08-REMAR01.corp.intrado.pri";

        $body = $bodyhead . "Server: " . $remedyserver . "\n" . $bodytail;
        mail($remedyemail, "Changelog Submission", $body, $headers);
      }

      if ($remedy9 == 'yes') {
        $remedyemail = "Remedy91HelpdeskProd@intrado.com";
        $remedyserver = "LNMT1CWASRMAP01.corp.intrado.pri";

        $body = $bodyhead . "Server: " . $remedyserver . "\n" . $bodytail;
        mail($remedyemail, "Changelog Submission", $body, $headers);
      }
    }

# qa server information
    if ($sqa == 'yes') {
      if ($remedy9 == 'yes') {
        $remedyemail = "Remedy91HelpdeskQA@intrado.com";
        $remedyserver = "lnmt0cwasrmap10.corp.intrado.pri";

        $body = $bodyhead . "Server: " . $remedyserver . "\n" . $bodytail;
        mail($remedyemail, "Changelog Submission", $body, $headers);
      }
    }
  }
}


function submit_DNS( $rsdp ) {
  include('settings.php');

###############################
###  Format the mail message
###############################

# the requestor opens all tickets
  $q_string  = "select usr_name,usr_first,usr_last,usr_clientid,usr_email ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "where rsdp_id = " . $rsdp . " ";
  $q_requestor = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_requestor = mysqli_fetch_array($q_requestor);

# Template:
# Wrap the specific information in the listed tags

  $headers  = "From: RSDP <rsdp@incojs01.scc911.com>\r\n";

  $ticket = $Siteadmins;  # Alpha/Beta Testing
#  $ticket = "svc_MagicAdminDev@intrado.com"; # Testing to Dev
#  $ticket = "svc_magicprodemail@intrado.com"; # Production


#########
### Opened by Client ID: -u-/*u*
#########
  if ($a_requestor['usr_clientid'] != '') {
    $body = "-u-" . $a_requestor['usr_clientid'] . "*u*\n\n";

#########
### Group Assigned to: -a-/*a*
#########
    $q_string  = "select grp_magic,grp_category ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $GRP_Windows;
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    if ($a_groups['grp_magic'] != '') {
      $body .= "-g-" . $a_groups['grp_magic'] . "*g*\n\n";
  
#########
### Category: -c-/*c*
#########
      if ($a_groups['grp_category'] != '') {
        $body .= "-c-" . $a_groups['grp_category'] . "*c*\n\n";

#########
### Description: -d-/*d*
#########

        $body .= "-d-\n";
        $body .= "Request a forward and reverse DNS record for the following hostnames and IP addresses.\n\n";

# get the project code
        $q_string  = "select rsdp_project ";
        $q_string .= "from rsdp_server ";
        $q_string .= "where rsdp_id = " . $rsdp;
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_rsdp_server) > 0) {
          $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);
        } else {
          $a_rsdp_server['rsdp_project'] = 0;
        }

# using the project code, get all the interfaces for this project. Makes it easier to create the ticket.
        $q_string  = "select if_name,if_ip,os_fqdn ";
        $q_string .= "from rsdp_interface ";
        $q_string .= "left join rsdp_server on rsdp_interface.if_rsdp = rsdp_server.rsdp_id ";
        $q_string .= "left join rsdp_osteam on rsdp_osteam.os_rsdp = rsdp_server.rsdp_id ";
# commented for tickets to be single systems vs entire project
#        $q_string .= "where rsdp_project = " . $a_rsdp_server['rsdp_project'] . " and if_ipcheck = 1 ";
        $q_string .= "where if_rsdp = " . $rsdp . " and if_ipcheck = 1 ";
        $q_string .= "order by if_name,if_interface";
        $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# set the start variable for the display of the information
        while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

          $body .= $a_rsdp_interface['if_name'] . "." . $a_rsdp_interface['os_fqdn'] . " - " . $a_rsdp_interface['if_ip'] . "\n";

        }

        $body .= "\nContact me if there are any questions.\n";
        $body .= "*d*\n\n";

###############################
###  Send the mail to magic
###############################

        if ($Siteenv == 'PROD') {
          $mailto = $magic . "," . $Siteadmins;
        } else {
          if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
            $mailto = $Sitedev . "," . $_SESSION['email'];
          } else {
            $mailto = $Sitedev;
          }
        }
        mail($mailto, "RSDPOpen", $body, $headers);

        print "alert('Magic email sent.');\n";
      } else {
        print "alert(\"ERROR: Group Magic Category not set.\\nMagic email not sent.\");\n";
      }
    } else {
      print "alert(\"ERROR: Group Magic value not set.\\nMagic email not sent.\");\n";
    }
  } else {
    print "alert(\"ERROR: Ticket Client ID hasn't been set for " . $a_requestor['usr_first'] . " " . $a_requestor['usr_last'] . ".\\nA Ticket will need to be created manually.\");\n";

    $body  = "Ticket creation failed for RSDP " . $rsdp . " due to " . $a_requestor['usr_name'] . " not having a Client ID set.\n\n";
    $body .= "Your Ticket Client ID field has not been set in your profile.\n\n";
    $body .= "When this field is blank, a Ticket will not be ";
    $body .= "created and you'll need to manually create the IP request.";

    if ($Siteenv == 'PROD') {
      $mailto = $a_users['usr_email'] . "," . $Siteadmins;
    } else {
      if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
        $mailto = $Sitedev . "," . $_SESSION['email'];
      } else {
        $mailto = $Sitedev;
      }
    }
    mail($mailto, "ERROR: RSDP Magic Failure", $body, $headers);
  }
}


function submit_Scan( $rsdp ) {
  include('settings.php');

###############################
###  Format the mail message
###############################

# the requestor opens all tickets
  $q_string  = "select usr_name,usr_first,usr_last,usr_clientid,usr_email ";
  $q_string .= "from rsdp_server ";
  $q_string .= "left join users on users.usr_id = rsdp_server.rsdp_requestor ";
  $q_string .= "where rsdp_id = " . $rsdp . " ";
  $q_requestor = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_requestor = mysqli_fetch_array($q_requestor);

# Template:
# Wrap the specific information in the listed tags

  $headers  = "From: RSDP <rsdp@incojs01.scc911.com>\r\n";

  $ticket = $Siteadmins;  # Alpha/Beta Testing
#  $ticket = "svc_MagicAdminDev@intrado.com"; # Testing to Dev
#  $ticket = "svc_magicprodemail@intrado.com"; # Production


#########
### Opened by Client ID: -u-/*u*
#########
  if ($a_requestor['usr_clientid'] != '') {
    $body = "-u-" . $a_requestor['usr_clientid'] . "*u*\n\n";

#########
### Group Assigned to: -a-/*a*
#########
    $q_string  = "select grp_magic,grp_category ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $GRP_InfoSec;
    $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_groups = mysqli_fetch_array($q_groups);

    if ($a_groups['grp_magic'] != '') {
      $body .= "-g-" . $a_groups['grp_magic'] . "*g*\n\n";
  
#########
### Category: -c-/*c*
#########
      if ($a_groups['grp_category'] != '') {
        $body .= "-c-" . $a_groups['grp_category'] . "*c*\n\n";

#########
### Description: -d-/*d*
#########

        $body .= "-d-\n";
        $body .= "Request a Security Scan of the following IP addresses.\n\n";

        $q_string  = "select if_ip, zone_desc ";
        $q_string .= "from rsdp_interface ";
        $q_string .= "left join ip_zones on ip_zones.zone_id = rsdp_interface.if_zone ";
        $q_string .= "where if_rsdp = " . $rsdp . " and if_ipcheck = 1 ";
        $q_string .= "order by if_ip ";
        $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# set the start variable for the display of the information
        while ($a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface)) {

          if ($a_rsdp_interface['zone_desc'] == '') {
            $a_rsdp_interface['zone_desc'] = "No network zone selected.";
          }

          $body .= $a_rsdp_interface['if_ip'] . " - " . $a_rsdp_interface['zone_desc'] . "\n";
        }

        $body .= "\nContact me if there are any questions.\n";
        $body .= "*d*\n\n";

###############################
###  Send the mail to magic
###############################

        if ($Siteenv == 'PROD') {
          $mailto = $magic . "," . $Siteadmins;
        } else {
          if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
            $mailto = $Sitedev . "," . $_SESSION['email'];
          } else {
            $mailto = $Sitedev;
          }
        }
        mail($mailto, "RSDPOpen", $body, $headers);

        print "alert('Magic email sent.');\n";
      } else {
        print "alert(\"ERROR: Group Magic Category not set.\\nMagic email not sent.\");\n";
      }
    } else {
      print "alert(\"ERROR: Group Magic value not set.\\nMagic email not sent.\");\n";
    }
  } else {
    print "alert(\"ERROR: Ticket Client ID hasn't been set for " . $a_requestor['usr_first'] . " " . $a_requestor['usr_last'] . ".\\nA Ticket will need to be created manually.\");\n";

    $body  = "Ticket creation failed for RSDP " . $rsdp . " due to " . $a_requestor['usr_name'] . " not having a Client ID set.\n\n";
    $body .= "Your Ticket Client ID field has not been set in your profile.\n\n";
    $body .= "When this field is blank, a Ticket will not be ";
    $body .= "created and you'll need to manually create the IP request.";

    if ($Siteenv == 'PROD') {
      $mailto = $a_users['usr_email'] . "," . $Siteadmins;
    } else {
      if (strlen($_SESSION['email']) > 0 && $_SESSION['email'] != $Sitedev) {
        $mailto = $Sitedev . "," . $_SESSION['email'];
      } else {
        $mailto = $Sitedev;
      }
    }
    mail($mailto, "ERROR: RSDP Magic Failure", $body, $headers);
  }
}


function rsdp_Virtual( $p_rsdp ) {
# get the model that's been selected
# if there's a corresponding model,
# return the mod_virtual setting (could be 0 or 1)
# otherwise just return 0

  $q_string  = "select mod_virtual ";
  $q_string .= "from rsdp_platform ";
  $q_string .= "left join models on models.mod_id = rsdp_platform.pf_model ";
  $q_string .= "where pf_rsdp = " . $p_rsdp;
  $q_rsdp_platform = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  if (mysqli_num_rows($q_rsdp_platform) > 0) {
    $a_rsdp_platform = mysqli_fetch_array($q_rsdp_platform);
    return $a_rsdp_platform['mod_virtual'];
  } else {
    return 0;
  }

}

function rsdp_System( $p_rsdp ) {
  $output = 'Unknown';

  $q_string  = "select operatingsystem.os_software ";
  $q_string .= "from operatingsystem ";
  $q_string .= "left join rsdp_osteam on rsdp_osteam.os_software = operatingsystem.os_id ";
  $q_string .= "where rsdp_osteam.os_rsdp = " . $p_rsdp . " ";
  $q_operatingsystem = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_operatingsystem = mysqli_fetch_array($q_operatingsystem);

  if (stripos($a_operatingsystem['os_software'], "linux") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "red hat") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "debian") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "ubuntu") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "centos") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "suse") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "fedora") !== false) {
    $output = 'Linux';
  }
  if (stripos($a_operatingsystem['os_software'], "solaris") !== false) {
    $output = 'SunOS';
  }
  if (stripos($a_operatingsystem['os_software'], "hp-ux") !== false) {
    $output = 'HP-UX';
  }
  if (stripos($a_operatingsystem['os_software'], "tru64") !== false) {
    $output = 'OSF1';
  }
  if (stripos($a_operatingsystem['os_software'], "osf1") !== false) {
    $output = 'OSF1';
  }
  if (stripos($a_operatingsystem['os_software'], "freebsd") !== false) {
    $output = 'FreeBSD';
  }
  if (stripos($a_operatingsystem['os_software'], "windows") !== false) {
    $output = 'Windows';
  }
  if (stripos($a_operatingsystem['os_software'], "esx") !== false) {
    $output = 'VMWare';
  }
  if (stripos($a_operatingsystem['os_software'], "vmware") !== false) {
    $output = 'VMware';
  }
  if (stripos($a_operatingsystem['os_software'], "cisco ios") !== false) {
    $output = 'Cisco';
  }

  return $output;
}


function return_Checklist( $p_rsdp, $p_task ) {
  include('settings.php');

# get the host name for the $host variable
  $q_string  = "select os_sysname ";
  $q_string .= "from rsdp_osteam ";
  $q_string .= "where os_rsdp = " . $p_rsdp . " ";
  $q_rsdp_osteam = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_osteam = mysqli_fetch_array($q_rsdp_osteam);

# get the platform group id
  $q_string  = "select rsdp_platform,rsdp_application ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $p_rsdp . " ";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  switch ($p_task) {
    case 3:  $task_poc = "rsdp_sanpoc";
             $task_group = $GRP_SAN;
             break;
    case 4:  $task_poc = "rsdp_networkpoc";
             $task_group = $GRP_Networking;
             break;
    case 5:  $task_poc = "rsdp_dcpoc";
             $task_group = $GRP_DataCenter;
             break;
    case 6:  $task_poc = "rsdp_virtpoc";
             $task_group = $GRP_Virtualization;
             break;
    case 10: $task_poc = "rsdp_platformspoc"; 
             $task_group = $a_rsdp_server['rsdp_platform'];
             break;
    case 11: $task_poc = "rsdp_sanpoc"; 
             $task_group = $GRP_SAN;
             break;
    case 12: $task_poc = "rsdp_platformspoc"; 
             $task_group = $a_rsdp_server['rsdp_platform'];
             break;
    case 13: $task_poc = "rsdp_backuppoc"; 
             $task_group = $GRP_Backups;
             break;
    case 14: $task_poc = "rsdp_monitorpoc"; 
             $task_group = $GRP_Monitoring;
             break;
    case 15: $task_poc = "rsdp_apppoc"; 
             $task_group = $a_rsdp_server['rsdp_application'];
             break;
    case 16: $task_poc = "rsdp_monitorpoc"; 
             $task_group = $GRP_Monitoring;
             break;
    case 17: $task_poc = "rsdp_apppoc"; 
             $task_group = $a_rsdp_server['rsdp_application'];
             break;
    case 18: $task_poc = "rsdp_platformspoc"; 
             $task_group = $a_rsdp_server['rsdp_platform'];
             break;
  }

  $debug = $task_poc . ":" . $task_group . ":";
  $q_string  = "select " . $task_poc . " ";
  $q_string .= "from rsdp_server ";
  $q_string .= "where rsdp_id = " . $p_rsdp . " ";
  $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

  $debug .= $q_string . ":";
  if ($a_rsdp_server[$task_poc] == 0) {
    $group = $task_group;
  } else {
    $q_string  = "select usr_group ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_rsdp_server[$task_poc] . " ";
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_users = mysqli_fetch_array($q_users);

    $group = $a_users['usr_group'];
  }

  $debug .= $group . ":";
# get the maintenance and application ip address for use in replacing the $app/$main variables.
  $maintip = '';
  $appip = '';

  $q_string  = "select if_ip ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "where if_rsdp = " . $p_rsdp . " and if_type = 1 and if_ip != '' ";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_interface) > 0) {
    $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);
    $maintip = $a_rsdp_interface['if_ip'];
  }
  $debug .= $q_string . ":";

  $q_string  = "select if_ip ";
  $q_string .= "from rsdp_interface ";
  $q_string .= "where if_rsdp = " . $p_rsdp . " and if_type = 2 and if_ip != '' ";
  $q_rsdp_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_rsdp_interface) > 0) {
    $a_rsdp_interface = mysqli_fetch_array($q_rsdp_interface);
    $appip = $a_rsdp_interface['if_ip'];
    if ($maintip == '') {
      $maintip = $appip;
    }
  }
  $debug .= $q_string . ":";


# have the rsdp id, task number, and group
# now get the checklist
  $output = "<input type=\"hidden\" name=\"chk_group\" value=\"" . $group . "\">";
  $q_string  = "select chk_id,chk_index,chk_text,chk_link ";
  $q_string .= "from checklist ";
  $q_string .= "where chk_group = " . $group . " and chk_task = " . $p_task . " ";
  $q_string .= "order by chk_index";
  $debug .= $q_string . ":";
  $q_checklist = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_checklist = mysqli_fetch_array($q_checklist)) {

# make the link from the checklist -> documentation generally
    if (strlen($a_checklist['chk_link']) > 0) {
      $linkstart = "<a href=\"" . $a_checklist['chk_link'] . "\">";
      $linkend = "</a>";
    } else {
      $linkstart = "";
      $linkend = "";
    }

# exchange text variables with values from the table for the server
    $a_checklist['chk_text'] = str_replace("\$host", $a_rsdp_osteam['os_sysname'], $a_checklist['chk_text']);
    $a_checklist['chk_text'] = str_replace("\$app", $appip, $a_checklist['chk_text']);
    $a_checklist['chk_text'] = str_replace("\$mgt", $maintip, $a_checklist['chk_text']);

# get whether or not the item has been checked and any comment
    $q_string  = "select chk_comment,chk_checked ";
    $q_string .= "from rsdp_check ";
    $q_string .= "where chk_rsdp = " . $p_rsdp . " and chk_index = " . $a_checklist['chk_index'] . " and chk_group = " . $group . " and chk_task = " . $p_task . " ";
    $q_rsdp_check = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_rsdp_check = mysqli_fetch_array($q_rsdp_check);

# set checked
    if ($a_rsdp_check['chk_checked'] == 1) {
      $checked = "checked ";
    } else {
      $checked = "";
    }

    if (check_grouplevel($db, $task_group)) {
      $disabled = "";
    } else {
      $disabled = "disabled";
    }

# print the checked item and text
    $output .= "<tr>\n";
    $output .= "  <td align=\"left\" class=\"ui-widget-content\">";
      $output .= "<label>--<input type=\"checkbox\" name=\"chk_" . $a_checklist['chk_index'] . "\" " . $checked . " " . $disabled . " ";
      $output .= "onclick=\"attach_checklist('";
        $output .= $RSDProot . "/admin/checklist.check.php', ";
        $output .= $a_checklist['chk_index'] . ", ";
        $output .= $p_task . ", ";
        $output .= "chk_" . $a_checklist['chk_index'] . ".checked, ";
        $output .= "comment_" . $a_checklist['chk_index'] . ".value";
      $output .= ");\">";
      $output .= $linkstart . $a_checklist['chk_text'] . $linkend . "</label>";
    $output .= "</td>\n";
# print the comment field if any
    $output .= "  <td align=\"left\" class=\"ui-widget-content\">";
      $output .= "<input type=\"text\" name=\"comment_" . $a_checklist['chk_index'] . "\" value=\"" . $a_rsdp_check['chk_comment'] . "\" size=\"40\" " . $disabled . " ";
      $output .= "onblur=\"attach_checklist('";
        $output .= $RSDProot . "/admin/checklist.check.php', ";
        $output .= $a_checklist['chk_index'] . ", ";
        $output .= $p_task . ", ";
        $output .= "chk_" . $a_checklist['chk_index'] . ".checked, ";
        $output .= "comment_" . $a_checklist['chk_index'] . ".value";
      $output .= ");\">";
    $output .= "</td>\n";
# end it
    $output .= "</tr>\n";
  }

  $output .= "</table>\n";

  return $output;

}

?>
