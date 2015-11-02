<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Api_Subscriber extends Newsman_Newsletter_Model_Api_Newsman
{
    public function saveSubscribe(Mage_Newsletter_Model_Subscriber $subscriber, $props = null)
    {
        $result = '';
        try {
            $email = $subscriber->getSubscriberEmail();
            $firstname = null;
            if ($subscriber->hasCustomerFirstname()) {
                $firstname = $subscriber->getCustomerFirstname();
            }

            $lastname = null;
            if ($subscriber->hasCustomerLastname()) {
                $lastname = $subscriber->getCustomerLastname();
            }

            $listId = Mage::helper('newsman_newsletter')->getListId();
            $ip = Mage::helper('core/http')->getRemoteAddr();
            $result = $this->getClient()->subscriber->saveSubscribe(
                $listId, $email, $firstname, $lastname, $ip, $props
            );

            if ($result == '') {
                Mage::throwException(Mage::helper('newsman_newsletter')->__('Error on method subscriber.saveSubscribe'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }

    public function saveUnsubscribe($email)
    {
        $result = '';
        try {
            $listId = Mage::helper('newsman_newsletter')->getListId();
            $ip = Mage::helper('core/http')->getRemoteAddr();
            $result = $this->getClient()->subscriber->saveUnsubscribe(
                $listId, $email, $ip
            );

            if ($result == '') {
                Mage::throwException(Mage::helper('newsman_newsletter')->__('Error on method subscriber.saveUnsubscribe'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }

    public function initSubscribe(Mage_Newsletter_Model_Subscriber $subscriber, $options = null, $props = null)
    {
        $result = '';
        try {
            $email = $subscriber->getSubscriberEmail();
            $firstname = null;
            if ($subscriber->hasCustomerFirstname()) {
                $firstname = $subscriber->getCustomerFirstname();
            }

            $lastname = null;
            if ($subscriber->hasCustomerLastname()) {
                $lastname = $subscriber->getCustomerLastname();
            }

            $listId = Mage::helper('newsman_newsletter')->getListId();
            $ip = Mage::helper('core/http')->getRemoteAddr();
            $result = $this->getClient()->subscriber->initSubscribe(
                $listId, $email, $firstname, $lastname, $ip, $props, $options
            );

            if ($result == '') {
                Mage::throwException(Mage::helper('newsman_newsletter')->__('Error on method subscriber.initSubscribe'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }
}