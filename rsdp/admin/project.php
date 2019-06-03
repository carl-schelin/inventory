<?php
# Script: project.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  $called = 'no';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');
  check_login('2');

  $package = "project.php";

  logaccess($_SESSION['uid'], $package, "Viewing the Project table");

  if (isset($_GET['group'])) {
    $formVars['group'] = clean($_GET['group'], 10);
  } else {
    $formVars['group'] = $_SESSION['group'];
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Manage <?php print $Sitecompany; ?>Products</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">
<?php
  if (check_userlevel(1)) {
?>
function delete_line( p_script_url ) {
  var answer = confirm("Delete this Project?")

  if (answer) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}
<?php
  }
?>
function attach_file( p_script_url, update ) {
  var af_form = document.dialog;
  var af_url;

  af_url  = '?update='   + update;
  af_url += '&id='       + af_form.id.value;

  af_url += "&prj_name="     + encode_URI(af_form.prj_name.value);
  af_url += "&prj_code="     + encode_URI(af_form.prj_code.value);
  af_url += "&prj_close="    + af_form.prj_close.checked;
  af_url += "&prj_group="    + <?php print $formVars['group']; ?>;
  af_url += "&prj_product="  + af_form.prj_product.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('project.mysql.php?update=-1&prj_group=<?php print $formVars['group']; ?>');
}

$(document).ready( function() {
  $( "#tabs" ).tabs( ).addClass( "tab-shadow" );
});

$(function() {

  $( '#clickAddProject' ).click(function(e) {
    $( "#dialogProject" ).dialog('open');
  });

  $( "#dialogProject" ).dialog({
    autoOpen: false,
    modal: true,
    height: 200,
    width:  900,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogProject" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          attach_file('project.mysql.php', -1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Project",
        click: function() {
          attach_file('project.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Project",
        click: function() {
          attach_file('project.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

</script>

</head>
<body onLoad="clear_fields();" class="ui-widget-content">

<?php include($Sitepath . '/topmenu.start.php'); ?>
<?php include($Sitepath . '/topmenu.end.php'); ?>

<div id="main">

<form name="projects">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Management</th>
  <th class="ui-state-default" width="5"><a href="javascript:;" onmousedown="toggleDiv('project-help');">Help</a></th>
</tr>
</table>

<div id="project-help" style="display: none">

<div class="main-help ui-widget-content">

<p>This listing shows the Projects your team is working on. This keeps the list of projects to a manageable handful. It also means you can close a Project when it has been completed without affecting other teams.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Add Project</strong> - Create a new project record. You can copy an existing project by editing it, changing a field and saving it again.</li>
  </ul></li>
</ul>

<ul>
  <li><strong>Project Form</strong>
  <ul>
    <li><strong>Project Name</strong> - The name of the project you're creating servers for. The <strong>Project</strong> would be a task being done to add or improve a <strong>Product</strong>.</li>
    <li><strong>Project Code</strong> - The project code to be used to charge work time to. This is necessary for the other teams so they know which Project the servers are for.</li>
    <li><strong>Close Project</strong> - Checking this <span class="ui-state-highlight">highlights</span> the Project in the listing and hides it in your group's drop down listing.</li>
  </ul></li>
  <li><strong>Update Project</strong> Save changes to the form.</li>
  <li><strong>Add Project</strong> Add a new project.</li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" name="addbtn" id="clickAddProject" value="Add Project"></td>
</tr>
</table>

<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</form>

</div>


<div id="dialogProject" title="Project Form">

<form name="dialog">

<input type="hidden" name="id" value="0">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default" colspan="3">Project Form</th>
</tr>
<tr>
  <td class="ui-widget-content">Project Name: <input type="text" name="prj_name" size="30"></td>
  <td class="ui-widget-content">Project Code: <input type="text" name="prj_code" size="15"></td>
  <td class="ui-widget-content"><label>Close Project <input type="checkbox" name="prj_close"></label></td>
</tr>
<tr>
  <td class="ui-widget-content" colspan="3">Intrado Product: <select name="prj_product">
<option value="0">Unassigned</option>
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name ";
  $q_products = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_products = mysql_fetch_array($q_products)) {
    print "<option value=\"" . $a_products['prod_id'] . "\">" . $a_products['prod_name'] . "</option>\n";
  }
?></select></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
