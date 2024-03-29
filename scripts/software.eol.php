#!/usr/local/bin/php
<?php
# Script: software.eol.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# 

include('settings.php');
include($Sitepath . '/function.php');

function dbconn($server,$database,$user,$pass) {
  $db = mysqli_connect($server,$user,$pass,$database);
  $db_select = mysqli_select_db($db,$database);
  return $db;
}

$db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# need a list of software and the EOL dates.
# needs to be updated when something new is discovered
# updates the inventory nightly.
# Red Hat OEL is here:
# https://access.redhat.com/articles/3078

# crontab listing:
# update the current software table with the end of life information.
#0 16 * * * /usr/local/bin/php /var/www/html/inventory/scripts/software.eol.php > /dev/null 2>&1

print "Updating CentOS\n";

### CentOS
$q_string = "update inv_software set sw_eol = \"2014-01-31\",sw_type = 8 where sw_software = \"CentOS 5.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-01-31\",sw_type = 8 where sw_software = \"CentOS release 5.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-01-31\",sw_type = 8 where sw_software = \"CentOS release 5.8 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));


$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS 6.2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.2 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.2 (Final)(Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.4 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.5 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.5 (Final) Santiago\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.7 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.8 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-06-30\",sw_type = 8 where sw_software = \"CentOS release 6.9 (Final)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));


$q_string = "update inv_software set sw_eol = \"2015-03-05\",sw_type = 8 where sw_software = \"CentOS Linux 7 (64-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-03-05\",sw_type = 8 where sw_software = \"CentOS Linux release 7.0.1406 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-11-19\",sw_type = 8 where sw_software = \"CentOS Linux release 7.1.1503 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-11-03\",sw_type = 8 where sw_software = \"CentOS Linux release 7.2.1511 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-31\",sw_type = 8 where sw_software = \"CentOS Linux release 7.3.1611 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-04-10\",sw_type = 8 where sw_software = \"CentOS Linux release 7.4.1708 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-10-30\",sw_type = 8 where sw_software = \"CentOS Linux release 7.5.1804 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-08-06\",sw_type = 8 where sw_software = \"CentOS Linux release 7.6.1810 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-31\",sw_type = 8 where sw_software = \"CentOS Linux release 7.7.1908 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-09-29\",sw_type = 8 where sw_software = \"CentOS Linux release 7.8.2003 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2024-06-30\",sw_type = 8 where sw_software = \"CentOS Linux release 7.9.2009 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# CentOS 7 End of Product is 06/30/2024 so the last release is always set to this date.
# All point releases are end of support as of the release date of the next point release.

$q_string = "update inv_software set sw_eol = \"2019-11-05\",sw_type = 8 where sw_software = \"CentOS Linux release 8.0.1905 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-05-05\",sw_type = 8 where sw_software = \"CentOS Linux release 8.1.1911 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-11-05\",sw_type = 8 where sw_software = \"CentOS Linux release 8.2.2004 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2021-12-31\",sw_type = 8 where sw_software = \"CentOS Linux release 8.3.2011 (Core)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# CentOS 8 End of Product is 12/31/2021 so the last release is always set to this date.
# All point releases are end of support as of the release date of the next point release.



print "Updating Red Hat\n";

### Red Hat
$q_string = "update inv_software set sw_eol = \"2002-07-01\",sw_type = 8 where sw_software = \"Red Hat ES 2.1 (Panama)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2002-07-01\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 2.1 (Panama)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 3 (32-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 4)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 5)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 6)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 8)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 3 (Taroon Update 9)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 2)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 3)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 4)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 5)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 6)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon Update 8)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2006-07-20\",sw_type = 8 where sw_software = \"Red Hat ES 3 (Taroon)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat AS 4 (Nahant)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 4 (32-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 4 (64-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat EL 4 (Nahant 2)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise AS 4 (Nahant 4)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 4)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 5)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat AS 4 (Nahant Update 5)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 6)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Enterprise Linux Enterprise Linux AS release 4 (October Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Oracle Enterprise Linux Enterprise Linux AS release 4 (October Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Oracle Red Hat Enterprise Linux ES release 4 (Nahant Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Oracle ES 4 (Nahant Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux ES release 4 (Nahant Update 8)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux AS release 4 (Nahant Update 8)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 (Nahant 2)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 (Nahant Update 2)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 (Nahant Update 3)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 (Nahant Update 4)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 (Nahant Update 5)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 (Nahant Update 7)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-02-16\",sw_type = 8 where sw_software = \"Red Hat ES 4 32bit\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2007-11-07\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 5 (32-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2007-11-07\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 5 (64-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-02-20\",sw_type = 8 where sw_software = \"Red Hat ES 5.7 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2009-01-20\",sw_type = 8 where sw_software = \"Red Hat Server 5.2 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-03-30\",sw_type = 8 where sw_software = \"Red Hat Server 5.4 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2007-11-07\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-05-21\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.1 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2009-01-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.2 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2009-09-02\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.3 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-03-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.4 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-03-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 5.4 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-03-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise 5.4 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-03-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.4 Beta (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-01-13\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.5 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-01-13\",sw_type = 8 where sw_software = \"Red Hat 5.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-02-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.7 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2013-01-07\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.8 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-01-31\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 5.11 (Tikanga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2011-05-19\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 6 (64-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-05-19\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.0 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2011-12-06\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.1 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2011-12-06\",sw_type = 8 where sw_software = \"Red Hat Server 6.1 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2012-06-20\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.2 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-06-20\",sw_type = 8 where sw_software = \"Red Hat Server 6.2 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2013-02-21\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.3 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2013-02-21\",sw_type = 8 where sw_software = \"Red Hat Server 6.3 (Saratoga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2013-11-21\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.4 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2013-11-21\",sw_type = 8 where sw_software = \"Red Hat Server 6.4 (Saratoga)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2014-10-14\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.5 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-10-14\",sw_type = 8 where sw_software = \"Red Hat Server 6.5 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2015-07-22\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.6 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-07-22\",sw_type = 8 where sw_software = \"Red Hat Server 6.6 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2016-05-10\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.7 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-05-10\",sw_type = 8 where sw_software = \"Red Hat Server 6.7 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2017-03-21\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.8 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-03-21\",sw_type = 8 where sw_software = \"Red Hat Server 6.8 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2018-06-19\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.9 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-06-19\",sw_type = 8 where sw_software = \"Red Hat Server 6.9 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2020-11-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 6.10 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-11-30\",sw_type = 8 where sw_software = \"Red Hat Server 6.9 (Santiago)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# Red Hat 6 End of Product is 11/30/2020 so the last release is always set to this date.
# All point releases are end of support as of the release date of the next point release.

$q_string = "update inv_software set sw_eol = \"2015-03-05\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux 7 (64-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-03-05\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.0 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-11-19\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.1 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-11-03\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.2 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-31\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.3 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-04-10\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.4 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-10-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.5 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-08-06\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.6 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-31\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.7 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-09-29\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.8 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2024-06-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 7.9 (Maipo)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# Red Hat 7 End of Product is 06/30/2024 so the last release is always set to this date.
# All point releases are end of support as of the release date of the next point release.

$q_string = "update inv_software set sw_eol = \"2019-11-05\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 8.0 (Oopta)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-04-28\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 8.1 (Oopta)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-11-03\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 8.2 (Oopta)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2029-05-30\",sw_type = 8 where sw_software = \"Red Hat Enterprise Linux Server release 8.3 (Oopta)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# Red Hat 8 End of Product is 05/30/2029 so the last release is always set to this date.
# All point releases are end of support as of the release date of the next point release.

print "Updating Debian\n";

$q_string = "update inv_software set sw_eol = \"2014-05-31\",sw_type = 8 where sw_software = \"Debian 6.0.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-02-06\",sw_type = 8 where sw_software = \"lenny/sid\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# There are a couple of Debian servers from the old Contact One days.


print "Updating HP-UX\n";

# HP-UX
$q_string = "update inv_software set sw_eol = \"2005-12-31\",sw_type = 8 where sw_software = \"HP-UX B.11.00\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-12-31\",sw_type = 8 where sw_software = \"HP-UX B.11.11\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-12-31\",sw_type = 8 where sw_software = \"HP-UX B.11.23\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-12-31\",sw_type = 8 where sw_software = \"HP-UX B.11.31\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Solaris\n";

# Solaris
$q_string = "update inv_software set sw_eol = \"2004-02-01\",sw_type = 8 where sw_software = \"Solaris 8\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2004-02-01\",sw_type = 8 where sw_software = \"Solaris 8 HW\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2004-02-01\",sw_type = 8 where sw_software = \"Solaris 8 HW 7/03 s28s_hw3wos_05a SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2004-02-01\",sw_type = 8 where sw_software = \"Solaris 8 SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-10-01\",sw_type = 8 where sw_software = \"Solaris 9\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-10-01\",sw_type = 8 where sw_software = \"Solaris 9 9/04 s9s_u7wos_09 SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Oracle Solaris 10 (64-bit)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Oracle Solaris 10 8/11 s10s_u10wos_17b SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Oracle Solaris 10 9/10 s10x_u9wos_14a X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 10/06 u8\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 10/08\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 10/08 s10s_u6wos_07b SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 10/08 s10x_u6wos_07b X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 10/09\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 10/09 s10x_u8wos_08a X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 11/06\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 11/06 s10s_u3wos_10 SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 11/06 s10x_u3wos_10 X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 5/08\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 5/08 s10s_u5wos_10 SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 5/08 s10x_u5wos_10 X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 5/09 s10s_u7wos_08 SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 5/09 s10x_u7wos_08 X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 6/06\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 6/06 s10s_u2wos_09a SPARC\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 8/07\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-01-01\",sw_type = 8 where sw_software = \"Solaris 10 8/07 s10x_u4wos_12b X86\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Netbackup\n";

# Netbackup 5
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-RedHat2.4 5.1MP1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-RedHat2.4 5.1MP3S0949\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-RedHat2.4 5.1MP5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-Solaris7 5.1MP5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-Solaris9 5.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-Solaris9 5.1MP5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-03-31\" where sw_software = \"NetBackup-Solaris_x86_9 5.1MP5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# Netbackup 6
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-HP-UX11.23 6.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-OSF1_V5 6.5.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-OSF1_V5 6.5.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-RedHat2.4 6.5.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-RedHat2.6 6.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-RedHat2.6 6.5.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-RedHat2.6 6.5.3.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-RedHat2.6 6.5.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-Solaris10 6.5.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2012-08-01\" where sw_software = \"NetBackup-Solaris8 6.5.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# Netbackup 7

$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6 7.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6 7.1.0.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6.18 7.5.0.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6.18 7.6.0.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6.18 7.6.0.2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6.18 7.7.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-RedHat2.6.18 7.7.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris10 7.1.0.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris10 7.7.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris9 7.1.0.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris_x86_10_64 7.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris_x86_10_64 7.1.0.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris_x86_10_64 7.6.0.2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2019-05-05\" where sw_software = \"NetBackup-Solaris_x86_10_64 7.7.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# Netbackup 8

$q_string = "update inv_software set sw_eol = \"2020-03-26\" where sw_software = \"NetBackup-RedHat2.6.18 8.0\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-26\" where sw_software = \"NetBackup-RedHat2.6.18 8.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Java\n";

$q_string = "update inv_software set sw_eol = \"2015-12-01\" where sw_software like \"Java 1.4%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2015-12-01\" where sw_software like \"Java 1.5.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2015-12-01\" where sw_software like \"Java 1.6.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2019-07-01\" where sw_software like \"Java 1.7.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2022-03-01\" where sw_software like \"Java 1.8.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# date change because Oracle said that once the next release is available (10 here), the current release is EOL
$q_string = "update inv_software set sw_eol = \"2018-03-01\" where sw_software like \"Java 1.9.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2018-09-01\" where sw_software like \"Java 1.10.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2023-09-01\" where sw_software like \"Java 1.11.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2019-09-01\" where sw_software like \"Java 1.12.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2020-03-01\" where sw_software like \"Java 1.13.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));


print "Updating OpenJDK\n";

$q_string = "update inv_software set sw_eol = \"2022-03-01\" where sw_software like \"openjdk 1.8.0%\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));


print "Updating VMWare ESX\n";

$q_string = "update inv_software set sw_eol = \"2018-09-22\" where sw_software = \"ESXi vSphere4.1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

# setting everything at 5.5 and earlier to the same EOL date. The ones before 5.5 aren't even listed
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESX v3.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESX v4.0 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESX v5.1 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESX v5.1 U2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESX v5.1U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 4.1 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 5.0 Update 1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 5.1 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 5.1 Update 1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 5.1 Update 2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v3.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v4.1 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.0\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.0 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.1 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.1 U2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.1u1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.1u2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESX v5.5 U2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 5.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi 5.5 Update 2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2018-09-19\" where sw_software = \"ESXi v5.5 U2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2020-03-12\" where sw_software = \"ESX v6.0 U2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-12\" where sw_software = \"ESXi 6.0 Update 1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-12\" where sw_software = \"ESXi v6.0 U1\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-12\" where sw_software = \"ESXi v6.0 u1a\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2020-03-12\" where sw_software = \"ESXi v6.0 u2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Apache\n";

$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Apache 1.3.22\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Apache 1.3.41\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Apache/2.0.52\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Apache/2.0.63\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"apache/2.2.11\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Apache/2.2.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Oracle HTTP Server Powered by Apache/1.3.19\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Oracle HTTP Server Powered by Apache/1.3.9\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/1.3.41 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.0.46\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.0.52\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.0.59  HP-UX_Apache-based_Web_Server\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.0.63\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.0\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.14 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.15  HP-UX_Apache-based_Web_Server (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.15 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.22 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.27 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.29  HP-UX_Apache-based_Web_Server (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.29 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Apache/2.2.4 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-07-01\" where sw_software = \"Server version: Oracle HTTP Server Powered by Apache/1.3.19 (Unix)\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Data Palette\n";
# was merged with some HP software which we don't use. Picking July 2016 as an arbitrary date.

$q_string = "update inv_software set sw_eol = \"2016-07-01\" where sw_software = \"DataPalette\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-07-01\" where sw_software = \"Data Palette\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-07-01\" where sw_software = \"Data Palette Collector 6.0.14\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-07-01\" where sw_software = \"Data Palette Expert Engine 6.0.14\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-07-01\" where sw_software = \"Data Palette Web Server 6.0.14\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating HP Monitoring\n";
# more arbitrary dates. Plus "2 years" from the original number; so 06 is 08, 08 is 10, 11 is 13. Leaving 12 alone for now.

$q_string = "update inv_software set sw_eol = \"2003-07-01\" where sw_software = \"HP OpenView Control 01.50.241\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2005-07-01\" where sw_software = \"HP OpenView Control 03.10.010\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2005-07-01\" where sw_software = \"HP OpenView Control 03.10.011\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2008-07-01\" where sw_software = \"HP Software Control 06.00.051\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-07-01\" where sw_software = \"HP Software Control 06.00.075\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-07-01\" where sw_software = \"HP Software Control 06.00.080\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2008-07-01\" where sw_software = \"HP Software Control 06.20.052\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2010-07-01\" where sw_software = \"08.06.501\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-07-01\" where sw_software = \"08.16.000\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-07-01\" where sw_software = \"08.51.102\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-07-01\" where sw_software = \"08.53.006\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-07-01\" where sw_software = \"08.60.005\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-07-01\" where sw_software = \"08.60.501\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2013-07-01\" where sw_software = \"11.00.044\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2013-07-01\" where sw_software = \"11.13.007\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2013-07-01\" where sw_software = \"11.14.014\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating HP Nonstop OS\n";

$q_string = "update inv_software set sw_eol = \"2014-07-01\" where sw_software = \"J06.14.00\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2015-07-01\" where sw_software = \"J06.16.02\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2016-07-01\" where sw_software = \"J06.18.01\" and sw_vendor = \"HP\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating PostgreSQL\n";
# https://www.postgresql.org/support/versioning/

$q_string = "update inv_software set sw_eol = \"2010-10-01\" where sw_software = \"psql (PostgreSQL) 7.4.17\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2010-10-01\" where sw_software = \"psql (PostgreSQL) 7.4.2\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2014-07-24\" where sw_software = \"psql (PostgreSQL) 8.4.20\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2014-07-24\" where sw_software = \"psql (PostgreSQL) 8.4.9\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2016-10-27\" where sw_software = \"psql (PostgreSQL) 9.1.6\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2017-11-09\" where sw_software = \"psql (PostgreSQL) 9.2.15\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));
$q_string = "update inv_software set sw_eol = \"2017-11-09\" where sw_software = \"psql (PostgreSQL) 9.2.24\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2018-11-08\" where sw_software = \"psql (PostgreSQL) 9.3.4\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2021-02-11\" where sw_software = \"psql (PostgreSQL) 9.5.3\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

$q_string = "update inv_software set sw_eol = \"2021-11-11\" where sw_software = \"psql (PostgreSQL) 9.6.6\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating IBM BPMS\n";
# https://www-01.ibm.com/support/docview.wss?uid=swg3x618741x22680p31

$q_string = "update inv_software set sw_eol = \"2019-09-30\" where sw_software = \"BPMS 8.5\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Riverbed OpNet\n";
#https://support.riverbed.com/content/support/eos_eoa.html

$q_string = "update inv_software set sw_eol = \"2018-08-31\" where sw_software = \"OpNet\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

print "Updating Centrify\n";
# marking as EOL as the company has moved away and we haven't replaced the agents

$q_string = "update inv_software set sw_eol = \"2015-04-01\" where sw_software = \"Centrify\" ";
$result = mysqli_query($db, $q_string)or die($q_string . ": " . mysqli_error($db));

mysqli_close($db);

?>
