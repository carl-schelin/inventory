#!/usr/local/bin/php
<?php
  include('/usr/local/httpd/htsecure/inventory/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $color[0] = "#ffffcc";  # set to the background color of yellow.
  $color[1] = "#bced91";
  $color[2] = "yellow";
  $color[3] = "#fa8072";

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
    exit(1);
  } else {
    $email = $argv[1];
  }

  $q_string = "select usr_name from users where usr_id != 1 and usr_email = '" . $email . "'";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
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

# if script, e-mail, and server name is sent
  $action = '';
  if ($argc == 3) {
    $action = '';
  } else {
    $action = $argv[3];
  }

  $servername = strtolower($servername);
  $productlist = strtolower($productlist);
  $action = strtolower($action);
  $product = '';
  $server = '';

  if ($servername == 'help' || $servername == 'active' || $servername == "products") {
    $server = $servername;
  } else {
    $q_string = "select inv_id,inv_name,inv_manager from inventory where inv_name like '" . $servername . "%' and inv_status = 0 order by inv_name";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_inventory) == 0) {
      $q_string = "select prod_name from products where prod_name = '" . $productlist . "'";
      $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_products) == 0) {
        $server = "help";
      } else {
        $product = $productlist;
      }
    } else {
      if (mysql_num_rows($q_inventory) == 1) {
        $server = $servername;
      } else {
        $output .= "<table width=80%>\n";
        $output .= "<tr>\n";
        $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Intrado: Partial Server Listing</th>\n";
        $output .= "</tr>\n";
        $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <th>Servername</th>\n";
        $output .= "  <th>Managed By</th>\n";
        $output .= "</tr>\n";

        while ($a_inventory = mysql_fetch_array($q_inventory)) {
          $q_string = "select grp_name from groups where grp_id = " . $a_inventory['inv_manager'];
          $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
          $a_groups = mysql_fetch_array($q_groups);

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

        mail($email, "Inventory: Partial Server List", $body, $headers);
        exit(1);
      }
    }
  }

  if ($server == 'help') {
    $body  = "<p>Your Input:</p>\n";
    $body .= "<p>" . $subjectline . "</p>\n\n";
    $body .= "<p>Usage:</p>\n";
    $body .= "<p>To: inventory@incojs01.scc911.com\n";
    $body .= "<br>Subject: [{servername} | {intrado product} | help | active | products | {blank}] [keyword]</p>\n\n";

    $body .= "<p>The Subject line consists of up to two keywords. The first can be one of five options;</p>\n";
    $body .= "<ul>\n";
    $body .= "  <li><b>{blank} or active</b> - If the Subject line is empty or contains 'active', a list of all active servers will be returned via e-mail.</li>\n";
    $body .= "  <li><b>{servername}</b> - An e-mail will be returned containing information about the identified server. If it's a partial name (like inco or lnmtco), then a partial listing of servers will be returned.</li>\n";
    $body .= "  <li><b>{intrado product}</b> - An e-mail will be returned containing a list of all servers assigned to this Intrado Product</li>\n";
    $body .= "  <li><b>products</b> - A list of all Intrado products will be returned.</li>\n";
    $body .= "  <li><b>help</b> - An e-mail will be returned with this message.</li>\n";
    $body .= "</ul>\n\n";

    $body .= "<p>The second keyword describes what information you want to retrieve. This only works if the first keyword is the name of a server.</p>\n";
    $body .= "<ul>\n";
    $body .= "  <li><b>{blank}</b> - An e-mail will be returned containing basic details about the requested server.</li>\n";
    $body .= "  <li><b>*</b> - An e-mail will be returned containing details from all the following keywords.</li>\n";
    $body .= "  <li><b>hardware</b> - An e-mail will be returned containing minimal details plus a list of the hardware.</li>\n";
    $body .= "  <li><b>software</b> - An e-mail will be returned containing minimal details plus a list of the installed software, not including the list of installed packages.</li>\n";
    $body .= "  <li><b>interface</b> - An e-mail will be returned containing minimal details plus a list of the active interfaces.</li>\n";
    $body .= "</ul>\n\n";

    $body .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

    $body .= "</body>\n";
    $body .= "</html>\n";

    mail($email, "Inventory: Help", $body, $headers);
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
    $output .= "  <th>Managed By</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_function,inv_manager from inventory where inv_status = 0 group by inv_name";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_inventory = mysql_fetch_array($q_inventory)) {
      $q_string = "select grp_name from groups where grp_id = " . $a_inventory['inv_manager'];
      $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_groups = mysql_fetch_array($q_groups);

      $q_string = "select hw_active from hardware where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_hardware = mysql_fetch_array($q_hardware);
      if ($a_hardware['hw_active'] == '0000-00-00') {
        $bgcolor = $color[1];
      } else {
        $bgcolor = $color[0];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
      $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";

    $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    mail($email, "Inventory: Active Server Listing", $body, $headers);
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

    $q_string = "select prod_name,prod_desc from products order by prod_name";
    $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_products = mysql_fetch_array($q_products)) {
      $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_products['prod_name'] . "</td>\n";
      $output .= "  <td>" . $a_products['prod_desc'] . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";

    $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    mail($email, "Intrado Product Listing", $body, $headers);
    exit(1);
  }

  if ($product != '') {
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Intrado Product : " . $product . "</th>\n";
    $output .= "</tr>\n";
    $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <th>Servername</th>\n";
    $output .= "  <th>Function</th>\n";
    $output .= "  <th>Managed By</th>\n";
    $output .= "</tr>\n";

    $q_string = "select prod_id from products where prod_name = '" . $product . "'";
    $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_products = mysql_fetch_array($q_products);

    $q_string  = "select inv_id,inv_name,inv_function,inv_manager from inventory left join software on inventory.inv_id = software.sw_companyid ";
    $q_string .= "where inv_status = 0 and sw_product = " . $a_products['prod_id'] . " group by inv_name";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_inventory = mysql_fetch_array($q_inventory)) {
      $q_string = "select grp_name from groups where grp_id = " . $a_inventory['inv_manager'];
      $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_groups = mysql_fetch_array($q_groups);

      $q_string = "select hw_active from hardware where hw_primary = 1 and hw_companyid = " . $a_inventory['inv_id'];
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_hardware = mysql_fetch_array($q_hardware);
      if ($a_hardware['hw_active'] == '0000-00-00') {
        $bgcolor = $color[1];
      } else {
        $bgcolor = $color[0];
      }

      $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $output .= "  <td>" . $a_inventory['inv_function'] . "</td>\n";
      $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
      $output .= "</tr>\n";
    }
    $output .= "</table>\n\n";

    $output .= "<br>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    mail($email, "Inventory: " . $product . " server list", $body, $headers);
    exit(1);

  } else {
    $output .= "<table width=80%>\n";
    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=3>Inventory Management</th>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_function,inv_location,inv_rack,inv_row,inv_unit,inv_manager,inv_product ";
    $q_string .= "from inventory where inv_name = '" . $servername . "' and inv_status = 0";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_inventory = mysql_fetch_array($q_inventory) or die($q_string . ": " . mysql_error());

    $q_string = "select grp_name from groups where grp_id = " . $a_inventory['inv_manager'];
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Server</strong>: " . $a_inventory['inv_name'] . "</td>\n";
    $output .= "  <td><strong>Function</strong>: " . $a_inventory['inv_function'] . "</td>\n";
    $output .= "  <td><strong>Managed by</strong>: " . $a_groups['grp_name'] . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=5>Support Information</th>\n";
    $output .= "</tr>\n";

    $q_string = "select hw_supportid from hardware where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_hardware = mysql_fetch_array($q_hardware);

    $q_string  = "select sup_company,sup_phone,sup_contract,sup_hwresponse,sup_swresponse from support where sup_id = " . $a_hardware['hw_supportid'];
    $q_support = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_support = mysql_fetch_array($q_support);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Company</strong>: " . $a_support['sup_company'] . "</td>\n";
    $output .= "  <td><strong>Phone</strong>: " . $a_support['sup_phone'] . "</td>\n";
    $output .= "  <td><strong>Contract</strong>: " . $a_support['sup_contract'] . "</td>\n";
    $output .= "  <td><strong>Hardware</strong>: " . $a_support['sup_hwresponse'] . "</td>\n";
    $output .= "  <td><strong>Software</strong>: " . $a_support['sup_swresponse'] . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=5>Primary Hardware Information</th>\n";
    $output .= "</tr>\n";

    $q_string = "select hw_serial,hw_asset,hw_service,hw_vendorid from hardware where hw_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1";
    $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_hardware = mysql_fetch_array($q_hardware);

    $q_string  = "select mod_vendor,mod_name from models where mod_id = " . $a_hardware['hw_vendorid'];
    $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_models = mysql_fetch_array($q_models);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><strong>Vendor</strong>: " . $a_models['mod_vendor'] . "</td>\n";
    $output .= "  <td><strong>Model</strong>: " . $a_models['mod_name'] . "</td>\n";
    $output .= "  <td><strong>Serial</strong>: " . $a_hardware['hw_serial'] . "</td>\n";
    $output .= "  <td><strong>Asset Tag</strong>: " . $a_hardware['hw_asset'] . "</td>\n";
    $output .= "  <td><strong>Service</strong>: " . $a_hardware['hw_service'] . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";


    $output .= "<table width=80%>\n";

    $output .= "<tr>\n";
    $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=4>Location Information</th>\n";
    $output .= "</tr>\n";

    $q_string = "select loc_name,loc_addr1,loc_city,loc_state,loc_zipcode,loc_country from locations where loc_id = " . $a_inventory['inv_location'];
    $q_locations = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_locations = mysql_fetch_array($q_locations);

    $output .= "<tr style=\"background-color: " . $color[0] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td>" . $a_locations['loc_name'] . "</td>\n";
    $output .= "  <td>" . $a_locations['loc_addr1'] . "</td>\n";
    $output .= "  <td>" . $a_locations['loc_city'] . ", " . $a_locations['loc_state'] . " " . $a_locations['loc_zipcode'] . " (" . $a_locations['loc_country'] . ")</td>\n";
    $output .= "  <td>" . $a_inventory['inv_rack'] . "-" . $a_inventory['inv_row'] . "/U" . $a_inventory['inv_unit'] . "</td>\n";
    $output .= "</tr>\n";

    $output .= "</table>\n\n";

    if ($action == "hardware" || $action == "*") {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=8>Full Hardware Listing</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Serial Number</th>\n";
      $output .= "  <th>Asset Tag</th>\n";
      $output .= "  <th>Service Tag (Dell)</th>\n";
      $output .= "  <th>Vendor</th>\n";
      $output .= "  <th>Model</th>\n";
      $output .= "  <th>Size</th>\n";
      $output .= "  <th>Speed</th>\n";
      $output .= "  <th>Type</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select hw_serial,hw_asset,hw_service,hw_vendorid,hw_size,hw_speed,part_name,hw_verified from hardware left join parts on parts.part_id = hardware.hw_type ";
      $q_string .= "where hw_deleted = 0 and hw_companyid = " . $a_inventory['inv_id'] . " order by part_name";
      $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_hardware = mysql_fetch_array($q_hardware)) {
        $q_string  = "select mod_vendor,mod_name,mod_size,mod_speed from models where mod_id = " . $a_hardware['hw_vendorid'];
        $q_models = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_models = mysql_fetch_array($q_models);

        if ($a_hardware['hw_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_hardware['hw_serial'] . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_asset'] . "</td>\n";
        $output .= "  <td>" . $a_hardware['hw_service'] . "</td>\n";
        $output .= "  <td>" . $a_models['mod_vendor'] . "</td>\n";
        $output .= "  <td>" . $a_models['mod_name'] . "</td>\n";
        $output .= "  <td>" . $a_models['mod_size'] . "</td>\n";
        $output .= "  <td>" . $a_models['mod_speed'] . "</td>\n";
        $output .= "  <td>" . $a_hardware['part_name'] . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

    if ($action == "software" || $action == "*") {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=5>Software Listing</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Product</th>\n";
      $output .= "  <th>Vendor</th>\n";
      $output .= "  <th>Software</th>\n";
      $output .= "  <th>Type</th>\n";
      $output .= "  <th>Support Group</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select sw_product,sw_vendor,sw_software,sw_type,sw_group,sw_verified from software ";
      $q_string .= "where (sw_type != 'PKG' and sw_type != 'RPM') and sw_companyid = " . $a_inventory['inv_id'] . " order by sw_software";
      $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_software = mysql_fetch_array($q_software)) {
        $q_string = "select prod_name from products where prod_id = " . $a_software['sw_product'];
        $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_products = mysql_fetch_array($q_products);

        $q_string = "select grp_name from groups where grp_id = " . $a_software['sw_group'];
        $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_groups = mysql_fetch_array($q_groups);

        if ($a_software['sw_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_products['prod_name'] . "</td>\n";
        $output .= "  <td>" . $a_software['sw_vendor'] . "</td>\n";
        $output .= "  <td>" . $a_software['sw_software'] . "</td>\n";
        $output .= "  <td>" . $a_software['sw_type'] . "</td>\n";
        $output .= "  <td>" . $a_groups['grp_name'] . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

    if ($action == "interface" || $action == "*") {
      $output .= "<table width=80%>\n";
      $output .= "<tr>\n";
      $output .= "  <th style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\" colspan=7>Interfaces</th>\n";
      $output .= "</tr>\n";
      $output .= "<tr style=\"background-color: #99ccff; border: 1px solid #000000; font-size: 75%;\">\n";
      $output .= "  <th>Name</th>\n";
      $output .= "  <th>Interface</th>\n";
      $output .= "  <th>IP Address</th>\n";
      $output .= "  <th>MAC Address</th>\n";
      $output .= "  <th>Subnet</th>\n";
      $output .= "  <th>Gateway</th>\n";
      $output .= "  <th>Type</th>\n";
      $output .= "</tr>\n";

      $q_string  = "select int_server,int_face,int_ip6,int_addr,int_eth,int_mask,int_gate,int_verified,int_type from interface ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " order by int_face";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_interface = mysql_fetch_array($q_interface)) {
        $q_string  = "select itp_acronym from inttype where itp_id = " . $a_interface['int_type'];
        $q_inttype = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_inttype = mysql_fetch_array($q_inttype);

        if ($a_interface['int_verified'] == 1) {
          $bgcolor = $color[1];
        } else {
          $bgcolor = $color[0];
        }

        $output .= "<tr style=\"background-color: " . $bgcolor . "; border: 1px solid #000000; font-size: 75%;\">\n";
        $output .= "  <td>" . $a_interface['int_server'] . "</td>\n";
        $output .= "  <td>" . $a_interface['int_face'] . "</td>\n";
        $output .= "  <td>" . $a_interface['int_addr'] . "</td>\n";
        $output .= "  <td>" . $a_interface['int_eth'] . "</td>\n";
        $output .= "  <td>" . $a_interface['int_mask'] . "</td>\n";
        $output .= "  <td>" . $a_interface['int_gate'] . "</td>\n";
        $output .= "  <td>" . $a_inttype['itp_acronym'] . "</td>\n";
        $output .= "</tr>\n";
      }
      $output .= "</table>\n\n";
    }

    $output .= "<table width=80%>\n";
    $output .= "<tr style=\"background-color: " . $color[1] . "; border: 1px solid #000000; font-size: 75%;\">\n";
    $output .= "  <td><b>Green</b> indicates that the information was automatically gathered from the system so is accurate.</td>\n";
    $output .= "</tr>\n";
    $output .= "</table>\n\n";

    $output .= "<p>This mail box is not monitored, please do not reply.</p>\n\n";

    $output .= "</body>\n";
    $output .= "</html>\n";

    $body = $output;

    mail($email, "Inventory: " . $servername, $body, $headers);
  }

?>
