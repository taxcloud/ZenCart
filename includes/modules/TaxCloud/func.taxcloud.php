<?php
/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
require("classes.php");

define('TAXCLOUD_VERSION', 'v1.5');

define('FILENAME_TAXCLOUD', 'taxcloud');
define('BOX_TAXES_TAXCLOUD', 'TaxCloud Tax Calculation');

function func_taxcloud_get_client() {
	if ( isset($client) ) {
		return $client;
	} else {
		$client = new SoapClient("https://api.taxcloud.net/1.0/TaxCloud.asmx?wsdl");
		return $client;
	}
}

function func_taxcloud_is_enabled($country_code='') {	

	if ($country_code != '223') {
		return false;
	}
  	$configuration_query = "select c.configuration_value from " . TABLE_CONFIGURATION . " c, " . TABLE_CONFIGURATION_GROUP . " cg where cg.configuration_group_title = 'TaxCloud Configuration Settings' and c.configuration_key = 'TAXCLOUD_ENABLED' and c.configuration_group_id = cg.configuration_group_id";
 	global $db;	
  	$results = $db->Execute($configuration_query);
  	$isEnabled = $results->fields['configuration_value'];	
	return $isEnabled;
}


/**
 * Verify an address using TaxCloud service
 */
function func_taxcloud_verify_address($address, &$err) {

	global $client;
	

	// Verify the address through the TaxCloud verify address service
	$params = array( "uspsUserID" => TAXCLOUD_USPS_ID,
				 "address1" => $address->getAddress1(),
				 "address2" => $address->getAddress2(),
				 "city" => $address->getCity(),
				 "state" => $address->getState(),
				 "zip5" => $address->getZip5(),
				 "zip4" => $address->getZip4());

	try {
		$client = func_taxcloud_get_client();
		$verifyaddressresponse = $client->verifyAddress( $params );

	} catch (Exception $e) {

		//retry in case of timeout
		try {
			$verifyaddressresponse = $client->verifyAddress( $params );
		} catch (Exception $e) {

	//		$err[] = "Error encountered while verifying address ".$address->getAddress1().
	//		" ".$address->getState()." ".$address->getCity()." "." ".$address->getZip5().
	//		" ".$e->getMessage();
	//		//irreparable, return
			return null;
		}
	}

	if($verifyaddressresponse->{'VerifyAddressResult'}->ErrNumber == 0) {
		// Use the verified address values
		$address->setAddress1($verifyaddressresponse->{'VerifyAddressResult'}->Address1);
		$address->setAddress2($verifyaddressresponse->{'VerifyAddressResult'}->Address2);
		$address->setCity($verifyaddressresponse->{'VerifyAddressResult'}->City);
		$address->setState($verifyaddressresponse->{'VerifyAddressResult'}->State);
		$address->setZip5($verifyaddressresponse->{'VerifyAddressResult'}->Zip5);
		$address->setZip4($verifyaddressresponse->{'VerifyAddressResult'}->Zip4);

	} else {

	//	$err[] = "Error encountered while verifying address ".$address->getAddress1().
	//		" ".$address->getState()." ".$address->getCity()." "." ".$address->getZip5().
	//		" ".$verifyaddressresponse->{'VerifyAddressResult'}->ErrDescription;

		return null;
	}
	return $address;
}

function func_taxcloud_get_customer_address($delivery) {	
	// Customer's address
	$destination = new Address();	
	$destination->setAddress1($delivery['street_address']);
	$destination->setAddress2('');
	$destination->setCity($delivery['city']);
	$destination->setState($delivery['state']); // Two character state appreviation
	$destination->setZip5($delivery['postcode']);
	$destination->setZip4('');
	return $destination;
}

/**
 * Get the store's location
 */
function func_taxcloud_get_company_address() {	
	$origin = new Address();
	$origin->setAddress1(TAXCLOUD_STORE_ADDR);
	$origin->setZip5(TAXCLOUD_STORE_ZIP); 	
	return $origin;
}

/**
 * Retrieves a product's TIC ID.
 * @param $product_id
 */
function func_taxcloud_get_tic($product_id) {
	global $db;	
	$query = "select tax_class_title from ".TABLE_PRODUCTS .", ".TABLE_TAX_CLASS." where products_tax_class_id = tax_class_id and products_id = ".$product_id;	
	$result = $db->Execute($query);						
	$tic = $result->fields['tax_class_title'];		
	$i = preg_match("/^(\d+)/", $tic);
	if($i != 0)
		return $tic;
	else 
		return $i;			
}

/**
 * Look up tax cost using TaxCloud web services
 * @param $product
 * @param $customer
 */
