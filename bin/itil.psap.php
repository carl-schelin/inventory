#!/usr/local/bin/php
<?php
# Script: itil.psap.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve the 'psap' listing for the conversion to Remedy.
# Requires:
# Product Type
# Product Name

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# psap pairs
# culalic1 9692 and elmalic1 9691
# ffdalic1 9694 and sdgalic1 9693
# hnlali01 3307 and hnlali02 3308
# hpalask0 1369 and hpalask1 1370
# hpcdfle  1373 and hplgmte  1372
# hplgmtn  1379 and hpmiamn  1380
# hplgmtw  1371 and hpstlew  1374
# hpchlt0  1376 and hpmiam0  1378
# hpbghm0  1375 and hpnsvl0  1377
# hpnbrk0  1382 and hpsthf0  1381
# lgtalnc1 9702 and miaalnc1 9701
# lgtalic1 9700 and rchalic1 9699
# dalalic1 9696 and stlalic1 9695
# dalalic2 9698 and stlalic2 9697

$psap[0] = 'No Match';
# first pair id = second pair name
$psap[9692] = 'elmalic1';
$psap[9694] = 'sdgalic1';
$psap[3307] = 'hnlali02';
$psap[1369] = 'hpalask1';
$psap[1373] = 'hplgmte';
$psap[1379] = 'hpmiamn';
$psap[1371] = 'hpstlew';
$psap[1376] = 'hpmiam0';
$psap[1375] = 'hpnsvl0';
$psap[1382] = 'hpsthf0';
$psap[9702] = 'miaalnc1';
$psap[9700] = 'rchalic1';
$psap[9696] = 'stlalic1';
$psap[9698] = 'stlalic2';
$psap[10372] = 'vzienva';

$psapid[9692] = 9691;
$psapid[9694] = 9693;
$psapid[3307] = 3308;
$psapid[1369] = 1370;
$psapid[1373] = 1372;
$psapid[1379] = 1380;
$psapid[1371] = 1374;
$psapid[1376] = 1378;
$psapid[1375] = 1377;
$psapid[1382] = 1381;
$psapid[9702] = 9701;
$psapid[9700] = 9699;
$psapid[9696] = 9695;
$psapid[9698] = 9697;
$psapid[10372] = 10373;

# second pair id = first pair name
$psap[9691] = 'culalic1';
$psap[9693] = 'ffdalic1';
$psap[3308] = 'hnlali01';
$psap[1370] = 'hpalask0';
$psap[1372] = 'hpcdfle';
$psap[1380] = 'hplgmtn';
$psap[1374] = 'hplgmtw';
$psap[1378] = 'hpchlt0';
$psap[1377] = 'hpbghm0';
$psap[1381] = 'hpnbrk0';
$psap[9701] = 'lgtalnc1';
$psap[9699] = 'lgtalic1';
$psap[9695] = 'dalalic1';
$psap[9697] = 'dalalic2';
$psap[10373] = 'vzienva';

$psapid[9691] = 9692;
$psapid[9693] = 9694;
$psapid[3308] = 3307;
$psapid[1370] = 1369;
$psapid[1372] = 1373;
$psapid[1380] = 1379;
$psapid[1374] = 1371;
$psapid[1378] = 1376;
$psapid[1377] = 1375;
$psapid[1381] = 1382;
$psapid[9701] = 9702;
$psapid[9699] = 9700;
$psapid[9695] = 9696;
$psapid[9697] = 9698;
$psapid[10373] = 10372;

