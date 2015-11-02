<?php

/**
 * @category  Newsman
 * @package   Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Observer
{
    public function scheduleSync()
    {
        try {
            Mage::helper('newsman_newsletter/task')->insertTasks();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function subscribe($observer)
    {
        try {
            /** @var $subscriber Newsman_Newsletter_Model_Api_Subscriber */
            $subscriber = $observer->getSubscriber();
            $sendConfirmation = $observer->getSendConfirmation();
            if (!$sendConfirmation) {
                $subscriberId = Mage::getModel('newsman_newsletter/api_subscriber')->saveSubscribe($subscriber);
                $customer = $observer->getCustomer();
                if ($customer && $subscriberId) {
                    $this->addSubscriberToSegment($customer, $subscriberId);
                }
            } else {
                Mage::getModel('newsman_newsletter/api_subscriber')->initSubscribe($subscriber);
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @param $subscriberId
     * @return $this
     */
    public function addSubscriberToSegment(Mage_Customer_Model_Customer $customer, $subscriberId)
    {
        $segments = Mage::helper('newsman_newsletter')->getSegments();
        if (!$segments) {
            return $this;
        }

        foreach ($segments as $segment) {
            if ($segment['customer_group_id'] == $customer->getGroupId()) {
                Mage::getModel('newsman_newsletter/api_segment')->addSubscriber($segment['segment'], $subscriberId);
            }
        }
        return $this;
    }

    public function unsubscribe($observer)
    {
        try {
            $email = $observer->getSubscriber()->getSubscriberEmail();
            Mage::getModel('newsman_newsletter/api_subscriber')->saveUnsubscribe($email);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function delete($observer)
    {
        try {
            $email = $observer->getSubscriber()->getSubscriberEmail();
            Mage::getModel('newsman_newsletter/api_subscriber')->saveUnsubscribe($email);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}