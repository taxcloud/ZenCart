<?php
/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
 
  require('includes/application_top.php');    
  
  $args = $_SERVER['argv'];
  $loadclasses = $args[0];
  if ( (strpos($loadclasses, 'loadclasses') > -1) &&  (strpos($loadclasses, 'true') > 0) ) {	
  	$tics = func_taxcloud_get_tics();
  	func_taxcloud_create_tax_classes($tics);
  }
  else {
  	die();
  }
 
  
function func_taxcloud_create_tax_classes($tics) {

	global $db;
	
	$query = "select tax_class_id, tax_class_title, tax_class_description from ". TABLE_TAX_CLASS;
	$tax_class = $db->Execute($query);
	global $tax_class_array;
	global $tax_classes_inserted;
	
	while (!$tax_class->EOF) {
		$tax_class_array[] = array('id' => $tax_class->fields['tax_class_id'],
	                                 'text' => $tax_class->fields['tax_class_title'],
	                                 'desc' => $tax_class->fields['tax_class_description']);
	      	$tax_class->MoveNext();
    	}
    	
    	$nextId = func_taxcloud_get_next_id($tax_class_array);
 	$index = 0;
 	
 	//The tic list has a hierarchy. Flatten this into a list of tic objects
 	$tics = func_taxcloud_flatten_list($tics);
	
	foreach($tics as $set) {

		$tic = $set->tic;  
		$desc = $tic->title; 
		$ticCode = $tic->id; 
		
		if( !func_taxcloud_title_exists($ticCode, $tax_class_array) ) { 
			func_taxcloud_insert_tax_class($nextId, $ticCode, $desc);
			$aTaxClass = Array('tic'=>$ticCode,'desc'=>$desc);
			$tax_classes_inserted[$index] = $aTaxClass;
			$nextId++;
			$index++;
		}
	}
}

function func_taxcloud_flatten_list($tics) {
	$flatList = Array();
	
	$index = 0;
	foreach($tics as $tic) {
		$children = $tic->tic->children;
		if ( isset($children) ) { 
			$childList = func_taxcloud_flatten_list($children);
			foreach($childList as $child) {
				$flatList[$index] = $child;
				$index++;
			}
		}
		$flatList[$index] = $tic;
		$index++;
	}
	
	return $flatList;
}

function func_taxcloud_insert_tax_class($nextId, $tic, $desc) {
	global $db;
	
	// tax_class_description is limited to 255 characters
	$desc = substr($desc, 0, 255);
	
	$insert = "insert into ". TABLE_TAX_CLASS. " (tax_class_id, tax_class_title, tax_class_description, last_modified, date_added) values (". $nextId. ", '". trim($tic) ."', '". trim($desc). "', sysdate(), sysdate())";
	
	$db->Execute($insert);
	
}

function func_taxcloud_title_exists($tic, $tax_class_array) {

	foreach($tax_class_array as $record) {
		if ($tic == $record['text']) {
			return 1;
		}
	}
	return 0;
}

function func_taxcloud_get_next_id($tax_class_array) {
	$nextId = 0;
	foreach($tax_class_array as $record) {
		$id = $record['id'];
		if ($id >= $nextId) {
			$nextId = $id;
		}
	}
	return $nextId+1;
}

function func_taxcloud_get_last_index($str, $content) {
	if(strstr($content, $str)!="") {
		return(strlen($content)-strpos(strrev($content),strrev($str)));
	}
	return (-1);
}
  
function func_taxcloud_get_tics() {
  	
  	// note the following will only work if fopen wrappers are enabled
  	if (ini_get('allow_url_fopen') == '1') {
	} else {
	   // use curl a custom function
	  echo("Error - can't open TIC file. You will need to add the tax classes manually. This is either a PHP configuration issue, a firewall issue, or a permissions issue."); 
	  die();
	} 
  	
  	$content = '';
  	
  	$ticURL = "http://taxcloud.net/tic/?format=json";
  	if ($handle = fopen($ticURL, 'r') ) {
  	
		while($line = fread($handle, 1024)) {
			$content = $content.$line;
		}
	} else {
  		echo("Error opening TIC file. Tax classes will need to be created manually");
  	}

	//TODO - extra character at the end of the file
	$index = strripos($content, ';'); 
	if ( $index > strlen($content-10) ) {
		$content = substr($content, 0, $index);	
	}

	$words = json_decode($content);
		
	foreach($words as $word) {  // there are some messages at the beginning that we need to pull out
		if ( is_array($word) ) {
			return $word;
		}
	}
			
	return null;
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

<table border="0" width="80%" cellspacing="2" cellpadding="2">
	<tr>
       	   	<td class="pageHeading" colspan="2"><?php echo 'TaxCloud Tax Classes'; ?></td>
      	</tr>
      	<tr class="dataTableHeadingRow">
        	<td class="dataTableHeadingContent" colspan="2">The following Tax Classes have been created for your cart. They can now be assigned to products in your catalog.</td>
        	<td></td>
      	</tr>
      	
      	<?php 
      	foreach ($tax_classes_inserted as $taxclass) {
      		echo("<tr>");
      		echo("<td width=5%>".$taxclass['tic']."</td><td width=95%>".$taxclass['desc']."</td>");
      		echo("</tr>");
      	}
      	if (empty($tax_classes_inserted)) {
      		echo("<tr>");
		echo("<td width=5%>None</td><td width=95%></td>");
      		echo("</tr>");
      	}
      	?>     
      
      	<tr><td></td><td></td></tr>
      	<tr><td></td><td></td></tr>
      	<tr class="dataTableHeadingRow">
        	<td class="dataTableHeadingContent" colspan="2">The following Tax Classes already existed in your cart and were not duplicated:</td>
        	<td></td>
      	</tr>
      	
      	<?php 
      	foreach ($tax_class_array as $taxclass) {
      		echo("<tr>");
      		echo("<td width=5%>".$taxclass['text']."</td><td width=95%>".$taxclass['desc']."</td>");
      		echo("</tr>");
        }
      	if (empty($tax_class_array)) {
      		echo("<tr>");
		echo("<td width=5%>None</td><td width=95%></td>");
	      	echo("</tr>");
      	}
      	?>
</table>
    
     
     
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>