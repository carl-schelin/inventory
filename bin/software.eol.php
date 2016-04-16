#!/usr/local/bin/php
<?php
# Script: software.eol.php
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

# need a list of software and the EOL dates.
# needs to be updated when something new is discovered
# updates the inventory nightly.

print "Updating CentOS\n";

### CentOS
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"CentOS 5.3\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"CentOS release 5.3\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"CentOS release 5.8 (Final)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"CentOS 6.2\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"CentOS release 6.2 (Final)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"CentOS release 6.2 (Final)(Santiago)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"CentOS release 6.4 (Final)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"CentOS release 6.5 (Final)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"CentOS release 6.5 (Final)(Santiago)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

print "Updating Red Hat\n";

### Red Hat
$q_string = "update software set sw_eol = \"2002-07-01\",sw_type = \"OS\" where sw_software = \"Red Hat ES 2.1 (Panama)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2002-07-01\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 2.1 (Panama)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 3 (32-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 4)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 5)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 6)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 8)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 9)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 2)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 3)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 4)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 5)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 6)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon Update 8)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2006-07-20\",sw_type = \"OS\" where sw_software = \"Red Hat ES 3 (Taroon)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat AS 4 (Nahant)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 4 (32-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 4 (64-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat EL 4 (Nahant 2)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise AS 4 (Nahant 4)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 4)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 5)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat AS 4 (Nahant Update 5)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 6)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Enterprise Linux Enterprise Linux AS release 4 (October Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Oracle Enterprise Linux Enterprise Linux AS release 4 (October Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Oracle Red Hat Enterprise Linux ES release 4 (Nahant Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Oracle ES 4 (Nahant Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 8)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux AS release 4 (Nahant Update 8)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 (Nahant 2)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 (Nahant Update 2)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 (Nahant Update 3)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 (Nahant Update 4)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 (Nahant Update 5)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 (Nahant Update 7)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2011-02-16\",sw_type = \"OS\" where sw_software = \"Red Hat ES 4 32bit\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 5 (32-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 5 (64-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat ES 5.7 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Server 5.2 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Server 5.4 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.1 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.2 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.3 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.4 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 5.4 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise 5.4 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.4 Beta (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.5 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat 5.5\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.7 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-01-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 5.8 (Tikanga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 6 (64-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 6.2 (Santiago)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux Server release 6.5 (Santiago)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"Red Hat Server 6.2 (Santiago)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"Red Hat Server 6.2 (Saratoga)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2017-06-30\",sw_type = \"OS\" where sw_software = \"Red Hat Server 6.5 (Santiago)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

$q_string = "update software set sw_eol = \"2020-12-31\",sw_type = \"OS\" where sw_software = \"Red Hat Enterprise Linux 7 (64-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

print "Updating HP-UX\n";

# HP-UX
$q_string = "update software set sw_eol = \"2005-12-31\",sw_type = \"OS\" where sw_software = \"HP-UX B.11.00\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2015-12-31\",sw_type = \"OS\" where sw_software = \"HP-UX B.11.11\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2015-12-31\",sw_type = \"OS\" where sw_software = \"HP-UX B.11.23\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2020-12-31\",sw_type = \"OS\" where sw_software = \"HP-UX B.11.31\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

print "Updating Solaris\n";

# Solaris
$q_string = "update software set sw_eol = \"2004-02-01\",sw_type = \"OS\" where sw_software = \"Solaris 8\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2004-02-01\",sw_type = \"OS\" where sw_software = \"Solaris 8 HW\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2004-02-01\",sw_type = \"OS\" where sw_software = \"Solaris 8 HW 7/03 s28s_hw3wos_05a SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2004-02-01\",sw_type = \"OS\" where sw_software = \"Solaris 8 SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-10-01\",sw_type = \"OS\" where sw_software = \"Solaris 9\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2014-10-01\",sw_type = \"OS\" where sw_software = \"Solaris 9 9/04 s9s_u7wos_09 SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Oracle Solaris 10 (64-bit)\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Oracle Solaris 10 8/11 s10s_u10wos_17b SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Oracle Solaris 10 9/10 s10x_u9wos_14a X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 10/06 u8\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 10/08\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 10/08 s10s_u6wos_07b SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 10/08 s10x_u6wos_07b X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 10/09\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 10/09 s10x_u8wos_08a X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 11/06\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 11/06 s10s_u3wos_10 SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 11/06 s10x_u3wos_10 X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 5/08\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 5/08 s10s_u5wos_10 SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 5/08 s10x_u5wos_10 X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 5/09 s10s_u7wos_08 SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 5/09 s10x_u7wos_08 X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 6/06\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 6/06 s10s_u2wos_09a SPARC\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 8/07\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());
$q_string = "update software set sw_eol = \"2018-01-01\",sw_type = \"OS\" where sw_software = \"Solaris 10 8/07 s10x_u4wos_12b X86\" ";
$result = mysql_query($q_string)or die($q_string . ": " . mysql_error());

?>
