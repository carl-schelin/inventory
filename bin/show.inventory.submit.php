#!/usr/local/bin/php
<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";  # green
  $color[2] = "yellow";   # yellow
  $color[3] = "#fa8072";  # red

  $output  = "<html>\n";
  $output .= "<body>\n";
 
# $argv[0] = script name.
# $argc = the number of items in the $argv array

# build subject line for the help screen
  $subjectline = '';
  $subjspace = '';
  for ($i = 2; $i < $argc; $i++) {
    $subjectline = $subjectline . $subjspace . $argv[$i];
    $subjspace = ' ';
  }

# looking for server or product name, :, and specific item if any.
# if only the script name then we're missing the e-mail address
  if ($argc == 1) {
    print "ERROR: invalid command line parameters\n";
    print "./inventory.submit.php email server|product flags\n";
    exit(1);
  } else {
    $email = $argv[1];
  }

  $q_string  = "select usr_name ";
  $q_string .= "from users ";
  $q_string .= "where usr_id != 1 and usr_email = '" . $email . "'";
  if ($debug == 'yes') {
    print $q_string . "\n\n";
  }
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
  $a_users = mysql_fetch_array($q_users);

  logaccess($a_users['usr_name'], "show.inventory.submit.php", $subjectline);

# if the script name and e-mail address is all, then default to active
  $productlist = '';
  $prodspace = '';
  $servername = '';
  if ($argc == 2) {
    $servername = "active";
  } else {
# build the product listing in case it's multiple words
    for ($i = 2; $i < $argc; $i++) {
      $productlist = $productlist . $prodspace . $argv[$i];
      $prodspace = ' ';
    }
# and of course, set servername = to just the server name.
    $servername = $argv[2];
  }

# is this a valid IP address? If so, send it intact, otherwise split it to get the server name.
  if (filter_var($servername, FILTER_VALIDATE_IP)) {
    $servername = strtolower($servername);
  } else {
    $serverlist = explode('.', $servername);
    $servername = strtolower($serverlist[0]);
  }
  $productlist = str_replace("_", " ", strtolower($productlist));
  $serverip = $servername;
  $product = '';
  $server = '';
  $error = '';

