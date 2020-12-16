<?php
# Script: ticket.php
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

  $package = "ticket.php";

  logaccess($db, $_SESSION['uid'], $package, "Managing an issue");

  $formVars['id']     = clean($_GET['id'], 10);
  if (!isset($_GET['id'])) {
    $formVars['id'] = 0;
  }

  $formVars['server'] = clean($_GET['server'], 10);
  if (!isset($_GET['server'])) {
    $formVars['server'] = 0;
  }


  if ($formVars['id'] == 0) {
    $q_string  = "select usr_last,usr_first,usr_phone,usr_email ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $_SESSION['uid'];
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_users = mysqli_fetch_array($q_users);
  } else {
    $q_string  = "select iss_discovered,iss_closed,iss_subject,iss_user ";
    $q_string .= "from issue ";
    $q_string .= "where iss_id = " . $formVars['id'];
    $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_issue = mysqli_fetch_array($q_issue);

    $q_string  = "select usr_last,usr_first,usr_phone,usr_email ";
    $q_string .= "from users ";
    $q_string .= "where usr_id = " . $a_issue['iss_user'];
    $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    $a_users = mysqli_fetch_array($q_users);
  }

  $q_string  = "select inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_id = " . $formVars['server'];
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php print $a_inventory['inv_name']; ?> Issue</title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_ticket( p_script_url ) {
  var answer = confirm("Delete Ticket?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function delete_detail( p_script_url ) {
  var answer = confirm("Delete Detail Record?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function delete_morning( p_script_url ) {
  var answer = confirm("Delete Morning Report Record?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_file( p_script_url, update ) {
  var af_form = document.start;
  var af_url;

  af_url  = '?update='     + update;
  af_url += '&id='         + <?php print $formVars['id']; ?>;

  af_url += '&iss_companyid='   + <?php print $formVars['server']; ?>;
  af_url += '&iss_discovered='  + encode_URI(af_form.iss_discovered.value);
  af_url += '&iss_subject='     + encode_URI(af_form.iss_subject.value);
  af_url += '&iss_closed='      + encode_URI(af_form.iss_closed.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_ticket( p_script_url, update ) {
  var af_form = document.start;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + <?php print $formVars['id']; ?>;

  af_url += '&sup_id='        + af_form.sup_id.value;
  af_url += "&sup_company="   + encode_URI(af_form.sup_company.value);
  af_url += "&sup_case="      + encode_URI(af_form.sup_case.value);
  af_url += "&sup_contact="   + encode_URI(af_form.sup_contact.value);
  af_url += "&sup_phone="     + encode_URI(af_form.sup_phone.value);
  af_url += "&sup_email="     + encode_URI(af_form.sup_email.value);
  af_url += "&sup_govid="     + encode_URI(af_form.sup_govid.value);
  af_url += "&sup_timestamp=" + encode_URI(af_form.sup_timestamp.value);
  af_url += "&sup_rating="    + radio_Loop(af_form.sup_rating, 6);

  if (af_form.sup_case.value == '') {
    alert("You need to supply a case number.");
  } else {
    script = document.createElement('script');
    script.src = p_script_url + af_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

function attach_detail( p_script_url, update ) {
  var af_form = document.start;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + <?php print $formVars['id']; ?>;

  af_url += '&det_id='        + af_form.det_id.value;
  af_url += "&det_text="      + encode_URI(af_form.det_text.value);
  af_url += "&det_timestamp=" + encode_URI(af_form.det_timestamp.value);
  af_url += "&det_user="      + af_form.det_user.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_morning( p_script_url, update ) {
  var am_form = document.start;
  var am_url;

  am_url  = '?update='   + update;
  am_url += '&id='       + <?php print $formVars['id']; ?>;
  am_url += '&server='   + <?php print $formVars['server']; ?>;

  am_url += '&morn_id='        + am_form.morning_id.value;
  am_url += "&morn_text="      + encode_URI(am_form.morn_text.value);
  am_url += "&morn_timestamp=" + encode_URI(am_form.morn_timestamp.value);
  am_url += "&morn_user="      + am_form.morn_user.value;
  am_url += "&morn_status="    + am_form.morn_status.value;

  script = document.createElement('script');
  script.src = p_script_url + am_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_hardware( p_script_url, update ) {
  var ah_form = document.start;
  var ah_url;

  ah_url  = '?update='    + update;
  ah_url += '&id='        + <?php print $formVars['id']; ?>;
  ah_url += '&hw_server=' + <?php print $formVars['server']; ?>;

  ah_url += '&hw_rma='   + encode_URI(ah_form.hw_rma.value);
  ah_url += '&hw_note='  + encode_URI(ah_form.hw_note.value);
  ah_url += '&hw_radio=' + radio_Loop(ah_form.hw_radio, ah_form.hw_count.value);

  script = document.createElement('script');
  script.src = p_script_url + ah_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function verify_rma() {
  if (document.start.hw_rma.value.length == 0) {
    document.start.hw_button.value = "RMA Number Required";
    document.start.hw_button.disabled = true;
  } else {
    document.start.hw_button.value = "Advise Shipping & Receiving";
    document.start.hw_button.disabled = false;
  }
}

function reset_detail() {
  document.start.det_text.value = '';
  document.start.remLen.value = 1800;
  document.start.det_user[0].selected = true;
  document.start.det_timestamp.value = 'Current Time';
  document.start.detupdate.disabled = true;
  document.start.det_id.value = 0;
  document.start.format_bold.value = 0;
  document.start.format_italic.value = 0;
  document.start.format_underline.value = 0;
  document.start.format_preserve.value = 0;
<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
  document.getElementById('show_bold').innerHTML = 'Bold';
  document.getElementById('show_italic').innerHTML = 'Italic';
  document.getElementById('show_underline').innerHTML = 'Underline';
  document.getElementById('show_preserve').innerHTML = 'Preserve Formatting';
<?php
  }
?>
}

function clear_fields() {
<?php
  if ($formVars['id'] > 0) {
    print "  show_file('support.mysql.php?update=-1&id=" . $formVars['id'] . "');\n";
    print "  show_file('morning.mysql.php?update=-1&id=" . $formVars['id'] . "&server=" . $formVars['server'] . "');\n";
    print "  show_file('comments.mysql.php?update=-1&id=" . $formVars['id'] . "');\n";
    print "  show_file('shipping.mysql.php?update=-1&id=" . $formVars['id'] . "&hw_server=" . $formVars['server'] . "');\n";
  }
?>
}

<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
// the purpose here is to permit the insertion/replacement of formatted text
function formatText(p_format) {
  var ft_form = document.start;
  var ft_text = ft_form.det_text.value;

  ft_form.det_text.focus();
  var ft_cursor = getInputSelection(ft_form.det_text);

  var ft_st_start  = ft_text.substring(0, ft_cursor.start);
  var ft_st_middle = ft_text.substring(ft_cursor.start, ft_cursor.end);
  var ft_st_end    = ft_text.substring(ft_cursor.end);

  if (p_format == "bold") {

    if (ft_form.format_bold.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_bold').value = 'BOLD';
        ft_form.format_bold.value = 1;
        ft_det_text = ft_st_start + "<b>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_det_text = ft_st_start + "<b>" + ft_st_middle + "</b>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_bold').value = 'Bold';
        ft_form.format_bold.value = 0;
        ft_det_text = ft_st_start + "</b>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_det_text = ft_st_start + "</b>" + ft_st_middle + "<b>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "italic") {

    if (ft_form.format_italic.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_italic').value = 'ITALIC';
        ft_form.format_italic.value = 1;
        ft_det_text = ft_st_start + "<i>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_det_text = ft_st_start + "<i>" + ft_st_middle + "</i>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_italic').value = 'Italic';
        ft_form.format_italic.value = 0;
        ft_det_text = ft_st_start + "</i>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_det_text = ft_st_start + "</i>" + ft_st_middle + "<i>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "underline") {

    if (ft_form.format_underline.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_underline').value = 'UNDERLINE';
        ft_form.format_underline.value = 1;
        ft_det_text = ft_st_start + "<u>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_det_text = ft_st_start + "<u>" + ft_st_middle + "</u>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_underline').value = 'Underline';
        ft_form.format_underline.value = 0;
        ft_det_text = ft_st_start + "</u>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_det_text = ft_st_start + "</u>" + ft_st_middle + "<u>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "preserve") {

    if (ft_form.format_preserve.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_preserve').value = 'PRESERVE FORMATTING';
        ft_form.format_preserve.value = 1;
        ft_det_text = ft_st_start + "<pre>" + ft_st_end;
        ft_cursor.end += 5;
      } else {
        ft_det_text = ft_st_start + "<pre>" + ft_st_middle + "</pre>" + ft_st_end;
        ft_cursor.end += 11;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_preserve').value = 'Preserve Formatting';
        ft_form.format_preserve.value = 0;
        ft_det_text = ft_st_start + "</pre>" + ft_st_end;
        ft_cursor.end += 6;
      } else {
        ft_det_text = ft_st_start + "</pre>" + ft_st_middle + "<pre>" + ft_st_end;
        ft_cursor.end += 11;
      }
    }

  }

  ft_form.det_text.value = ft_det_text;
  setCaretPosition('det_text', ft_cursor.end);
}

function getInputSelection(el) {
  var start = 0, end = 0, normalizedValue, range, textInputRange, len, endRange;

  if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
    start = el.selectionStart;
    end = el.selectionEnd;
  } else {
    range = document.selection.createRange();

    if (range && range.parentElement() == el) {
      len = el.value.length;
      normalizedValue = el.value.replace(/\r\n/g, "\n");

      // Create a working TextRange that lives only in the input
      textInputRange = el.createTextRange();
      textInputRange.moveToBookmark(range.getBookmark());

      // Check if the start and end of the selection are at the very end
      // of the input, since moveStart/moveEnd doesn't return what we want
      // in those cases
      endRange = el.createTextRange();
      endRange.collapse(false);

      if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
        start = end = len;
      } else {
        start = -textInputRange.moveStart("character", -len);
        start += normalizedValue.slice(0, start).split("\n").length - 1;

        if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
          end = len;
        } else {
          end = -textInputRange.moveEnd("character", -len);
          end += normalizedValue.slice(0, end).split("\n").length - 1;
        }
      }
    }
  }

  return {
    start: start,
    end: end
  };
}

function setCaretPosition(elemId, caretPos) {
  var elem = document.getElementById(elemId);

  if (elem != null) {
    if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.move('character', caretPos);
      range.select();
    } else {
      if (elem.selectionStart) {
        elem.focus();
        elem.setSelectionRange(caretPos, caretPos);
      } else {
        elem.focus();
      }
    }
  }
}
<?php
  }
?>

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

</script>

</head>
<body class="ui-widget-content" onLoad="clear_fields();">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="start">

<div id="tabs">

<ul>
  <li><a href="#information"><?php print $a_inventory['inv_name']; ?> Information</a></li>
  <li><a href="#support">Support Form</a></li>
  <li><a href="#morning">Morning Report</a></li>
  <li><a href="#problem">Problem Form</a></li>
  <li><a href="#hardware">Hardware Form</a></li>
</ul>


<div id="information">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Issue Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('issue-help');">Help</a></th>
</tr>
</table>

<div id="issue-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Close Issue</strong> - Sets the Closed field to today's date which closed the issue.</li>
    <li><strong>Save Issue Changes</strong> - If you update the Issue Form, click here to save changes.</li>
    <li><strong>Reopen Issue</strong> - Reopen a closed issue. Resets the Closed field to '0000-00-00'.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Issue Form</strong>
  <ul>
    <li><strong>Discovered</strong> - The date the problem was discovered.</li>
    <li><strong>Closed</strong> - Enter the date to close the Issue. 'Current Date' is replaced by today's date.</li>
    <li><strong>Problem Description</strong> - Enter a short description of the problem here.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<?php
  if ($formVars['id'] == 0) {
    $newissue = "";
    $updateissue = "disabled";
  } else {
    $newissue = "disabled";
    $updateissue = "";
  }
  if ($a_issue['iss_closed'] == '0000-00-00') {
    print "  <input type=\"button\" " . $updateissue . " name=\"close\"     value=\"Close Issue\"        onClick=\"javascript:attach_file('ticket.mysql.php', 2);\">\n";
    print "  <input type=\"button\" " . $updateissue . " name=\"save\"      value=\"Save Issue Changes\" onClick=\"javascript:attach_file('ticket.mysql.php', 1);\">\n";
  } else {
    print "  <input type=\"button\" " . $updateissue . " name=\"close\"     value=\"Reopen Issue\"       onClick=\"javascript:attach_file('ticket.mysql.php', 2);\">\n";
  }
?>
</td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Requestor Information</th>
</tr>
<?php
  print "<tr>\n";
  print "  <td class=\"ui-widget-content\"><strong>User</strong>: "   . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>Phone</strong>: "  . $a_users['usr_phone'] . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>E-Mail</strong>: " . $a_users['usr_email'] . "</td>\n";
  print "</tr>\n";
?>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Support Information</th>
</tr>
<tr>
  <th class="ui-state-default" colspan="5">Hardware Support</th>
</tr>
<?php

  $q_string  = "select sup_company,sup_phone,sup_email,sup_web,sup_contract ";
  $q_string .= "from hardware ";
  $q_string .= "left join support on hardware.hw_supportid = support.sup_id ";
  $q_string .= "where hw_companyid = " . $formVars['server'] . " and hw_primary = 1";
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_hardware) > 0) {
    while ($a_hardware = mysqli_fetch_array($q_hardware)) {

      print "<tr>\n";
      print "  <td class=\"ui-widget-content\" colspan=\"2\"><strong>Company</strong>: <a href=\"" . $a_hardware['sup_web'] . "\">" . $a_hardware['sup_company'] . "</a></td>\n";
      print "  <td class=\"ui-widget-content\"><strong>Phone</strong>: "    . $a_hardware['sup_phone'] . "</td>\n";
      print "  <td class=\"ui-widget-content\"><strong>E-Mail</strong>: "   . $a_hardware['sup_email'] . "</td>\n";
      print "  <td class=\"ui-widget-content\"><strong>Contract</strong>: " . $a_hardware['sup_contract'] . "</td>\n";
      print "</tr>\n";
    }
  } else {
    print "<tr>\n";
    print "  <td class=\"ui-widget-content\" colspan=\"5\">No Support Information Available</td>\n";
    print "</tr>\n";
  }
?>
<tr>
  <th class="ui-state-default" colspan="5">Software Support</th>
</tr>
<?php
  $q_string  = "select sw_software,sw_supportid ";
  $q_string .= "from software ";
  $q_string .= "where sw_companyid = " . $formVars['server'] . " ";
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_software) > 0) {
    while ($a_software = mysqli_fetch_array($q_software)) {
      if ($a_software['sw_supportid'] != 0) {
        $q_string  = "select sup_company,sup_phone,sup_email,sup_web,sup_contract ";
        $q_string .= "from support ";
        $q_string .= "where sup_id = " . $a_software['sw_supportid'];
        $q_support = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_support = mysqli_fetch_array($q_support);

        print "<tr>\n";
        print "  <td class=\"ui-widget-content\"><strong>Software</strong>: " . $a_software['sw_software'] . "</td>\n";
        print "  <td class=\"ui-widget-content\"><strong>Company</strong>: <a href=\"" . $a_support['sup_web'] . "\">" . $a_support['sup_company'] . "</a></td>\n";
        print "  <td class=\"ui-widget-content\"><strong>Phone</strong>: "    . $a_support['sup_phone'] . "</td>\n";
        print "  <td class=\"ui-widget-content\"><strong>E-Mail</strong>: "   . $a_support['sup_email'] . "</td>\n";
        print "  <td class=\"ui-widget-content\"><strong>Contract</strong>: " . $a_support['sup_contract'] . "</td>\n";
        print "</tr>\n";
      }
    }
  } else {
    print "  <td class=\"ui-widget-content\" colspan=\"5\">No Support Information Available</td>\n";
  }
?>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Primary Hardware Information</th>
</tr>
<?php
  $q_string  = "select part_name,hw_serial,hw_asset,mod_name ";
  $q_string .= "from hardware ";
  $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where hw_primary = 1 and hw_companyid = " . $formVars['server'] . " ";
  $q_string .= "order by hw_type,hw_vendorid";
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_hardware = mysqli_fetch_array($q_hardware);

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\"><strong>Type</strong>: "    . $a_hardware['part_name']  . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>Serial</strong>: "  . $a_hardware['hw_serial']  . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>Asset</strong>: "   . $a_hardware['hw_asset']   . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>Model</strong>: "   . $a_hardware['mod_name']   . "</td>\n";
  print "</tr>\n";
?>
</table>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="10">Location Information</th>
</tr>
<?php
  $q_string  = "select loc_name,loc_addr1,loc_addr2,ct_city,st_acronym,loc_zipcode,cn_acronym,inv_row,inv_rack,inv_unit ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on inventory.inv_location = locations.loc_id ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = locations.loc_state ";
  $q_string .= "left join country on country.cn_id = locations.loc_country ";
  $q_string .= "where inv_id = " . $formVars['server'];
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  print "<tr>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['loc_name'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['loc_addr1'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['loc_addr2'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['ct_city'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['st_acronym'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['loc_zipcode'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['cn_acronym'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_row'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_rack'] . "</td>\n";
  print "  <td class=\"ui-widget-content\">" . $a_inventory['inv_unit'] . "</td>\n";
  print "</tr>\n";
?>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Issue Form</th>
</tr>
<?php

  $q_string  = "select iss_discovered,iss_closed,iss_subject ";
  $q_string .= "from issue ";
  $q_string .= "where iss_id = " . $formVars['id'];
  $q_issue = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_issue = mysqli_fetch_array($q_issue);

  if ($a_issue['iss_closed'] == '0000-00-00') {
    print "  <td class=\"ui-widget-content\"><strong>Discovered</strong>: <input type=\"text\" name=\"iss_discovered\" size=\"10\" value=\"" . $a_issue['iss_discovered'] . "\"></td>\n";
    print "  <td class=\"ui-widget-content\"><strong>Closed</strong>: <input type=\"text\" name=\"iss_closed\" size=\"15\" value=\"Current Date\"></td>\n";
    print "  <td class=\"ui-widget-content\"><strong>Problem Description</strong>: <input type=\"text\" name=\"iss_subject\" size=\"50\" value=\"" . $a_issue['iss_subject'] . "\"></td>\n";
  } else {
    print "  <td class=\"ui-widget-content\"><strong>Discovered</strong>: " . $a_issue['iss_discovered'] . "<input type=\"hidden\" name=\"iss_discovered\" value=\"" . $a_issue['iss_discovered'] . "\"</td>\n";
    print "  <td class=\"ui-widget-content\"><strong>Closed</strong>: " . $a_issue['iss_closed']     . "<input type=\"hidden\" name=\"iss_closed\" value=\"" . $a_issue['iss_closed'] . "\"</td>\n";
    print "  <td class=\"ui-widget-content\"><strong>Problem Description</strong>: " . $a_issue['iss_subject']    . "<input type=\"hidden\" name=\"iss_subject\" value=\"" . $a_issue['iss_subject'] . "\"</td>\n";
  }

?>
</tr>
</table>

</div>

<?php
  if ($formVars['id'] > 0) {
?>

<div id="support">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('support-hide');">Support Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('support-help');">Help</a></th>
</tr>
</table>

<div id="support-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Update Ticket</strong> - After selecting a ticket to edit, click here to save changes.</li>
    <li><strong>Add Ticket</strong> - Add a new ticket. You can also select an existing ticket, make necessary changes, and click this button to add a new item.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Support Form</strong>
  <ul>
    <li><strong>Company</strong> - The name of the company you called or are working with to resolve the issue.</li>
    <li><strong>Case #</strong> - The case number from the support company. This field is required.</li>
    <li><strong>Rate Call</strong> - Rate the support call.</li>
    <li><strong>Full Name</strong> - The name of the contact.</li>
    <li><strong>E-Mail</strong> - E-Mail address of the support contact.</li>
    <li><strong>Phone</strong> - Enter the phone number of the contact.</li>
    <li><strong>Government ID Number</strong> - ID information so the field engineer can enter the data center.</li>
    <li><strong>Time of Call</strong> - Enter the time of the ticket.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>Support Management</strong> title bar to toggle the <strong>Support Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="support-hide" style="display: none">

<table class="ui-styled-table">
<?php
  if ($a_issue['iss_closed'] == '0000-00-00') {
?>
<tr>
  <td colspan="4" class="ui-widget-content button">
<input type="button" disabled="true" name="supupdate"     value="Update Ticket" onClick="javascript:attach_ticket('support.mysql.php', 1);hideDiv('support-hide');">
<input type="hidden" name="sup_id" value="0">
<input type="button"                 name="supportbutton" value="Add Ticket"    onClick="javascript:attach_ticket('support.mysql.php', 0);">
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="4">Support Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Company: <input type="text" name="sup_company" size="25"></td>
  <td class="ui-widget-content">Case #: <input type="text" name="sup_case" size="20">*</td>
  <td class="ui-widget-content" title="Rate the knowledge and helpfulness of the person you're working with.">Rate Call: <input type="radio" checked="checked" value="0" name="sup_rating"><input type="radio" value="1" name="sup_rating"><input type="radio" value="2" name="sup_rating"><input type="radio" value="3" name="sup_rating"><input type="radio" value="4" name="sup_rating"><input type="radio" value="5" name="sup_rating"></td>
</tr>
<tr>
  <td class="ui-widget-content">Full Name: <input type="text" name="sup_contact" size="20"></td>
  <td class="ui-widget-content">E-Mail: <input type="text" name="sup_email" size="20"></td>
  <td class="ui-widget-content">Phone: <input type="text" name="sup_phone" size="20"></td>
</tr>
<tr>
  <td class="ui-widget-content">Goverment ID Number: <input type="text" name="sup_govid" size="20"></td>
  <td class="ui-widget-content">Time of call: <input type="text" name="sup_timestamp" size="15"></td>
  <td class="ui-widget-content" title="Full Name as it appears on the Government ID">Data Center Access requires Full Name, Gov ID, and Phone Number</td>
</tr>
<?php
  }
?>
</table>

</div>

<span id="support_mysql"></span>

</div>


<div id="morning">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('morning-hide');">Morning Report Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('morning-help');">Help</a></th>
</tr>
</table>

<div id="morning-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This page lets you create a summary of the issue to be published in the Morning Report. You create a short line describing 
the issue along with the Status.</p>

<p>While you can make changes and update the information, until you click the <strong>Publish</strong> button, the Morning Report
doesn't change.</p>

<p>The <strong>Status</strong> drop down lets you advise Operations of any system related problems.</p>

<ul>
  <li><strong>Major</strong> - This problem will cause a system outage and may impact customers of the system. It is a high priority for the team in question.</li>
  <li><strong>Warning</strong> - This problem will cause a system outage but of a non-customer impacting system such as a Lab system.</li>
  <li><strong>Resolved</strong> - This problem has been resolved and service is back to normal.</li>
</ul>

<p>As you work the problem, the Status may change from Major to Warning and finally to Resolved. Any tasks that are Resolved will be cleared from 
the Morning Report at midnight. Note that you don't have to change all statuses to Resolved, just the final status.</p>

<p>Between the Form and the Listing is an example area of what will be inserted into the Morning Report. The Server Name and Function are automatically added 
to the first line and <strong>Update</strong> is added to subsequent updates.</p>

</div>

</div>

<div id="morning-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td colspan="7" class="ui-widget-content button">
<input type="button"                 name="mornpublish" value="Publish"       onClick="javascript:attach_morning('morning.mysql.php', 2);">
<input type="button" disabled="true" name="mornupdate"  value="Update Report" onClick="javascript:attach_morning('morning.mysql.php', 1);hideDiv('morning-hide');">
<input type="hidden" name="morning_id" value="0">
<input type="button"                 name="mornbutton"  value="Save Report"   onClick="javascript:attach_morning('morning.mysql.php', 0);"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Morning Report Form</th>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Status: <input type="text" name="morn_text" size="80"></td>
</tr>
<tr>
  <td class="ui-widget-content" title="Leave Timestamp field set to Current Time to use current time, otherwise use YYYY-MM-DD HH:MM:SS.">Timestamp: <input type="text" name="morn_timestamp" value="Current Time" size="23"></td>
  <td class="ui-widget-content">Support Tech: <select name="morn_user">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content">Status: <select name="morn_status">
<option value="0">No Status</option>
<option selected value="1">Resolved</option>
<option value="2">Warning</option>
<option value="3">Major</option>
</select></td>
</tr>
</table>

</div>

<span id="morning_mysql"></span>

</div>


<div id="problem">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('problem-hide');">Problem Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('problem-help');">Help</a></th>
</tr>
</table>

<div id="problem-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Reset</strong> - Reset the data entry form clearing the textarea and resetting the formatting buttons and data entry fields.</li>
    <li><strong>Update Detail</strong> - After selecting a detail record to edit, click here to save any changes.</li>
    <li><strong>Save New Detail</strong> - Add a new detail record. You can also select an existing record, make changes if needed, and click this button to add a second detail.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Problem Form</strong>
  <ul>
    <li><strong>Data Entry</strong> - Enter data about the issue here. The Bold, Italic, Underline, 
and Preserve Formatting buttons let you format the output of the data. The <strong>character count</strong> 
field shows you the limit of the number of characters. This limit is set by the browser.</li>
    <li><strong>Timestamp</strong> - The time the work was done.</li>
    <li><strong>Support Tech</strong> - The person performing the work.</li>
    <li><strong>Generate Wiki Page</strong> - Generates a formated page for insertion into a wiki.</li>
    <li><strong>Generate Text Page</strong> - Generates a formated log suitable for emailing to a support technician.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>Problem Management</strong> title bar to toggle the <strong>Problem Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>


<div id="problem-hide" style="display: none">

<?php
  if ($a_issue['iss_closed'] == '0000-00-00') {
?>
<table class="ui-styled-table">
<tr>
  <td colspan="7" class="ui-widget-content button">
<input type="button"                 name="reset"       value="Reset"           onClick="javascript:reset_detail();">
<input type="button" disabled="true" name="detupdate"   value="Update Detail"   onClick="javascript:attach_detail('comments.mysql.php', 1);hideDiv('problem-hide');">
<input type="hidden" name="det_id" value="0">
<input type="hidden" name="format_bold" value="0">
<input type="hidden" name="format_italic" value="0">
<input type="hidden" name="format_underline" value="0">
<input type="hidden" name="format_preserve" value="0">
<input type="button"                 name="issuebutton" value="Save New Detail" onClick="javascript:attach_detail('comments.mysql.php', 0);"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="7">Problem Form</th>
</tr>
<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
<tr>
  <td class="ui-widget-content delete"><input type="button" id="show_bold"      value="Bold"                onclick="javascript:formatText('bold');"></td>
  <td class="ui-widget-content delete"><input type="button" id="show_italic"    value="Italic"              onclick="javascript:formatText('italic');"></td>
  <td class="ui-widget-content delete"><input type="button" id="show_underline" value="Underline"           onclick="javascript:formatText('underline');"></td>
  <td class="ui-widget-content delete"><input type="button" id="show_preserve"  value="Preserve Formatting" onclick="javascript:formatText('preserve');"></td>
  <td class="ui-widget-content" colspan="3">&nbsp;</td>
</tr>
<?php
  }
?>
<tr>
  <td class="ui-widget-content" colspan="7">
<textarea id="det_text" name="det_text" cols=130 rows=10 
  onKeyDown="textCounter(document.start.det_text, document.start.remLen, 1800);" 
  onKeyUp  ="textCounter(document.start.det_text, document.start.remLen, 1800);">
</textarea>

<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1800"> characters left
</td>
</tr>
<tr>
  <td class="ui-widget-content" title="Leave Timestamp field set to Current Time to use current time, otherwise use YYYY-MM-DD HH:MM:SS." colspan="4">Timestamp: <input type="text" name="det_timestamp" value="Current Time" size=23></td>
  <td class="ui-widget-content">Support Tech: <select name="det_user">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_last'] . ", " . $a_users['usr_first'] . "</option>\n";
  }
?>
</select></td>
  <td class="ui-widget-content"><a href="wiki.mysql.php?id=<?php print $formVars['id']; ?>" target="_blank">Generate Wiki Page</a></td>
  <td class="ui-widget-content"><a href="text.mysql.php?id=<?php print $formVars['id']; ?>" target="_blank">Generate Text Page</a></td>
</tr>
<?php
  }
?>
</table>

</div>

<span id="detail_mysql"></span>

</div>


<div id="hardware">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default"><a href="javascript:;" onmousedown="toggleDiv('hardware-hide');">Hardware Management</a></th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('hardware-help');">Help</a></th>
</tr>
</table>

<div id="hardware-help" style="display: none">

<div class="main-help ui-widget-content">

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>RMA Number Required</strong> - Reminder that you need to fill in the RMA number before you can notify Shipping & Receiving.</li>
    <li><strong>Advise Shipping & Receiving</strong> - When the RMA number has been entered, this activates. Clicking it will send an email to Shipping & Receiving to let them know hardware is coming.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Hardware Form</strong>
  <ul>
    <li><strong>RMA Number</strong> - When the support company gives you the RMA number for the failed equipment, enter that here.</li>
    <li><strong>Message to Shipping & Receiving</strong> - Provide any additional instructions to Shipping & Receiving other than <a href="javascript:;" onmousedown="toggleDiv('example-email');">this example email</a>.</li>
  </ul></li>
</ul>

<div id="example-email" style="display: none">

<div class="ui-state-highlight">

<ul>
  <li>Affected System
  <ul>
    <li>Name: incojs01</li>
    <li>Location: <?php print $Sitecompany; ?>Production Data Center - Longmont (Longmont CO)</li>
    <li>Asset Tag Number: L.015377</li>
    <li>Serial Number: 0850BD0CB4</li>
    <li>Dell Service Tag Number:</li>
  </ul></li>
  <li>Failed Hardware
  <ul>
    <li>Vendor: Seagate</li>
    <li>Model: ST973451SSUN72G</li>
    <li>Type: Hard Disk</li>
    <li>RMA: rqreq</li>
    <li>Asset Tag Number:</li>
    <li>Serial Number:</li>
    <li>Dell Service Tag Number:</li>
  </ul></li>
</ul>

<p>Additional Note: This equipment is scheduled to arrive at 10am.</p>

</div>

</div>

<ul>
  <li><strong>Notes</strong>
  <ul>
    <li>Click the <strong>Hardware Management</strong> title bar to toggle the <strong>Hardware Form</strong>.</li>
  </ul></li>
</ul>

</div>

</div>

<div id="hardware-hide" style="display: none">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
<input type="hidden" name="hw_count" value="0">
<input type="button" name="hw_button" value="RMA Number Required" disabled="true" onClick="javascript:attach_hardware('shipping.mysql.php', 0);"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Hardware Form</th>
</tr>
<tr>
  <td class="ui-widget-content">RMA Number: <input type="text" name="hw_rma" size="30" onkeyup="verify_rma();"></td>
</tr>
<tr>
  <td class="ui-widget-content">Message to Shipping and Receiving: <input type="text" name="hw_note" size="80"></td>
</tr>
</table>

</div>

<span id="mysql_hardware"></span>

</form>

</div>

<?php
# end the block for a new issue
}
?>

</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
