<?php
/**
 * TaxCloud v1.5.1
 * @license https://taxcloud.net/ftpsl.pdf
 */
 
  require(DIR_WS_MODULES . 'require_languages.php');
  require_once('includes/modules/TaxCloud/func.taxcloud.php');
  
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

 
 	$exemptState = $_POST['ExemptState'];  
 	
 	$singlePurchase = $_POST['SinglePurchase']; 
 	$singlePurchaseOrderNumber = $_POST['SinglePurchaseOrderNumber'];  
 	$blanketPurchase = $_POST['BlanketPurchase'];  //BlanketPurchase OR SinglePurchase will be passed

 	$purchaserFirstName = $_POST['PurchaserFirstName'];
 	$purchaserLastName = $_POST['PurchaserLastName'];
 	$purchaserAddress1 = $_POST['PurchaserAddress1'];
 	$purchaserCity = $_POST['PurchaserCity'];
 	$purchaserState = $_POST['PurchaserState']; //
 	$purchaserZip = $_POST['PurchaserZip'];
 	$taxType = $_POST['TaxType']; //
 	$idNumber = $_POST['IDNumber'];
 	$stateOfIssue = $_POST['StateOfIssue']; //
 	$countryOfIssue = $_POST['CountryOfIssue']; //
 	$purchaserBusinessType = $_POST['PurchaserBusinessType']; //
 	$purchaserBusinessTypeOtherValue = $_POST['PurchaserBusinessTypeOtherValue'];  //optional
 	$purchaserExemptionReason = $_POST['PurchaserExemptionReason']; //
 	$purchaserExemptionReasonValue = $_POST['PurchaserExemptionReasonValue'];
 	
 	$customerID = $_POST['customer_id'];

 	
 //	echo('params: exemptState: '.$exemptState.' singlePurchase: '.$singlePurchase.' singlePurchaseOrderNumber: '.$singlePurchaseOrderNumber.'blanketPurchase: '.$blanketPurchase.' purchaserFirstName: '.$purchaserFirstName.' purchaserLastName: '.$purchaserLastName.' purchaserAddress1: '.$purchaserAddress1.
 //	' purchaserCity: '.$purchaserCity.' purchaserState: '.$purchaserState.' purchaserZip: '.$purchaserZip.' taxType: '.$taxType.' idNumber: '.$idNumber.' stateOfIssue: '.$stateOfIssue.' countryOfIssue: '.$countryOfIssue.
 //	' purchaserBusinessType: '.$purchaserBusinessType.' purchaserBusinessTypeOtherValue: '.$purchaserBusinessTypeOtherValue.' purchaserExemptionReason: '.$purchaserExemptionReason.' purchaserExemptionReasonValue: '.$purchaserExemptionReasonValue);

	$isValid = false;
 	
 	if ( $singlePurchase == 'on' ) {
 		if ( $blanketPurchase == 'on' ) {
 			$isValid = false;
 			echo("Invalid Exemption Certificate - An Exemption Certificate cannot be a Single Use AND a Blanket (Multiple Use) Certificate.");
 		} else if (!isset($singlePurchaseOrderNumber)) {
 			$isValid = false;
 			echo("Invalid Exemption Certificate - Purchase Order Required for Single Use Exemption Certificates.");
 		} else {
 			$isValid = true;
 		}
 	} else if ($blanketPurchase == 'on') {
 		$isValid = true;
 		//echo("valid");
 	} else {
 		$isValid = false;
 		//echo("not valid - neither on");
 	}
 	
 	
 	if (isset($exemptState)&&$isValid&&isset($purchaserFirstName)&&isset($purchaserAddress1)
 		&&isset($purchaserCity)&&isset($purchaserState)&&isset($purchaserZip)&&isset($taxType)&&isset($idNumber)&&isset($stateOfIssue)&&isset($purchaserBusinessType)
 		&&isset($purchaserExemptionReason)&&isset($purchaserExemptionReasonValue)) {
 		
 		$taxcloudExemption = new taxcloudExemption();
 		$taxcloudExemption->saveCert($exemptState,$singlePurchase,$singlePurchaseOrderNumber,$purchaserFirstName,$purchaserLastName,$purchaserAddress1,$purchaserCity,$purchaserState,$purchaserZip,$taxType,$idNumber,
 			$stateOfIssue,$countryOfIssue,$purchaserBusinessType,$purchaserBusinessTypeOtherValue,$purchaserExemptionReason,$purchaserExemptionReasonValue,$customerID);
 	} else {
 		echo('Invalid Exemption Certificate.');
 		if(!isset($exemptState)){
 			echo('Missing Exemption State');
 		}
 		if(!isset($purchaserFirstName)){
 			echo('Missing Exemption Claimant Name');
 		}
 		if(!isset($purchaserAddress1)){
 			echo('Missing Exemption Claimant Street Address');
 		}
 		if(!isset($purchaserCity)){
 			echo('Missing Exemption Claimant Address City');
 		}
 		if(!isset($purchaserCity)){
 			echo('Missing Exemption Claimant Address State');
 		}
 		if(!isset($purchaserZip)){
 			echo('Missing Exemption Claimant Address Zip Code');
 		}
 		if(!isset($taxType)){
 			echo('Missing Exemption ID Type');
 		}
 		if(!isset($idNumber)){
 			echo('Missing Exemption ID Number');
 		}
 		if(!isset($purchaserBusinessType)){
 			echo('Missing Business Type');
 		}
 		if(!isset($purchaserExemptionReason)){
 			echo('Missing Exemption Reason');
 		}
 	}
 	
 	class taxcloudExemption { 
 	
 		public function saveCert($exemptState,$singlePurchase,$singlePurchaseOrderNumber,$purchaserFirstName,$purchaserLastName,$purchaserAddress1,$purchaserCity,$purchaserState,$purchaserZip,
 			$taxType,$idNumber,$stateOfIssue,$countryOfIssue,$purchaserBusinessType,$purchaserBusinessTypeOtherValue,$purchaserExemptionReason,$purchaserExemptionReasonValue,$customerID) {
 	
 			
 			$exemptionCertificateDetail = new ExemptionCertificateDetail();
 			  
 			if ( isset($singlePurchase) && $singlePurchase == 'on' ) {
 				$exemptionCertificateDetail->setSinglePurchase($singlePurchase); 
 				$exemptionCertificateDetail->setSinglePurchaseOrderNumber($singlePurchaseOrderNumber);  
 			} else {
 				$exemptionCertificateDetail->setSinglePurchase(false);
 			}
 			$exemptionCertificateDetail->setPurchaserFirstName($purchaserFirstName);
 			$exemptionCertificateDetail->setPurchaserLastName($purchaserLastName);
 			$exemptionCertificateDetail->setPurchaserAddress1($purchaserAddress1);
 			$exemptionCertificateDetail->setPurchaserCity($purchaserCity);
 			$exemptionCertificateDetail->setPurchaserState($purchaserState);
 			$exemptionCertificateDetail->setPurchaserZip($purchaserZip);
 			$createDate = date("Y-m-d");
 			$createDate = $createDate . "T00:00:00";
 			$exemptionCertificateDetail->setCreatedDate($createDate);
 			
 			
 			$taxID = new TaxID();
 			
 			$taxID->setTaxType($taxType);
 			$taxID->setIDNumber($idNumber);
 			$taxID->setStateOfIssue($stateOfIssue); //May be null
 			$exemptionCertificateDetail->setPurchaserTaxID($taxID);
 			
 			$businessType = new BusinessType($purchaserBusinessType);
 			$exemptionCertificateDetail->setPurchaserBusinessType($businessType);
 			
 			$exemptionCertificateDetail->setPurchaserBusinessTypeOtherValue($purchaserBusinessTypeOtherValue);
 			
 			$exemptionReason = new ExemptionReason($purchaserExemptionReason);
 			$exemptionCertificateDetail->setPurchaserExemptionReason($exemptionReason);
 			
 			$exemptionCertificateDetail->setPurchaserExemptionReasonValue($purchaserExemptionReasonValue);
 			
 			$exemptState = new ExemptState($exemptState,$purchaserExemptionReasonValue,$idNumber);
 			
 			$exemptionCertificateDetail->addExemptState($exemptState);
 			
 			$exemptionCertificate = new ExemptionCertificate();
 			$exemptionCertificate->setDetail($exemptionCertificateDetail);
 			
 			
 			if ( $exemptionCertificateDetail->getSinglePurchase() == true ) {
 				$_SESSION['singlePurchase'] = $exemptionCertificate;
 				$_SESSION['selectedCertID'] = null;
 				zen_redirect(zen_href_link("checkout_payment"));
 			} else {
 			
 				func_taxcloud_add_exemption_certificate($exemptionCertificate,$customerID);
 				
 				zen_redirect(zen_href_link("taxcloud_exemptions"));
 			}
 			
 		}
 		
 	
 		
	}

  

?>
