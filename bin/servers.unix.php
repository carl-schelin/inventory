<?php
include('function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  $field = clean($_REQUEST["sort"], 20);

  if (isset($_REQUEST["sort"])) {
    $orderby = " order by " . $field;
  } else {
    $orderby = " order by inv_name";
  }

  $q_string = "select zone_id,zone_name from zones";
  $q_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_zones = mysql_fetch_array($q_zones)) {
    $zoneval[$a_zones['zone_id']] = $a_zones['zone_name'];
  }

  $bgcolor = "#EEEEEE";

  $q_string = "select inv_id,inv_name,inv_zone,inv_ssh from inventory where inv_manager = 1 and inv_ssh = 1 and inv_status = 0 order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());

  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string = "select sw_software from software where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS'";
    $q_software = mysql_query($q_string) or die(mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $os = "";
    $pre = "";
    $note = "";
    $peering = "";


# determine operating system
    $value = split(" ", $a_software['sw_software']);

    if ($value[0] == "Solaris") {
      $os = "SunOS";
    }
    if ($value[0] == "Red" || $value[0] == "RedHat") {
      $os = "Linux";
    }
    if ($value[0] == "Debian" || $value[0] == "Ubuntu" || $value[0] == "CentOS") {
      $os = "Linux";
    }
    if ($value[0] == "Oracle") {
      $os = "Linux";
    }
    if ($value[0] == "HP-UX") {
      $os = "HP-UX";
    }
    if ($value[0] == "Tru64") {
      $os = "OSF1";
    }
    if ($value[0] == "Free") {
      $os = "FreeBSD";
    }
    if ($os == "") {
      $os = $value[0];
    }

    $value = split("/", $a_inventory['inv_name']);
    
# determine any notes or commented out systems

# Peering servers
    if ($value[0] == "inilpsx1") {
      $value[0] = "cilpsx1";
    }
    if ($value[0] == "cilpsx1") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtems1") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtnfs1") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtnfs2") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtpsx1") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtpsx2") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtsgx1") {
      $peering = "Peering";
    }
    if ($value[0] == "lnmtsgx2") {
      $peering = "Peering";
    }
    if ($value[0] == "nycnfs1") {
      $peering = "Peering";
    }
    if ($value[0] == "nycnfs2") {
      $peering = "Peering";
    }
    if ($value[0] == "nycpsx1") {
      $peering = "Peering";
    }
    if ($value[0] == "miapsx1") {
      $peering = "Peering";
    }
    if ($value[0] == "mianfs1") {
      $peering = "Peering";
    }
    if ($value[0] == "mianfs2") {
      $peering = "Peering";
    }
    if ($value[0] == "miasgx1") {
      $peering = "Peering";
    }
    if ($value[0] == "miasgx2") {
      $peering = "Peering";
    }
    if ($value[0] == "lgmtsgx1") {
      $peering = "Peering";
    }
    if ($value[0] == "lgmtsgx2") {
      $peering = "Peering";
    }

# servers are called one thing but listed as another.
    if ($value[0] == "incoag13") {
      $value[0] = "incoag10";
    }
    if ($value[0] == "incoag23") {
      $value[0] = "incoag20";
    }
    if ($value[0] == "incoga13") {
      $value[0] = "incoga10";
    }
    if ($value[0] == "incoga23") {
      $value[0] = "incoga20";
    }
    if ($value[0] == "incolp10") {
      $value[0] = "incolp11";
    }
    if ($value[0] == "incolp20") {
      $value[0] = "incolp21";
    }
    if ($value[0] == "incolp30") {
      $value[0] = "incolp31";
    }
    if ($value[0] == "incoce04") { # server is part of a manual cluster
      $value[0] = "incoce00";
    }

# IEN servers

    if ($value[0] == "coolcacsdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcacsdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcacslga25") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecada1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecadb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecera1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecera2b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecera3b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecerb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecerb2b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecerb3b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecgc11") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecgc21") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecmpa1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecmpb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecuta1b") {
      $peering = "IEN";
    }
    if ($value[0] == "coolcaecutb1b") {
      $peering = "IEN";
    }

    if ($value[0] == "dthvcaecada1b") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecera1b") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecera2b") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecera3b") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecgc11") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecgc21") {
      $peering = "IEN";
    }
    if ($value[0] == "dthvcaecuta1b") {
      $peering = "IEN";
    }

    if ($value[0] == "flsmcacsdba15") {
      $peering = "IEN";
    }
    if ($value[0] == "flsmcacsdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "flsmcacsdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "flsmcacslga25") {
      $peering = "IEN";
    }
    if ($value[0] == "flsmcacsmpa1b") {
      $peering = "IEN";
    }
    if ($value[0] == "flsmcacsmpb1b") {
      $peering = "IEN";
    }

    if ($value[0] == "enwdcocsdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcocsdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcocslg10") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecada1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecadb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecera1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecera2b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecera3b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecerb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecerb2b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecerb3b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecgc11") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecgc21") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecmpa1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecmpb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecuta1b") {
      $peering = "IEN";
    }
    if ($value[0] == "enwdcoecutb1b") {
      $peering = "IEN";
    }

    if ($value[0] == "lnmtcocsdba15") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcocsdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcocsdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcocslg10") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcocsmpa1b") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcocsmpb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcocswsa1b") {
      $peering = "IEN";
    }
    if ($value[0] == "lnmtcodcbm11") {
      $peering = "IEN";
    }

    if ($value[0] == "miamflecada1b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecadb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecdca15") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecdca25") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecera1b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecera2b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecera3b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecerb1b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecerb2b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecerb3b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecgc11") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecgc21") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecuta1b") {
      $peering = "IEN";
    }
    if ($value[0] == "miamflecutb1b") {
      $peering = "IEN";
    }

    print "$pre$value[0]:$value[1]:$os:" . $zoneval[$a_inventory['inv_zone']] . ":$peering:$note:" . $a_inventory['inv_id'] . "\n";

  }

?>
