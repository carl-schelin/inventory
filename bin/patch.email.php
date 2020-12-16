#!/usr/local/bin/php
<?php
# Script: patch.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

//define the receiver of the email
  $to = "UnixServerReport@intradonet.intrado.com,carl.schelin@intrado.com,marcus.cotey@intrado.com";
// Stan Trevino added for Denise as she's on PTO for August.
  if (date('Y/m') == "2017/08") {
    $to .= ",etrevino@west.com";
  }

//define the subject of the email
  $subject     = date('Y-m-d') . " TechOps Patch Report";

//create a boundary string. It must be unique
//so we use the MD5 algorithm to generate a random hash
  $random_hash = md5(date('r', time()));

//define the headers we want passed. Note that they are separated with \r\n
  $headers = "From: root@" . $hostname . "\r\nReply-To: " . $Sitedev;

//add boundary string and mime type specification
  $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";

//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
  $attachment = chunk_split(base64_encode(file_get_contents('/usr/local/httpd/htsecure/reports/patch.count.techops.' . date('Y-m-d') . '.csv')));

//define the body of the message.
  ob_start(); //Turn on output buffering
?>
--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

TechOps Patch Count report.

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<h2>TechOps Report</h2>
<p>Patch count report for TechOps.</p>

--PHP-alt-<?php echo $random_hash; ?>--

--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: text/plain; name="patch.count.techops.<?php print date('Y-m-d'); ?>.csv" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

<?php echo $attachment; ?>
--PHP-mixed-<?php echo $random_hash; ?>--

<?php
//copy current buffer contents into $message variable and delete current output buffer
  $message = ob_get_clean();

//send the email
  $mail_sent = @mail( $to, $subject, $message, $headers );

//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
  echo $mail_sent ? "TechOps Mail sent\n" : "TechOps Mail failed\n";




##########
## Engineering Report
##########

//define the subject of the email
  $subject = date('Y-m-d') . " Engineering Patch Report";

//create a boundary string. It must be unique
//so we use the MD5 algorithm to generate a random hash
  $random_hash = md5(date('r', time()));

//define the headers we want passed. Note that they are separated with \r\n
  $headers = "From: root@" . $hostname . "\r\nReply-To: " . $Sitedev;

//add boundary string and mime type specification
  $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";

//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
  $attachment = chunk_split(base64_encode(file_get_contents('/usr/local/httpd/htsecure/reports/patch.count.engineering.' . date('Y-m-d') . '.csv')));

//define the body of the message.
  ob_start(); //Turn on output buffering
?>
--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

Engineering patch count report.

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<h2>Engineering Report</h2>
<p>Patch count report for Engineering</p>

--PHP-alt-<?php echo $random_hash; ?>--

--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: text/plain; name="patch.count.engineering.<?php print date('Y-m-d'); ?>.csv" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

<?php echo $attachment; ?>
--PHP-mixed-<?php echo $random_hash; ?>--

<?php
//copy current buffer contents into $message variable and delete current output buffer
  $message = ob_get_clean();

//send the email
  $mail_sent = @mail( $to, $subject, $message, $headers );

//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
  echo $mail_sent ? "Engineering Mail sent\n" : "Engineering Mail failed\n";

  mysqli_close($db);

?>
