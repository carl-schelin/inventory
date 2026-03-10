<?php
# Script: connect.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "connect.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_connect");

      $q_string  = "select con_sourceid,con_targetid,con_type,pt_name ";
      $q_string .= "from inv_connect ";
      $q_string .= "left join inv_powertype on inv_powertype.pt_id = inv_connect.con_type ";
      $q_string .= "where con_id = " . $formVars['id'];
      $q_inv_connect = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      $a_inv_connect = mysqli_fetch_array($q_inv_connect);
      mysqli_free_result($q_inv_connect);

      $contype = return_Index($db, $a_inv_connect['con_type'], "select pt_id from inv_powertype order by pt_name");

# network port
      if ($a_inv_connect['pt_name'] == 'Network Interface') {
        $q_string  = "select pat_id,pat_name,ast_name ";
        $q_string .= "from inv_patch ";
        $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
        $q_string .= "order by ast_name,pat_name ";
        $consourceid = return_Index($db, $a_inv_connect['con_sourceid'], $q_string);

        $q_string  = "select pat_id,pat_name,ast_name ";
        $q_string .= "from inv_patch ";
        $q_string .= "left join inv_assets on inv_assets.ast_id = inv_patch.pat_deviceid ";
        $q_string .= "order by pat_name,ast_name ";
        $contargetid = return_Index($db, $a_inv_connect['con_targetid'], $q_string);

        print "document.formUpdateCat5.con_sourceid['" . $consourceid . "'].selected = true;\n";
        print "document.formUpdateCat5.con_targetid['" . $contargetid . "'].selected = true;\n";
        print "document.formUpdateCat5.con_type['"     . $contype     . "'].selected = true;\n";

        print "document.formUpdateCat5.id.value = " . $formVars['id'] . ";\n";
      }

# power port
      if ($a_inv_connect['pt_name'] == 'Power') {
        $q_string  = "select port_id,port_name,ast_name ";
        $q_string .= "from inv_ports ";
        $q_string .= "left join inv_assets on inv_assets.ast_id = inv_ports.port_deviceid ";
        $q_string .= "order by ast_name,port_name ";
        $consourceid = return_Index($db, $a_inv_connect['con_sourceid'], $q_string);

        $q_string  = "select out_id,out_name,ast_name ";
        $q_string .= "from inv_outlets ";
        $q_string .= "left join inv_assets on inv_assets.ast_id = inv_outlets.out_deviceid ";
        $q_string .= "order by ast_name,out_name ";
        $contargetid = return_Index($db, $a_inv_connect['con_targetid'], $q_string);

        print "document.formUpdatePower.con_sourceid['" . $consourceid . "'].selected = true;\n";
        print "document.formUpdatePower.con_targetid['" . $contargetid . "'].selected = true;\n";
        print "document.formUpdatePower.con_type['"     . $contype     . "'].selected = true;\n";

        print "document.formUpdatePower.id.value = " . $formVars['id'] . ";\n";
      }

# fiber port
      if ($a_inv_connect['pt_name'] == 'Fibre') {
        $q_string  = "select fib_id,fib_name,ast_name ";
        $q_string .= "from inv_fiber ";
        $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
        $q_string .= "order by ast_name,fib_name ";
        $consourceid = return_Index($db, $a_inv_connect['con_sourceid'], $q_string);

        $q_string  = "select fib_id,fib_name,ast_name ";
        $q_string .= "from inv_fiber ";
        $q_string .= "left join inv_assets on inv_assets.ast_id = inv_fiber.fib_deviceid ";
        $q_string .= "order by ast_name,fib_name ";
        $contargetid = return_Index($db, $a_inv_connect['con_targetid'], $q_string);

        print "document.formUpdateFiber.con_sourceid['" . $consourceid . "'].selected = true;\n";
        print "document.formUpdateFiber.con_targetid['" . $contargetid . "'].selected = true;\n";
        print "document.formUpdateFiber.con_type['"     . $contype     . "'].selected = true;\n";

        print "document.formUpdateFiber.id.value = " . $formVars['id'] . ";\n";
      }

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
