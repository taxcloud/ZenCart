<?php

/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */

  require('includes/application_top.php');    
  
  require_once('../includes/modules/TaxCloud/func.taxcloud.php');
  
  global $TAXCLOUD_VERSION;
 
  
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $editClicked = false;
  if(zen_not_null($action)) {
  	if($action == 'edit') {
  		$editClicked = true;
  	} else {
  		$editClicked = false;  		
  	}
  }
  
  
  if ( isset($_POST['TAXCLOUD_API_ID']) ) {
  	
  	$TAXCLOUD_API_ID = $_POST['TAXCLOUD_API_ID'];
  	$TAXCLOUD_API_KEY = $_POST['TAXCLOUD_API_KEY'];
  	$TAXCLOUD_USPS_ID = $_POST['TAXCLOUD_USPS_ID'];
  	$TAXCLOUD_STORE_ADDR = $_POST['TAXCLOUD_STORE_ADDR'];
  	$TAXCLOUD_STORE_ZIP = $_POST['TAXCLOUD_STORE_ZIP'];
  	$TAXCLOUD_ENABLED = $_POST['TAXCLOUD_ENABLE'];
  	
  	// Update settings
  	$update = "update " . TABLE_CONFIGURATION . " set configuration_value = '" . zen_db_prepare_input($TAXCLOUD_API_ID) . "' where configuration_key = 'TAXCLOUD_API_ID'";
  	$db->Execute($update);
   	$update = "update " . TABLE_CONFIGURATION . " set configuration_value = '" . zen_db_prepare_input($TAXCLOUD_API_KEY) . "' where configuration_key = 'TAXCLOUD_API_KEY'";
  	$db->Execute($update);
  	$update = "update " . TABLE_CONFIGURATION . " set configuration_value = '" . zen_db_prepare_input($TAXCLOUD_USPS_ID) . "' where configuration_key = 'TAXCLOUD_USPS_ID'";
  	$db->Execute($update);
  	$update = "update " . TABLE_CONFIGURATION . " set configuration_value = '" . zen_db_prepare_input($TAXCLOUD_STORE_ADDR) . "' where configuration_key = 'TAXCLOUD_STORE_ADDR'";
  	$db->Execute($update);
  	$update = "update " . TABLE_CONFIGURATION . " set configuration_value = '" . zen_db_prepare_input($TAXCLOUD_STORE_ZIP) . "' where configuration_key = 'TAXCLOUD_STORE_ZIP'";
  	$db->Execute($update);
  	$update = "update " . TABLE_CONFIGURATION . " set configuration_value = '" . zen_db_prepare_input($TAXCLOUD_ENABLED) . "' where configuration_key = 'TAXCLOUD_ENABLED'";
  	$db->Execute($update);
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

<?php

  // Look up settings, if they exist
  $configuration_query = "select configuration_group_id from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_title = 'TaxCloud Configuration Settings'";
  $configurationGroupIDs = $db->Execute($configuration_query);
  $configurationGroupID = $configurationGroupIDs->fields['configuration_group_id'];
  
  if (($configurationGroupIDs->RecordCount() < 1)){

	$id_query = "select max(configuration_group_id) from " . TABLE_CONFIGURATION_GROUP;
	$maxID = $db->Execute($id_query); print_r($maxID->fields['max(configuration_group_id)']);
	
	$nextID = ($maxID->fields['max(configuration_group_id)'] +1);
	$configurationGroupID = $nextID;
	
	// Create new TaxCloud configuration group
	$configuration_insert = "insert into " . TABLE_CONFIGURATION_GROUP . " (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) " .
		"values (" . $nextID . ", 'TaxCloud Configuration Settings', 'TaxCloud Configuration Settings', 50, 0)";
	$db->Execute($configuration_insert);
	
	// Create default TaxCloud settings
	$id_query = "select max(configuration_id) from " . TABLE_CONFIGURATION;
	$maxID = $db->Execute($id_query); print_r($maxID->fields['max(configuration_id)']);
	
	$nextID = ($maxID->fields['max(configuration_id)'] +1);	
	
	$title = array('TaxCloud API ID', 'TaxCloud API Key', 'USPS_ID', 'Store Street Address', 'Store Zip Code', 'TaxCloud Enabled');
	$ckey = array('TAXCLOUD_API_ID', 'TAXCLOUD_API_KEY', 'TAXCLOUD_USPS_ID', 'TAXCLOUD_STORE_ADDR', 'TAXCLOUD_STORE_ZIP', 'TAXCLOUD_ENABLED');
	$value = array('', '', '', '', 'false');
	$desc = array('TaxCloud merchant account API ID', 'TaxCloud merchant account API key', 'USPS_ID', 'The street address of the store', 'The zip code of the store', 'TaxCloud enabled');
	$sortOrder = 99;
	
	$index = 0;
	
	foreach ($title as $id) {
		$settings_insert = "insert into " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order) " .
			" values (" . $nextID . ", '" . $title[$index] . "', '" . $ckey[$index] . "', '" . $value[$index] . "', '" . $desc[$index] . "', " . $configurationGroupID . ", " . $sortOrder . ")"; 
		
		$db->Execute($settings_insert);
		$index++;
		$nextID++;
		$sortOrder++;
	}
	
  } else {
  	$configuration_data_query = "select configuration_id, configuration_title, configuration_key, configuration_value from " . TABLE_CONFIGURATION . " where configuration_group_id = " . $configurationGroupID;
  	$results = $db->Execute($configuration_data_query);
  	
  	while (!$results->EOF) {

  		$aKey = $results->fields['configuration_key'];
  		$value = $results->fields['configuration_value'];
  		
  		switch($aKey) {
  			case 'TAXCLOUD_API_ID':
  				$TAXCLOUD_API_ID = $value;
  				break;
  			case 'TAXCLOUD_API_KEY':
  				$TAXCLOUD_API_KEY = $value;
  				break;
  			case 'TAXCLOUD_USPS_ID':
  				$TAXCLOUD_USPS_ID = $value;
  				break;
  			case 'TAXCLOUD_STORE_ADDR':
  				$TAXCLOUD_STORE_ADDR = $value;
  				break;
  			case 'TAXCLOUD_STORE_ZIP':
  				$TAXCLOUD_STORE_ZIP = $value;
  				break;
  			case 'TAXCLOUD_ENABLED':
  				$TAXCLOUD_ENABLE = $value;
  				break;
  		}
  		
  		$results->MoveNext();
  	}
  }
 	
  	
