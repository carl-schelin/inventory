<?php
include('settings.php');
include($Sitepath . 'function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  $q_string  = "select inv_id,inv_name,inv_ssh,zone_name,inv_tags ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where inv_manager = 1 and inv_status = 0 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());

  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS'";
    $q_software = mysql_query($q_string) or die(mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $os = "";
    $pre = "";
    $tags .= "";

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
    if ($a_inventory['inv_ssh'] == 0) {
      $pre = '#';
    }

# determine operating system
    $value = split(" ", $a_software['sw_software']);

# straight linux check
    if ($value[0] == 'Linux' || $value[1] == 'Linux' || $value[2] == 'Linux') {
      $os = "Linux";
    }
# red hat based systems
    if ($value[0] == 'CentOS' || $value[0] == 'Fedora' || $value[0] == 'Red') {
      $os = "Linux";
    }
# misc non redhat/linux systems
    if ($value[0] == 'Debian' || $value[0] == 'Ubuntu' || $value[0] == 'SUSE') {
      $os = "Linux";
    }
    if ($value[0] == "Solaris" || $value[1] == 'Solaris') {
      $os = "SunOS";
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

    $tags = $a_inventory['inv_tags'];

    $value = split("/", $a_inventory['inv_name']);

    $interfaces = '';
    $q_string  = "select int_server ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
    $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_interface = mysql_fetch_array($q_interface)) {
      $interfaces .= "," . $a_interface['int_server'] . ",";
    }

# determine any notes or commented out systems

# Peering servers
    if ($value[0] == "inilpsx1") {
      $value[0] = "cilpsx1";
    }
    if ($value[0] == "cilpsx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtems1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtnfs1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtnfs2") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtpsx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtpsx2") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtsgx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lnmtsgx2") {
      $tags .= ",peering,";
    }
    if ($value[0] == "nycnfs1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "nycnfs2") {
      $tags .= ",peering,";
    }
    if ($value[0] == "nycpsx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "miapsx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "mianfs1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "mianfs2") {
      $tags .= ",peering,";
    }
    if ($value[0] == "miasgx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "miasgx2") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lgmtsgx1") {
      $tags .= ",peering,";
    }
    if ($value[0] == "lgmtsgx2") {
      $tags .= ",peering,";
    }

# servers are called one thing but listed as another.
    if ($value[0] == "miamfldctxc0") {
      $value[0] = "miamfldctxc1";
    }
    if ($value[0] == "lnmtcodctxc0") {
      $value[0] = "lnmtcodctxc1";
    }
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
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcacsdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcacslga25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecada1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecadb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecera1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecera2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecera3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecerb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecerb2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecerb3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecgc11") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecgc21") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecmpa1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecmpb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecuta1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "coolcaecutb1b") {
      $tags .= ",ienvoice,";
    }

    if ($value[0] == "dthvcaecada1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecera1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecera2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecera3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecgc11") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecgc21") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "dthvcaecuta1b") {
      $tags .= ",ienvoice,";
    }

    if ($value[0] == "flsmcacsdba15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "flsmcacsdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "flsmcacsdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "flsmcacslga25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "flsmcacsmpa1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "flsmcacsmpb1b") {
      $tags .= ",ienvoice,";
    }

    if ($value[0] == "enwdcocsdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcocsdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcocslg10") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecada1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecadb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecera1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecera2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecera3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecerb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecerb2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecerb3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecgc11") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecgc21") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecmpa1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecmpb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecuta1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "enwdcoecutb1b") {
      $tags .= ",ienvoice,";
    }

    if ($value[0] == "lnmtcocsdba15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcocsdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcocsdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcocslg10") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcocsmpa1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcocsmpb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcocswsa1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "lnmtcodcbm11") {
      $tags .= ",ienvoice,";
    }

    if ($value[0] == "miamflecada1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecadb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecdca15") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecdca25") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecera1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecera2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecera3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecerb1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecerb2b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecerb3b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecgc11") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecgc21") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecuta1b") {
      $tags .= ",ienvoice,";
    }
    if ($value[0] == "miamflecutb1b") {
      $tags .= ",ienvoice,";
    }

    print "$pre$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$interfaces:" . $a_inventory['inv_id'] . "\n";

  }
# add the centrify application for changelog work
  print "#centrify:::::,centrify,:0\n";

?>
