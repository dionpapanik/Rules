<?php

final class Apply_Rules
{

    protected $_rootPath;
    protected $_appCode = 'admin';
    protected $_appType = 'store';

    private function _getRootPath()
    {
        if (is_null($this->_rootPath)) {
            $this->_rootPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
        }
        return $this->_rootPath;
    }

    public function run()
    {
        $starttime = (float)array_sum(explode(' ', microtime()));

        require_once $this->_getRootPath() . 'httpdocs' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
        Mage::app($this->_appCode, $this->_appType);

        try {
            $catalogPriceRule = Mage::getModel('catalogrule/rule');
            $catalogPriceRule->applyAll();
            Mage::log('Rules Applied', null, 'rules.log', true);
        } catch
        (Exception $e) {
            Mage::log('Rules Not Applied' . $e, null, 'rules.log', true);
            $this->_sendEmail($e);
        }

        $endtime = (float)array_sum(explode(' ', microtime()));
        echo "time: " . sprintf("%.3f", ($endtime - $starttime)) . " seconds";
        echo '<br> Memory Usage: ' . round(memory_get_usage(true) / 1048576, 2) . " MB";
    }

    private function _sendEmail($problem)
    {
        $fromEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $fromName = Mage::getStoreConfig('trans_email/ident_general/name');
        $toEmail = Mage::getStoreConfig('trans_email/ident_custom2/email');
        $subject = "Problem in rules";

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($problem);
        $mail->setFrom($fromEmail, $fromName);
        $mail->addTo($toEmail);
        $mail->setSubject($subject);
        $mail->send();
    }
}

$rules = new Apply_Rules();
$rules->run();

?>