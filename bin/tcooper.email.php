#!/usr/local/bin/php
<?php
# Script: tcooper.email.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

//define the receiver of the email
  $to = "tlcooper@west.com,cschelin@west.com";
  $to = "cschelin@west.com";

//define the subject of the email
  $subject     = "West Safety Services Asset Report";

//create a boundary string. It must be unique
//so we use the MD5 algorithm to generate a random hash
  $random_hash = md5(date('r', time()));

//define the headers we want passed. Note that they are separated with \r\n
  $headers = "From: root@incojs01.scc911.com\r\nReply-To: cschelin@west.com";

//add boundary string and mime type specification
  $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";

//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
  $attachment = chunk_split(base64_encode(file_get_contents('/usr/local/httpd/htsecure/reports/tcooper.spreadsheet.csv')));

//define the body of the message.
  ob_start(); //Turn on output buffering
?>
--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

West Safety Services Asset report.
Unix team
Virtualization team
Engineering Lab team
Windows team
Network Engineering team

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<h2>TechOps Report</h2>
<p>West Safety Service Asset report.</p>

<p>Unix team</br>
Virtualization team</br>
Engineering Lab team</br>
Windows team</br>
Network Engineering team</p>

--PHP-alt-<?php echo $random_hash; ?>--

--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: text/plain; name="tcooper.spreadsheet.csv" 
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
  echo $mail_sent ? "WSS Asset Report eMail sent\n" : "WSS Asset Report eMail failed\n";

?>
