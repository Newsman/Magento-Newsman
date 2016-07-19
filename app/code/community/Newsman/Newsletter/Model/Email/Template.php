<?php

/**
 * Template model
 *
 * @category    Newsman
 * @package     Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Send mail to recipient
     *
     * @param array|string $email - E-mail(s)
     * @param null $name - receiver name(s)
     * @param array $variables - template variables
     * @return boolean
     * @throws Exception
     * @throws Zend_Mail_Exception
     */
    public function send($email, $name = null, array $variables = array())
    {
        /** @var Newsman_Newsletter_Helper_Smtp $_helper */
        $_helper = Mage::helper('newsman_newsletter/smtp');

        if (!$_helper->isEnabled()) {
            return parent::send($email, $name, $variables);
        }

        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);
        $subject = $this->getProcessedTemplateSubject($variables);


        if ($this->hasQueue() && $this->getQueue() instanceof Mage_Core_Model_Email_Queue) {
            /** @var $emailQueue Mage_Core_Model_Email_Queue */
            $emailQueue = $this->getQueue();
            $emailQueue->setMessageBody($text);
            $emailQueue->setMessageParameters(array(
                'subject' => $subject,
                'is_plain' => $this->isPlain(),
                'from_email' => $this->getSenderEmail(),
                'from_name' => $this->getSenderName(),
                'reply_to' => $this->getMail()->getReplyTo(),
                'return_to' => $this->getMail()->getReturnPath(),
            ))
                ->addRecipients($emails, $names, Mage_Core_Model_Email_Queue::EMAIL_TYPE_TO)
                ->addRecipients($this->_bccEmails, array(), Mage_Core_Model_Email_Queue::EMAIL_TYPE_BCC);
            $emailQueue->addMessageToQueue();

            return true;
        }

        ini_set('SMTP', $_helper->getHost());
        ini_set('smtp_port', $_helper->getPort());

        $mail = $this->getMail();

        $config = array(
            'ssl' => $_helper->getSslType(),
            'port' => $_helper->getPort(),
            'auth' => $_helper->getAuth(),
            'username' => $_helper->getUsername(),
            'password' => $_helper->getPassword()
        );

        $mailTransport = new Zend_Mail_Transport_Smtp($_helper->getHost(), $config);
        Zend_Mail::setDefaultTransport($mailTransport);

        foreach ($emails as $key => $email) {
            $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
        }

        if ($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject('=?utf-8?B?' . base64_encode($subject) . '?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());

        try {
            $mail->send();
            $this->_mail = null;
        } catch (Exception $e) {
            $this->_mail = null;
            Mage::logException($e);
            return false;
        }

        return true;
    }
}