function func_taxcloud_lookup_tax($products,$customerAddress,&$errMsg,$shipping, $exemptionCertificate) {
	global $client; 

	$origin = func_taxcloud_get_company_address();	
	$origin = func_taxcloud_verify_address($origin, $errMsg);
	
	if(is_null($origin)) return -1;	
	
	//Verify Customer's destination
	$destination = func_taxcloud_get_customer_address($customerAddress);	
	$verified_destination = func_taxcloud_verify_address($destination, $errMsg);
	
	if(!is_null($verified_destination)) {
		$destination = $verified_destination;
	}
	
	if(!is_null($origin) && !is_null($destination)) {
	
		$cartItems = Array();

		$index = 0;
		
		foreach ($products as $k => $product) {

			$cartItem = new CartItem();
						
			preg_match("/^(\d+)/", $product['id'], $match);			

			$cartItem->setItemID($match[0]);
			$cartItem->setIndex($index); // Each cart item must have a unique index

			$tic = func_taxcloud_get_tic($match[0]);
			if(!$tic) {
				//no TIC has been assigned to this product, use default
				$tic = null;
			}
			
			$cartItem->setTIC($tic);
			
			$price = $product['final_price'];
			
			$cartItem->setPrice($price); // Price of each item
			$cartItem->setQty($product['qty']); // Quantity   

			$cartItems[$index] = $cartItem;

			$index++;

		}

		//Shipping as a cart item
		$cartItem = new CartItem();
		$cartItem->setItemID('shipping');
		$cartItem->setIndex($index);
		$cartItem->setTIC(11010);
		$cartItem->setPrice($shipping);
		$cartItem->setQty(1);
		$cartItems[$index] = $cartItem;	
	
		$params = array( "apiLoginID" => TAXCLOUD_API_ID,
				 "apiKey" => TAXCLOUD_API_KEY,
				 "customerID" => $_SESSION['customer_id'],
				 "cartID" => $_SESSION['cartID'],
				 "cartItems" => $cartItems,
				 "origin" => $origin,
				 "destination" => $destination,
				 "deliveredBySeller" => false,
				 "exemptCert" => $exemptionCertificate
		);
		
		$client = func_taxcloud_get_client();

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
			
			$cartID = $lookupResult->CartID;
			if ( isset($cartID) ) {
				$_SESSION['cartID'] = $cartID;
			}
			
			$taxTotal = 0;
			//Calculate tax totals
			foreach( $taxes as $tax ) {
				$taxTotal = $taxTotal + round($tax,2);
			}
			
			$_SESSION['taxcloudTaxTotal'] =  $taxTotal;
			return array('name' => 'Sales Tax', 'tax' => $taxTotal);
			
		} else {
			$errMsgs = $lookupResult->{'Messages'};
			foreach($errMsgs as $err) {
				$errMsg[] = "Error encountered looking up tax amount ".$err->{'Message'};				
			}
			return -1;
		}
	} else {

		return -1;
	}
}

/**
 * Authorized with Capture 
 * @param $orderID
 */
function func_taxcloud_authorized_with_capture($orderID, &$errMsg) {
	global $client;

	$result = 0;
	
	$dup = "This purchase has already been marked as authorized";
	
	// Current date - example of format: '2010-09-08T00:00:00';
	$dateAuthorized = date("Y-m-d");
	$dateAuthorized = $dateAuthorized . "T00:00:00";	

	$params = array( "apiLoginID" => TAXCLOUD_API_ID,
					 "apiKey" => TAXCLOUD_API_KEY,
					 "customerID" => $_SESSION['customer_id'],
					 "cartID" => $_SESSION['cartID'],
					 "orderID" => $orderID,
					 "dateAuthorized" => $dateAuthorized,
					 "dateCaptured" => $dateAuthorized);
					 
	$client = func_taxcloud_get_client();

	// The authorizedResponse array contains the response verification (Error, OK, ...)
	$authorizedResponse = null;
	try {
		$authorizedResponse = $client->authorizedWithCapture( $params );	

	} catch (Exception $e) {

		//infrastructure error, try again	
		try {
			$authorizedResponse = $client->authorizedWithCapture( $params );

			$authorizedResult = $authorizedResponse->{'AuthorizedWithCaptureResult'};		
			if ($authorizedResult->ResponseType != 'OK') {
				$msgs = $authorizedResult->{'Messages'};
				$respMsg = $msgs->{'ResponseMessage'};
		
				//duplicate means the the previous called was good. Therefore, consider this to be good
				if (trim ($respMsg->Message) == $dup) {
					return 1;
				}
			} else if ($authorizedResult->ResponseType == 'Error') {
				$msgs = $authorizedResult->{'Messages'};
				$respMsg = $msgs->{'ResponseMessage'};
				//duplicate means the the previous called was good. Therefore, consider this to be good
				if (trim ($respMsg->Message) == $dup) {
					return 1;
				} else {
					$errMsg[] = "Error encountered looking up tax amount ".$respMsg;
					return -1;
				}
			} else {
				return -1;
			}
			
		} catch (Exception $e) {
			//give up
			$errMsg[] = $e->getMessage();
			return 0;
		}
	}

	$authorizedResult = $authorizedResponse->{'AuthorizedWithCaptureResult'};
	if ($authorizedResult->ResponseType == 'OK') {
		$_SESSION['singlePurchase'] = null;   //one-time certificate
		$_SESSION['selectedCertID'] = null;  //saved certificate
	//	$_SESSION['taxcloudTaxTotal'] =  null;
		return 1;
	} else {
		$msgs = $authorizedResult->{'Messages'};
		$respMsg = $msgs->{'ResponseMessage'};				
		
		$errMsg [] = $respMsg->Message;
		return 0;
	}		
	
	return $result;	
}

