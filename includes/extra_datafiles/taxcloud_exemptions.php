<?php
/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
 
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

require_once('includes/modules/TaxCloud/func.taxcloud.php');
require_once('includes/modules/TaxCloud/classes.php');

define("taxcloud_exemptions", 'taxcloud_exemptions');
define("taxcloud_exemption_delete", "taxcloud_exemption_delete");
define("taxcloud_exemption_save", "taxcloud_exemption_save");
define("taxcloud_exemption_select", "taxcloud_exemption_select");

define(EXEMPTION_HEADING_TITLE, "Manage Exemption Certificates");
define(EXEMPTION_TABLE_HEADING_COMMENTS, "Existing Exemption Certificates");
define(TITLE_CONTINUE_CHECKOUT_PROCEDURE, "<b>Continue to Step 3</b>");
define(TEXT_CONTINUE_CHECKOUT_PROCEDURE, "to confirm your order");


define(EXEMPTION_TABLE_HEADING_NEW, "Create new Exemption Certificate");


?>