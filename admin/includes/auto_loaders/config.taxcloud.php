<?php

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
} 

// Auto-load the TaxCloud configuration file. 
// This file will install the TaxCloud admin menu item
$autoLoadConfig[199][] = array(
    'autoType' => 'init_script',
    'loadFile' => 'init_taxcloud_config.php'
    );  
?>