$psap[10114] = 'Default';
$psapid[10114] = 10114;

  $header = 
    "\"PSAP Name\"," . 
    "\"PSAP ID\"," . 
    "\"PSAP Type\"," . 
    "\"Partner PSAP ID\"," . 
    "\"Partner PSAP Name\"," . 
    "\"Partner Pseudo Circuit ID\"," . 
    "\"Partner Circuit ID\"," . 
    "\"LEC\"," . 
    "\"Partner LEC\"," . 
    "\"Primary Circuit ID\"," . 
    "\"Primary IP\"," . 
    "\"Primary Port\"," . 
    "\"Secondary Circuit ID\"," . 
    "\"Secondary IP\"," . 
    "\"Secondary Port\"," . 
    "\"ALI Name\"," . 
    "\"ALI Node\"," . 
    "\"Related ALI Node\"," . 
    "\"Related ALI Name\"," . 
    "\"Status\"," . 
    "\"Description\"," . 
    "\"Texas CSEC?\"," . 
    "\"Deletion Flag\"";

# psap_id          | int(10)   | NO   | PRI | NULL    | auto_increment |
# psap_customerid  | int(10)   | NO   |     | 0       |                |
# psap_parentid    | int(10)   | NO   |     | 0       |                |
# psap_ali_id      | char(10)  | NO   |     |         |                |
# psap_companyid   | int(10)   | NO   |     | 0       |                |
# psap_psap_id     | int(10)   | NO   |     | 0       |                |
# psap_description | char(255) | NO   |     |         |                |
# psap_lport       | int(10)   | NO   |     | 0       |                |
# psap_circuit_id  | char(255) | NO   |     |         |                |
# psap_pseudo_cid  | char(255) | NO   |     |         |                |
# psap_lec         | char(10)  | NO   |     |         |                |
# psap_texas       | int(10)   | NO   |     | 0       |                |


#    "PSAP Name," .                 Intrado: $a_psaps['psap_description'] PSAP Description – CORRECT 
#    "PSAP ID," .                   Intrado: $a_psaps['psap_psap_id'] PSAP ID - CORRECT
#    "PSAP Type," .                 Unknown – Send it as blank
#    "Partner PSAP ID," .           CenturyLink: $a_ctl['psap_psap_id'] CTL-ID - CORRECT
#    "Partner PSAP Name," .         CenturyLink: $a_ctl['psap_description'] CTL-PSAP Name - CORRECT
#    "Partner Pseudo Circuit ID," . CenturyLink: $a_ctl['psap_pseudo_cid'] CTL-PSAP Circuit ID - CORRECT
#    "Partner Circuit ID," .        CenturyLink: $a_ctl['psap_circuit_id'] Circuit ID - CORRECT
#    "LEC," .                       Intrado: $a_psaps['psap_lec'] - LEC - CORRECT - Updated 2016/06/08 for Verizon import
#    "Partner LEC," .               CenturyLink: $a_ctl['psap_lec'] LEC - CORRECT
#    "Primary Circuit ID," .        Intrado: $a_psaps['psap_circuit_id'] Circuit ID - CORRECT
#    "Primary IP," .                Unknown – Send it as blank
#    "Primary Port," .              Intrado: $a_psaps['psap_lport'] Port - CORRECT
#    "Secondary Circuit ID," .      Unknown – Send it as blank
#    "Secondary IP," .              Unknown – Send it as blank
#    "Secondary Port," .            Unknown – Send it as blank
#    "ALI Name," .                  Intrado: $a_psaps['inv_name'] ALI Name - CORRECT
#    "ALI Node," .                  Intrado: $a_psaps['psap_ali_id'] ALI-ID - CORRECT
#    "Related ALI Node," .          Unknown – Send it as blank
#    "Related ALI Name," .          $psap[$a_psaps['psap_customerid']] If culalic1 then populate with elmalic1: see data translation table below
#    "Status," .                    Unknown – Send it as blank
#    "Description," .               Unknown – Send it as blank
#    "Texas CSEC?," .               Unknown – This field should be populated as YES for a Texas CSEC PSAP once Pete provides the final data
#    "Deletion Flag";               (YES|NO)
 
# From centurylink
#      A        B         C            D              E         F    G     H              I      
# ALI-ID,ALI Name,Intrado PSAP ID,Intrado PSAP Name,Circuit ID,LEC,CTL-ID,CTL-PSAP Name,CTL-PSAP Circuit ID,

  print $header . "\n";

  $checking = 'Carl';
  $checking = 'bob';

