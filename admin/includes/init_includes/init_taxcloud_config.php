<?php

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// Install the TaxCloud admin menu item under "Locations/taxes"
if (function_exists('zen_register_admin_page')) {
    if (!zen_page_key_exists('taxcloud')) {
        // Add the link to the TaxCloud Tax Lookup Configuration
        zen_register_admin_page('taxCloud', 'BOX_TAXES_TAXCLOUD',
            'FILENAME_TAXCLOUD', '', 'taxes', 'Y', 60);
    }
}

// Now that the menu item has been created/registered, can stop the wasteful process of having
// this script run again by removing it from the auto-loader array
@unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.taxcloud.php');  


?>