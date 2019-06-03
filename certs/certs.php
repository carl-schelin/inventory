<?php
# Script: certs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "certs.php";

  logaccess($_SESSION['uid'], $package, "Accessing script");

# if help has not been seen yet,
  if (show_Help($Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Certificate Management</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_line( p_script_url ) {
  var answer = confirm("Delete this Certificate?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.dialog;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&cert_desc="       + encode_URI(af_form.cert_desc.value);
  af_url += "&cert_url="        + encode_URI(af_form.cert_url.value);
  af_url += "&cert_expire="     + encode_URI(af_form.cert_expire.value);
  af_url += "&cert_authority="  + encode_URI(af_form.cert_authority.value);
  af_url += "&cert_group="      + af_form.cert_group.value;
  af_url += "&cert_ca="         + af_form.cert_ca.value;
  af_url += "&cert_memo="       + encode_URI(af_form.cert_memo.value);
  af_url += "&cert_isca="       + af_form.cert_isca.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function textCounter(field,cntfield,maxlimit) {
  if (field.value.length > maxlimit)
    field.value = field.value.substring(0, maxlimit);
  else
    cntfield.value = maxlimit - field.value.length;
}

function clear_fields() {
  show_file('certs.mysql.php?update=-1');
}

$(function() {

  $( '#clickAddCertificate' ).click(function() {
    $( "#dialogCertificate" ).dialog('open');
  });

  $( "#dialogCertificate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 325,
    width: 1100,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCertificate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_file('certs.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Certificate",
        click: function() {
          attach_file('certs.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Certificate",
        click: function() {
          attach_file('certs.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="mainform">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('cert-hide');">Certificate Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('cert-help');">Help</a></th>
</tr>
</table>

<div id="cert-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Certificate</strong> - Update the Certificate being editied.</li>
    <li><strong>Add Certificate</strong> - Add a new Certificate.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Certificate Form</strong>
  <ul>
    <li><strong>Description</strong> - A description of the Certificate.</li>
    <li><strong>URL</strong> - What is the URL or server that is using this certificate.</li>
    <li><strong>Associated Certificate Authority</strong> - Select an Associated Certificate Authority</li>
    <li><strong>Is this a Certificate Authority?</strong> - If this is checked, the Certificate Authority entered below will be added to the Associated Certificate Authority list.</li>
    <li><strong>Expiration Date</strong> - When this Certificate expires. Your profile indicates whether you want to be notified of a pending expiration or an expired Certificate.</li>
    <li><strong>Certificate Authority</strong> - If this entry is a Certificate Authority, enter that here.</li>
    <li><strong>Managed By</strong> - Which group will receive notification emails about this Certificate prior to expiration and when it does expire.</li>
    <li><strong>Certificate Text</strong> - Enter up to 1024 characters of information to provide details about the Certificate.</li>
  </ul></li>
</ul>

<p><strong>NOTE:</strong> Regardless of the 'Managed By' group email, the Web Applications team will also receive copies of the email for verification.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickAddCertificate" value="Add Certificate"></td>
</tr>
</table>

</form>

<span id="table_mysql"></span>

</div>


<div id="dialogCertificate" title="Certificate Form">

<form name="dialog">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="6">Certificate Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Description: <input type="text" name="cert_desc" size="40"></td>
  <td class="ui-widget-content" colspan="3">URL: <input type="text" name="cert_url" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="4">Associated Certificate Authority <select name="cert_ca">
<option value="0">Select Certificate Authority</option>
<?php
  $q_string  = "select cert_id,cert_desc ";
  $q_string .= "from certs ";
  $q_string .= "where cert_isca = 1 ";
  $q_string .= "order by cert_desc";
  $q_certs = mysql_query($q_string) or die(q_string . ": " . mysql_error());
  while ($a_certs = mysql_fetch_array($q_certs)) {
    print "<option value=\"" . $a_certs['cert_id'] . "\">" . htmlspecialchars($a_certs['cert_desc']) . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content" colspan="2"><label><input type="checkbox" name="cert_isca"> Is this a Certificate Authority?</label></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="2">Expiration Date: <input type="text" name="cert_expire" value="0000-00-00" size="12"></td>
  <td class="ui-widget-content" colspan="2">Certificate Authority: <input type="text" name="cert_authority" size="40"></td>
  <td class="ui-widget-content" colspan="2">Managed By: <select name="cert_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysql_query($q_string) or die(mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="6"><textarea name="cert_memo" cols="140" rows="5" onKeyDown="textCounter(document.dialog.cert_memo,document.dialog.remLen,1024);" onKeyUp="textCounter(document.dialog.cert_memo,document.dialog.remLen,1024);"></textarea><br><input readonly type="text" name="remLen" size="4" maxlength="4" value="1024"> characters left</td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
