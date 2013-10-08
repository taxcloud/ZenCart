<?php 

/**
 * TaxCloud v1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */

$autoLoadConfig[10][] = array('autoType'=>'class',
                              'loadFile'=>'observers/class.taxcloudObserver.php');
$autoLoadConfig[90][] = array('autoType'=>'classInstantiate',
                              'className'=>'taxcloudObserver',
                              'objectName'=>'taxcloudObserver');
                             
                              
?>