<?php
/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */

class taxcloudObserver extends base {

	function taxcloudObserver() {
		global $zco_notifier;
		
		$zco_notifier->attach($this, array('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_CREATE_ADD_PRODUCTS'));
		
	}
	
	// This method gets called once the order is created in the database. At this point we should call TaxCloud and confirm the order.
	function update(&$callingClass, $notifier, $paramsArray) {
	 
		$insert_id = $_SESSION['order_number_created'];
		
		// Find out what country the order is being shipped to
		global $db;
			
		$results = $db->Execute("select delivery_country from " . TABLE_ORDERS . "
                        where orders_id = '" . (int)$insert_id . "'");
              
                $country_id = 0;
                if ( $results->fields['delivery_country'] == 'United States' ) {
                	$country_id = 223;
                }
		
	   	$TAXCLOUD_ENABLE = func_taxcloud_is_enabled($country_id); 
	   	if ($TAXCLOUD_ENABLE=='true') {
	   		$errMsg = array();
	   		$result = func_taxcloud_authorized_with_capture($insert_id, $errMsg);
	   		if($result > 0) {
	   			//OK
	   		} else {
	   			die(print_r($errMsg, true));
	   		}
	   		
	   		
	   	} 
	}

}

?>