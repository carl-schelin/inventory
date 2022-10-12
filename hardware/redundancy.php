<?php
# Script: redundancy.php
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

  $package = "redundancy.php";

  logaccess($db, $_SESSION['uid'], $package, "Accessing script");

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
<title>Edit Redundancy Descriptions</title>

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
  var answer = confirm("Delete this Redundancy Description?")

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

  af_url += "&red_text="    + encode_URI(af_form.red_text.value);
  af_url += "&red_default=" + af_form.red_default.checked;

  script = document.createElement('script');
  script.src = p_script_url + af_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function update_file( p_script_url, update ) {
  var uf_form = document.formUpdate;
  var uf_url;

  uf_url  = '?update='   + update;
  uf_url += '&id='       + uf_form.id.value;

  uf_url += "&red_text="    + encode_URI(uf_form.red_text.value);
  uf_url += "&red_default=" + uf_form.red_default.checked;

  script = document.createElement('script');
  script.src = p_script_url + uf_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function clear_fields() {
  show_file('redundancy.mysql.php?update=-1');
}

$(document).ready( function() {
  $( '#clickCreate' ).click(function() {
    $( "#dialogCreate" ).dialog('open');
  });

  $( "#dialogCreate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
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
          show_file('redundancy.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Redundancy Description",
        click: function() {
          attach_file('redundancy.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });

  $( "#dialogUpdate" ).dialog({
    autoOpen: false,
    modal: true,
    height: 175,
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
          show_file('redundancy.mysql.php?update=-1');
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Redundancy Description",
        click: function() {
          update_file('redundancy.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Redundancy Description",
        click: function() {
          update_file('redundancy.mysql.php', 0);
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

<div class="main">

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Redundancy Description Editor</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('redundancy-help');">Help</a></th>
</tr>
</table>



<div id="redundancy-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p>Redundancy when used with network interfaces generally means you're associating at least two physical or in 
some cases, virtual interfaces into a bonded pair. This provides redundancy as each interface would be connected 
to a different switch so that if a switch (or cable or interface) failed, the system would still be accessible. 
Different vendors and operating systems use different approaches but all are effectively similar in that they 
bond multiple interfaces into a single virtual one.</p>

<p>Below I'll provide a quick description of each of the options to make it easier for you to track down the 
purpose and configuration if necessary.</p>

<ul>
  <li>Adaptive Load Balancing (ALB) / Active/Active or Source Level Bonding (SLB). ALB is a Linux configuration 
generally used as a balance-alb setting in LACP. SLB is more specific to Xen for Virtual Machines running on a 
physical server with physical interfaces (PIF)</li>
  <li>Auto Port Aggregation (APA). This is a HP-UX specific configuration used to bond at least two physical 
interfaces into a single virtual interface.</li>
  <li>Bond. This is the pretty standard method for pairing multiple physical interfaces.</li>
  <li>EtherChannel is a port link aggregation technology or port-channel architecture used primarily on Cisco 
switches. It allows grouping of several physical Ethernet links to create one logical Ethernet link for the 
purpose of providing fault-tolerance and high-speed links between switches, routers and servers</li>
  <li>IP network multipathing (IPMP) provides physical interface failure detection and transparent network 
access failover for a system with multiple interfaces on the same IP link. IPMP also provides load spreading 
of packets for systems with multiple interfaces.</li>
  <li>Link Aggregation Control Protocol or LACP is one element of an IEEE specification (802.3ad) that provides 
guidance on the practice of link aggregation for data connections. Importantly, LACP typically applies to 
strategies that bundle individual links of Ethernet connections, and not wireless transfers. (Note that I've 
used LACP on a physical server when created a Bridge interface used with KVM based servers.)</li>
  <li>Multi-link trunking (MLT) is a link aggregation technology developed at Nortel in 1999. It allows grouping 
several physical Ethernet links into one logical Ethernet link to provide fault-tolerance and high-speed links 
between routers, switches, and servers.</li>
  <li>System Fault Tolerance (SFT) is a fault tolerant system built into NetWare operating systems.
  <ul>
    <li>SFT Level I - Disk block level fault tolerance.</li>
    <li>SFT Level II - Fault tolerance on the disk level. Effectively RAID level 1.</li>
    <li>SFT Level III - Fault tolerance on the system level. Controllers connect two systems so that if one 
fails, the second takes over seamlessly.</li>
  </ul></li>
  <li>Adapter teaming with Intel(r) Advanced Network Services (Intel(r) ANS) uses an intermediate driver to 
group multiple physical ports. You can use teaming to add fault tolerance, load balancing, and link aggregation 
features to a group of ports. Note that support for this ended with Windows Server 2012 R2.</li>
  <li>Windows Teaming with nVidia is supported at Windows Server 2012 and above.</li>
</ul>

</div>

</div>



<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button"><input type="button" id="clickCreate" value="Add Redundancy Description"></td>
</tr>
</table>

<p></p>

<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Redundancy Description Listing</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('redundancy-listing-help');">Help</a></th>
</tr>
</table>



<div id="redundancy-listing-help" style="<?php print $display; ?>">

<div class="main-help ui-widget-content">

<p><strong>Network Redundancy Description</strong></p>

<p>This page lists all the defined Network Redundancies.</p>

<p>To add a Network Redundancy Description, click the Add Redundancy Description button. This will bring up a dialog box which 
you can use to add a Network Redundancy Description.</p>

<p>To edit an existing Network Redundancy Description, click on an entry in the listing. A dialog box will be presented where you 
can edit the current entry, or if there is a small difference, you can make changes and add a new Network Redundancy Description.</p>

<p>The Default setting indicates this is the top displayed item in the menu and generally means that this isn't an 
interface that's used for redundancy. The text can be anything descriptive. Note that when this is selected and saved, 
the (r) won't be displayed, any interface tied to this interface will be returned to zero (aka no parent interface). In the 
listing below, a Redundancy configured as a Default will be <span class="ui-state-highlight">highlighted</span>.</p>

<p>Note that under the Members colum is a number which indicates the number of times this Network Redundancy Description is in use. 
You cannot delete a Description as long as this value is greater than zero.</p>

</div>

</div>


<span id="mysql_table"><?php print wait_Process('Waiting...')?></span>

</div>


<div id="dialogCreate" title="Add Redundancy Description">

<form name="formCreate">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Redundancy Description: <input type="text" name="red_text" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Default? <input type="checkbox" name="red_default"></td>
</tr>
</table>

</form>

</div>


<div id="dialogUpdate" title="Update Redundancy Description">

<form name="formUpdate">

<input type="hidden" name="id" value="0">

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content">Network Redundancy Description: <input type="text" name="red_text" size="30"></td>
</tr>
<tr>
  <td class="ui-widget-content">Default? <input type="checkbox" name="red_default"></td>
</tr>
</table>

</form>

</div>


<?php include($Sitepath . '/footer.php'); ?>

</body>
</html>
