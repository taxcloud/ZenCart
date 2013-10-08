<?php
/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */

class Address {

 	private $Address1;

 	private $Address2;

 	private $City;

 	private $State;

 	private $Zip5;

 	private $Zip4;

 	function __construct() {
	}

 	function setAddress1($address1) {
 		$this->Address1 = $address1;
 	}

 	function getAddress1() {
 		return $this->Address1;
 	}

 	function setAddress2($address2) {
 		$this->Address2 = $address2;
 	}

 	function getAddress2() {
 		return $this->Address2;
 	}

 	function setCity($city) {
 		$this->City = $city;
 	}

 	function getCity() {
 		return $this->City;
 	}

 	function setState($state) {
 		$this->State = $state;
 	}

 	function getState() {
 		return $this->State;
 	}

 	function setZip5($zip5) {
 		$this->Zip5 = $zip5;
 	}

 	function getZip5() {
 		return $this->Zip5;
 	}

 	function setZip4($zip4) {
 		$this->Zip4 = $zip4;
 	}

 	function getZip4() {
 		return $this->Zip4;
 	}

}

class BusinessType {

	public static $AccommodationAndFoodServices = "AccommodationAndFoodServices";
	public static $Agricultural_Forestry_Fishing_Hunting = "Agricultural_Forestry_Fishing_Hunting";
	public static $Construction = "Construction";
	public static $FinanceAndInsurance = "FinanceAndInsurance";
	public static $Information_PublishingAndCommunications = "Information_PublishingAndCommunications";
	public static $Manufacturing = "Manufacturing";
	public static $Mining = "Mining";
	public static $RealEstate = "RealEstate";
	public static $RentalAndLeasing = "RentalAndLeasing";
	public static $RetailTrade = "RetailTrade";
	public static $TransportationAndWarehousing = "TransportationAndWarehousing";
	public static $Utilities = "Utilities";
	public static $WholesaleTrade = "WholesaleTrade";
	public static $BusinessServices = "BusinessServices";
	public static $ProfessionalServices = "ProfessionalServices";
	public static $EducationAndHealthCareServices = "EducationAndHealthCareServices";
	public static $NonprofitOrganization = "NonprofitOrganization";
	public static $Government = "Government";
	public static $NotABusiness = "NotABusiness";
	public static $Other = "Other";

	private $Value;

	function __construct($value) {
		$this->Value = $value;
	}

	function setValue($value) {
		$this->Value = $value;
	}

	function getValue() {
		return $this->Value;
	}
	
	function __toString() {
		return "Other";
	}
}


class CartItem {

 	private $ItemID;

 	private $Index;

 	private $TIC;

 	private $Price;

 	private $Qty;

	function __construct() {
 	}

 	function setItemID($itemID) {
 		$this->ItemID = $itemID;
 	}

 	function getItemID() {
 		return $this->ItemID;
 	}

 	function setIndex($index) {
 		$this->Index = $index;
 	}

 	function getIndex() {
 		return $this->Index;
 	}

 	function setTIC($TIC) {
 		$this->TIC = $TIC;
 	}

 	function getTIC() {
 		return $this->TIC;
 	}

 	function setPrice($price) {
 		$this->Price = $price;
 	}

 	function getPrice() {
 		return $this->Price;
 	}

 	function setQty($qty) {
 		$this->Qty = $qty;
 	}

 	function getQty() {
 		return $this->Qty;
 	}

}

class ExemptionCertificate {

	private $CertificateID;
	
	private $Detail;
	
	function __contruct() {
	}
	
	function setCertificateID($certificateID) {
		$this->CertificateID = $certificateID;
	}
	
	function getCertificateID() {
		return $this->CertificateID;
	}
	
	function setDetail($exemptionCertificateDetail) {
		$this->Detail = $exemptionCertificateDetail;
	}
	
	function getDetail() {
		return $this->Detail;
	}
}

class ExemptionCertificateDetail {

	private $ExemptStates = Array();

	private $SinglePurchase;

