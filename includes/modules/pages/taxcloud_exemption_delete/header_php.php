<?php

/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
 
  require(DIR_WS_MODULES . 'require_languages.php');
  require_once('includes/modules/TaxCloud/func.taxcloud.php');
  
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
 
  
  if (isset($_GET['delete'])) {
  
   	$certID = $_GET['delete'];
  	func_taxcloud_delete_exemption_certificate($certID);
  }
  
  $selectedCert = $_SESSION['selectedCertID']; 
  if ( $selectedCert == $certID ) {
  	$_SESSION['selectedCertID'] = null;
  }
  
  zen_redirect(zen_href_link("taxcloud_exemptions"));
  

?>