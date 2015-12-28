#!/usr/local/bin/php
<?php
# Script: itil.psap.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve the 'psap' listing for the conversion to Remedy.
# Requires:
# Product Type
# Product Name
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
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


#    "PSAP Name," .                 Intrado: $a_psaps['psap_description'] PSAP Description – CORRECT 
#    "PSAP ID," .                   Intrado: $a_psaps['psap_psap_id'] PSAP ID - CORRECT
#    "PSAP Type," .                 Unknown – Send it as blank
#    "Partner PSAP ID," .           CenturyLink: $a_ctl['psap_psap_id'] CTL-ID - CORRECT
#    "Partner PSAP Name," .         CenturyLink: $a_ctl['psap_description'] CTL-PSAP Name - CORRECT
#    "Partner Pseudo Circuit ID," . CenturyLink: $a_ctl['psap_pseudo_cid'] CTL-PSAP Circuit ID - CORRECT
#    "Partner Circuit ID," .        CenturyLink: $a_ctl['psap_circuit_id'] Circuit ID - CORRECT
#    "LEC," .                       Unknown – Send it as blank
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

  $q_string  = "select psap_id,psap_description,psap_psap_id,psap_circuit_id,psap_lport,psap_ali_id,inv_name,psap_companyid ";
  $q_string .= "from psaps ";
  $q_string .= "left join inventory on inventory.inv_id = psaps.psap_companyid ";
  $q_string .= "where psap_parentid = 0 ";
  $q_psaps = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_psaps = mysql_fetch_array($q_psaps)) {

    $q_string  = "select psap_description,psap_lec,psap_psap_id,psap_circuit_id,psap_lport,psap_ali_id,psap_companyid,psap_pseudo_cid ";
    $q_string .= "from psaps ";
    $q_string .= "where psap_parentid = " . $a_psaps['psap_id'] . " ";
    $q_ctl = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_ctl) > 0) {
      $a_ctl = mysql_fetch_array($q_ctl);

# need to get the ali-id from the mate
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","stlalic2","A3","More than One","dalalic2","","","No","No"
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","dalalic2","A4","More than One","stlalic2","","","No","No"

      $ali_node = "Blank";
      $q_string  = "select psap_ali_id ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_psap_id = '" . $a_psaps['psap_psap_id'] . "' and psap_parentid = 0 and psap_companyid = " . $psapid[$a_psaps['psap_companyid']] . " and psap_id != '" . $a_psaps['psap_id'] . "' ";
      $q_string .= "order by psap_psap_id ";
      $q_ali_id = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_ali_id) == 0) {
        $ali_node = "None found";
      }
      if (mysql_num_rows($q_ali_id) == 1) {
        $a_ali_id = mysql_fetch_array($q_ali_id);
        $ali_node = $a_ali_id['psap_ali_id'];
      }
      if (mysql_num_rows($q_ali_id) > 1) {
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
      print "\"" . "No" . "\",";
      print "\"" . "No" . "\"\n";
    } else {
# need to get the ali-id from the mate
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","stlalic2","A3","More than One","dalalic2","","","No","No"
#"TX-MEDINA CO SO","00204","","","","","NULL","","","NULL","","9030","","","","dalalic2","A4","More than One","stlalic2","","","No","No"

      $ali_node = "Blank";
      $q_string  = "select psap_ali_id ";
      $q_string .= "from psaps ";
      $q_string .= "where psap_psap_id = '" . $a_psaps['psap_psap_id'] . "' and psap_parentid = 0 and psap_companyid = " . $psapid[$a_psaps['psap_companyid']] . " and psap_id != '" . $a_psaps['psap_id'] . "' ";
      $q_string .= "order by psap_psap_id ";
      $q_ali_id = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_ali_id) == 0) {
        $ali_node = "None found";
      }
      if (mysql_num_rows($q_ali_id) == 1) {
        $a_ali_id = mysql_fetch_array($q_ali_id);
        $ali_node = $a_ali_id['psap_ali_id'];
      }
      if (mysql_num_rows($q_ali_id) > 1) {
        $ali_node = "More than One";
      }

      print "\"" . $a_psaps['psap_description'] . "\",";
      printf("\"%05d\",", $a_psaps['psap_psap_id']);
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "" . "\",";
      print "\"" . "NULL" . "\",";
      print "\"" . "" . "\",";
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
      print "\"" . "No" . "\",";
      print "\"" . "No" . "\"\n";
    }

  }

?>
