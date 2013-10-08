<?php
/*
 *	PayPal Express Checkout Callback Integration
 *	
 *	As a part of the PayPal Express Checkout process, PayPal calls back to this service which 
 *	computes the appropriate shipping options for the order and calculates the appropriate tax rates.
 */
 
 
  require('includes/application_top.php');    
  require('includes/classes/shipping.php');
  require_once('includes/modules/TaxCloud/func.taxcloud.php');
  
  $token = $_REQUEST['TOKEN'];
  $referrer = $_SERVER['HTTP_REFERER'];
  if (!isset($token)) {
  	die();
  }
  

  $returnString = getReturnString($request);
  
  echo($returnString);

  

 
function getReturnString($request) {

	global $db;
	
	
	$query = 'select * from ' . TABLE_CONFIGURATION . ' where configuration_key = \'MODULE_SHIPPING_INSTALLED\'';
	$result = $db->Execute($query);						
	$installedModules = $result->fields['configuration_value'];	
	$installedOptions = explode(';', $installedModules);
	$module = 'flat';
	$index = 0;
	$products = Array();
	
	while ( $index < 100 ) {
		$productName = $_REQUEST['L_NAME'.$index];
		if ( isset($productName) ) {
			$productInfo = func_get_product_info($_REQUEST['L_NUMBER'.$index]);
			$product = Array();
			$product['id'] = $productInfo['id']; 
			$product['price'] = $_REQUEST['L_AMT'.$index];
			$product['qty'] = $_REQUEST['L_QTY'.$index];
			$product['freeshipping'] = $productInfo['freeshipping'];
			$product['weight'] = $productInfo['weight'];
			$products[$index] = $product;
			
		} else {
			break;
		}
		$index++;
	}
	
   	if (is_array($installedOptions)) {
      		$include_quotes = array();
      
      		foreach ($installedOptions as $value ) {

        		$class = substr($value, 0, strrpos($value, '.'));   
        
        		if (zen_not_null($module)) {	
            			$include_quotes[] = $class;
        		} elseif ($GLOBALS[$class]->enabled) {
          			$include_quotes[] = $class;
        		}
      		}
						
      		$size = sizeof($include_quotes);
      		for ($i=0; $i<$size; $i++) {

      			$quote = getQuote($include_quotes[$i], $products);

        		if ( ! empty($quote) ) {
        			$quotes_array[$i] = $quote;
        		}
      		}
    	}	
	
	$shippingQuotes = $quotes_array;
	
	$rates = Array();
	$index = 0;
	
	foreach ($shippingQuotes as $quote) { 
		$title = $quote['module'];
		$methods = $quote['methods'];
		if ( isset($methods) && is_array($methods) ) {
			
			$cost = $methods['cost'];
		} 

		$rates[$index]['cost'] = $cost;
		$rates[$index]['title'] = $title;
		$index++;
	}
	
	$token = $_REQUEST['TOKEN'];
	$shipToStreet = $_REQUEST['SHIPTOSTREET'];
	$shipToStreet2 = $_REQUEST['SHIPTOSTREET2'];
	$shipToState = $_REQUEST['SHIPTOCITY'];
	$shipToCountry = $_REQUEST['SHIPTOCOUNTRY'];
	$shipToZip = $_REQUEST['SHIPTOZIP'];
	
	
	$delivery = Array();
	$delivery['street_address'] = $shipToStreet;
	$delivery['street_address2'] = $shipToStreet2;
	$delivery['city'] = $shipToCity;
	$delivery['state'] = $shipToState;
	$delivery['postcode'] = $shipToZip;
	
	$_SESSION['customer_id'] = getCustomerID($token);
	$_SESSION['cartID'] = getCartID($token);  //print("<Br>cartID: "); 
	
	//clean up hash
	removeToken($token);
	
	//Start building the response string
	$response = 'METHOD=CallbackResponse&OFFERINSURANCEOPTION=false';
	
	// Call TaxCloud and calculate the tax rates for each shipping option
	
	$shipping = Array();
	foreach ($rates as $rate) {
		$product = Array();
		$product['id'] = 'shipping';
		$product['price'] = $rate['cost'];
		if ($rate['cost'] == 0) {
			$product['price'] = 0;
		}
		$product['qty'] = 1;
		$shipping[$index] = $product;
		$index++;
	}
	
	$err = Array();
	$result = func_lookup_tax($products,$delivery,$err,$shipping);
	
	$index = 0;
	$productTotalTax = 0;
	foreach ($products as $product) {
		$productTotalTax = $productTotalTax + $result[$index];
		$index++;
	}

	
	$firstIndex = $index;
	$numberingIndex = 0;
	foreach ($rates as $rate) {	

		$err = Array();
		
		if ($result[$index] != -1) {
		
			$response = $response . '&L_SHIPPINGOPTIONNAME'. $numberingIndex . '=' . ''; //$rate['title'];  If this is supplied, double names show up in the drop-down
			$response = $response . '&L_SHIPPINGOPTIONLABEL'. $numberingIndex . '=' . $rate['title'];
			$response = $response . '&L_SHIPPINGOPTIONAMOUNT'. $numberingIndex . '=' . $rate['cost'];
			$response = $response . '&L_TAXAMT'. $numberingIndex . '=' . round($result[$index]+$productTotalTax, 2);
			$response = $response . '&L_SHIPPINGOPTIONISDEFAULT' . $numberingIndex . '=';
			if ( $index == $firstIndex ) {
				$response = $response . 'true';
			} else {
				$response = $response . 'false';
			}
		}
		$index++;
		$numberingIndex++;
	} 
	

	return $response;

}