	private $SinglePurchaseOrderNumber;

	private $PurchaserFirstName;

	private $PurchaserLastName;

	private $PurchaserTitle;

	private $PurchaserAddress1;

	private $PurchaserAddress2;

	private $PurchaserCity;

	private $PurchaserState;

	private $PurchaserZip;

	private $PurchaserTaxID;

	private $PurchaserBusinessType;

	private $PurchaserBusinessTypeOtherValue;

	private $PurchaserExemptionReason;

	private $PurchaserExemptionReasonValue;
	
	private $CreatedDate;

	function __construct() {
	}
	
	function setExemptStates($exemptStates) {
		$this->ExemptStates = $exemptStates;
	}
	
	function getExemptStates() {
		return $this->ExemptStates;
	}

	function setSinglePurchase($singlePurchase) {
		$this->SinglePurchase = $singlePurchase;
	}

	function getSinglePurchase() {
		return $this->SinglePurchase;
	}

	function setSinglePurchaseOrderNumber($singlePurchaseOrderNumber) {
		$this->SinglePurchaseOrderNumber = $singlePurchaseOrderNumber;
	}

	function getSinglePurchaseOrderNumber() {
		return $this->SinglePurchaseOrderNumber;
	}

	function setPurchaserFirstName($purchaserFirstName) {
		$this->PurchaserFirstName = $purchaserFirstName;
	}

	function getPurchaserFirstName() {
		return $this->PurchaserFirstName;
	}

	function setPurchaserLastName($purchaserLastName) {
		$this->PurchaserLastName = $purchaserLastName;
	}

	function getPurchaserLastName() {
		return $this->PurchaserLastName;
	}

	function setPurchaserTitle($purchaserTitle) {
		$this->PurchaserTitle = $purchaserTitle;
	}

	function getPurchaserTitle() {
		return $this->PurchaserTitle;
	}

	function setPurchaserAddress1($purchaserAddress1) {
		$this->PurchaserAddress1 = $purchaserAddress1;
	}

	function getPurchaserAddress1() {
		return $this->PurchaserAddress1;
	}

	function setPurchaserCity($purchaserCity) {
		$this->PurchaserCity = $purchaserCity;
	}

	function getPurchaserCity() {
		return $this->PurchaserCity;
	}

	function setPurchaserState($purchaserState) {
		$this->PurchaserState = $purchaserState;
	}

	function getPurchaserState() {
		return $this->PurchaserState;
	}

	function setPurchaserZip($purchaserZip) {
		$this->PurchaserZip = $purchaserZip;
	}

	function getPurchaserZip() {
		return $this->PurchaserZip;
	}

	function setPurchaserTaxID($purchaserTaxID) {
		$this->PurchaserTaxID = $purchaserTaxID;
	}

	function getPurchaserTaxID() {
		return $this->PurchaserTaxID;
	}

	function setPurchaserBusinessType($purchaserBusinessType) {
		$this->PurchaserBusinessType = $purchaserBusinessType;
	}

	function getPurchaserBusinessType() {
		return $this->PurchaserBusinessType;
	}

	function setPurchaserBusinessTypeOtherValue($purchaserBusinessTypeOtherValue) {
		$this->PurchaserBusinessTypeOtherValue = $purchaserBusinessTypeOtherValue;
	}

	function getPurchaserBusinessTypeOtherValue() {
		return $this->PurchaserBusinessTypeOtherValue;
	}

	function setPurchaserExemptionReason($purchaserExemptionReason) {
		$this->PurchaserExemptionReason = $purchaserExemptionReason;
	}

	function getPurchaserExemptionReason() {
		return $this->PurchaserExemptionReason;
	}

	function setPurchaserExemptionReasonValue($purchaserExemptionReasonValue) {
		$this->PurchaserExemptionReasonValue = $purchaserExemptionReasonValue;
	}
	
	function addExemptState($exemptState) {

		$index = sizeof($this->ExemptStates);
	//	print("<br><br>addExemptState - index: ");
	//	print_r($index);
		$this->ExemptStates[$index] = $exemptState;
	}
	
