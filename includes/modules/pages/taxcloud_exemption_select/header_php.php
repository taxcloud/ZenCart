<?php
/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
  require(DIR_WS_MODULES . 'require_languages.php');
  
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
 
 	$certID = $_GET['select'];
	
	$_SESSION['selectedCertID'] = $certID;
	$_SESSION['singlePurchase'] = null;
  	zen_redirect(zen_href_link("checkout_payment"));

?>