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

  logaccess($db, $_SESSION['uid'], $package, "Managing a bug");

  if (isset($_GET['id'])) {
    $formVars['id']     = clean($_GET['id'], 10);
  } else {
    $formVars['id'] = 0;
  }

  $q_string  = "select mod_name,bug_discovered,bug_closed,bug_subject,bug_openby ";
  $q_string .= "from bugs ";
  $q_string .= "left join modules on modules.mod_id = bugs.bug_module ";
  $q_string .= "where bug_id = " . $formVars['id'];
  $q_bugs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_bugs = mysqli_fetch_array($q_bugs);

  $bugs = $a_bugs['mod_name'] . ' Bug: ' . $formVars['id'];

  $q_string  = "select usr_last,usr_first,usr_phone,usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $a_bugs['bug_openby'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

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
<title><?php print $bugs; ?></title>

<style type="text/css" title="currentStyle" media="screen">
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

function delete_detail( p_script_url ) {
  var answer = confirm("Delete Detail Record?")

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

  af_url += '&bug_module='      + encode_URI(af_form.bug_module.value);
  af_url += '&bug_severity='    + encode_URI(af_form.bug_severity.value);
  af_url += '&bug_priority='    + encode_URI(af_form.bug_priority.value);
  af_url += '&bug_discovered='  + encode_URI(af_form.bug_discovered.value);
  af_url += '&bug_subject='     + encode_URI(af_form.bug_subject.value);
  af_url += '&bug_closed='      + encode_URI(af_form.bug_closed.value);

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function attach_detail( p_script_url, update ) {
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + <?php print $formVars['id']; ?>;

  af_url += '&bug_id='        + af_form.bug_id.value;
  af_url += "&bug_text="      + encode_URI(af_form.bug_text.value);
  af_url += "&bug_timestamp=" + encode_URI(af_form.bug_timestamp.value);
  af_url += "&bug_user="      + af_form.bug_user.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_detail( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + <?php print $formVars['id']; ?>;

  uf_url += '&bug_id='        + uf_form.bug_id.value;
  uf_url += "&bug_text="      + encode_URI(uf_form.bug_text.value);
  uf_url += "&bug_timestamp=" + encode_URI(uf_form.bug_timestamp.value);
  uf_url += "&bug_user="      + uf_form.bug_user.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function textCounter(p_field, p_cntfield, p_maxlimit) {
  if (p_field.value.length > p_maxlimit)
    p_field.value = p_field.value.substring(0, p_maxlimit);
  else
    p_cntfield.value = p_maxlimit - p_field.value.length;
}

function clear_fields() {
<?php
  if ($formVars['id'] > 0) {
    print "  show_file('comments.mysql.php?update=-1&id=" . $formVars['id'] . "');\n";
  }
?>
}

<?php
  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
// the purpose here is to permit the insertion/replacement of formatted text
function formatText(p_field, p_format) {
  var ft_form = p_field;
  var ft_text = ft_form.bug_text.value;

  ft_form.bug_text.focus();
  var ft_cursor = getInputSelection(ft_form.bug_text);

  var ft_st_start  = ft_text.substring(0, ft_cursor.start);
  var ft_st_middle = ft_text.substring(ft_cursor.start, ft_cursor.end);
  var ft_st_end    = ft_text.substring(ft_cursor.end);

  if (p_format == "bold") {

    if (ft_form.format_bold.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_bold').value = 'BOLD';
        ft_form.format_bold.value = 1;
        ft_bug_text = ft_st_start + "<b>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_bug_text = ft_st_start + "<b>" + ft_st_middle + "</b>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_bold').value = 'Bold';
        ft_form.format_bold.value = 0;
        ft_bug_text = ft_st_start + "</b>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_bug_text = ft_st_start + "</b>" + ft_st_middle + "<b>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "italic") {

    if (ft_form.format_italic.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_italic').value = 'ITALIC';
        ft_form.format_italic.value = 1;
        ft_bug_text = ft_st_start + "<i>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_bug_text = ft_st_start + "<i>" + ft_st_middle + "</i>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_italic').value = 'Italic';
        ft_form.format_italic.value = 0;
        ft_bug_text = ft_st_start + "</i>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_bug_text = ft_st_start + "</i>" + ft_st_middle + "<i>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "underline") {

    if (ft_form.format_underline.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_underline').value = 'UNDERLINE';
        ft_form.format_underline.value = 1;
        ft_bug_text = ft_st_start + "<u>" + ft_st_end;
        ft_cursor.end += 3;
      } else {
        ft_bug_text = ft_st_start + "<u>" + ft_st_middle + "</u>" + ft_st_end;
        ft_cursor.end += 7;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_underline').value = 'Underline';
        ft_form.format_underline.value = 0;
        ft_bug_text = ft_st_start + "</u>" + ft_st_end;
        ft_cursor.end += 4;
      } else {
        ft_bug_text = ft_st_start + "</u>" + ft_st_middle + "<u>" + ft_st_end;
        ft_cursor.end += 7;
      }
    }

  }
  if (p_format == "preserve") {

    if (ft_form.format_preserve.value == 0) {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_preserve').value = 'PRESERVE FORMATTING';
        ft_form.format_preserve.value = 1;
        ft_bug_text = ft_st_start + "<pre>" + ft_st_end;
        ft_cursor.end += 5;
      } else {
        ft_bug_text = ft_st_start + "<pre>" + ft_st_middle + "</pre>" + ft_st_end;
        ft_cursor.end += 11;
      }
    } else {
      if (ft_cursor.start == ft_cursor.end) {
        document.getElementById('show_preserve').value = 'Preserve Formatting';
        ft_form.format_preserve.value = 0;
        ft_bug_text = ft_st_start + "</pre>" + ft_st_end;
        ft_cursor.end += 6;
      } else {
        ft_bug_text = ft_st_start + "</pre>" + ft_st_middle + "<pre>" + ft_st_end;
        ft_cursor.end += 11;
      }
    }

  }

  ft_form.bug_text.value = ft_bug_text;
  setCaretPosition('bug_text', ft_cursor.end);
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
  $( "#tabs" ).tabs( );

  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 525,
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
          show_file('comments.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Comment",
        click: function() {
          attach_detail('comments.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 525,
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
          show_file('ipaddress.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Comment",
        click: function() {
          update_detail('comments.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Comment",
        click: function() {
          update_detail('comments.mysql.php', 0);
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

<div class="main">

<!-- This is the start of the tab block -->

<div id="tabs">

<ul>
  <li><a href="#bug"><strong><?php print $a_bugs['mod_name']; ?></strong> Bug</a></li>
  <li><a href="#comments">Problem Form</a></li>
</ul>



<!-- This is the start of the bug tab -->

<div id="bug">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Bug Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('bug-help');">Help</a></th>
</tr>
</table>

<div id="bug-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Bug Reporting</strong></p>

<p>This form comes in two states. Either an open or new ticket or a closed ticket. The two states have different options.</p>

<p><strong>Open/New Ticket</strong></p>

<p>In this state, the Requestor information is already filled in. You need to identify the Module that's affected and then the severity 
and priority of the bug. Then a brief description of the problem. Under the Problem Form, you can provide additional details about the 
issue.</p>

<p><strong>Closed Ticket</strong></p>

<p>In this state, you are able to view the ticket details and all the comments that have been made about the problem. Unless you reopen 
the ticket, no new comments can be added.</p>


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
  if ($a_bugs['bug_closed'] == '1971-01-01') {
    print "  <input type=\"button\" " . $updateissue . " name=\"close\"     value=\"Close Ticket\"  onClick=\"javascript:attach_file('ticket.mysql.php', 2);\">\n";
    print "  <input type=\"button\" " . $updateissue . " name=\"save\"      value=\"Save Changes\"  onClick=\"javascript:attach_file('ticket.mysql.php', 1);\">\n";
  } else {
    print "  <input type=\"button\" " . $updateissue . " name=\"close\"     value=\"Reopen Ticket\" onClick=\"javascript:attach_file('ticket.mysql.php', 2);\">\n";
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
  print "  <td class=\"ui-widget-content\"><strong>User</strong>: " . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>Phone</strong>: " . $a_users['usr_phone'] . "</td>\n";
  print "  <td class=\"ui-widget-content\"><strong>E-Mail</strong>: " . $a_users['usr_email'] . "</td>\n";
  print "</tr>\n";
?>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="5">Bug Form</th>
</tr>
<tr>
<?php
  $severity[0] = 'Note';
  $severity[1] = 'Minor';
  $severity[2] = 'Major';
  $severity[3] = 'Critical';

  $priority[0] = 'Low';
  $priority[1] = 'Medium';
  $priority[2] = 'High';

  $q_string  = "select bug_module,mod_name,bug_severity,bug_priority,bug_discovered,bug_closed,bug_subject ";
  $q_string .= "from bugs ";
  $q_string .= "left join modules on modules.mod_id = bugs.bug_module ";
  $q_string .= "where bug_id = " . $formVars['id'];
  $q_bugs = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_bugs = mysqli_fetch_array($q_bugs);

  if ($a_bugs['bug_closed'] == '1971-01-01') {
    print "  <td class=\"ui-widget-content\"><strong>Module</strong>: <select name=\"bug_module\">\n";

    $q_string  = "select mod_id,mod_name ";
    $q_string .= "from modules ";
    $q_string .= "order by mod_name ";
    $q_modules = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_modules = mysqli_fetch_array($q_modules)) {
      if ($a_bugs['bug_module'] == $a_modules['mod_id']) {
        print "<option selected value=\"" . $a_modules['mod_id'] . "\">" . $a_modules['mod_name'] . "</option>\n";
      } else {
        print "<option value=\"" . $a_modules['mod_id'] . "\">" . $a_modules['mod_name'] . "</option>\n";
      }
    }
?>
  </select></td>
  <td class="ui-widget-content"><strong>Severity</strong>: <select name="bug_severity">
<?php
  for ($i = 0; $i < 4; $i++) {
    if ($i == $a_bugs['bug_severity']) {
      print "<option selected value=\"" . $i . "\">" . $severity[$i] . "</option>\n";
    } else {
      print "<option value=\"" . $i . "\">" . $severity[$i] . "</option>\n";
    }
  }
?>
</select></td>
  <td class="ui-widget-content"><strong>Priority</strong>: <select name="bug_priority">
<?php
  for ($i = 0; $i < 3; $i++) {
    if ($i == $a_bugs['bug_priority']) {
      print "<option selected value=\"" . $i . "\">" . $priority[$i] . "</option>\n";
    } else {
      print "<option value=\"" . $i . "\">" . $priority[$i] . "</option>\n";
    }
  }
?>
</select></td>
  <td class="ui-widget-content"><strong>Discovered</strong>:          <input type="text" name="bug_discovered" size="10" value="<?php print $a_bugs['bug_discovered']; ?>"></td>
  <td class="ui-widget-content"><strong>Closed</strong>:              <input type="text" name="bug_closed"     size="15" value="Current Date"></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="6"><strong>Brief Description</strong>: <input type="text" name="bug_subject"    size="100" value="<?php print $a_bugs['bug_subject']; ?>"></td>
<?php
  } else {
?>
  <td class="ui-widget-content"><strong>Module</strong>:              <?php print $a_bugs['mod_name']; ?>       <input type="hidden" name="bug_module"     value="<?php print $a_bugs['bug_module']; ?>"</td>
  <td class="ui-widget-content"><strong>Severity</strong>:            <?php print $severity[$a_bugs['bug_severity']]; ?>   <input type="hidden" name="bug_severity"   value="<?php print $a_bugs['bug_severity']; ?>"</td>
  <td class="ui-widget-content"><strong>Priority</strong>:            <?php print $priority[$a_bugs['bug_priority']]; ?>   <input type="hidden" name="bug_priority"   value="<?php print $a_bugs['bug_priority']; ?>"</td>
  <td class="ui-widget-content"><strong>Discovered</strong>:          <?php print $a_bugs['bug_discovered']; ?> <input type="hidden" name="bug_discovered" value="<?php print $a_bugs['bug_discovered']; ?>"</td>
  <td class="ui-widget-content"><strong>Closed</strong>:              <?php print $a_bugs['bug_closed']; ?>     <input type="hidden" name="bug_closed"     value="<?php print $a_bugs['bug_closed']; ?>"</td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="6"><strong>Brief Description</strong>: <?php print $a_bugs['bug_subject']; ?>    <input type="hidden" name="bug_subject"    value="<?php print $a_bugs['bug_subject']; ?>"</td>
<?php
  }
?>
</tr>
</table>

</div>


<!-- This is the end of the bug tab -->




<!-- This is the start of the problem tab -->

<div id="comments">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Problem Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('problem-help');">Help</a></th>
</tr>
</table>

<div id="problem-help" style="<?php print $display;?>">

<div class="main-help ui-widget-content">

<p><strong>Comments</strong></p>

<p>This tab lets you add comments about the reported bug either more details or comments on how the problem was solved.</p>

<p>When the reported bug has been solved and the ticket closed, you are able to view all the comments however you cannot 
manage the comments without reopening the ticket.</p>

</div>

</div>

<?php
  if ($a_bugs['bug_closed'] == '1971-01-01') {
?>
<table class="ui-styled-table">
<tr>
  <td colspan="7" class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Comment"></td>
</tr>
</table>
<?php
  }
?>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Problem Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('problem-listing-help');">Help</a></th>
</tr>
</table>

<div id="problem-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Problem Listing</strong></p>

<p>This page lists all comments related to this bug.</p>

<p>To add a new Comment, click the Add Comment button. This will bring up a dialog box which you can then use to add a comment.</p>

<p>To edit an existing Comment, click on the entry in the listing. A dialog box will be displayed where you can edit the current 
entry, or if there is a small difference, you can make changes and add a new comment.</p>


</div>

</div>

<span id="detail_mysql"></span>



<div id="dialogCreate" title="Add Comment">

<form name="formCreate">

<input type="hidden" name="bug_id" value="0">
<input type="hidden" name="format_bold" value="0">
<input type="hidden" name="format_italic" value="0">
<input type="hidden" name="format_underline" value="0">
<input type="hidden" name="format_preserve" value="0">

<table class="ui-styled-table">
<?php

# only allow button formatting if not IE.

  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
<tr>
  <td class="ui-widget-content delete">
    <input type="button" id="show_bold"      value="Bold"                onclick="javascript:formatText(formCreate, 'bold');">
    <input type="button" id="show_italic"    value="Italic"              onclick="javascript:formatText(formCreate, 'italic');">
    <input type="button" id="show_underline" value="Underline"           onclick="javascript:formatText(formCreate, 'underline');">
    <input type="button" id="show_preserve"  value="Preserve Formatting" onclick="javascript:formatText(formCreate, 'preserve');">
  </td>
</tr>
<?php
  }


?>
<tr>
  <td class="ui-widget-content">
<textarea id="bug_text" name="bug_text" cols="80" rows="16" 
  onKeyDown="textCounter(document.formCreate.bug_text, document.formCreate.remLen, 1800);" 
  onKeyUp  ="textCounter(document.formCreate.bug_text, document.formCreate.remLen, 1800);">
</textarea>

<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1800"> characters left
  </td>
</tr>
<tr>
  <td class="ui-widget-content" title="Leave Timestamp field set to Current Time to use current time, otherwise use YYYY-MM-DD HH:MM:SS." colspan="4">Timestamp: <input type="text" name="bug_timestamp" value="Current Time" size=23></td>
</tr>
<tr>
  <td class="ui-widget-content">Comment by: <select name="bug_user">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";
  }
?>
</select></td>
</tr>

</table>

</form>

</div>



<div id="dialogUpdate" title="Edit Comment">

<form name="formUpdate">

<input type="hidden" name="bug_id" value="0">
<input type="hidden" name="format_bold" value="0">
<input type="hidden" name="format_italic" value="0">
<input type="hidden" name="format_underline" value="0">
<input type="hidden" name="format_preserve" value="0">

<table class="ui-styled-table">
<?php

# only allow button formatting if not IE.

  if (!preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
?>
<tr>
  <td class="ui-widget-content delete">
    <input type="button" id="show_bold"      value="Bold"                onclick="javascript:formatText(formUpdate, 'bold');">
    <input type="button" id="show_italic"    value="Italic"              onclick="javascript:formatText(formUpdate, 'italic');">
    <input type="button" id="show_underline" value="Underline"           onclick="javascript:formatText(formUpdate, 'underline');">
    <input type="button" id="show_preserve"  value="Preserve Formatting" onclick="javascript:formatText(formUpdate, 'preserve');">
  </td>
</tr>
<?php
  }


?>
<tr>
  <td class="ui-widget-content">
<textarea id="bug_text" name="bug_text" cols="80" rows="16"
  onKeyDown="textCounter(document.formUpdate.bug_text, document.formUpdate.remLen, 1800);"
  onKeyUp  ="textCounter(document.formUpdate.bug_text, document.formUpdate.remLen, 1800);">
</textarea>

<br><input readonly type="text" name="remLen" size="5" maxlength="5" value="1800"> characters left
  </td>
</tr>
<tr>
  <td class="ui-widget-content" title="Leave Timestamp field set to Current Time to use current time, otherwise use YYYY-MM-DD HH:MM:SS." colspan="4">Timestamp: <input type="text" name="bug_timestamp" value="Current Time" size=23></td>
</tr>
<tr>
  <td class="ui-widget-content">Comment by: <select name="bug_user">
<?php
  $q_string  = "select usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_id = " . $_SESSION['uid'];
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_users = mysqli_fetch_array($q_users);

  print "<option value=\"" . $_SESSION['uid'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";

  $q_string  = "select usr_id,usr_first,usr_last ";
  $q_string .= "from users ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {
    print "<option value=\"" . $a_users['usr_id'] . "\">" . $a_users['usr_first'] . " " . $a_users['usr_last'] . "</option>\n";
  }
?>
</select></td>
</tr>

</table>

</form>

</div>



<!-- This is the end of the problem tab -->
</div>

<!-- This is the end of the tab block -->
</div>

</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