function func_taxcloud_add_exemption_certificate($exemptionCertificate,$customerID) {
	global $client;
	
	$params = array( "apiLoginID" => TAXCLOUD_API_ID,
					 "apiKey" => TAXCLOUD_API_KEY,
					 "customerID" => $customerID,
				 	 "exemptCert" => $exemptionCertificate 
				 	 );
	try {
		$client = func_taxcloud_get_client();
		$addExemptionResponse = $client->addExemptCertificate( $params );

	} catch (Exception $e) {
		return -1;
	}

}

function func_taxcloud_get_exemption_certificates($customerID) {
	global $client;
	
	$params = array( "apiLoginID" => TAXCLOUD_API_ID,
					 "apiKey" => TAXCLOUD_API_KEY,
					 "customerID" => $customerID
				 	 );
		
	try {
		$client = func_taxcloud_get_client();
		$getExemptCertificatesResponse = $client->getExemptCertificates( $params );
		$getCertificatesRsp = $getExemptCertificatesResponse->{'GetExemptCertificatesResult'};
		$exemptCertificatesArray = $getCertificatesRsp->{'ExemptCertificates'};
		$exemptCertificates = $exemptCertificatesArray->{'ExemptionCertificate'};
		
		if (is_array($exemptCertificates)) {
			return $exemptCertificates;
		} else {
			return $exemptCertificatesArray;
		}
		
	} catch (Exception $e) {
		return Array();
	}
	return Array();
}

function func_taxcloud_delete_exemption_certificate($certID) {
	
	global $client;
	
	$params = array( 
	 	"apiLoginID" => TAXCLOUD_API_ID,
		"apiKey" => TAXCLOUD_API_KEY,
		"certificateID" => $certID
	);
	
	try {
		$client = func_taxcloud_get_client();
		$deleteExemptCertificateResponse = $client->deleteExemptCertificate( $params );

	} catch (Exception $e) {
		return -1;
	}
}


function func_taxcloud_return_order($order_id) {   

	global $client;
	
	global $db;
	
	$results = $db->Execute("select products_id, products_quantity, final_price
	                     from " . TABLE_ORDERS_PRODUCTS . "
                             where orders_id = '" . (int)$order_id . "'");
        
	$cartItems = Array();

	$index = 0;
        
        while ( !$results->EOF ) {
        	$fields = $results->fields;
      		
      		$cartItem = new CartItem();
		$cartItem->setItemID($fields['products_id']);
		$cartItem->setIndex($index);
		$cartItem->setTIC(func_taxcloud_get_tic($fields['products_id']));  
		$cartItem->setPrice($fields['final_price']);
		$cartItem->setQty($fields['products_quantity']);
		$cartItems[$index] = $cartItem;
      		
      		$index++;
      		$results->MoveNext();
      		
        }
        
        //Reverse the order shipping
	$results = $db->Execute("select value
	                        from " . TABLE_ORDERS_TOTAL . "
                                where orders_id = '" . (int)$order_id . "'
                                and class = 'ot_shipping'");
        
        while ( !$results->EOF ) {
        	$fields = $results->fields;
      		
		//Shipping as a cart item
		$cartItem = new CartItem();
		$cartItem->setItemID('shipping');
		$cartItem->setIndex($index);
		$cartItem->setTIC(11010);
		$cartItem->setPrice($fields['value']);
		$cartItem->setQty(1);
		$cartItems[$index] = $cartItem;	
               
      		$index++;
      		$results->MoveNext();
      		
        }
        
	$returnDate = date("Y-m-d");
	$returnDate = $returnDate . "T00:00:00";
	
	$params = array(
		"apiLoginID" => TAXCLOUD_API_ID,
		"apiKey" => TAXCLOUD_API_KEY,
		"orderID" => $order_id,
		"cartItems" => $cartItems,
		"returnedDate" => $returnDate); 
	
	try {
		$client = func_taxcloud_get_client();
		$returnResponse = $client->Returned($params);	
	} catch (Exception $e) { 
		return -1;
	}

}
?>
