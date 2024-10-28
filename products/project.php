<?php
# Script: project.php
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

  $package = "project.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

# if help has not been seen yet,
  if (show_Help($db, $Projectpath . "/" . $package)) {
    $display = "display: block";
  } else {
    $display = "display: none";
  }

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Project Editor</title>

<style type='text/css' title='currentStyle' media='screen'>
<?php include($Sitepath . "/mobile.php"); ?>
</style>

<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/css/jquery-ui/jquery-ui.js"></script>
<link   rel="stylesheet" type="text/css"            href="<?php print $Siteroot; ?>/css/jquery-ui-themes/themes/<?php print $_SESSION['theme']; ?>/jquery-ui.css">
<script type="text/javascript" language="javascript" src="<?php print $Siteroot; ?>/functions/jquery.inventory.js"></script>

<script type="text/javascript">

<?php
  if (check_userlevel($db, $AL_Admin)) {
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
  var af_form = document.formCreate;
  var af_url;

  af_url  = '?update='   + update;

  af_url += "&prj_name="      + encode_URI(af_form.prj_name.value);
  af_url += "&prj_code="      + encode_URI(af_form.prj_code.value);
  af_url += "&prj_task="      + encode_URI(af_form.prj_task.value);
  af_url += "&prj_desc="      + encode_URI(af_form.prj_desc.value);
  af_url += "&prj_directory=" + encode_URI(af_form.prj_directory.value);
  af_url += "&prj_group="     + af_form.prj_group.value;
  af_url += "&prj_product="   + af_form.prj_product.value;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&prj_name="      + encode_URI(uf_form.prj_name.value);
  uf_url += "&prj_code="      + encode_URI(uf_form.prj_code.value);
  uf_url += "&prj_task="      + encode_URI(uf_form.prj_task.value);
  uf_url += "&prj_desc="      + encode_URI(uf_form.prj_desc.value);
  uf_url += "&prj_directory=" + encode_URI(uf_form.prj_directory.value);
  uf_url += "&prj_group="     + uf_form.prj_group.value;
  uf_url += "&prj_product="   + uf_form.prj_product.value;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('project.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
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
          show_file('project.mysql.php?update=-1');
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

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 300,
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
          show_file('project.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Project",
        click: function() {
          update_file('project.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Project",
        click: function() {
          update_file('project.mysql.php', 0);
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

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('project-help');">Help</a></th>
</tr>
</table>

<div id="project-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>A Product or Service is something that the company is providing. This could be anything from an inhouse built application 
or something like cloud services.</p>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Project"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Project Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('project-listing-help');">Help</a></th>
</tr>
</table>

<div id="project-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Project Listing</strong></p>

<p>This page lists all the Products and Services that are offerred by the company.</p>

<p>To add a Product or Service, click the Add Product/Service button. This will bring up a dialog box which 
you can use to create a new Product or Service.</p>

<p>To edit an existing Product or Service, click on an entry in the listing. A dialog box will be presented 
where you can edit the current entry, or if there is a small difference, you can make changes and add a new 
Product or Service.</p>

<p>Note that under the Members column is a number which indicates the number of servers that are part of 
this project. You cannot remove a product until this value is zero. Clicking on the number will take you 
to a server listing where you can edit the servers and remove them from the product.</p>


</div>

</div>


<span id="table_mysql"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Project">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Product <select name="prj_product">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "order by prod_name";
  $q_inv_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_products = mysqli_fetch_array($q_inv_products)) {
    print "<option value=\"" . $a_inv_products['prod_id'] . "\">" . $a_inv_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="prj_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Project Code: <input type="text" name="prj_code" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Task: <input type="text" name="prj_task" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="prj_desc" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Terraform: <input type="text" name="prj_directory" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Ownership: <select name="prj_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "order by grp_name ";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Edit Product/Service">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Product <select name="prj_product">
<?php
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from inv_products ";
  $q_string .= "order by prod_name";
  $q_inv_products = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_products = mysqli_fetch_array($q_inv_products)) {
    print "<option value=\"" . $a_inv_products['prod_id'] . "\">" . $a_inv_products['prod_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
<tr>
  <td class="ui-widget-content">Name: <input type="text" name="prj_name" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Project Code: <input type="text" name="prj_code" size="10"></td>
</tr>
<tr>
  <td class="ui-widget-content">Task: <input type="text" name="prj_task" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Description: <input type="text" name="prj_desc" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Terraform: <input type="text" name="prj_directory" size="40"></td>
</tr>
<tr>
  <td class="ui-widget-content">Group Ownership: <select name="prj_group">
<?php
  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from inv_groups ";
  $q_string .= "order by grp_name ";
  $q_inv_groups = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  while ($a_inv_groups = mysqli_fetch_array($q_inv_groups)) {
    print "<option value=\"" . $a_inv_groups['grp_id'] . "\">" . $a_inv_groups['grp_name'] . "</option>\n";
  }
?>
</select></td>
</tr>
</table>

</form>

</div>


</div>

<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
