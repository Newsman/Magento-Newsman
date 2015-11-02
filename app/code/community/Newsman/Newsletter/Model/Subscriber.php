<?php

/**
 * @category  Newsman
 * @package   Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function subscribe($email)
    {
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;
        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        $isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true) {
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                } else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($isSubscribeOwnEmail) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }
            if (Mage::helper('newsman_newsletter')->useAutoSync()) {
                $sendConfirmation = ($isConfirmNeed === true && $isOwnSubscribes === false);
                Mage::dispatchEvent('newsletter_subscriber_subscribe', array('subscriber' => $this, 'send_confirmation' => $sendConfirmation));
            }
            return $this->getStatus();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Unsubscribes loaded subscription
     *
     * @return Newsman_Newsletter_Model_Subscriber
     */
    public function unsubscribe()
    {
        parent::unsubscribe();
        if (Mage::helper('newsman_newsletter')->useAutoSync()) {
            Mage::dispatchEvent('newsletter_subscriber_unsubscribe', array('subscriber' => $this));
        }

        return $this;
    }

    /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Newsman_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
        $this->loadByCustomer($customer);

        if ($customer->getImportMode()) {
            $this->setImportMode(true);
        }

        if (!$customer->getIsSubscribed() && !$this->getId()) {
            // If subscription flag not set or customer is not a subscriber
            // and no subscribe below
            return $this;
        }

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        /*
         * Logical mismatch between customer registration confirmation code and customer password confirmation
         */
        $confirmation = null;
        if ($customer->isConfirmationRequired() && ($customer->getConfirmation() != $customer->getPassword())) {
            $confirmation = $customer->getConfirmation();
        }

        $sendInformationEmail = false;
        if ($customer->hasIsSubscribed()) {
            $status = $customer->getIsSubscribed()
                ? (!is_null($confirmation) ? self::STATUS_UNCONFIRMED : self::STATUS_SUBSCRIBED)
                : self::STATUS_UNSUBSCRIBED;
            /**
             * If subscription status has been changed then send email to the customer
             */
            if ($status != self::STATUS_UNCONFIRMED && $status != $this->getStatus()) {
                $sendInformationEmail = true;
            }
        } elseif (($this->getStatus() == self::STATUS_UNCONFIRMED) && (is_null($confirmation))) {
            $status = self::STATUS_SUBSCRIBED;
            $sendInformationEmail = true;
        } else {
            $status = ($this->getStatus() == self::STATUS_NOT_ACTIVE ? self::STATUS_UNSUBSCRIBED : $this->getStatus());
        }

        if ($status != $this->getStatus()) {
            $this->setIsStatusChanged(true);
        }

        $this->setStatus($status);

        if (!$this->getId()) {
            $storeId = $customer->getStoreId();
            if ($customer->getStoreId() == 0) {
                $storeId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            }
            $this->setStoreId($storeId)
                ->setCustomerId($customer->getId())
                ->setEmail($customer->getEmail());
        } else {
            $this->setStoreId($customer->getStoreId())
                ->setEmail($customer->getEmail());
        }

        $this->save();
        $sendSubscription = $customer->getData('sendSubscription') || $sendInformationEmail;
        if (is_null($sendSubscription) xor $sendSubscription) {
            if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();

                if (Mage::helper('newsman_newsletter')->useAutoSync()) {
                    Mage::dispatchEvent('newsletter_subscriber_unsubscribe', array('subscriber' => $this));
                }

            } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                $this->sendConfirmationSuccessEmail();

                if (Mage::helper('newsman_newsletter')->useAutoSync()) {
                    Mage::dispatchEvent('newsletter_subscriber_subscribe', array('subscriber' => $this, 'customer' => $customer));
                }
            }
        }

        return $this;
    }

    /**
     * Delete object from database
     *
     * @return Newsman_Newsletter_Model_Subscriber
     */
    public function delete()
    {
        parent::delete();
        if (Mage::helper('newsman_newsletter')->useAutoSync()) {
            Mage::dispatchEvent('newsletter_subscriber_unsubscribe', array('subscriber' => $this));
        }

        return $this;
    }
}