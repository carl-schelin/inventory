#!/usr/local/bin/php
<?php
# Script: itil.services.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve the 'service catalog' listing
# for the conversion to Remedy.
# Requires:
# Product Type
# Product Name
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $header  = "\"Service Type\",";			# Field: 0
  $header .= "\"Submitter\",";				# Field: 1 Green: Blank
  $header .= "\"CI Name*\",";				# Field: 2 Green: Column A
  $header .= "\"CI Description*\",";			# Field: 3 Green: Column A
  $header .= "\"Status*\",";				# Field: 4 Green: 3
  $header .= "\"Status Reason\",";			# Field: 5 Green: 2000
  $header .= "\"Fixed Asset\",";			# Field: 6 Green: 2
  $header .= "\"Mark As Deleted\",";			# Field: 7 Green: Blank
  $header .= "\"Depreciated?\",";			# Field: 8 Green: 1
  $header .= "\"Failed Auto Identification\",";		# Field: 9 Green: 0
  $header .= "\"Y2K Compliant\",";			# Field: 10 Green: 0
  $header .= "\"Reconciliation Identity\",";		# Field: 11 Green: Blank
  $header .= "\"CI ID+\",";				# Field: 12 
  $header .= "\"Supported\",";				# Field: 13 
  $header .= "\"Owner Name\",";				# Field: 14 
  $header .= "\"Owner Contact\",";			# Field: 15 
  $header .= "\"Tier 1\",";				# Field: 16 
  $header .= "\"Tier 2\",";				# Field: 17 
  $header .= "\"Tier 3\",";				# Field: 18 
  $header .= "\"Product Name+\",";			# Field: 19 
  $header .= "\"Manufacturer\",";			# Field: 20 
  $header .= "\"Supplier Name+\",";			# Field: 21 
  $header .= "\"System Role\",";			# Field: 22 
  $header .= "\"Users Affected\",";			# Field: 23 
  $header .= "\"Impact\",";				# Field: 24 
  $header .= "\"Urgency\",";				# Field: 25 
  $header .= "\"Priority\",";				# Field: 26 
  $header .= "\"Additional Information\",";		# Field: 27 
  $header .= "\"Region\",";				# Field: 28 
  $header .= "\"Site Group\",";				# Field: 29 
  $header .= "\"Site+\",";				# Field: 30 
  $header .= "\"Floor\",";				# Field: 31 
  $header .= "\"Room\",";				# Field: 32 
  $header .= "\"Ownership Type\",";			# Field: 33 
  $header .= "\"Cost Center\",";			# Field: 34 
  $header .= "\"Budget Code\",";			# Field: 35 
  $header .= "\"Project Number\",";			# Field: 36 
  $header .= "\"Requisition ID\",";			# Field: 37 
  $header .= "\"Class Id\",";				# Field: 38 Green: Blank
  $header .= "\"Order ID\",";				# Field: 39 
  $header .= "\"Invoice Number\",";			# Field: 40 
  $header .= "\"Cost\",";				# Field: 41 
  $header .= "\"Unit Price\",";				# Field: 42 
  $header .= "\"Sales Tax\",";				# Field: 43 
  $header .= "\"Total Purchase Cost\",";		# Field: 44 
  $header .= "\"Market Value\",";			# Field: 45 
  $header .= "\"Accounting Code\",";			# Field: 46 
  $header .= "\"Purchase Date\",";			# Field: 47 
  $header .= "\"Received Date+\",";			# Field: 48 
  $header .= "\"Available Date+\",";			# Field: 49 
  $header .= "\"Installation Date+\",";			# Field: 50 
  $header .= "\"Disposal Date+\",";			# Field: 51 
  $header .= "\"ReturnDate\",";				# Field: 52 
  $header .= "\"Notes Log\",";				# Field: 53 
  $header .= "\"Current State\",";			# Field: 54 
  $header .= "\"AssetClass\",";				# Field: 55 
  $header .= "\"Inventory\",";				# Field: 56 
  $header .= "\"AccountID\",";				# Field: 57 
  $header .= "\"AdditionalDetails\",";			# Field: 58 
  $header .= "\"Asset History\",";			# Field: 59 
  $header .= "\"Assigned To\",";			# Field: 60 
  $header .= "\"ASTAttributeInstanceId\",";		# Field: 61 
  $header .= "\"Backup Asset ID+\",";			# Field: 62 
  $header .= "\"Book Value\",";				# Field: 63 
  $header .= "\"ChargeBackCode\",";			# Field: 64 
  $header .= "\"Configuration\",";			# Field: 65 
  $header .= "\"Department\",";				# Field: 66 
  $header .= "\"Instance Used By\",";			# Field: 67 
  $header .= "\"LogicalID\",";				# Field: 68 
  $header .= "\"Manufacturer ID+ (foreign key)\",";	# Field: 69 
  $header .= "\"NameFormat\",";				# Field: 70 
  $header .= "\"OpIdWeakReference\",";			# Field: 71 
  $header .= "\"Part Number\",";			# Field: 72 
  $header .= "\"PRInstanceID\",";			# Field: 73 
  $header .= "\"PurchaseCostInstanceID\",";		# Field: 74 
  $header .= "\"QuantityInStock\",";			# Field: 75 
  $header .= "\"Realm\",";				# Field: 76 
  $header .= "\"ReturnReceiptID\",";			# Field: 77 
  $header .= "\"Serial Number\",";			# Field: 78 
  $header .= "\"Supplier ID+ (foreign key)\",";		# Field: 79 
  $header .= "\"Tag Number\",";				# Field: 80 
  $header .= "\"Tax Credit\",";				# Field: 81 
  $header .= "\"TaxCostInstanceID\",";			# Field: 82 
  $header .= "\"UserDisplayObjectName\",";		# Field: 83 
  $header .= "\"VendorApplication\",";			# Field: 84 
  $header .= "\"Version Number\",";			# Field: 85 
  $header .= "\"zConfigInstanceID\",";			# Field: 86 
  $header .= "\"zPOInstanceID\",";			# Field: 87 
  $header .= "\"zPOLineItemInstanceID\",";		# Field: 88 
  $header .= "\"zPrevAssetID\",";			# Field: 89 
  $header .= "\"zSchemaProperName\",";			# Field: 90 
  $header .= "\"zTmpKeyword\"";				# Field: 91 

  $output = ",,A,A";

  $defaults = ",3,2000,2,,1,0,0,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,";

  print $header . "\n";

  $q_string  = "select prod_id,prod_name,prod_type,prod_citype,prod_tier1,prod_tier2,prod_tier3 ";
  $q_string .= "from products ";
  $q_string .= "where prod_remedy = 1 ";
  $q_string .= "order by prod_name ";
  $q_products = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_products = mysqli_fetch_array($q_products)) {

    print ",,\"" . $a_products['prod_name'] . "\",\"" . $a_products['prod_name'] . "\"" . $defaults . "\n";

  }

  mysqli_free_request($db);

?>
