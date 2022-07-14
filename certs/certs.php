<?php
# Script: certs.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

# connect to the database
  $db = db_connect($DBserver, $DBname, $DBuser, $DBpassword);

  check_login($db, $AL_Edit);

  $package = "certs.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");


  $q_string  = "select usr_notify ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . $mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

  if ($a_users['usr_notify'] == 0) {
    $a_users['usr_notify'] = 90;
  }


# if help has not been seen yet,
  if (show_Help($db, $Sitepath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Certificate Editor</title>

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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&cert_desc="       + encode_URI(af_form.cert_desc.value);
  af_url += "&cert_url="        + encode_URI(af_form.cert_url.value);
  af_url += "&cert_expire="     + encode_URI(af_form.cert_expire.value);
  af_url += "&cert_authority="  + encode_URI(af_form.cert_authority.value);
  af_url += "&cert_subject="    + encode_URI(af_form.cert_subject.value);
  af_url += "&cert_group="      + af_form.cert_group.value;
  af_url += "&cert_ca="         + af_form.cert_ca.value;
  af_url += "&cert_memo="       + encode_URI(af_form.cert_memo.value);
  af_url += "&cert_isca="       + af_form.cert_isca.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&cert_desc="       + encode_URI(uf_form.cert_desc.value);
  uf_url += "&cert_url="        + encode_URI(uf_form.cert_url.value);
  uf_url += "&cert_expire="     + encode_URI(uf_form.cert_expire.value);
  uf_url += "&cert_authority="  + encode_URI(uf_form.cert_authority.value);
  uf_url += "&cert_subject="    + encode_URI(uf_form.cert_subject.value);
  uf_url += "&cert_group="      + uf_form.cert_group.value;
  uf_url += "&cert_ca="         + uf_form.cert_ca.value;
  uf_url += "&cert_memo="       + encode_URI(uf_form.cert_memo.value);
  uf_url += "&cert_isca="       + uf_form.cert_isca.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function textCounter(field, cntfield, maxlimit) {
  if (field.value.length > maxlimit)
    field.value = field.value.substring(0, maxlimit);
  else
    cntfield.value = maxlimit - field.value.length;
}

function clear_fields() {
  show_file('certs.mysql.php?update=-1');
}

$(function() {

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 450,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogCreate" ).hide();
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
        text: "Add Certificate",
        click: function() {
          attach_file('certs.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 450,
    width: 600,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogUpdate" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          update_file('certs.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Certificate",
        click: function() {
          update_file('certs.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Certificate",
        click: function() {
          update_file('certs.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Certificate Manager</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('cert-help');">Help</a></th>
</tr>
</table>

<div id="cert-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Certificate management is important to the company as an expired certificate can result in a system that is inaccessible.</p>

<p>When adding certificates, there should be a top level certificate authority defined and any new certs are attached to that CA.</p>

<p><strong>NOTE:</strong> Regardless of the 'Managed By' group email, the Web Applications team will also receive copies of the email for verification.</p>

</div>

</div>


<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Certificate"></td>
</tr>
</table>


<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Certificate Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('cert-listing-help');">Help</a></th>
</tr>
</table>

<div id="cert-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Certificate Management Listing</strong></p>

<p>This page lists all the certificate authorities and certificates that are associated with those CAs. These certificates can be 
used for a multitude of purposes from websites to monitoring agents to kubernetes.</p>

<p>To add a certificate, click the Add Certificate button. This will bring up a dialog box which you can then use to add a 
certificate to the manager.</p>

<p>To edit an existing certificate, click on the entry in the listing. A dialog box will be displayed where you can edit the current 
entry, or to easily make a change, you can create a new entry.</p>

<p>Note that under the Members column is a number which indicates the number of servers that are currently associated with the 
certificate. You cannot remove a certificate until this value is zero. Clicking on the number will take you to a listing of 
servers that are using this certificate.</p>

</div>

</div>


<span id="table_mysql"></span>

</div>



<div id="dialogCreate" title="Add Certificate">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="cert_desc" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">URL: <input type="text" name="cert_url" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Associated Certificate Authority <select name="cert_ca">
<option value="0">Select Certificate Authority</option>
<?php
  $q_string  = "select cert_id,cert_desc ";
  $q_string .= "from certs ";
  $q_string .= "where cert_isca = 1 ";
  $q_string .= "order by cert_desc";
  $q_certs = mysqli_query($db, $q_string) or die(q_string . ": " . mysqli_error($db));
  while ($a_certs = mysqli_fetch_array($q_certs)) {
    print "<option value=\"" . $a_certs['cert_id'] . "\">" . htmlspecialchars($a_certs['cert_desc']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="cert_isca"> Is this a Certificate Authority?</label></td>
</tr>
<tr>
  <td class="ui-widget-content">Not After Date: <input type="date" name="cert_expire" value="1971-01-01" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Certificate Issuer: <input type="text" name="cert_authority" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Certificate Subject: <input type="text" name="cert_subject" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Managed By: <select name="cert_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><textarea name="cert_memo" cols="80" rows="5" onKeyDown="textCounter(document.formCreate.cert_memo,document.formCreate.remLenCreate,1024);" onKeyUp="textCounter(document.formCreate.cert_memo,document.formCreate.remLenCreate,1024);"></textarea><br><input readonly type="text" name="remLenCreate" size="4" maxlength="4" value="1024"> characters left</td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Certificate">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="cert_desc" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">URL: <input type="text" name="cert_url" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Associated Certificate Authority <select name="cert_ca">
<option value="0">Select Certificate Authority</option>
<?php
  $q_string  = "select cert_id,cert_desc ";
  $q_string .= "from certs ";
  $q_string .= "where cert_isca = 1 ";
  $q_string .= "order by cert_desc";
  $q_certs = mysqli_query($db, $q_string) or die(q_string . ": " . mysqli_error($db));
  while ($a_certs = mysqli_fetch_array($q_certs)) {
    print "<option value=\"" . $a_certs['cert_id'] . "\">" . htmlspecialchars($a_certs['cert_desc']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><label><input type="checkbox" name="cert_isca"> Is this a Certificate Authority?</label></td>
</tr>
<tr>
  <td class="ui-widget-content">Not After Date: <input type="date" name="cert_expire" value="1971-01-01" size="12"></td>
</tr>
<tr>
  <td class="ui-widget-content">Certificate Issuer: <input type="text" name="cert_authority" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Certificate Subject: <input type="text" name="cert_subject" size="60"></td>
</tr>
<tr>
  <td class="ui-widget-content">Managed By: <select name="cert_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_disabled = 0 ";
  $q_string .= "order by grp_name";
  $q_groups = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_groups = mysqli_fetch_array($q_groups)) {
    print "<option value=\"" . $a_groups['grp_id'] . "\">" . htmlspecialchars($a_groups['grp_name']) . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content"><textarea name="cert_memo" cols="80" rows="5" onKeyDown="textCounter(document.formUpdate.cert_memo,document.formUpdate.remLenUpdate,1024);" onKeyUp="textCounter(document.formUpdate.cert_memo,document.formUpdate.remLenUpdate,1024);"></textarea><br><input readonly type="text" name="remLenUpdate" size="4" maxlength="4" value="1024"> characters left</td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
