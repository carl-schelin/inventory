#!/usr/local/bin/php
<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

  if ($argv[$argc - 1] == 'debug') {
    $debug = 'yes';
    $argc--;
  }

  $headers  = "From: Inventory Management <inventory@" . $hostname . ">\r\n";
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
  $q_string .= "from inv_users ";
  $q_string .= "where usr_id != 1 and usr_email = '" . $email . "'";
  if ($debug == 'yes') {
    print $q_string . "\n\n";
  }
  $q_inv_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
  $a_inv_users = mysqli_fetch_array($q_inv_users);

  logaccess($db, $a_inv_users['usr_name'], "show.inventory.submit.php", $subjectline);

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
    $servername = str_replace("*", "%", strtolower($serverlist[0]));
  }
  $productlist = str_replace("_", " ", str_replace("*", "%", strtolower($productlist)));
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
    switch ($action) {
      case 'hardware':
      case 'h':
        break;
      case 'filesystems':
      case 'f':
        break;
      case 'software':
      case 's':
        break;
      case 'network':
      case 'n':
        break;
      case 'route':
      case 'routing':
      case 'r':
        break;
      case 'issues':
      case 'i':
        break;
      case 'all':
      case 'a':
      case '*':
        break;
      default:
        $servername = 'help';
        $error = "<p><strong>Error</strong>: Invalid option: " . $action . ".</p>\n\n";
    }