#if ($checking == 'Carl') {
# get the Intrado PSAP information (1319)
  $q_string  = "select psap_id,psap_description,psap_psap_id,psap_circuit_id,psap_lport,psap_ali_id,psap_lec,psap_texas,inv_name,psap_companyid ";
  $q_string .= "from psaps ";
  $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
  $q_string .= "where psap_customerid = 1319 and psap_delete = 0 ";                    # only intrado PSAPs.
  $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_psaps = mysqli_fetch_array($q_psaps)) {

# get the Partner PSAP information for the Intrado PSAP retrieved
    $q_string  = "select psap_description,psap_lec,psap_psap_id,psap_circuit_id,psap_lport,psap_ali_id,psap_companyid,psap_texas,psap_pseudo_cid ";
    $q_string .= "from psaps ";
    $q_string .= "where psap_parentid = " . $a_psaps['psap_id'] . " and psap_delete = 0  ";
    $q_ctl = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_ctl) > 0) {
      $a_ctl = mysqli_fetch_array($q_ctl);

# need to get the ali-id from the mate
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","stlalic2","A3","More than One","dalalic2","","","No","No"
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","dalalic2","A4","More than One","stlalic2","","","No","No"

      $ali_node = "Blank";
      $q_string  = "select psap_ali_id ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_psap_id = '" . $a_psaps['psap_psap_id'] . "' and psap_parentid = 0 and psap_companyid = " . $psapid[$a_psaps['psap_companyid']] . " and psap_id != '" . $a_psaps['psap_id'] . "' and psap_delete = 0  ";
      $q_string .= "order by psap_psap_id ";
      $q_ali_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_ali_id) == 0) {
        $ali_node = "None found";
      }
      if (mysqli_num_rows($q_ali_id) == 1) {
        $a_ali_id = mysqli_fetch_array($q_ali_id);
        $ali_node = $a_ali_id['psap_ali_id'];
      }
      if (mysqli_num_rows($q_ali_id) > 1) {
        $ali_node = "More than One";
      }

      print "\"" . $a_psaps['psap_description'] . "\",";
      printf("\"%05d\",", $a_psaps['psap_psap_id']);
      print "\"" . "" . "\",";
      printf("\"%05d\",", $a_ctl['psap_psap_id']);
      print "\"" . $a_ctl['psap_description'] . "\",";
      print "\"" . $a_ctl['psap_pseudo_cid'] . "\",";
#      print "\"" . "NULL" . "\",";
      print "\"" . $a_ctl['psap_circuit_id'] . "\",";
      print "\"" . $a_ctl['psap_lec'] . "\",";
      print "\"" . "" . "\",";
      if (strlen($a_psaps['psap_circuit_id']) > 0) {
        print "\"" . $a_psaps['psap_circuit_id'] . "\",";
      } else {
        print "\"" . "NULL" . "\",";
      }
      print "\"" . "" . "\",";
      print "\"" . $a_psaps['psap_lport'] . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . $a_psaps['inv_name'] . "\",";
      print "\"" . $a_psaps['psap_ali_id'] . "\",";
      print "\"" . $ali_node . "\",";
      print "\"" . $psap[$a_psaps['psap_companyid']] . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      if ($a_ctl['psap_texas']) {
        print "\"" . "Yes" . "\",";
      } else {
        print "\"" . "No" . "\",";
      }
      print "\"" . "No" . "\"\n";
    } else {
# need to get the ali-id from the mate
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","stlalic2","A3","More than One","dalalic2","","","No","No"
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","dalalic2","A4","More than One","stlalic2","","","No","No"

      $ali_node = "Blank";
      $q_string  = "select psap_ali_id ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_psap_id = '" . $a_psaps['psap_psap_id'] . "' and psap_parentid = 0 and psap_companyid = " . $psapid[$a_psaps['psap_companyid']] . " and psap_id != '" . $a_psaps['psap_id'] . "' and psap_delete = 0  ";
      $q_string .= "order by psap_psap_id ";
      $q_ali_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_ali_id) == 0) {
        $ali_node = "None found";
      }
      if (mysqli_num_rows($q_ali_id) == 1) {
        $a_ali_id = mysqli_fetch_array($q_ali_id);
        $ali_node = $a_ali_id['psap_ali_id'];
      }
      if (mysqli_num_rows($q_ali_id) > 1) {
        $ali_node = "More than One";
      }

