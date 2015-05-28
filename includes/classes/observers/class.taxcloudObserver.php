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
	// UPDATE - Change to the lookups of the delivery_country. Now will pull the customers_country, delivery_country, AND billing_country.
	//          If there is no delivery address (in the case of virtual goods like gift certificates), use the billing address. 
	//          If that fails, use the customer's address. If that fails, since some mods remove customer info, use the store info.
	//          Without a valid non-zero $country_id, the authorize_with_capture call will fail to trigger since it is not known
	//          if TaxCloud is enabled for this transaction or not.
	function update(&$callingClass, $notifier, $paramsArray) {
	 
		$insert_id = $_SESSION['order_number_created'];
		
		// Find out what country the order is being shipped to
		global $db;
			
		$order_details = $db->Execute("select c1.countries_id AS delivery_lid, c2.countries_id AS billing_lid, c3.countries_id AS customer_lid
							FROM " . TABLE_ORDERS . " o
							LEFT JOIN " . TABLE_COUNTRIES . " c1
							ON c1.countries_name = o.delivery_country
							LEFT JOIN " . TABLE_COUNTRIES . " c2
							ON c2.countries_name = o.billing_country
							LEFT JOIN " . TABLE_COUNTRIES . " c3
					        	ON c3.countries_name = o.customers_country							
							WHERE o.orders_id = " . (int)$insert_id);

              
		if (is_numeric($order_details->fields['delivery_lid'])) { 
		// Is the delivery country id from the select query a number? If so, assign it to $country_id
			$country_id = $order_details->fields['delivery_lid'];
		} else if (is_numeric($order_details->fields['billing_lid'])) {
		// The number wasn't a number, it was likely a null value. Instead, assign it to the BILLING address
			$country_id = $order_details->fields['billing_lid'];
		} else if (is_numeric($order_details->fields['customer_lid'])) {
			$country_id = $order_details->fields['customer_lid'];
		} else if (is_numeric(STORE_COUNTRY)) {
			$country_id = STORE_COUNTRY; // So we don't have an id for delivery, billing, OR the customer, use the store ID
		} else {
			$country_id = 0;
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
