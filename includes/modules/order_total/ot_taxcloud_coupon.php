<?php
/**
 * ot_taxcloud_coupon order-total module
 * Taxcloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
 require_once('ot_coupon.php');

/**
 * Order Total class  to handle discount coupons
 *
 */
class ot_taxcloud_coupon extends ot_coupon {
  /**
   * coupon title
   *
   * @var unknown_type
   */
  var $title;
  /**
   * Output used on checkout pages
   *
   * @var unknown_type
   */
  var $output;
  
  /**
   * Enter description here...
   *
   * @return ot_taxcloud_coupon
   */
  function ot_taxcloud_coupon() {
    $this->code = 'ot_taxcloud_coupon';
    $this->header = MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_HEADER;
    $this->title = MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_TITLE;
    $this->description = MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_DESCRIPTION;
    $this->user_prompt = '';
    $this->sort_order = MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_SORT_ORDER;
    $this->credit_class = true;
    $this->output = array();
    if (IS_ADMIN_FLAG === true) {
      if ($this->include_tax == 'true' && $this->calculate_tax != "None") {
        $this->title .= '<span class="alert">' . MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_INCLUDE_ERROR . '</span>';
      }
    }
  }

/**
 * The main difference between this and the original method (in ot_coupon) is that we don't discount the tax.
 *
 */
  function calculate_deductions() {
    global $db, $order, $messageStack, $currencies;
    $tax_address = zen_get_tax_locations();
    $od_amount = array();
    $orderTotalDetails = $this->get_order_total($_SESSION['cc_id']);
    $orderTotalTax = $orderTotalDetails['tax'];
    $orderTotal = $orderTotalDetails['total'];
    if ($_SESSION['cc_id']) {
      $coupon = $db->Execute("select * from " . TABLE_COUPONS . " where coupon_id = '" . (int)$_SESSION['cc_id'] . "'");
      $this->coupon_code = $coupon->fields['coupon_code'];
      
      if (($coupon->RecordCount() > 0 && $orderTotal !=0) || ($coupon->RecordCount() > 0 && $coupon->fields['coupon_type']=='S') ) {
  
          if (strval($orderTotalDetails['total']) >= $coupon->fields['coupon_minimum_order']) {
    
              if ($coupon->fields['coupon_type']=='S') {  
                  $od_amount['total'] = $_SESSION['shipping']['cost'];
                  $od_amount['type'] = 'S';
                  $od_amount['tax'] = ($this->calculate_tax == 'Standard') ? $_SESSION['shipping_tax_amount'] : 0;
                  if (DISPLAY_PRICE_WITH_TAX == 'true')
                  {
                    $od_amount['total'] += $od_amount['tax'];
                  }
                  if (isset($_SESSION['shipping_tax_description']) && $_SESSION['shipping_tax_description'] != '') {
                    $od_amount['tax_groups'][$_SESSION['shipping_tax_description']] = $od_amount['tax'];
                  }
                  return $od_amount;
              }
              if ($coupon->fields['coupon_type'] == 'P') {  
                  $od_amount['total'] = round($orderTotal*($coupon->fields['coupon_amount']/100), 2);
                  $od_amount['type'] = 'P';
              } elseif ($coupon->fields['coupon_type'] == 'F') {
                  $od_amount['total'] = round($coupon->fields['coupon_amount'] * ($orderTotal>0), 2);
                  $od_amount['type'] = 'F';
              }
              if ($od_amount['total'] > $orderTotal) $od_amount['total'] = $orderTotal;
  
          } else {
              $messageStack->add_session('redemptions', sprintf(TEXT_INVALID_REDEEM_COUPON_MINIMUM, $currencies->format($coupon->fields['coupon_minimum_order'])),'caution');
              $this->clear_posts();
              zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL',true, false));
          }
       }
    }
    return $od_amount;
  }
  
  function check() {
    global $db;
    if (!isset($this->check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_STATUS'");
      $this->check = $check_query->RecordCount();
    }

    return $this->check;
  }

  function keys() {
      return array('MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_STATUS', 'MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_SORT_ORDER');
  }
  

  function install() {
    global $db;
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('This module is installed', 'MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_STATUS', 'true', '', '6', '1','zen_cfg_select_option(array(\'true\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TAXCLOUD_COUPON_SORT_ORDER', '280', 'Sort order of display.', '6', '2', now())");
  }
 
}
