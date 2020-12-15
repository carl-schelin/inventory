<?php
# Script: walkthrough.pdf.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Sitepath . '/guest.php');

  $package = "walkthrough.pdf.php";

  logaccess($db, $formVars['uid'], $package, "Group: " . $formVars['group']);

  if (isset($_GET['group'])) {
    $formVars['group']    = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = 1;
  }

  if (isset($_GET['location'])) {
    $formVars['location'] = clean($_GET['location'], 10);
  } else {
    $formVars['location'] = 3;
  }

# PDF Configuration
  require_once($Sitedir . '/tcpdf/config/lang/eng.php');
  require_once($Sitedir . '/tcpdf/tcpdf.php');

  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetCreator('Walkthrough Process');
  $pdf->SetAuthor($_SESSION['username']);
  $pdf->SetTitle('Data Center Walkthrough');
  $pdf->SetSubject('');
  $pdf->SetKeywords('');
#  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
#  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
#  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setLanguageArray($l);
  $pdf->setFontSubsetting(true);
  $pdf->SetFont('dejavusans', '', 8, '', true);
  $pdf->AddPage();


# Now create the data to be printed
  $output = "<h4>Data Center Walkthrough Page</h4>\n\n";

  $output .= "<table>\n";
  $output .= "<tr>\n";
  $output .= "  <th>System Name</th>\n";
  $output .= "  <th>Rack Location</th>\n";
  $output .= "  <th>Unit Location</th>\n";
  $output .= "  <th>Asset Tag</th>\n";
  $output .= "  <th>Serial Number</th>\n";
  $output .= "</tr>\n";

  if ($formVars['group'] != 0) {
    $group = " and inv_manager = " . $formVars['group'];
  } else {
    $group = '';
  }
  if ($formVars['location'] != 0) {
    $location = " and inv_location = " . $formVars['location'];
    $orderby = ' order by inv_row,inv_rack,inv_unit,inv_name';
  } else {
    $location = '';
    $orderby = ' order by loc_state,loc_city,loc_name,inv_row,inv_rack,inv_unit,inv_name';
  }

  $locheader = '';
  $q_string  = "select inv_id,inv_name,inv_rack,inv_row,inv_unit,loc_name,loc_city,loc_state,hw_asset,hw_serial ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on inventory.inv_location = locations.loc_id ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where inv_companyid = 0 and hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 and inv_status = 0 " . $group . $location . " ";
  $q_string .= $orderby;
  $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    if ($a_inventory['inv_unit'] == 0) {
      $unit = '';
    } else {
      $unit = "U" . $a_inventory['inv_unit'];
    }

    if ($location == '' && $locheader != $a_inventory['loc_name']) {
      $output .= "<tr>\n";
      $output .= "  <th colspan=6>" . $a_inventory['loc_name'] . " (" . $a_inventory['loc_city'] . " " . $a_inventory['loc_state'] . ")</th>\n";
      $output .= "</tr>\n";
      $locheader = $a_inventory['loc_name'];
    }

    $output .= "<tr>\n";
    $output .= "  <td>" . $a_inventory['inv_name']   . "</td>\n";
    $output .= "  <td>" . $a_inventory['inv_row']    . " - " . $a_inventory['inv_rack'] . "</td>\n";
    $output .= "  <td>" . $unit                      . "</td>\n";
    $output .= "  <td>" . $a_inventory['hw_asset']   . "</td>\n";
    $output .= "  <td>" . $a_inventory['hw_serial']  . "</td>\n";
    $output .= "</tr>\n";

    $q_string  = "select inv_id,inv_name,inv_rack,inv_row,inv_unit,loc_name,loc_city,loc_state,hw_asset,hw_serial ";
    $q_string .= "from inventory ";
    $q_string .= "left join locations on inventory.inv_location = locations.loc_id ";
    $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
    $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
    $q_string .= "where inv_companyid = " . $a_inventory['inv_id'] . " and hw_primary = 1 and hw_deleted = 0 and mod_virtual = 0 and inv_status = 0 " . $group . $location . " ";
    $q_string .= $orderby;
    $q_child = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
    while ($a_child = mysqli_fetch_array($q_child)) {

      $output .= "<tr>\n";
      $output .= "  <td>&gt; " . $a_child['inv_name']   . "</td>\n";
      $output .= "  <td>"      . "Blade"                . "</td>\n";
      $output .= "  <td>Slot " . $a_child['inv_unit']   . "</td>\n";
      $output .= "  <td>"      . $a_child['hw_asset']   . "</td>\n";
      $output .= "  <td>"      . $a_child['hw_serial']  . "</td>\n";
      $output .= "</tr>\n";

    }
  }

  $output .= "</table>\n";

  $output .= "<br><p>* Indicates that the Asset tag was previously gathered but is not able to be confirmed visually.</p>\n";

  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $output, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
  $pdf->Output('datacenter.walkthrough' . $formVars['id'] . '.pdf', 'I');

?>
