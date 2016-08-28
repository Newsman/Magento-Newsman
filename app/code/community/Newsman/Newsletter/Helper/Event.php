<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Helper_Event extends Mage_Core_Helper_Abstract
{
    const NEWSMAN_EVENT_TYPE_UNSUBSCRIBE = 'unsub';
    const NEWSMAN_EVENT_TYPE_SPAM = 'spam';
    const NEWSMAN_EVENT_TYPE_BOUNCE = 'bounce';

    public function isUnsubscribeEvent($type)
    {
        return ($type == self::NEWSMAN_EVENT_TYPE_UNSUBSCRIBE);
    }

    public function isSpamEvent($type)
    {
        return ($type == self::NEWSMAN_EVENT_TYPE_SPAM);
    }

    public function isBounceEvent($type, $data)
    {
        return ($type == self::NEWSMAN_EVENT_TYPE_BOUNCE && property_exists($data, 'hard_bounce') && $data->hard_bounce == true);
    }

    public function unsubscribe($email)
    {
        try {
            /** @var Newsman_Newsletter_Model_Subscriber $subscriber */
            $subscriber = Mage::getModel('newsletter/subscriber')->load($email, 'subscriber_email');
            if ($subscriber->getId()) {
                $subscriber->unsubscribe(false);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}