?>

<!-- body //-->

    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo 'TaxCloud Service Settings - '.TAXCLOUD_VERSION; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          	<td>Please enter the following data to configure TaxCloud lookup services in your cart.</td>
          </tr>
          <tr>
            <td valign="top">
            <form name="classes" method="post" action="<?php echo zen_href_link('taxcloud.php'); ?>">
             <input type="hidden" name = "securityToken" value = "<?php echo $_SESSION['securityToken']; ?>" /> 
            <table border="0" width="40%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" colspan="2"><?php echo 'Settings'; ?></td>                
              </tr>
              <tr>   
			  	<td class="smallText" align="left">TaxCloud API ID: &nbsp;</td>
			  	<td align="left"><?php echo $editClicked ? zen_draw_input_field('TAXCLOUD_API_ID',$TAXCLOUD_API_ID) : $TAXCLOUD_API_ID; ?></td>                             
              </tr>
              <tr>   
			  	<td class="smallText" align="left">TaxCloud API Key: &nbsp;</td>
			  	<td align="left"><?php echo $editClicked ? zen_draw_input_field('TAXCLOUD_API_KEY',$TAXCLOUD_API_KEY) : $TAXCLOUD_API_KEY; ?></td>                             
              </tr>
              <tr>   
			  	<td class="smallText" align="left">USPS_ID: &nbsp;</td>
			  	<td align="left"><?php echo $editClicked ? zen_draw_input_field('TAXCLOUD_USPS_ID',$TAXCLOUD_USPS_ID) : $TAXCLOUD_USPS_ID; ?></td>                             
              </tr>			  			                			  			  			  			  
	      <tr>   
			  	<td class="smallText" align="left">Store Street Address: &nbsp;</td>
			  	<td align="left"><?php echo $editClicked ? zen_draw_input_field('TAXCLOUD_STORE_ADDR',$TAXCLOUD_STORE_ADDR) : $TAXCLOUD_STORE_ADDR; ?></td>                            
              </tr>	
 	      <tr>   
 			  	<td class="smallText" align="left">Store Zip Code: &nbsp;</td>
 			  	<td align="left"><?php echo $editClicked ? zen_draw_input_field('TAXCLOUD_STORE_ZIP',$TAXCLOUD_STORE_ZIP) : $TAXCLOUD_STORE_ZIP; ?></td>                            
              </tr>	
  	      <tr>   
 			  	<td class="smallText" align="left">TaxCloud Enabled: &nbsp;</td>
 			  	<td align="left">
 			<?php                
                		if($editClicked) {
					if ($TAXCLOUD_ENABLE=='true') {
						echo zen_draw_checkbox_field('TAXCLOUD_ENABLE', 'true', true, null);
					} else {
						echo zen_draw_checkbox_field('TAXCLOUD_ENABLE', 'true', false, null);
					}
                			
                		} else {
                			if ($TAXCLOUD_ENABLE=='true') {
                				echo 'true';
                			} else {
                				echo 'false';
                			}
                		}
                	?>
 			  	
 			  	</td>                            
              </tr>             
              <tr>
                <td class="smallText" colspan="2" align="left">                                
                	<?php                
                		if(!$editClicked) {
							echo '<a href='. zen_href_link('taxcloud', 'action=edit').'>' . zen_image_button('button_update.gif') . '</a>';
                		}
                		else {
                			echo zen_image_submit('button_save.gif', IMAGE_SAVE);
                		} 
                	?>
                </td>
              </tr>
            </table>
            </form>
            </td>
          </tr>
        </table></td>
      </tr>
     </table>
     	<tr>
	    <td> 

     <BR><BR>
     <table border="0" width="40%" cellspacing="0" cellpadding="2">
     	<tr class="dataTableHeadingRow">
        	<td class="dataTableHeadingContent" colspan="2"><?php echo 'Configuration Test'; ?></td>                
         </tr>
              <td>
  <?php             
	
	try {
		$client = new SoapClient("https://api.taxcloud.net/1.0/TaxCloud.asmx?wsdl");  
		$params = Array();
		$pingResponse = $client->ping($params); //this will always fail (no API key)
		$message = $pingResponse->PingResult->Messages->ResponseMessage->Message;
		if ( $message == 'Invalid apiLoginID and/or apiKey' ) {
			echo('<font color="green">Server is configured to reach TaxCloud</font>');
		} else {
			echo('<font color="red"><b>Server is not configured to reach TaxCloud</b>');
			echo('<br><br>Please go to the Tools>Server/Version Info menu item. Search for "cURL support=enabled" and "Soap Client=enabled". If both of these are not enabled you will not be able to connect to TaxCloud. Please contact your server administrator to have these settings changed.</font>');
		}
	} catch (Exception $e) {
		echo('<font color="red"><b>Server is not configured to reach TaxCloud</b>');
		echo('<br><br>Please go to the Tools>Server/Version Info menu item. Search for "cURL support=enabled" and "Soap Client=enabled". If both of these are not enabled you will not be able to connect to TaxCloud. Please contact your server administrator to have these settings changed</font>');
		echo('<br><br>Exception: '.$e);
	}
?>	
		</td>
	</tr>
      </table>

<!--      
     <BR><BR>
     <table width="40%" cellspacing="0" cellpadding="2">
     <form name="taxclasses" method="post" action="<?php echo zen_href_link('taxcloud_taxclass.php'); ?>?loadclasses=true">
        <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="2"><?php echo 'Tax Class Loader'; ?></td>                
        </tr>
     	<tr>
     	    <td colspan="2">TaxCloud requires that there is a Tax Class assigned to each product in the catalog. These Tax Classes include a "TIC ID" which is TaxCloud's designation for the tax category for the product.
     	    This is roughly the same as a product category. This Loader function automatically creates Tax Classes within Zen Cart which represent all the tax categorizations within TaxCloud. Click the button below to create the tax classes. 
     	    <p>Note - your existing tax classes will be preserved. You can also create your Tax Classes manually by using the "Tax Classes" menu item.</td>
     	</tr>
     	<tr>
	    <td><?php   echo zen_image_submit('button_download_now.gif', 'download now'); ?></td>
	    <td></td>
     	</tr>
     </form>
     </table>
  -->   


   
     
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