	function setCreatedDate($createdDate) {
		$this->CreatedDate = $createdDate;
	}
	
	function getCreatedDate() {
		return $this->CreatedDate;
	}
	

}

class ExemptionReason {

	public static $FederalGovernmentDepartment = "FederalGovernmentDepartment";
	public static $StateOrLocalGovernmentName = "StateOrLocalGovernmentName";
	public static $TribalGovernmentName = "TribalGovernmentName";
	public static $ForeignDiplomat = "ForeignDiplomat";
	public static $CharitableOrganization = "CharitableOrganization";
	public static $ReligiousOrEducationalOrganization = "ReligiousOrEducationalOrganization";
	public static $Resale = "Resale";
	public static $AgriculturalProduction = "AgriculturalProduction";
	public static $IndustrialProductionOrManufacturing = "IndustrialProductionOrManufacturing";
	public static $DirectPayPermit = "DirectPayPermit";
	public static $DirectMail = "DirectMail";
	public static $Other = "Other";

	function __construct($value) {
		$this->Value = $value;
	}

	function setValue($value) {
		$this->Value = $value;
	}

	function getValue() {
		return $this->Value;
	}
	
	function __toString() {
		return "Other";
	}
}

class ExemptState {

	private $StateAbbr;

	private $ReasonForExemption;

	private $IdentificationNumber;

	function __construct($stateAbbr, $reasonForExemption, $identificationNumber) {
		$this->StateAbbr = $stateAbbr;
		$this->ReasonForExemption = $reasonForExemption;
		$this->IdentificationNumber = $identificationNumber;
	}

	function setStateAbbr($stateAbbr) {
		$this->StateAbbr = $stateAbbr;
	}

	function getStateAbbr() {
		return $this->StateAbbr;
	}

	function setReasonForExemption($reasonForExemption) {
		$this->ReasonForExemption = $reasonForExemption;
	}

	function getReasonForExemption() {
		return $this->ReasonForExemption;
	}

	function setIdentificationNumber($identificationNumber) {
		$this->IdentificationNumber = $identificationNumber;
	}

	function getIdentificationNumber() {
		return $this->IdentificationNumber;
	}

}

class State {

	public static $AL = "AL";
	public static $AK = "AK";
	public static $AZ = "AZ";
	public static $AR = "AR";
	public static $CA = "CA";
	public static $CO = "CO";
	public static $CT = "CT";
	public static $DE = "DE";
	public static $DC = "DC";
	public static $FL = "FL";
	public static $GA = "GA";
	public static $HI = "HI";
	public static $ID = "ID";
	public static $IL = "IL";
	public static $IN = "IN";
	public static $IA = "IA";
	public static $KS = "KS";
	public static $KY = "KY";
	public static $LA = "LA";
	public static $ME = "ME";
	public static $MD = "MD";
	public static $MA = "MA";
	public static $MI = "MI";
	public static $MN = "MN";
	public static $MS = "MS";
	public static $MO = "MO";
	public static $MT = "MT";
	public static $NE = "NE";
	public static $NV = "NV";
	public static $NH = "NH";
	public static $NJ = "NJ";
	public static $NM = "NM";
	public static $NY = "NY";
	public static $NC = "NC";
	public static $ND = "ND";
	public static $OH = "OH";
	public static $OK = "OK";
	public static $OR = "OR";
	public static $PA = "PA";
	public static $RI = "RI";
	public static $SC = "SC";
	public static $SD = "SD";
	public static $TN = "TN";
	public static $TX = "TX";
	public static $UT = "UT";
	public static $VT = "VT";
	public static $VA = "VA";
	public static $WA = "WA";
	public static $WV = "WV";
	public static $WI = "WI";
	public static $WY = "WY";

	private $Value;

	function __construct($value) {
		$this->Value = $value;
	}

	function setValue($value) {
		$this->Value = $value;
	}

	function getValue() {
		return $this->Value;
	}

}

class TaxID {

	private $TaxType;