# if script, e-mail, and server name is sent
  $action = '';
  if ($argc == 3) {
    $action = '';
  } else {
    $action = strtolower($argv[3]);

# options are: *, Hardware, Filesystems, Software, Network, Route, Issues
# check the full keyword
    if ($action != 'hardware' && $action != 'filesystems' && $action != 'software' && $action != 'network' && $action != 'route' && $action != 'routing' && $action != 'issues' && $action != 'all') {
      $servername = 'help';
      $error = "<p><strong>Error</strong>: Invalid option.</p>\n\n";
    }

# check the initials as well as the full option isn't necessary
    $firstchar = substr($action, 0, 1);
    if (strpos("*ahfsnri", $firstchar) === false) {
      $servername = 'help';
      $error = "<p><strong>Error</strong>: Invalid option.</p>\n\n";
    }

    if ($debug == 'yes') {
      print "FirstChar: " . $firstchar . "\n";
      print "Error: " . $error . "\n";
    }

  }

  if ($servername == 'help' || $servername == 'active' || $servername == "products") {
    $server = $servername;
  } else {
# need to get the inventory servername if int_server == servername
# can't check it in the interface section due to the number of entries returned.
# activate the network check if action is blank so the user can see the alternate interface entries.
# make sure a wildcard hasn't been sent too, ignore the check if so.
    if (strpos($servername, '%') === false) {
      $q_string  = "select inv_name ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "where int_server like '" . $servername . "' ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if ($debug == 'yes') {
        print $q_string . "\n\n";
      }
      if (mysql_num_rows($q_interface) > 0) {
        $a_interface = mysql_fetch_array($q_interface);
# only replace it if it came from the interface table (keeps action from being replaced when you know what you're looking for).
        if ($servername != $a_interface['inv_name']) {
          $servername = $a_interface['inv_name'];
          if (strlen($action) == 0 && $action != '*' && substr($action, 0, 1) != 'a') {
            $action = 'network';
          }
        }
      }
    }

    $q_string  = "select inv_id,inv_name,inv_manager ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name like '" . $servername . "' and inv_status = 0 ";
    $q_string .= "order by inv_name";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");

    if ($debug == 'yes') {
      print $q_string . "\n\n";
    }
    if (mysql_num_rows($q_inventory) == 0) {
      $q_string  = "select prod_name ";
      $q_string .= "from products ";
      $q_string .= "where prod_name = '" . $productlist . "'";
      $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");

      if ($debug == 'yes') {
        print $q_string . "\n\n";
      }
      if (mysql_num_rows($q_products) == 0) {
        $q_string  = "select int_companyid,int_addr,int_face,int_eth ";
        $q_string .= "from interface ";
        $q_string .= "where int_addr like '" . $serverip . "' ";
        $q_string .= "order by INET_ATON(int_addr) ";
        $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");

        if ($debug == 'yes') {
          print $q_string . "\n\n";
        }
        if (mysql_num_rows($q_interface) == 0) {
          $error = "<p><strong>Error</strong>: The requested server, product, or IP was not found in the Inventory database.</p>\n\n";
          $server = "help";
        } else {
          if (mysql_num_rows($q_interface) == 1) {
            $a_interface = mysql_fetch_array($q_interface);

            $q_string  = "select inv_id,inv_name,inv_manager ";
            $q_string .= "from inventory ";
            $q_string .= "where inv_id = " . $a_interface['int_companyid'];
            $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");

            if ($debug == 'yes') {
              print $q_string . "\n\n";
            }
            if (mysql_num_rows($q_inventory) > 0) {
              $a_inventory = mysql_fetch_array($q_inventory);
              $servername = $a_inventory['inv_name'];
            } else {
              $error = "<p><strong>Error</strong>: Can't find the matching server name in the Inventory database.</p>\n\n";
              $server = "help";
            }
          } else {
            $output .= "<table width=80%>\n";
            $output .= "<tr>\n";
            $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Intrado: IP Listing</th>\n";
            $output .= "</tr>\n";
            $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
            $output .= "  <th>Servername</th>\n";
            $output .= "  <th>IP Address</th>\n";
            $output .= "  <th>Interface</th>\n";
            $output .= "  <th>MAC</th>\n";
            $output .= "  <th>Managed By</th>\n";
            $output .= "</tr>\n";

            while ($a_interface = mysql_fetch_array($q_interface)) {

              $q_string  = "select inv_name,inv_manager,inv_status ";
              $q_string .= "from inventory ";
              $q_string .= "where inv_id = " . $a_interface['int_companyid'];
              $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
              $a_inventory = mysql_fetch_array($q_inventory);

              if ($debug == 'yes') {
                print $q_string . "\n\n";
              }
              $q_string  = "select grp_name ";
              $q_string .= "from groups ";
              $q_string .= "where grp_id = '" . $a_inventory['inv_manager'] . "'";
              $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
              $a_groups = mysql_fetch_array($q_groups);

              if ($debug == 'yes') {
                print $q_string . "\n\n";
              }
              if ($a_inventory['inv_status'] == 0) {
                $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
                $output .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
                $output .= "  <td>" . $a_interface['int_addr'] . "</td>\n";
                $output .= "  <td>" . $a_interface['int_face'] . "</td>\n";
                $output .= "  <td>" . $a_interface['int_eth'] . "</td>\n";
                $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
                $output .= "</tr>\n";
              }
            }

            $output .= "</table>\n\n";

            $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

            $output .= "</body>\n";
            $output .= "</html>\n";

            $body = $output;

            if ($debug == 'yes') {
              print "mail($email, \"Inventory: IP Listing\", $body, $headers);\n\n";
            } else {
              mail($email, "Inventory: IP Listing", $body, $headers);
            }
            exit(1);
          }

        }
      } else {
        $product = $productlist;
      }
    } else {
      if (mysql_num_rows($q_inventory) == 1) {
        $server = $servername;
      } else {
        $output .= "<table width=80%>\n";
        $output .= "<tr>\n";
        $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"3\">Intrado: Server Listing</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <th>Servername</th>\n";
        $output .= "  <th>Managed By</th>\n";
        $output .= "</tr>\n";

        while ($a_inventory = mysql_fetch_array($q_inventory)) {
          $q_string = "select grp_name from groups where grp_id = " . $a_inventory['inv_manager'];
          $q_groups = mysql_query($q_string) or die($q_string . ":(1): " . mysql_error() . "\n\n");
          $a_groups = mysql_fetch_array($q_groups);

          if ($debug == 'yes') {
            print $q_string . "\n\n";
          }
          $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
          $output .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
          $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
          $output .= "</tr>\n";
        }

        $output .= "</table>\n\n";

        $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

        $output .= "</body>\n";
        $output .= "</html>\n";

        $body = $output;

        if ($debug == 'yes') {
          print "mail($email, \"Inventory: Server Listing\", $body, $headers);\n\n";
        } else {
          mail($email, "Inventory: Server Listing", $body, $headers);
        }
        exit(1);
      }
    }
  }

  if ($server == 'help') {
    $body  = $error;
    $body .= "<p><u>Your Input:</u></p>\n";
    $body .= "<p>" . $subjectline . "</p>\n\n";
    $body .= "<p><u>Usage:</u></p>\n";
    $body .= "<p>To: inventory@incojs01.scc911.com\n";
    $body .= "<br>Subject: {servername} or {serverip} [keyword option]\n";
    $body .= "<br>Subject: {intrado product}\n";
    $body .= "<br>Subject: [{empty subject} or {active} or {help} or {products}</p>\n\n";

    $body .= "<p>The Subject line consists of up to two keywords. The first can be one of five options;</p>\n";
    $body .= "<ul>\n";
    $body .= "  <li><b>{empty subject} or active</b> - If the Subject line is empty or contains the keyword 'active', a list of all active servers will be returned via e-mail.</li>\n";
    $body .= "  <li><b>help</b> - An e-mail will be returned with this message.</li>\n";
    $body .= "  <li><b>products</b> - A list of all Intrado products will be returned.</li>\n";

    $body .= "  <li><b>{servername}</b> - An e-mail will be returned containing information about the identified server.</li>\n";
    $body .= "  <li><b>{serverip}</b> - An e-mail will be returned containing information about the server associated with the IP.</li>\n";

    $body .= "  <li><b>{intrado product}</b> - An e-mail will be returned containing a list of all servers assigned to this Intrado Product</li>\n";
    $body .= "</ul>\n\n";
    $body .= "<p>The email request supports Database wildcards (%) so to get a list of servers that start with 'inco', add the wildcard character to the end of the server name, example: 'inco%' or '10.100.78.%' for a list of servers with IP Addresses. You will receive a complete listing of all servers or IP addresses.</p>\n";

    $body .= "<p>The second <b>keyword option</b> for the passed <b>servername</b> or <b>serverip</b> describes what information in addition to the core server details you want to retrieve. ";
    $body .= "Note that only the first letter of the keyword is necessary to retrieve the requested information.</p>\n";
    $body .= "<ul>\n";
    $body .= "  <li><b>{blank}</b> - An e-mail will be returned containing basic details about the requested server.</li>\n";
    $body .= "  <li><b>*/<u>a</u>ll</b> - An e-mail will be returned containing details from all the following keywords.</li>\n";
    $body .= "  <li><b><u>h</u>ardware</b> - An e-mail will be returned containing basic information plus a list of the hardware.</li>\n";
    $body .= "  <li><b><u>f</u>ilesystems</b> - An e-mail will be returned containing basic information plus a list of the filesystems.</li>\n";
    $body .= "  <li><b><u>s</u>oftware</b> - An e-mail will be returned containing basic information plus a list of the installed software, not including the list of installed packages.</li>\n";
    $body .= "  <li><b><u>n</u>etwork</b> - An e-mail will be returned containing the basic information plus the network interfaces.</li>\n";
    $body .= "  <li><b><u>r</u>oute/<u>r</u>outing</b> - An e-mail will be returned containing basic information plus a list of the baseline routes.</li>\n";
    $body .= "  <li><b><u>i</u>ssues</b> - An e-mail will be returned containing basic information plus a list of open issues.</li>\n";
    $body .= "</ul>\n\n";

    $body .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

    $body .= "</body>\n";
    $body .= "</html>\n";

    if ($debug == 'yes') {
      print "mail($email, \"Inventory: Help\", $body, $headers);\n\n";
    } else {
      mail($email, "Inventory: Help", $body, $headers);
    }
    exit(1);
  }

  if ($server == "active") {
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Intrado: Active Server Listing</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "  <th>Platform Managed By</th>\n";
    $output .= "  <th>Applications Managed By</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_fqdn,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_status = 0 ";
    $q_string .= "group by inv_name";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    while ($a_inventory = mysql_fetch_array($q_inventory)) {
      $q_string  = "select grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
      $q_groups = mysql_query($q_string) or die($q_string . ":(2): " . mysql_error() . "\n\n");
      $a_groups = mysql_fetch_array($q_groups);

      $q_string  = "select hw_active ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      $a_hardware = mysql_fetch_array($q_hardware);
      if ($a_hardware['hw_active'] == '0000-00-00') {
        $bgcolor = $color[1];
      } else {
        $bgcolor = $color[0];
      }

      if ($a_inventory['inv_fqdn'] != '') {
        $invname = $a_inventory['inv_name'] . "." . $a_inventory['inv_fqdn'];
      } else {
        $invname = $a_inventory['inv_name'];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $invname                     . "</td>\n";
      $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
      $output .= "  <td>" . $a_inventory['grp_name']     . "</td>\n";
      $output .= "  <td>" . $a_groups['grp_name']        . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";

    $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    if ($debug == 'yes') {
      print "mail($email, \"Inventory: Active Server Listing\", $body, $headers);\n\n";
    } else {
      mail($email, "Inventory: Active Server Listing", $body, $headers);
    }
    exit(1);

  }

  if ($server == "products") {
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Intrado Product Listing</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select prod_name,prod_desc ";
    $q_string .= "from products ";
    $q_string .= "order by prod_name";
    $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    while ($a_products = mysql_fetch_array($q_products)) {
      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_products['prod_name'] . "</td>\n";
      $output .= "  <td>" . $a_products['prod_desc'] . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";

    $output .= "<p><a href=\"" . $Siteroot . "/products.php\">Inventory Product Listing.</a></p>\n\n";

    $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    if ($debug == 'yes') {
      print "mail($email, \"Intrado Product Listing\", $body, $headers);\n\n";
    } else {
      mail($email, "Intrado Product Listing", $body, $headers);
    }
    exit(1);
  }

  if ($product != '') {
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Intrado Product : " . $product . "</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "  <th>Platform Managed By</th>\n";
    $output .= "  <th>Applications Managed By</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select prod_id ";
    $q_string .= "from products ";
    $q_string .= "where prod_name = '" . $product . "'";
    $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_products = mysql_fetch_array($q_products);

    $q_string  = "select inv_id,inv_name,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join software on inventory.inv_id = software.sw_companyid ";
    $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_status = 0 and sw_product = " . $a_products['prod_id'] . " ";
    $q_string .= "group by inv_name";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    while ($a_inventory = mysql_fetch_array($q_inventory)) {
      $q_string  = "select grp_name ";
      $q_string .= "from groups ";
      $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
      $q_groups = mysql_query($q_string) or die($q_string . ":(3): " . mysql_error() . "\n\n");
      $a_groups = mysql_fetch_array($q_groups);

      $q_string  = "select hw_active ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      $a_hardware = mysql_fetch_array($q_hardware);
      if ($a_hardware['hw_active'] == '0000-00-00') {
        $bgcolor = $color[1];
      } else {
        $bgcolor = $color[0];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_inventory['inv_name']     . "</td>\n";
      $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
      $output .= "  <td>" . $a_inventory['grp_name']     . "</td>\n";
      $output .= "  <td>" . $a_groups['grp_name']        . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";

    $output .= "<p><a href=\"" . $Siteroot . "/show.products.php?id=" . $a_products['prod_id'] . "\">Product Listing for " . $product . ".</a></p>\n\n";

    $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    if ($debug == 'yes') {
      print "mail($email, \"Inventory: \" . $product . \" server list\", $body, $headers);\n\n";
    } else {
      mail($email, "Inventory: " . $product . " server list", $body, $headers);
    }
    exit(1);

  } else {
# Now we provide the basic server information including chassis or cluster members
#
# table one: name and managers
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Inventory Management</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_fqdn,inv_companyid,inv_function,inv_location,inv_product,inv_rack,";
    $q_string .= "inv_row,inv_unit,grp_name,inv_appadmin,inv_callpath,svc_acronym,inv_notes,inv_document ";
    $q_string .= "from inventory ";
    $q_string .= "left join service on service.svc_id = inventory.inv_class ";
    $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_name = '" . $servername . "' and inv_status = 0";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_inventory = mysql_fetch_array($q_inventory);

    if ($a_inventory['inv_fqdn'] != '') {
      $invname = $a_inventory['inv_name'] . "." . $a_inventory['inv_fqdn'];
    } else {
      $invname = $a_inventory['inv_name'];
    }

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
    $q_groups = mysql_query($q_string) or die($q_string . ":(4): " . mysql_error() . "\n\n");
    $a_groups = mysql_fetch_array($q_groups);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Server</strong>: " . $invname . "</td>\n";
    $output .= "  <td><strong>Service Class</strong>: " . $a_inventory['svc_acronym'] . "</td>\n";
    $output .= "  <td><strong>Function</strong>: " . $a_inventory['inv_function'] . "</td>\n";
    $output .= "  <td><strong>Platform Managed by</strong>: " . $a_inventory['grp_name'] . "</td>\n";
    $output .= "  <td><strong>Applications Managed by</strong>: " . $a_groups['grp_name'] . "</td>\n";
    $output .= "</tr>\n";

    if (strlen($a_inventory['inv_notes']) > 0) {
      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td colspan=\"5\"><strong>Notes</strong>: " . $a_inventory['inv_notes'] . "</td>\n";
      $output .= "</tr>\n";
    }

    if (strlen($a_inventory['inv_document']) > 0) {
      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td colspan=\"5\"><strong>Team Documentation</strong>: " . $a_inventory['inv_document'] . "</td>\n";
      $output .= "</tr>\n";
    }

    $q_string  = "select hw_active ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_hardware = mysql_fetch_array($q_hardware);

    if ($a_hardware['hw_active'] == '0000-00-00') {
      $output .= "<tr style=\"background-color: " . $color[3] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td colspan=\"5\"><strong>Server is not in Production at this time.</strong></td>\n";
      $output .= "</tr>\n";
    } else {

      $q_string = "select prod_name,svc_name ";
      $q_string .= "from products ";
      $q_string .= "left join service on service.svc_id = products.prod_service ";
      $q_string .= "where prod_id = " . $a_inventory['inv_product'] . " ";
      $q_products = mysql_query($q_string) or die($q_string .= ": " . mysql_error());
      $a_products = mysql_fetch_array($q_products);

      if ($a_inventory['inv_callpath']) {
        $bgcolor = $color[3];
        $service = "Server <strong>is</strong> in the 911 Call Path";
      } else {
        $bgcolor = $color[0];
        $service = "Server <strong>is <u>not</u></strong> in the 911 Call Path";
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td><strong>Product</strong>: " . $a_products['prod_name'] . "</td>\n";
      $output .= "  <td colspan=\"2\"><strong>Service Class</strong>: " . $a_products['svc_name'] . "</td>\n";
      $output .= "  <td colspan=\"2\"><strong>Call Path</strong>: " . $service . "</td>\n";
      $output .= "</tr>\n";
    }

    $output .= "</table>\n\n";

# table two; support details
    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=5>Support Information</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select hw_supportid,hw_active ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_hardware = mysql_fetch_array($q_hardware);

    $q_string  = "select sup_company,sup_phone,sup_contract,sup_hwresponse,sup_swresponse ";
    $q_string .= "from support ";
    $q_string .= "where sup_id = " . $a_hardware['hw_supportid'];
    $q_support = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_support = mysql_fetch_array($q_support);

    if ($a_support['sup_hwresponse'] == 0 || $a_support['sup_hwresponse'] == '') {
      $hwsupport = "No Support Selected";
    } else {
      $q_string  = "select slv_value ";
      $q_string .= "from supportlevel ";
      $q_string .= "where slv_id = " . $a_support['sup_hwresponse'];
      $q_supportlevel = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      $a_supportlevel = mysql_fetch_array($q_supportlevel);
      $hwsupport = $a_supportlevel['slv_value'];
    }
    
    if ($a_support['sup_swresponse'] == 0 || $a_support['sup_swresponse'] == '') {
      $swsupport = "No Support Selected";
    } else {
      $q_string  = "select slv_value ";
      $q_string .= "from supportlevel ";
      $q_string .= "where slv_id = " . $a_support['sup_swresponse'];
      $q_supportlevel = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      $a_supportlevel = mysql_fetch_array($q_supportlevel);
      $swsupport = $a_supportlevel['slv_value'];
    }

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Company</strong>: " . $a_support['sup_company'] . "</td>\n";
    $output .= "  <td><strong>Phone</strong>: " . $a_support['sup_phone'] . "</td>\n";
    $output .= "  <td><strong>Contract</strong>: " . $a_support['sup_contract'] . "</td>\n";
    $output .= "  <td><strong>Hardware</strong>: " . $hwsupport . "</td>\n";
    $output .= "  <td><strong>Software</strong>: " . $swsupport . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";

# table three: basic hardware information
    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Primary Hardware Information</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select hw_serial,hw_asset,hw_vendorid ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_hardware = mysql_fetch_array($q_hardware);

    $q_string  = "select mod_vendor,mod_name ";
    $q_string .= "from models ";
    $q_string .= "where mod_id = " . $a_hardware['hw_vendorid'];
    $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_models = mysql_fetch_array($q_models);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Vendor</strong>: "           . $a_models['mod_vendor']   . "</td>\n";
    $output .= "  <td><strong>Model</strong>: "            . $a_models['mod_name']     . "</td>\n";
    $output .= "  <td><strong>Serial Number</strong>: "    . $a_hardware['hw_serial']  . "</td>\n";
    $output .= "  <td><strong>Asset Tag</strong>: "        . $a_hardware['hw_asset']   . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


# table four: basic software information
    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Primary Software/Operating System Information</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select sw_software,sw_vendor ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_software = mysql_fetch_array($q_software);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Software</strong>: " . $a_software['sw_software'] . "</td>\n";
    $output .= "  <td><strong>Vendor</strong>: "   . $a_software['sw_vendor']   . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


# table five: optional: if a chassis id, list the members.
    $q_string  = "select inv_name,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_companyid = " . $a_inventory['inv_id'] . " and inv_status = 0 ";
    $q_string .= "order by inv_name ";
    $q_cluster = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_cluster) > 0) {
      $output .= "<table width=80%>\n";

      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Chassis Information</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Server</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Function</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Platform</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Application</th>\n";
      $output .= "</tr>\n";
      
      while ($a_cluster = mysql_fetch_array($q_cluster)) {

        $q_string  = "select grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_id = " . $a_cluster['inv_appadmin'] . " ";
        $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_groups = mysql_fetch_array($q_groups);

        $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_cluster['inv_name']     . "</td>\n";
        $output .= "  <td>" . $a_cluster['inv_function'] . "</td>\n";
        $output .= "  <td>" . $a_cluster['grp_name'] . "</td>\n";
        $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
        $output .= "</tr>\n";

      }
      $output .= "</table>\n\n";
    }


# table six: optional: if a cluster id, list the members.
    $q_string  = "select inv_name,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_clusterid = " . $a_inventory['inv_id'] . " and inv_status = 0 ";
    $q_string .= "order by inv_name ";
    $q_cluster = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_cluster) > 0) {
      $output .= "<table width=80%>\n";

      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Cluster Membership Information</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Server</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Function</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Platform</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Application</th>\n";
      $output .= "</tr>\n";
      
      while ($a_cluster = mysql_fetch_array($q_cluster)) {

        $q_string  = "select grp_name ";
        $q_string .= "from groups ";
        $q_string .= "where grp_id = " . $a_cluster['inv_appadmin'] . " ";
        $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_groups = mysql_fetch_array($q_groups);

        $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_cluster['inv_name']     . "</td>\n";
        $output .= "  <td>" . $a_cluster['inv_function'] . "</td>\n";
        $output .= "  <td>" . $a_cluster['grp_name'] . "</td>\n";
        $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
        $output .= "</tr>\n";

      }
      $output .= "</table>\n\n";
    }


# table seven: location information including blade number if it's in a chassis
    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"3\">Location Information</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select loc_name,loc_addr1,ct_city,st_acronym,loc_zipcode,cn_acronym ";
    $q_string .= "from locations ";
    $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
    $q_string .= "left join states on states.st_id = cities.ct_state ";
    $q_string .= "left join country on country.cn_id = states.st_country ";
    $q_string .= "where loc_id = " . $a_inventory['inv_location'];
    $q_locations = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
    $a_locations = mysql_fetch_array($q_locations);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Data Center</strong>: " . $a_locations['loc_name'] . "</td>\n";
    $output .= "  <td><strong>Location</strong>: " . $a_locations['loc_addr1'] . "  " . $a_locations['ct_city'] . ", " . $a_locations['st_acronym'] . " " . $a_locations['loc_zipcode'] . " (" . $a_locations['cn_acronym'] . ")</td>\n";

# don't provide info if it's a virtual machine
    if (return_Virtual($a_inventory['inv_id']) == 0) {
      if ($a_inventory['inv_companyid']) {
        $q_string  = "select inv_name,inv_rack,inv_row,inv_unit ";
        $q_string .= "from inventory ";
        $q_string .= "where inv_id = " . $a_inventory['inv_companyid'] . " ";
        $q_chassis = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_chassis = mysql_fetch_array($q_chassis);

        $output .= "  <td><strong>Chassis</strong>: " . $a_chassis['inv_name'] . "</td>\n";
        $output .= "  <td><strong>Chassis Rack/Unit</strong>: " . $a_chassis['inv_rack'] . "-" . $a_chassis['inv_row'] . "/U" . $a_chassis['inv_unit'] . "</td>\n";
        $output .= "  <td><strong>Blade Number</strong>: " . $a_inventory['inv_unit'] . "</td>\n";
      } else {
        $output .= "  <td><strong>Rack/Unit</strong>: " . $a_inventory['inv_rack'] . "-" . $a_inventory['inv_row'] . "/U" . $a_inventory['inv_unit'] . "</td>\n";
      }
    }
    $output .= "</tr>\n";

    $output .= "</table>\n\n";




# all done; now we check for flags if more detail is desired
# hardware display
    if (substr($action, 0, 1) == 'h' || $action == '*' || substr($action, 0, 1) == 'a') {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"8\">Full Hardware Listing</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Serial Number</th>\n";
      $output .= "  <th>Asset Tag</th>\n";
      $output .= "  <th>Vendor</th>\n";
      $output .= "  <th>Model</th>\n";
      $output .= "  <th>Size</th>\n";
      $output .= "  <th>Speed</th>\n";
      $output .= "  <th>Type</th>\n";
      $output .= "  <th>Last</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select hw_serial,hw_asset,hw_vendorid,hw_size,hw_speed,part_name,hw_verified,hw_update ";
      $q_string .= "from hardware ";
      $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
      $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by part_name";
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      while ($a_hardware = mysql_fetch_array($q_hardware)) {
        $q_string  = "select mod_vendor,mod_name,mod_size,mod_speed from models where mod_id = " . $a_hardware['hw_vendorid'];
        $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
        $a_models = mysql_fetch_array($q_models);

        if ($a_hardware['hw_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_hardware['hw_serial']  . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_asset']   . "</td>\n";
        $output .= "  <td>" . $a_models['mod_vendor']   . "</td>\n";
        $output .= "  <td>" . $a_models['mod_name']     . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_size']    . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_speed']   . "</td>\n";
        $output .= "  <td>" . $a_hardware['part_name']  . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_update']  . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

# filesystem display
    if (substr($action, 0, 1) == 'f' || $action == '*' || substr($action, 0, 1) == 'a') {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">Filesystem Listing</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Device</th>\n";
      $output .= "  <th>Size</th>\n";
      $output .= "  <th>Volume Name</th>\n";
      $output .= "  <th>Mount</th>\n";
      $output .= "  <th>WWNN</th>\n";
      $output .= "  <th>Last</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select fs_device,fs_size,fs_volume,fs_mount,fs_wwid,fs_verified,fs_update ";
      $q_string .= "from filesystem ";
      $q_string .= "where fs_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by fs_device,fs_mount";
      $q_filesystem = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      while ( $a_filesystem = mysql_fetch_array($q_filesystem) ) {

        if ($a_filesystem['fs_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">";
        $output .= "<td>" . $a_filesystem['fs_device'] . "</td>\n";
        $output .= "<td>" . $a_filesystem['fs_size']   . "</td>\n";
        $output .= "<td>" . $a_filesystem['fs_volume'] . "</td>\n";
        $output .= "<td>" . $a_filesystem['fs_mount']  . "</td>\n";
        $output .= "<td>" . $a_filesystem['fs_wwid']   . "</td>\n";
        $output .= "<td>" . $a_filesystem['fs_update'] . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

# software display
    if (substr($action, 0, 1) == 's' || $action == "*" || substr($action, 0, 1) == 'a') {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">Software Listing</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Product</th>\n";
      $output .= "  <th>Vendor</th>\n";
      $output .= "  <th>Software</th>\n";
      $output .= "  <th>Type</th>\n";
      $output .= "  <th>Support Group</th>\n";
      $output .= "  <th>Last</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sw_product,sw_vendor,sw_software,sw_type,sw_group,sw_verified,sw_update ";
      $q_string .= "from software ";
      $q_string .= "where (sw_type != 'PKG' and sw_type != 'RPM') and sw_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by sw_software";
      $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      while ($a_software = mysql_fetch_array($q_software)) {
        $q_string = "select prod_name from products where prod_id = " . $a_software['sw_product'];
        $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
        $a_products = mysql_fetch_array($q_products);

        $q_string = "select grp_name from groups where grp_id = " . $a_software['sw_group'];
        $q_groups = mysql_query($q_string) or die($q_string . ":(5): " . mysql_error() . "\n\n");
        $a_groups = mysql_fetch_array($q_groups);

        if ($a_software['sw_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_products['prod_name']   . "</td>\n";
        $output .= "  <td>" . $a_software['sw_vendor']   . "</td>\n";
        $output .= "  <td>" . $a_software['sw_software'] . "</td>\n";
        $output .= "  <td>" . $a_software['sw_type']     . "</td>\n";
        $output .= "  <td>" . $a_groups['grp_name']      . "</td>\n";
        $output .= "  <td>" . $a_software['sw_update']   . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

# network table
    if (substr($action, 0, 1) == 'n' || $action == "*" || substr($action, 0, 1) == 'a') {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"11\">Network</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Interface Name</th>\n";
      $output .= "  <th>Logical Interface</th>\n";
      if (return_Virtual($a_inventory['inv_id']) == 0) {
        $output .= "  <th>Physical Port</th>\n";
      }
      $output .= "  <th>MAC Address</th>\n";
      $output .= "  <th>IP Address/Netmask</th>\n";
      $output .= "  <th>Zone</th>\n";
      $output .= "  <th>Gateway</th>\n";
      if (return_Virtual($a_inventory['inv_id']) == 0) {
        $output .= "  <th>Switch</th>\n";
        $output .= "  <th>Port</th>\n";
      }
      $output .= "  <th>Type</th>\n";
      $output .= "  <th>Last</th>\n";
      $output .= "</tr>\n";
    
      $q_string = "select int_id,int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,"
                .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name "
                . "from interface "
                . "left join ip_zones on interface.int_zone = ip_zones.zone_id  "
                . "left join inttype on interface.int_type = inttype.itp_id "
                . "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = 0 and int_ip6 = 0 "
                . "order by int_face,int_addr";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    
      while ( $a_interface = mysql_fetch_array($q_interface) ) {
    
        $intnote = " title=\"" . $a_interface['int_note'] . "\"";
        if ($a_interface['int_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }
        if ($a_interface['int_eth'] == '00:00:00:00:00:00' ) {
          $showmac = '';
        } else {
          $showmac = $a_interface['int_eth'];
        }
        if ($a_interface['int_addr'] == '' ) {
          $showmask = '';
        } else {
          $showmask = '/' . $a_interface['int_mask'];
        }
        $redundancy = '';
        if ($a_interface['int_redundancy'] > 0 ) {
          $redundancy = ' (r)';
        }
        $virtual = '';
        if ($a_interface['int_virtual'] == 1 ) {
          $virtual = ' (v)';
        }
    
        if ($a_interface['int_type'] == 4 || $a_interface['int_type'] == 6) {
          $linkstart = "<a href=\"http://" . $a_interface['int_addr'] . "\" target=\"_blank\">";
          $linkend = "</a>";
        } else {
          $linkstart = "";
          $linkend = "";
        }
    
        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_server'] . $redundancy            . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_face'] . $virtual                 . "</td>\n";
        if (return_Virtual($a_inventory['inv_id']) == 0) {
          $output .= "<td" . $intnote . ">"            . $a_interface['int_sysport']                         . "</td>\n";
        }
        $output .= "<td" . $intnote . ">"              . $showmac                                            . "</td>\n";
        $output .= "<td" . $intnote . ">" . $linkstart . $a_interface['int_addr']     . $showmask . $linkend . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['zone_name']                           . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_gate']                            . "</td>\n";
        if (return_Virtual($a_inventory['inv_id']) == 0) {
          $output .= "<td" . $intnote . ">"            . $a_interface['int_switch']                          . "</td>\n";
          $output .= "<td" . $intnote . ">"            . $a_interface['int_port']                            . "</td>\n";
        }
        $output .= "<td" . $intnote . ">"              . $a_interface['itp_acronym']                         . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_update']                          . "</td>\n";
        $output .= "</tr>\n";
    
    
        $q_string = "select int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,"
                  .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_name,int_groupname "
                  . "from interface "
                  . "left join ip_zones on interface.int_zone = ip_zones.zone_id  "
                  . "left join inttype on interface.int_type = inttype.itp_id "
                  . "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = " . $a_interface['int_id'] . " and int_ip6 = 0 "
                  . "order by int_face,int_addr";
        $q_redundancy = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    
        while ( $a_redundancy = mysql_fetch_array($q_redundancy) ) {
    
          $intnote = " title=\"" . $a_redundancy['int_note'] . "\"";
          if ($a_redundancy['int_verified'] == 1) {
            $bgcolor = $color[1];
          } else {
            $bgcolor = $color[0];
          }
          if ($a_redundancy['int_eth'] == '00:00:00:00:00:00' ) {
            $showmac = '';
          } else {
            $showmac = $a_redundancy['int_eth'];
          }
          if ($a_redundancy['int_addr'] == '' ) {
            $showmask = '';
          } else {
            $showmask = '/' . $a_redundancy['int_mask'];
          }
          $group = '';
          if ($a_redundancy['int_groupname'] != '') {
            $group = ' (' . $a_redundancy['int_groupname'] . ')';
          }
          $virtual = '';
          if ($a_redundancy['int_virtual'] == 1 ) {
            $virtual = ' (v)';
          }
    
          if ($a_redundancy['int_type'] == 4 || $a_redundancy['int_type'] == 6) {
            $linkstart = "<a href=\"http://" . $a_redundancy['int_addr'] . "\" target=\"_blank\">";
            $linkend = "</a>";
          } else {
            $linkstart = "";
            $linkend = "";
          }
    
          $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
          $output .= "<td" . $intnote . ">> "            . $a_redundancy['int_server'] . $group                 . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['int_face'] . $virtual                 . "</td>\n";
          if (return_Virtual($a_inventory['inv_id']) == 0) {
            $output .= "<td" . $intnote . ">"            . $a_redundancy['int_sysport']                         . "</td>\n";
          }
          $output .= "<td" . $intnote . ">"              . $showmac                                            . "</td>\n";
          $output .= "<td" . $intnote . ">" . $linkstart . $a_redundancy['int_addr']     . $showmask . $linkend . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['zone_name']                           . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['int_gate']                            . "</td>\n";
          if (return_Virtual($a_inventory['inv_id']) == 0) {
            $output .= "<td" . $intnote . ">"            . $a_redundancy['int_switch']                          . "</td>\n";
            $output .= "<td" . $intnote . ">"            . $a_redundancy['int_port']                            . "</td>\n";
          }
          $output .= "<td" . $intnote . ">"              . $a_redundancy['itp_acronym']                         . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['int_update']                          . "</td>\n";
          $output .= "</tr>\n";
        }
      }
      $output .= "</table>\n";
    }

# routing table
    if (substr($action, 0, 1) == 'r' || $action == "*" || substr($action, 0, 1) == 'a') {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"6\">Routing Table</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Destination</th>\n";
      $output .= "  <th>Gateway</th>\n";
      $output .= "  <th>Netmask</th>\n";
      $output .= "  <th>Interface</th>\n";
      $output .= "  <th>Description</th>\n";
      $output .= "  <th>Last</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select route_address,route_gateway,route_mask,route_interface,route_desc,route_verified,int_face,route_update ";
      $q_string .= "from routing ";
      $q_string .= "left join interface on interface.int_id = route_interface ";
      $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by route_address";
      $q_routing = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      while ($a_routing = mysql_fetch_array($q_routing)) {
        if ($a_routing['route_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_routing['route_address']                 . "</td>\n";
        $output .= "  <td>" . $a_routing['route_gateway']                 . "</td>\n";
        $output .= "  <td>" . createNetmaskAddr($a_routing['route_mask']) . "</td>\n";
        $output .= "  <td>" . $a_routing['int_face']                      . "</td>\n";
        $output .= "  <td>" . $a_routing['route_desc']                    . "</td>\n";
        $output .= "  <td>" . $a_routing['route_update']                  . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

# prevent this test section from executing while it's being worked on
    $action = '';
# issue table
    if (substr($action, 0, 1) == 'i' || $action == "*" || substr($action, 0, 1) == 'a') {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=7>Issue Tracker</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Destination</th>\n";
      $output .= "  <th>Gateway</th>\n";
      $output .= "  <th>Netmask</th>\n";
      $output .= "  <th>Interface</th>\n";
      $output .= "  <th>Description</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select route_address,route_gateway,route_mask,route_interface,route_desc,route_verified,int_face from routing ";
      $q_string .= "left join interface on interface.int_id = route_interface ";
      $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by route_address";
      $q_routing = mysql_query($q_string) or die($q_string . ": " . mysql_error() . "\n\n");
      while ($a_routing = mysql_fetch_array($q_routing)) {
        if ($a_routing['route_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_routing['route_address'] . "</td>\n";
        $output .= "  <td>" . $a_routing['route_gateway'] . "</td>\n";
        $output .= "  <td>" . createNetmaskAddr($a_routing['route_mask']) . "</td>\n";
        $output .= "  <td>" . $a_routing['int_face'] . "</td>\n";
        $output .= "  <td>" . $a_routing['route_desc'] . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }


# this is the footer information

    $output .= "<table width=80%>\n";
    $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Green</strong> indicates that the information was automatically gathered from the system so is accurate as of the <strong>Last</strong> column date.</td>\n";
    $output .= "</tr>\n";
    $output .= "</table>\n\n";

    $output .= "<p><a href=\"" . $Showroot . "/inventory.php?server=" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . " Server Listing.</a></p>\n\n";

    $output .= "<p><a href=\"" . $Issueroot . "/issue.php?server=" . $a_inventory['inv_id'] . "\">" . $a_inventory['inv_name'] . " Server Issue Tracker.</a></p>\n\n";

    if (file_exists($Sitedir . "/rrdtool/" . $a_inventory['inv_name'])) {
      $output .= "<p><a href=\"" . $Siteurl . "/rrdtool/" . $a_inventory['inv_name'] . "\">" . $a_inventory['inv_name'] . " Performance Review.</a></p>\n\n";
    } else {
      $output .= "<p>Performance information unavailable.    - " . $Sitedir . "/rrdtool/" . $a_inventory['inv_name'] . "</p>\n\n";
    }

    $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    if ($debug == 'yes') {
      print "mail($email, \"Inventory: \" . $servername, $body, $headers);\n";
    } else {
      mail($email, "Inventory: " . $servername, $body, $headers);
    }
  }

?>
