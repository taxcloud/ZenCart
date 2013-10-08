<?php
/**
 * TaxCloud v1.5
 *
 * ot_taxcloud order-total module
 * @license https://taxcloud.net/ftpsl.pdf
 *
 * TaxCloud Tax Exemption module. Allows the user to enter and save tax exemption certificates in the TaxCloud system and apply them to the order.
 *
 * This class primarily just configures the module in the admin console. The logic for the tax calculation is in the TaxCloud module.
 */
 
require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'TaxCloud/func.taxcloud.php');
 
class ot_taxcloud_exemption {

	var $title;
  	var $output;
  

  	function ot_taxcloud_exemption() {                                                   
    		global $currencies;
    		$this->code = 'ot_taxcloud_exemption';
    		$this->title = MODULE_ORDER_TOTAL_TAXCLOUD_EXEMPTION_TITLE;
    		$this->description = MODULE_ORDER_TOTAL_TAXCLOUD_EXEMPTION_DESCRIPTION;
    		$this->sort_order = MODULE_ORDER_TOTAL_TAXCLOUD_EXEMPTION_SORT_ORDER;
    		$this->show_redeem_box = MODULE_ORDER_TOTAL_TAXCLOUD_REDEEM_BOX;   
    		$this->credit_class = true;  
    		$this->output = array();

  	}

  	function process() {
  	}

  	function pre_confirmation_check($order_total) {   
    		return 0;
  	} 

  	function credit_selection() {
    
    		$selectedCert = $_SESSION['selectedCert'];
    		$selectedCertID = $_SESSION['selectedCertID'];		
    		$countryCode = $_SESSION['customer_country_id'];	
    		
    		$message = '';
    		
    		if ( isset($selectedCert) || isset($selectedCertID) ) {
    			$message = '<font color=\'green\'>Your Exemption Certificate has been applied to your order</font><br>';
    		}
    		
    		if (func_taxcloud_is_enabled($countryCode)) { 
    			$newString = $message.'<a href="./index.php?main_page=taxcloud_exemptions">'.MODULE_ORDER_TOTAL_TAXCLOUD_EXEMPTION_REDEEM_INSTRUCTIONS.'</a>';
    		} else {
    			$newString = "";
    		}
    
    		$selection = array('id' => $this->code,
    			'module' => $this->title,
    			'redeem_instructions' => $newString,
    			'fields' => array(array('title' => '',
    			'field' => '',
    			'form' => '',
    			'tag' => 'disc-'.$this->code)));
    		return $selection;
  	}
  	
  	function update_credit_account() {
  		//required method
  	}
  	
  	function apply_credit() {
  		//required method
  	}
  
  
  	function collect_posts() {    
 		//required method
  	}
 
  	function get_order_total() { 
    		$order_total = $order->info['total'];
    		return $order_total;
  	} 
  
  	function check() {
    		global $db;
    		if (!isset($this->check)) {
      			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_EXEMPTION_STATUS'");
      			$this->check = $check_query->RecordCount();
    		}

    		return $this->check;
  	}

  	function keys() {
		return array('MODULE_ORDER_TOTAL_EXEMPTION_STATUS', 'MODULE_ORDER_TOTAL_TAXCLOUD_EXEMPTION_SORT_ORDER');
  	}

  	function install() {
    		global $db;
      		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('This module is installed', 'MODULE_ORDER_TOTAL_EXEMPTION_STATUS', 'true', '', '6', '1','zen_cfg_select_option(array(\'true\'), ', now())");
      		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TAXCLOUD_EXEMPTION_SORT_ORDER', '150', 'Sort order of display.', '6', '2', now())");
 	}

  	function remove() {
    		global $db;
    		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')"); 
  	}
}