<?php
ini_set('display_errors', 1);

define('MAGENTO', realpath(''));
require_once(MAGENTO . '/app/Mage.php');
Mage::setIsDeveloperMode(true);
umask(0);
Mage::app('admin', 'store');

try {
	$catalogPriceRule = Mage::getModel('catalogrule/rule');
	$catalogPriceRule->applyAll();
	Mage::log('Rules Applied', null, 'rules.log', true);
} catch (Exception $e) {
	Mage::log('Rules Not Applied'. $e, null, 'rules.log', true);
}
?>