#    "PSAP Name," .                 Intrado: $a_psaps['psap_description'] PSAP Description – CORRECT 
#    "PSAP ID," .                   Intrado: $a_psaps['psap_psap_id'] PSAP ID - CORRECT
#    "PSAP Type," .                 Unknown – Send it as blank
#    "Partner PSAP ID," .           CenturyLink: $a_ctl['psap_psap_id'] CTL-ID - CORRECT
#    "Partner PSAP Name," .         CenturyLink: $a_ctl['psap_description'] CTL-PSAP Name - CORRECT
#    "Partner Pseudo Circuit ID," . CenturyLink: $a_ctl['psap_pseudo_cid'] CTL-PSAP Circuit ID - CORRECT
#    "Partner Circuit ID," .        CenturyLink: $a_ctl['psap_circuit_id'] Circuit ID - CORRECT
#    "LEC," .                       Intrado: $a_psaps['psap_lec'] - LEC - CORRECT - Updated 2016/06/08 for Verizon import
#    "Partner LEC," .               CenturyLink: $a_ctl['psap_lec'] LEC - CORRECT
#    "Primary Circuit ID," .        Intrado: $a_psaps['psap_circuit_id'] Circuit ID - CORRECT
#    "Primary IP," .                Unknown – Send it as blank
#    "Primary Port," .              Intrado: $a_psaps['psap_lport'] Port - CORRECT
#    "Secondary Circuit ID," .      Unknown – Send it as blank
#    "Secondary IP," .              Unknown – Send it as blank
#    "Secondary Port," .            Unknown – Send it as blank
#    "ALI Name," .                  Intrado: $a_psaps['inv_name'] ALI Name - CORRECT
#    "ALI Node," .                  Intrado: $a_psaps['psap_ali_id'] ALI-ID - CORRECT
#    "Related ALI Node," .          Unknown – Send it as blank
#    "Related ALI Name," .          $psap[$a_psaps['psap_customerid']] If culalic1 then populate with elmalic1: see data translation table below
#    "Status," .                    Unknown – Send it as blank
#    "Description," .               Unknown – Send it as blank
#    "Texas CSEC?," .               Unknown – This field should be populated as YES for a Texas CSEC PSAP once Pete provides the final data
#    "Deletion Flag";               (YES|NO)

      print "\"" . $a_psaps['psap_description'] . "\",";
      printf("\"%05d\",", $a_psaps['psap_psap_id']);
      print "\"" . "" . "\",";   # type
      print "\"" . "" . "\",";   # partner
      print "\"" . "" . "\",";   # partner
      print "\"" . "" . "\",";   # partner
      print "\"" . "NULL" . "\",";   # partner
      print "\"" . $a_psaps['psap_lec'] . "\",";
      print "\"" . "" . "\",";
      if (strlen($a_psaps['psap_circuit_id']) > 0) {
        print "\"" . $a_psaps['psap_circuit_id'] . "\",";
      } else {
        print "\"" . "NULL" . "\",";
      }
      print "\"" . "" . "\",";
      print "\"" . $a_psaps['psap_lport'] . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . $a_psaps['inv_name'] . "\",";
      print "\"" . $a_psaps['psap_ali_id'] . "\",";
      print "\"" . $ali_node . "\",";
      print "\"" . $psap[$a_psaps['psap_companyid']] . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      if ($a_psaps['psap_texas']) {
        print "\"" . "Yes" . "\",";
      } else {
        print "\"" . "No" . "\",";
      }
      print "\"" . "No" . "\"\n";
    }
  }

