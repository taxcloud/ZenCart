<?php
/**
 * @package admin
 * @license https://taxcloud.net/ftpsl.pdf
 * TaxCloud v1.4
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
	if (MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_STATUS=='true') {
		//This just invokes the standard coupon admin page. Just adding the menu item here because we need to disable the standard coupon module
  		$za_contents[] = array('text' => 'TaxCloud Coupon', 'link' => zen_href_link(FILENAME_COUPON_ADMIN, '', 'NONSSL'));
  	} 
?>