# check the initials as well as the full option isn't necessary
    $firstchar = substr($action, 0, 1);
    if (strpos("*ahfsnriv", $firstchar) === false) {
      $servername = 'help';
      $error = "<p><strong>Error</strong>: Invalid option: " . $firstchar . ". Options: *ahfsnri</p>\n\n";
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
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if ($debug == 'yes') {
        print $q_string . "\n\n";
      }
      if (mysqli_num_rows($q_interface) > 0) {
        $a_interface = mysqli_fetch_array($q_interface);
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
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");

    if ($debug == 'yes') {
      print $q_string . "\n\n";
    }
    if (mysqli_num_rows($q_inventory) == 0) {
      $q_string  = "select prod_name ";
      $q_string .= "from products ";
      $q_string .= "where prod_name like '" . $productlist . "'";
      $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");

      if ($debug == 'yes') {
        print $q_string . "\n\n";
      }
      if (mysqli_num_rows($q_products) == 0) {
        $q_string  = "select int_companyid,int_addr,int_face,int_eth ";
        $q_string .= "from interface ";
        $q_string .= "where int_addr like '" . $serverip . "' ";
        $q_string .= "order by INET_ATON(int_addr) ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");

        if ($debug == 'yes') {
          print $q_string . "\n\n";
        }
        if (mysqli_num_rows($q_interface) == 0) {
          $error = "<p><strong>Error</strong>: The requested server, product, or IP was not found in the Inventory database.</p>\n\n";
          $server = "help";
        } else {
          if (mysqli_num_rows($q_interface) == 1) {
            $a_interface = mysqli_fetch_array($q_interface);

            $q_string  = "select inv_id,inv_name,inv_manager ";
            $q_string .= "from inventory ";
            $q_string .= "where inv_id = " . $a_interface['int_companyid'];
            $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");

            if ($debug == 'yes') {
              print $q_string . "\n\n";
            }
            if (mysqli_num_rows($q_inventory) > 0) {
              $a_inventory = mysqli_fetch_array($q_inventory);
              $server = $a_inventory['inv_name'];
            } else {
              $error = "<p><strong>Error</strong>: Can't find the matching server name in the Inventory database.</p>\n\n";
              $server = "help";
            }
          } else {
            $output .= "<table width=80%>\n";
            $output .= "<tr>\n";
            $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">IP Listing</th>\n";
            $output .= "</tr>\n";
            $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
            $output .= "  <th>Servername</th>\n";
            $output .= "  <th>IP Address</th>\n";
            $output .= "  <th>Interface</th>\n";
            $output .= "  <th>MAC</th>\n";
            $output .= "  <th>Managed By</th>\n";
            $output .= "</tr>\n";

            while ($a_interface = mysqli_fetch_array($q_interface)) {

              $q_string  = "select inv_name,inv_manager,inv_status ";
              $q_string .= "from inventory ";
              $q_string .= "where inv_id = " . $a_interface['int_companyid'];
              $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
              $a_inventory = mysqli_fetch_array($q_inventory);

              if ($debug == 'yes') {
                print $q_string . "\n\n";
              }
              $q_string  = "select grp_name ";
              $q_string .= "from inv_groups ";
              $q_string .= "where grp_id = '" . $a_inventory['inv_manager'] . "'";
              $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
              $a_inv_groups = mysqli_fetch_array($q_inv_groups);

              if ($debug == 'yes') {
                print $q_string . "\n\n";
              }
              if ($a_inventory['inv_status'] == 0) {
                $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
                $output .= "  <td><a href=\"mailto:inventory@" . $hostname . "?subject=" . $a_inventory['inv_name'] . "\">" . $a_inventory['inv_name'] . "</a></td>\n";
                $output .= "  <td>" . $a_interface['int_addr'] . "</td>\n";
                $output .= "  <td>" . $a_interface['int_face'] . "</td>\n";
                $output .= "  <td>" . $a_interface['int_eth'] . "</td>\n";
                $output .= "  <td>" . $a_inv_groups['grp_name'] . "</td>\n";
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
        $a_products = mysqli_fetch_array($q_products);
        $product = $a_products['prod_name'];
      }
    } else {
      if (mysqli_num_rows($q_inventory) == 1) {
        $a_inventory = mysqli_fetch_array($q_inventory);
        $server = $a_inventory['inv_name'];
      } else {
        $output .= "<table width=80%>\n";
        $output .= "<tr>\n";
        $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"3\">Server Listing</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <th>Servername</th>\n";
        $output .= "  <th>Managed By</th>\n";
        $output .= "</tr>\n";

        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          $q_string  = "select grp_name ";
          $q_strint .= "from inv_groups ";
          $q_strint .= "where grp_id = " . $a_inventory['inv_manager'];
          $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ":(1): " . mysqli_error($db) . "\n\n");
          $a_inv_groups = mysqli_fetch_array($q_inv_groups);

          if ($debug == 'yes') {
            print $q_string . "\n\n";
          }
          $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
          $output .= "  <td><a href=\"mailto:inventory@" . $hostname . "?subject=" . $a_inventory['inv_name'] . "\">" . $a_inventory['inv_name'] . "</a></td>\n";
          $output .= "  <td>" . $a_inv_groups['grp_name'] . "</td>\n";
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
    $body .= "<p>To: inventory@" . $hostname . "\n";
    $body .= "<br>Subject: {servername} or {serverip} [keyword option]\n";
    $body .= "<br>Subject: {product}\n";
    $body .= "<br>Subject: [{empty subject} or {active} or {help} or {products}</p>\n\n";

    $body .= "<p>The Subject line consists of up to two keywords. The first can be one of five options;</p>\n";
    $body .= "<ul>\n";
    $body .= "  <li><b>{empty subject} or active</b> - If the Subject line is empty or contains the keyword 'active', a list of all active servers will be returned via e-mail.</li>\n";
    $body .= "  <li><b>help</b> - An e-mail will be returned with this message.</li>\n";
    $body .= "  <li><b>products</b> - A list of all products will be returned. <strong>NOTE:</strong> Replace spaces with underscores on the Subject line for products with more than one word.</li>\n";

    $body .= "  <li><b>{servername}</b> - An e-mail will be returned containing information about the identified server.</li>\n";
    $body .= "  <li><b>{serverip}</b> - An e-mail will be returned containing information about the server associated with the IP.</li>\n";

    $body .= "  <li><b>{product}</b> - An e-mail will be returned containing a list of all servers assigned to this Product</li>\n";
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
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Active Server Listing</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "  <th>Platform Managed By</th>\n";
    $output .= "  <th>Applications Managed By</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join inv_groups on inv_groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_status = 0 ";
    $q_string .= "group by inv_name";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {
      $q_string  = "select grp_name ";
      $q_string .= "from inv_groups ";
      $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
      $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ":(2): " . mysqli_error($db) . "\n\n");
      $a_inv_groups = mysqli_fetch_array($q_inv_groups);

      $q_string  = "select hw_active ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      $a_hardware = mysqli_fetch_array($q_hardware);
      if ($a_hardware['hw_active'] == '1971-01-01') {
        $bgcolor = $color[1];
      } else {
        $bgcolor = $color[0];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td><a href=\"mailto:inventory@" . $hostname . "?subject=" . $a_inventory['inv_name'] . "\">" . $inv_name . "</a></td>\n";
      $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
      $output .= "  <td>" . $a_inventory['grp_name']     . "</td>\n";
      $output .= "  <td>" . $a_inv_groups['grp_name']        . "</td>\n";
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
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Product Listing</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select prod_name,prod_desc ";
    $q_string .= "from products ";
    $q_string .= "order by prod_name";
    $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    while ($a_products = mysqli_fetch_array($q_products)) {
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
      print "mail($email, \"Product Listing\", $body, $headers);\n\n";
    } else {
      mail($email, "Product Listing", $body, $headers);
    }
    exit(1);
  }

  if ($product != '') {
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"4\">Product : " . $product . "</th>\n";
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
    $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_products = mysqli_fetch_array($q_products);

    $q_string  = "select inv_id,inv_name,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join inv_svr_software on inv_svr_software.svr_companyid = inventory.inv_id ";
    $q_string .= "left join software on software.sw_id = inv_svr_software.svr_softwareid ";
    $q_string .= "left join inv_groups on inv_groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_status = 0 and sw_product = " . $a_products['prod_id'] . " ";
    $q_string .= "group by inv_name";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {
      $q_string  = "select grp_name ";
      $q_string .= "from inv_groups ";
      $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
      $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ":(3): " . mysqli_error($db) . "\n\n");
      $a_inv_groups = mysqli_fetch_array($q_inv_groups);

      $q_string  = "select hw_active ";
      $q_string .= "from hardware ";
      $q_string .= "where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      $a_hardware = mysqli_fetch_array($q_hardware);
      if ($a_hardware['hw_active'] == '1971-01-01') {
        $bgcolor = $color[1];
      } else {
        $bgcolor = $color[0];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td><a href=\"mailto:inventory@" . $hostname . "?subject=" . $a_inventory['inv_name'] . "\">" . $a_inventory['inv_name'] . "</a></td>\n";
      $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
      $output .= "  <td>" . $a_inventory['grp_name']     . "</td>\n";
      $output .= "  <td>" . $a_inv_groups['grp_name']        . "</td>\n";
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

    $q_string  = "select inv_id,inv_name,inv_companyid,inv_function,inv_location,inv_product,inv_rack,";
    $q_string .= "inv_row,inv_unit,grp_name,inv_appadmin,inv_callpath,svc_acronym,inv_notes,inv_document,man_text ";
    $q_string .= "from inventory ";
    $q_string .= "left join inv_service     on inv_service.svc_id     = inventory.inv_class ";
    $q_string .= "left join inv_groups      on inv_groups.grp_id      = inventory.inv_manager ";
    $q_string .= "left join inv_maintenance on inv_maintenance.man_id = inventory.inv_maint ";
    $q_string .= "where inv_name = '" . $server . "' and inv_status = 0";
    if ($debug == 'yes') {
      print $q_string . "\n";
    }
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_inventory = mysqli_fetch_array($q_inventory);

    $q_string  = "select grp_name ";
    $q_string .= "from inv_groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
    if ($debug == 'yes') {
      print $q_string . "\n";
    }
    $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ":(4): " . mysqli_error($db) . "\n\n");
    $a_inv_groups = mysqli_fetch_array($q_inv_groups);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Server</strong>: " . $a_inventory['inv_name'] . "</td>\n";
    $output .= "  <td><strong>Service Class</strong>: " . $a_inventory['svc_acronym'] . "</td>\n";
    $output .= "  <td><strong>Function</strong>: " . $a_inventory['inv_function'] . "</td>\n";
    $output .= "  <td><strong>Platform Managed by</strong>: " . $a_inventory['grp_name'] . "</td>\n";
    $output .= "  <td><strong>Applications Managed by</strong>: " . $a_inv_groups['grp_name'] . "</td>\n";
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

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td colspan=\"5\"><strong>Maintenance Window</strong>: " . $a_inventory['man_text'] . "</td>\n";
    $output .= "</tr>\n";

    $q_string  = "select hw_active ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_hardware = mysqli_fetch_array($q_hardware);

    if ($a_hardware['hw_active'] == '1971-01-01') {
      $output .= "<tr style=\"background-color: " . $color[3] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td colspan=\"5\"><strong>Server is not in Production at this time.</strong></td>\n";
      $output .= "</tr>\n";
    } else {

      $q_string = "select prod_name,svc_name ";
      $q_string .= "from products ";
      $q_string .= "left join inv_service on inv_service.svc_id = products.prod_service ";
      $q_string .= "where prod_id = " . $a_inventory['inv_product'] . " ";
      $q_products = mysqli_query($db, $q_string) or die($q_string .= ": " . mysqli_error($db));
      $a_products = mysqli_fetch_array($q_products);

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
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_hardware = mysqli_fetch_array($q_hardware);

    $q_string  = "select sup_company,sup_phone,sup_contract,sup_hwresponse,sup_swresponse ";
    $q_string .= "from inv_support ";
    $q_string .= "where sup_id = " . $a_hardware['hw_supportid'];
    $q_inv_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_inv_support = mysqli_fetch_array($q_inv_support);

    if ($a_inv_support['sup_hwresponse'] == 0 || $a_inv_support['sup_hwresponse'] == '') {
      $hwsupport = "No Support Selected";
    } else {
      $q_string  = "select slv_value ";
      $q_string .= "from inv_supportlevel ";
      $q_string .= "where slv_id = " . $a_inv_support['sup_hwresponse'];
      $q_inv_supportlevel = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      $a_inv_supportlevel = mysqli_fetch_array($q_inv_supportlevel);
      $hwsupport = $a_inv_supportlevel['slv_value'];
    }
    
    if ($a_inv_support['sup_swresponse'] == 0 || $a_inv_support['sup_swresponse'] == '') {
      $swsupport = "No Support Selected";
    } else {
      $q_string  = "select slv_value ";
      $q_string .= "from inv_supportlevel ";
      $q_string .= "where slv_id = " . $a_inv_support['sup_swresponse'];
      $q_inv_supportlevel = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      $a_inv_supportlevel = mysqli_fetch_array($q_inv_supportlevel);
      $swsupport = $a_inv_supportlevel['slv_value'];
    }

    $output .= "<tr style=\"background-color: "    . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Company</strong>: "  . $a_inv_support['sup_company'] . "</td>\n";
    $output .= "  <td><strong>Phone</strong>: "    . $a_inv_support['sup_phone'] . "</td>\n";
    $output .= "  <td><strong>Contract</strong>: " . $a_inv_support['sup_contract'] . "</td>\n";
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
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_hardware = mysqli_fetch_array($q_hardware);

    $q_string  = "select ven_name,mod_name ";
    $q_string .= "from inv_models ";
    $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
    $q_string .= "where mod_id = " . $a_hardware['hw_vendorid'] . " ";
    $q_inv_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_inv_models = mysqli_fetch_array($q_inv_models);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Vendor</strong>: "           . $a_inv_models['ven_name']     . "</td>\n";
    $output .= "  <td><strong>Model</strong>: "            . $a_inv_models['mod_name']     . "</td>\n";
    $output .= "  <td><strong>Serial Number</strong>: "    . $a_hardware['hw_serial']  . "</td>\n";
    $output .= "  <td><strong>Asset Tag</strong>: "        . $a_hardware['hw_asset']   . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


# table four: basic software information
    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Primary Software/Operating System Information</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select sw_software,ven_name ";
    $q_string .= "from software ";
    $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = software.sw_id ";
    $q_string .= "left join inv_sw_types on inv_sw_types.typ_id = software.sw_type ";
    $q_string .= "left join inv_vendors on inv_vendors.ven_id = software.sw_vendor ";
    $q_string .= "where svr_companyid = " . $a_inventory['inv_id'] . " and typ_name = 'OS' ";
    $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_software = mysqli_fetch_array($q_software);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Software</strong>: " . $a_software['sw_software'] . "</td>\n";
    $output .= "  <td><strong>Vendor</strong>: "   . $a_software['ven_name']   . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


# table five: optional: if a parent id, list the members.
    $q_string  = "select inv_name,inv_function,grp_name,inv_appadmin ";
    $q_string .= "from inventory ";
    $q_string .= "left join inv_groups on inv_groups.grp_id = inventory.inv_manager ";
    $q_string .= "where inv_companyid = " . $a_inventory['inv_id'] . " and inv_status = 0 ";
    $q_string .= "order by inv_name ";
    $q_cluster = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_cluster) > 0) {
      $output .= "<table width=80%>\n";

      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"5\">Parent Information</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Server</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Function</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Platform</th>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">Application</th>\n";
      $output .= "</tr>\n";
      
      while ($a_cluster = mysqli_fetch_array($q_cluster)) {

        $q_string  = "select grp_name ";
        $q_string .= "from inv_groups ";
        $q_string .= "where grp_id = " . $a_cluster['inv_appadmin'] . " ";
        $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_inv_groups = mysqli_fetch_array($q_inv_groups);

        $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_cluster['inv_name']     . "</td>\n";
        $output .= "  <td>" . $a_cluster['inv_function'] . "</td>\n";
        $output .= "  <td>" . $a_cluster['grp_name'] . "</td>\n";
        $output .= "  <td>" . $a_inv_groups['grp_name'] . "</td>\n";
        $output .= "</tr>\n";

      }
      $output .= "</table>\n\n";
    }


# table seven: location information including parent if it exists
    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=\"3\">Location Information</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select loc_name,loc_addr1,ct_city,st_acronym,loc_zipcode,cn_acronym ";
    $q_string .= "from inv_locations ";
    $q_string .= "left join inv_cities  on inv_cities.ct_id  = inv_locations.loc_city ";
    $q_string .= "left join inv_states  on inv_states.st_id  = inv_cities.ct_state ";
    $q_string .= "left join inv_country on inv_country.cn_id = inv_states.st_country ";
    $q_string .= "where loc_id = " . $a_inventory['inv_location'];
    $q_inv_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
    $a_inv_locations = mysqli_fetch_array($q_inv_locations);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Data Center</strong>: " . $a_inv_locations['loc_name'] . "</td>\n";
    $output .= "  <td><strong>Location</strong>: " . $a_inv_locations['loc_addr1'] . "  " . $a_inv_locations['ct_city'] . ", " . $a_inv_locations['st_acronym'] . " " . $a_inv_locations['loc_zipcode'] . " (" . $a_inv_locations['cn_acronym'] . ")</td>\n";

# show any parents of this system
    if ($a_inventory['inv_companyid']) {
      $q_string  = "select inv_name,inv_rack,inv_row,inv_unit ";
      $q_string .= "from inventory ";
      $q_string .= "where inv_id = " . $a_inventory['inv_companyid'] . " ";
      $q_parent = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_parent = mysqli_fetch_array($q_chassis);

      $output .= "  <td><strong>Parent Device</strong>: " . $a_parent['inv_name'] . "</td>\n";
      $output .= "  <td><strong>Parent Rack/Unit</strong>: " . $a_parent['inv_rack'] . "-" . $a_parent['inv_row'] . "/U" . $a_parent['inv_unit'] . "</td>\n";
      $output .= "  <td><strong>Blade Number</strong>: " . $a_inventory['inv_unit'] . "</td>\n";
    } else {
      $output .= "  <td><strong>Rack/Unit</strong>: " . $a_inventory['inv_rack'] . "-" . $a_inventory['inv_row'] . "/U" . $a_inventory['inv_unit'] . "</td>\n";
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

      $q_string  = "select hw_serial,hw_asset,hw_vendorid,part_name,hw_verified,hw_update ";
      $q_string .= "from hardware ";
      $q_string .= "left join inv_parts on inv_parts.part_id = hardware.hw_type ";
      $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by part_name";
      $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_hardware = mysqli_fetch_array($q_hardware)) {
        $q_string  = "select ven_name,mod_name,mod_size,mod_speed ";
        $q_string .= "from inv_models ";
        $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_models.mod_vendor ";
        $q_string .= "where mod_id = " . $a_hardware['hw_vendorid'] . " ";
        $q_inv_models = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_inv_models = mysqli_fetch_array($q_inv_models);

        if ($a_hardware['hw_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_hardware['hw_serial']  . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_asset']   . "</td>\n";
        $output .= "  <td>" . $a_inv_models['ven_name']     . "</td>\n";
        $output .= "  <td>" . $a_inv_models['mod_name']     . "</td>\n";
        $output .= "  <td>" . $a_inv_models['mod_size']     . "</td>\n";
        $output .= "  <td>" . $a_inv_models['mod_speed']    . "</td>\n";
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
      $q_string .= "from inv_filesystem ";
      $q_string .= "where fs_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by fs_device,fs_mount";
      $q_inv_filesystem = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ( $a_inv_filesystem = mysqli_fetch_array($q_inv_filesystem) ) {

        if ($a_filesystem['fs_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">";
        $output .= "<td>" . $a_inv_filesystem['fs_device'] . "</td>\n";
        $output .= "<td>" . $a_inv_filesystem['fs_size']   . "</td>\n";
        $output .= "<td>" . $a_inv_filesystem['fs_volume'] . "</td>\n";
        $output .= "<td>" . $a_inv_filesystem['fs_mount']  . "</td>\n";
        $output .= "<td>" . $a_inv_filesystem['fs_wwid']   . "</td>\n";
        $output .= "<td>" . $a_inv_filesystem['fs_update'] . "</td>\n";
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

      $q_string  = "select sw_product,ven_name,sw_software,typ_name,svr_groupid,svr_verified,svr_update ";
      $q_string .= "from software ";
      $q_string .= "left join inv_svr_software on inv_svr_software.svr_softwareid = software.sw_id ";
      $q_string .= "left join inv_vendors on inv_vendors.ven_id = software.sw_vendor ";
      $q_string .= "left join inv_sw_types on inv_sw_types.typ_id = software.sw_type ";
      $q_string .= "where (typ_name != 'PKG' and typ_name != 'RPM') and svr_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by sw_software";
      $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_software = mysqli_fetch_array($q_software)) {
        $q_string = "select prod_name from products where prod_id = " . $a_software['sw_product'];
        $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
        $a_products = mysqli_fetch_array($q_products);

        $q_string = "select grp_name from inv_groups where grp_id = " . $a_software['svr_groupid'];
        $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ":(5): " . mysqli_error($db) . "\n\n");
        $a_inv_groups = mysqli_fetch_array($q_inv_groups);

        if ($a_software['svr_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_products['prod_name']   . "</td>\n";
        $output .= "  <td>" . $a_software['ven_name']   . "</td>\n";
        $output .= "  <td>" . $a_software['sw_software'] . "</td>\n";
        $output .= "  <td>" . $a_software['sw_type']     . "</td>\n";
        $output .= "  <td>" . $a_inv_groups['grp_name']      . "</td>\n";
        $output .= "  <td>" . $a_software['svr_update']   . "</td>\n";
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
      if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
        $output .= "  <th>Physical Port</th>\n";
      }
      $output .= "  <th>MAC Address</th>\n";
      $output .= "  <th>IP Address/Netmask</th>\n";
      $output .= "  <th>Zone</th>\n";
      $output .= "  <th>Gateway</th>\n";
      if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
        $output .= "  <th>Switch</th>\n";
        $output .= "  <th>Port</th>\n";
      }
      $output .= "  <th>Type</th>\n";
      $output .= "  <th>Last</th>\n";
      $output .= "</tr>\n";
    
      $q_string = "select int_id,int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,"
                .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_zone,int_domain "
                . "from interface "
                . "left join net_zones on interface.int_zone = net_zones.zone_id  "
                . "left join inv_int_types on interface.int_type = inv_int_types.itp_id "
                . "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = 0 and int_ip6 = 0 "
                . "order by int_face,int_addr";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    
      while ( $a_interface = mysqli_fetch_array($q_interface) ) {
    
        if ($a_interface['int_domain'] == '') {
          $domain = $a_interface['int_server'];
        } else {
          $domain = $a_interface['int_server'] . "." . $a_interface['int_domain'];
        }
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
        $output .= "<td" . $intnote . ">"              . $domain . $redundancy            . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_face'] . $virtual                 . "</td>\n";
        if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
          $output .= "<td" . $intnote . ">"            . $a_interface['int_sysport']                         . "</td>\n";
        }
        $output .= "<td" . $intnote . ">"              . $showmac                                            . "</td>\n";
        $output .= "<td" . $intnote . ">" . $linkstart . $a_interface['int_addr']     . $showmask . $linkend . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['zone_zone']                           . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_gate']                            . "</td>\n";
        if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
          $output .= "<td" . $intnote . ">"            . $a_interface['int_switch']                          . "</td>\n";
          $output .= "<td" . $intnote . ">"            . $a_interface['int_port']                            . "</td>\n";
        }
        $output .= "<td" . $intnote . ">"              . $a_interface['itp_acronym']                         . "</td>\n";
        $output .= "<td" . $intnote . ">"              . $a_interface['int_update']                          . "</td>\n";
        $output .= "</tr>\n";
    
    
        $q_string = "select int_id,int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,int_domain,"
                  .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_zone,int_groupname "
                  . "from interface "
                  . "left join net_zones on interface.int_zone = net_zones.zone_id  "
                  . "left join inv_int_types on interface.int_type = inv_int_types.itp_id "
                  . "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = " . $a_interface['int_id'] . " and int_ip6 = 0 "
                  . "order by int_face,int_addr";
        $q_redundancy = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    
        while ( $a_redundancy = mysqli_fetch_array($q_redundancy) ) {
    
          if ($a_redundancy['int_domain'] == '') {
            $domain = $a_redundancy['int_server'];
          } else {
            $domain = $a_redundancy['int_server'] . "." . $a_redundancy['int_domain'];
          }
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
          $output .= "<td" . $intnote . ">> "            . $domain . $group                 . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['int_face'] . $virtual                 . "</td>\n";
          if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
            $output .= "<td" . $intnote . ">"            . $a_redundancy['int_sysport']                         . "</td>\n";
          }
          $output .= "<td" . $intnote . ">"              . $showmac                                            . "</td>\n";
          $output .= "<td" . $intnote . ">" . $linkstart . $a_redundancy['int_addr']     . $showmask . $linkend . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['zone_zone']                           . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['int_gate']                            . "</td>\n";
          if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
            $output .= "<td" . $intnote . ">"            . $a_redundancy['int_switch']                          . "</td>\n";
            $output .= "<td" . $intnote . ">"            . $a_redundancy['int_port']                            . "</td>\n";
          }
          $output .= "<td" . $intnote . ">"              . $a_redundancy['itp_acronym']                         . "</td>\n";
          $output .= "<td" . $intnote . ">"              . $a_redundancy['int_update']                          . "</td>\n";
          $output .= "</tr>\n";

          $q_string = "select int_server,int_face,int_addr,int_eth,int_mask,int_verified,int_sysport,int_redundancy,int_virtual,int_domain,"
                    .        "int_switch,int_port,int_primary,itp_acronym,int_gate,int_note,int_update,int_type,zone_zone,int_groupname "
                    . "from interface "
                    . "left join net_zones on interface.int_zone = net_zones.zone_id  "
                    . "left join inv_int_types on interface.int_type = inv_int_types.itp_id "
                    . "where int_companyid = " . $a_inventory['inv_id'] . " and int_int_id = " . $a_redundancy['int_id'] . " and int_ip6 = 0 "
                    . "order by int_face,int_addr";
          $q_secondary = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    
          while ( $a_secondary = mysqli_fetch_array($q_secondary) ) {
    
            if ($a_secondary['int_domain'] == '') {
              $domain = $a_secondary['int_server'];
            } else {
              $domain = $a_secondary['int_server'] . "." . $a_secondary['int_domain'];
            }
            $intnote = " title=\"" . $a_secondary['int_note'] . "\"";
            if ($a_secondary['int_verified'] == 1) {
              $bgcolor = $color[1];
            } else {
              $bgcolor = $color[0];
            }
            if ($a_secondary['int_eth'] == '00:00:00:00:00:00' ) {
              $showmac = '';
            } else {
              $showmac = $a_secondary['int_eth'];
            }
            if ($a_secondary['int_addr'] == '' ) {
              $showmask = '';
            } else {
              $showmask = '/' . $a_secondary['int_mask'];
            }
            $group = '';
            if ($a_secondary['int_groupname'] != '') {
              $group = ' (' . $a_secondary['int_groupname'] . ')';
            }
            $virtual = '';
            if ($a_secondary['int_virtual'] == 1 ) {
              $virtual = ' (v)';
            }
    
            if ($a_secondary['int_type'] == 4 || $a_secondary['int_type'] == 6) {
              $linkstart = "<a href=\"http://" . $a_secondary['int_addr'] . "\" target=\"_blank\">";
              $linkend = "</a>";
            } else {
              $linkstart = "";
              $linkend = "";
            }
    
            $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
            $output .= "<td" . $intnote . ">>> "            . $domain . $group                 . "</td>\n";
            $output .= "<td" . $intnote . ">"              . $a_secondary['int_face'] . $virtual                 . "</td>\n";
            if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
              $output .= "<td" . $intnote . ">"            . $a_secondary['int_sysport']                         . "</td>\n";
            }
            $output .= "<td" . $intnote . ">"              . $showmac                                            . "</td>\n";
            $output .= "<td" . $intnote . ">" . $linkstart . $a_secondary['int_addr']     . $showmask . $linkend . "</td>\n";
            $output .= "<td" . $intnote . ">"              . $a_secondary['zone_zone']                           . "</td>\n";
            $output .= "<td" . $intnote . ">"              . $a_secondary['int_gate']                            . "</td>\n";
            if (return_Virtual($db, $a_inventory['inv_id']) == 0) {
              $output .= "<td" . $intnote . ">"            . $a_secondary['int_switch']                          . "</td>\n";
              $output .= "<td" . $intnote . ">"            . $a_secondary['int_port']                            . "</td>\n";
            }
            $output .= "<td" . $intnote . ">"              . $a_secondary['itp_acronym']                         . "</td>\n";
            $output .= "<td" . $intnote . ">"              . $a_secondary['int_update']                          . "</td>\n";
            $output .= "</tr>\n";
          }
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
      $q_string .= "from inv_routing ";
      $q_string .= "left join interface on interface.int_id = route_interface ";
      $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by route_address";
      $q_inv_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_inv_routing = mysqli_fetch_array($q_inv_routing)) {
        if ($a_inv_routing['route_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_inv_routing['route_address']                 . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['route_gateway']                 . "</td>\n";
        $output .= "  <td>" . createNetmaskAddr($a_inv_routing['route_mask']) . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['int_face']                      . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['route_desc']                    . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['route_update']                  . "</td>\n";
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

      $q_string  = "select route_address,route_gateway,route_mask,route_interface,";
      $q_string .= "route_desc,route_verified,int_face ";
      $q_string .= "from inv_routing ";
      $q_string .= "left join interface on interface.int_id = route_interface ";
      $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " ";
      $q_string .= "order by route_address";
      $q_inv_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n\n");
      while ($a_inv_routing = mysqli_fetch_array($q_inv_routing)) {
        if ($a_inv_routing['route_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_inv_routing['route_address'] . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['route_gateway'] . "</td>\n";
        $output .= "  <td>" . createNetmaskAddr($a_inv_routing['route_mask']) . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['int_face'] . "</td>\n";
        $output .= "  <td>" . $a_inv_routing['route_desc'] . "</td>\n";
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
      print "mail($email, \"Inventory: \" . $server, $body, $headers);\n";
    } else {
      mail($email, "Inventory: " . $server, $body, $headers);
    }
  }

  mysqli_close($db);

?>