#}

#if ($checking == 'bob') {

#Need the psap name

# next step is to get the Default entries.
  $q_string  = "select psap_id,psap_description,psap_psap_id,psap_pseudo_cid,psap_circuit_id,psap_lport,psap_ali_id,psap_texas,inv_name,psap_companyid,psap_customerid,psap_lec ";
  $q_string .= "from psaps ";
  $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
  $q_string .= "where psap_parentid = 0 and psap_customerid != 1319 and psap_delete = 0 ";                    # anything but intrado PSAPs.
  $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_psaps = mysqli_fetch_array($q_psaps)) {

    $ali_node = "Blank";
    $q_string  = "select psap_ali_id ";
    $q_string .= "from psaps ";
    $q_string .= "where psap_psap_id = '" . $a_psaps['psap_psap_id'] . "' and psap_parentid = 0 and psap_companyid = " . $psapid[$a_psaps['psap_companyid']] . " and psap_id != '" . $a_psaps['psap_id'] . "' and psap_delete = 0  ";
    $q_string .= "order by psap_psap_id ";
    $q_ali_id = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_ali_id) == 0) {
      $ali_node = "None found";
    }
    if (mysqli_num_rows($q_ali_id) == 1) {
      $a_ali_id = mysqli_fetch_array($q_ali_id);
      $ali_node = $a_ali_id['psap_ali_id'];
    }
    if (mysqli_num_rows($q_ali_id) > 1) {
      $ali_node = "More than One";
    }

# match the centurylink default psap_psap_id with the existing centurylink non-default info in order to identify the intrado psap.
# need to match centurylink (41) with centurylink (41) but where parentid != 0 (original data)
# then and the psap_psap_id (11223) match and the psap_ali_id (Q1) match
# This provides the intrado match (the parentid) and with that, get the intrado information
# 
    $q_string  = "select psap_parentid ";
    $q_string .= "from psaps ";
    $q_string .= "where psap_customerid = " . $a_psaps['psap_customerid'] . " and psap_parentid != 0 and psap_psap_id = '" . $a_psaps['psap_psap_id'] . "' and psap_ali_id = '" . $a_psaps['psap_ali_id'] . "' and psap_delete = 0  ";
    $q_partner = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_partner) > 0) {
      $a_partner = mysqli_fetch_array($q_partner);

      $q_string  = "select psap_id,psap_description,psap_psap_id,psap_circuit_id,psap_lport,psap_ali_id,psap_texas,inv_name,psap_companyid ";
      $q_string .= "from psaps ";
      $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
      $q_string .= "where psap_id = " . $a_partner['psap_parentid'] . " and psap_delete = 0  ";
      $q_intrado = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_intrado = mysqli_fetch_array($q_intrado);

      print "\"" . $a_intrado['psap_description'] . "\",";
      printf("\"%05d\",", $a_intrado['psap_psap_id']);
      print "\"" . "" . "\",";
      printf("\"%05d\",", $a_psaps['psap_psap_id']);
      print "\"" . $a_psaps['psap_description'] . "\",";
      print "\"" . $a_psaps['psap_pseudo_cid'] . "\",";
#      print "\"" . "NULL" . "\",";
      print "\"" . $a_psaps['psap_circuit_id'] . "\",";
      print "\"" . $a_psaps['psap_lec'] . "\",";
      print "\"" . "" . "\",";
      if (strlen($a_intrado['psap_circuit_id']) > 0) {
        print "\"" . $a_intrado['psap_circuit_id'] . "\",";
      } else {
        print "\"" . "NULL" . "\",";
      }
      print "\"" . "" . "\",";
      print "\"" . $a_intrado['psap_lport'] . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "Default" . "\",";
      print "\"" . $a_intrado['psap_ali_id'] . "\",";
      print "\"" . $ali_node . "\",";
      print "\"" . $psap[$a_psaps['psap_companyid']] . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      if ($a_intrado['psap_texas']) {
        print "\"" . "Yes" . "\",";
      } else {
        print "\"" . "No" . "\",";
      }
      print "\"" . "No" . "\"\n";
    }
  }