function func_lookup_tax($products,$customer,&$errMsg,$shipping) {
	global $client; 

	$origin = func_taxcloud_get_company_address();	
	$origin = func_taxcloud_verify_address($origin, $errMsg);
	
	if(is_null($origin)) return -1;	
	
	//Verify Customer's destination
	$destination = func_taxcloud_get_customer_address($customer);	
	$destination = func_taxcloud_verify_address($destination, $errMsg);
	
	
	//Determine the total number of products in the cart
	$total_number_of_products_in_cart = 0;
	foreach ($products as $product) {
		$total_number_of_products_in_cart += $product['qty'];
	}
	
	
	if(is_null($destination)) return -1;
	
	if(!is_null($origin) && !is_null($destination)) {
	
		$cartItems = Array();

		$index = 0;

		//total cost of cartItems
		$items_cost = 0;
		
		foreach ($products as $k => $product) {

			$cartItem = new CartItem();
						
			preg_match("/^(\d+)/", $product['id'], $match);			

			$cartItem->setItemID($match[0]);
			$cartItem->setIndex($index); // Each cart item must have a unique index

			$tic = func_taxcloud_get_tic($match[0]);
			if(!$tic) {
				//no TIC has been assigned to this product, use default
				$tic = "00000";
			}
			
			$cartItem->setTIC($tic);
			
			$price = $product['price'];
			
			$cartItem->setPrice($price); // Price of each item
			$cartItem->setQty($product['qty']); // Quantity

			$cartItems[$index] = $cartItem;

			$index++;

		}

		//Shipping as a cart item
		foreach ($shipping as $s) {  
			$cartItem = new CartItem();
			$cartItem->setItemID('shipping');
			$cartItem->setIndex($index);
			$cartItem->setTIC(10070);
			$cartItem->setPrice($s['price']);
			$cartItem->setQty(1);
			$cartItems[$index] = $cartItem;
			$index++;
		}
		
		$exemptCert = null; 
	
		$params = array( "apiLoginID" => TAXCLOUD_API_ID,
				 "apiKey" => TAXCLOUD_API_KEY,
				 "customerID" => $_SESSION['customer_id'],
				 "cartID" => $_SESSION['cartID'],
				 "cartItems" => $cartItems,
				 "origin" => $origin,
				 "destination" => $destination,
				 "deliveredBySeller" => true,
				 "exemptCert" => $exemptCert
		);

		//Call the TaxCloud web service
		try {
			$lookupResponse = $client->lookup( $params );
		} catch (Exception $e) {
			//retry
			try {
				$lookupResponse = $client->lookup( $params );
			} catch (Exception $e) {

				$errMsg[] = "Error encountered looking up tax amount ".$e->getMessage();
	
				//irreparable, return
				return -1;
			}
		}

		$lookupResult = $lookupResponse->{'LookupResult'};

		if($lookupResult->ResponseType == 'OK' || $lookupResult->ResponseType == 'Informational') {
			$cartItemsResponse = $lookupResult->{'CartItemsResponse'};
			$cartItemResponse = $cartItemsResponse->{'CartItemResponse'};
			$taxes = Array();
			$index = 0;

			//response may be an array
			if ( is_array($cartItemResponse) ) {
				foreach ($cartItemResponse as $c) {
					$amount = ($c->TaxAmount);
					$taxes[$index] = $amount;
					$index++;
				}
			} else {
				$amount = ($cartItemResponse->TaxAmount);
				$taxes[0] = $amount;
			}
			
			return $taxes;
			
		} else {
			return -1;
		}
	} else {

		return -1;
	}
}


function getQuote($method, $products) {

	switch ($method) {
		case 'flat' :
			$quote = getFlatrateQuote($products);
			break;
		case 'item' :
			$quote = getPerItemQuote($products);
			break;
		case 'freeshipper' :
			$quote = getFreeShippingQuote($products);
			break;
		case 'perweightunit' : 
			$quote = getPerWeightUnit($products);
			break;
		case 'storepickup' :
			$quote = getStorePickupQuote($products);
			break;
		case 'table' :
			$quote = getTableRateQuote($products);
			break;
		case 'zones' :
			$quote = getZoneRateQuote($products);
			break;
		default:
			$quote = Array();
			break;
	
	}

	return $quote;
}

