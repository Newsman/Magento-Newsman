<?php

/**
 *
 * @category    Newsman
 * @package     Newsman_Newsletter
 */
class Newsman_Newsletter_WebhookController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this;
        }

        $eventHelper = Mage::helper('newsman_newsletter/event');
        $events = $this->getRequest()->getPost('newsman_events');
        $events = json_decode($events);
        foreach ($events as $event) {
            if (!(property_exists($event, 'type') && property_exists($event, 'data'))) {
                continue;
            }

            if ($eventHelper->isUnsubscribeEvent($event->type)
                || $eventHelper->isSpamEvent($event->type)
                || $eventHelper->isBounceEvent($event->type, $event->data)
            ) {
                $eventHelper->unsubscribe($event->data->email);
            }
        }

        return $this;
    }
}