###############
###  Generic Centurylink PSAP
###############

#"PSAP Name","PSAP ID","PSAP Type","Partner PSAP ID","Partner PSAP Name","Partner Pseudo Circuit ID","Partner Circuit ID","LEC","Partner LEC","Primary Circuit ID","Primary IP","Primary Port","Secondary Circuit ID","Secondary IP","Secondary Port","ALI Name","ALI Node","Related ALI Node","Related ALI Name","Status","Description","Texas CSEC?","Deletion Flag"

# this is from the centurylink.20160324.csv file
#"Q3     ",hpcdfle,13333,237,"   CO BROOMFIELD PD                        ","     20FDDZ481793MS SPLT PSAP 290    ",CO/EVXR/333/MS,CTL East         ,BROOMFIELD PD         ,CO/EVXR/333/MS,
#         ,       ,13999,   ,Generic PSAP/Wild Card                       ,                                       ,XX/EVXR/999/MS,                 ,Generic PSAP/Wild Card,XX/EVXR/999/MS,     
# updated input
#"Q1	"	Default	13999	9999	Generic PSAP/Wild Card	Generic Circuit ID	XX/EVXR/999/MS	CTL Generic	Generic PSAP/Wild Card	XX/EVXR/999/MS	yes							
#    A       B       C    D                        E                                        F                           G             H                 I                    J

# output:
#"NM SOCORRO PD","00049","","06444","SOCORRO PD","50/FDDZ/100960/MS","50FDDZ100960MS  172.24.32.73","CTL East","","50FDDZ100960MS  172.24.32.73","","8225","","","","hpcdfle","Q3","Q4","hplgmte","","","No","No"

#input
#ALI-ID	ALI Name	CTL-ID - Partner PSAP ID	Intrado PSAP ID	Intrado PSAP Name	Circuit ID - 	NEW CircuitID	LEC	CTL-PSAP Name - Partner PSAP Name	CTL-PSAP Circuit ID
# output will be:
 
  print "\"" . "Default" . "\",";            # Intrado PSAP Name
  print "\"" . "9999" . "\",";               # Intrado PSAP ID
  print "\"" . "" . "\",";                   # Intrado PSAP Type
  printf("\"%05d\",", 13999);                # Centurylink Partner PSAP ID
  print "\"Generic PSAP/Wild Card\",";       # Centurylink Parter PSAP Name ; Column I/8
  print "\"" . "Generic Circuit ID" . "\","; # Centurylink Partner Pseudo Circuit ID ; Column F/5
  print "\"" . "XX/EVXR/999/MS" . "\",";     # Centurylink Partner Circuit ID ; Column G/6
  print "\"" . "" . "\",";
  print "\"" . "CTL Generic" . "\",";        # LEC
  print "\"" . "NULL" . "\",";
  print "\"" . "" . "\",";
  print "\"" . "" . "\",";
  print "\"" . "" . "\",";
  print "\"" . "" . "\",";
  print "\"" . "" . "\",";
  print "\"" . "Default" . "\",";            # Intrado Opposite ALI Name
  print "\"" . "Q1" . "\",";                 # Intrado ALI ID
  print "\"" . "Q1" . "\",";                 # Intrado ALI ID
  print "\"" . "Default" . "\",";            # Intrado Opposite ALI Name
  print "\"" . "" . "\",";
  print "\"" . "" . "\",";
  print "\"" . "No" . "\",";
  print "\"" . "No" . "\"\n";

#}

mysqli_free_request($db);

?>