function getFlatrateQuote($products) { 

	include('includes/languages/english/modules/shipping/flat.php');
	$quote = Array();
	$quote['id'] = 'flat';
	$quote['module'] = MODULE_SHIPPING_FLAT_TEXT_TITLE;
	$methods = Array();
	$methods['id'] = 'flat';
	$methods['title'] = MODULE_SHIPPING_FLAT_TEXT_WAY;
	$methods['cost'] = MODULE_SHIPPING_FLAT_COST;
	$quote['methods'] = $methods;
	
	return $quote;
}

function getPerItemQuote($products) { 
	include('includes/languages/english/modules/shipping/item.php');
	$quote = Array();
	$quote['id'] = 'item';
	$quote['module'] = MODULE_SHIPPING_ITEM_TEXT_TITLE;
	$methods = Array();
	$methods['id'] = 'item';
	$methods['title'] = MODULE_SHIPPING_ITEM_TEXT_WAY;
	$methods['cost'] = ((MODULE_SHIPPING_ITEM_COST * sizeof($products)) + MODULE_SHIPPING_ITEM_HANDLING);
	$quote['methods'] = $methods;
		
	return $quote;
}

function getFreeShippingQuote($products) {  

	// Make sure all products qualify for free shipping
	$freeshippingQualifies = true;
	foreach ($products as $product) {
		if ( true != $product['freeshipping'] ) {
			$freeshippingQualifies = false;
			break;
		}
	}
	include('includes/languages/english/modules/shipping/freeshipper.php');
	$quote = Array();
	if ( $freeshippingQualifies ) {
		$quote['id'] = 'freeshipper';
		$quote['module'] = MODULE_SHIPPING_FREESHIPPER_TEXT_TITLE;
		$methods = Array();
		$methods['id'] = 'freeshipper';
		$methods['title'] = MODULE_SHIPPING_FREESHIPPER_TEXT_WAY;
		$methods['cost'] = MODULE_SHIPPING_FREESHIPPER_COST + MODULE_SHIPPING_FREESHIPPER_HANDLING;
		$quote['methods'] = $methods;
	}
	return $quote;
}

function getPerWeightUnit($products) {
	include('includes/languages/english/modules/shipping/perweightunit.php');
	$totalWeight = 0;
	foreach($products as $product) {
		$totalWeight = $totalWeight + $products['weight'];
	}
	$shipping_num_boxes = 1; //assuming shipping in one box
	$quote = Array();
	$quote['id'] = 'perweightunit';
	$quote['module'] = MODULE_SHIPPING_PERWEIGHTUNIT_TEXT_TITLE;
	$methods = Array();
	$methods['id'] = 'perweightunit';
	$methods['title'] = MODULE_SHIPPING_PERWEIGHTUNIT_TEXT_WAY;
	$methods['cost'] = MODULE_SHIPPING_PERWEIGHTUNIT_COST * ($totalWeight * $shipping_num_boxes) +
                                                   (MODULE_SHIPPING_PERWEIGHTUNIT_HANDLING_METHOD == 'Box' ? MODULE_SHIPPING_PERWEIGHTUNIT_HANDLING * $shipping_num_boxes : MODULE_SHIPPING_PERWEIGHTUNIT_HANDLING);
	$quote['methods'] = $methods;
		
	return $quote;
}

function getStorePickupQuote($products) {
	include('includes/languages/english/modules/shipping/storepickup.php');
	$quote = Array();
	$quote['id'] = 'storepickup';
	$quote['module'] = MODULE_SHIPPING_STOREPICKUP_TEXT_TITLE;
	$methods = Array();
	$methods['id'] = 'storepickup';
	$methods['title'] = MODULE_SHIPPING_STOREPICKUP_TEXT_WAY;
	$methods['cost'] = MODULE_SHIPPING_STOREPICKUP_COST;
	$quote['methods'] = $methods;
		
	return $quote;
}


function getTableRateQuote($products) {   //TODO this doesn't currently work
	
	include('includes/modules/shipping/table.php');
	include('includes/languages/english/modules/shipping/table.php');
			
	$quote = Array();
	return $quote;
}

function getZoneRateQuote($products) {   //TODO this doesn't currently work
	require('includes/modules/shipping/zones.php');
		
	$quote = Array();
	return $quote;
}

function  getCustomerID($token) {
    $aHash = TokenHash::getInstance();
    $customerID = $aHash->getCustomerID($token);
    return $customerID;
}

function getCartID($token) {
    $aHash = TokenHash::getInstance();
    $cartID = $aHash->getCartID($token);
    return $cartID;
}

function removeToken($token) {
    $aHash = TokenHash::getInstance();
    $aHash->removeToken($token);
}

function func_get_product_info($productName) {
	global $db;
	
	$query = 'select products_id, product_is_always_free_shipping, products_weight  from ' . TABLE_PRODUCTS . ' where products_model = \''. $productName . '\'';
	$result = $db->Execute($query);						
	$productInfo = Array();
	$productInfo['id'] = $result->fields['products_id'];
	$productInfo['freeshipping'] = $result->fields['product_is_always_free_shipping'];
	$productInfo['weight'] = $result->fields['products_weight'];
	return $productInfo;
}


?>