	private $IDNumber;

	private $StateOfIssue;

	function __construct($taxType = null, $idNumber = null, $stateOfIssue = null) {
		$this->TaxType = $taxType;
		$this->IDNumber = $idNumber;
		$this->StateOfIssue = $stateOfIssue;
	}

	function setTaxType($taxIDType) {
		$this->TaxType = $taxIDType;
	}

	function getTaxType() {
		return $this->TaxType;
	}

	function setIDNumber($idNumber) {
		$this->IDNumber = $idNumber;
	}

	function getIDNumber() {
		return $this->IDNumber;
	}

	function setStateOfIssue($stateOfIssue) {
		$this->StateOfIssue = $stateOfIssue;
	}

	function getStateOfIssue() {
		return $this->StateOfIssue;
	}
}

class TIC {

	private $TICID;

	private $Description;

	function __construct($ticID, $description) {
		$this->TICID = $ticID;
		$this->Description = $description;
	}

	function setTICID($ticID) {
		$this->TICID = $ticID;
	}

	function getTICID() {
		return $this->TICID;
	}

	function setDescription() {
		$this->Description = description;
	}

	function getDescription() {
		return $this->Description;
	}

}

class TICGroup {

	private $GroupID;
	private $Description;

	function __construct($groupID, $description) {
		$this->GroupID = $groupID;
		$this->Description = $description;
	}

	function setGroupID($groupID) {
		$this->GroupID = $groupID;
	}

	function getGroupID() {
		return $this->GroupID;
	}

	function setDescription($description) {
		$this->Description = $description;
	}

	function getDescription() {
		return $this->Description;
	}
}

class VerifiedAddress {

	private $ErrNumber;
	private $ErrDescription;

	function __construct() {
	}

	function setErrNumber($errNumber) {
		$this->ErrNumber = $errNumber;
	}

	function getErrNumber() {
		return $this->ErrNumber;
	}

	function setErrDescription($errDescription) {
		$this->ErrDescription = $errDescription;
	}

	function getErrDescription() {
		return $this->ErrDescrption;
	}

}

/**
 * This class is used for the Express Checkout process to correlate 
 * PayPal tokens to customers.
 */
class TokenHash {

	private function __construct() {
		//load serialized data, if any
		 include("classarm *.log.inc");
		
		 $s = file_get_contents('hashstore');
		 if ( isset($s) ) {
 		 	self::$hash = unserialize($s);
 		 } 
 		 $this->removeOldHashItems();
	}

	static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new TokenHash();
		}
		return self::$instance;
	}
	
	function AddToken($customerID,$token, $cartID) {
		$now = time();
		self::$hash[] = Array($token, $customerID, $cartID, $now);
		
		//serialize data
		$s = serialize(self::$hash);
		file_put_contents('store', $s);
	}
	
	function removeToken($token) {

		foreach (self::$hash as $id => $storedValues ) { 
			$storedToken = $storedValues[0]; 
			if ( $token == $storedToken ) {
				unset(self::$hash[$id]);
				$s = serialize(self::$hash);
				file_put_contents('store', $s);

			} 
		}
	}
	
	function getCustomerID($token) {
		foreach (self::$hash as $id => $storedValues ) { 
			$storedToken = $storedValues[0]; 
			if ( $token == $storedToken ) {
				return $storedValues[1];
			}
		}
		return 0;
	}
	
	function getCartID($token) {
		foreach (self::$hash as $id => $storedValues ) { 
			$storedToken = $storedValues[0]; 
			if ( $token == $storedToken ) {
				return $storedValues[2];
			} 
		}
		return 0;
	}
	
	function removeOldHashItems() {
		$aWeekAgo = time() - (7 * 24 * 60 * 60); 
		foreach (self::$hash as $item) {
			$hashTime = $item[3]; 
			 if ($hashTime < $aWeekAgo) {
			 	$this->removeToken($item[0]);
			 } 

		}
		
	}
	
	static private $instance = null;
	static private $hash = Array();
}

 ?>