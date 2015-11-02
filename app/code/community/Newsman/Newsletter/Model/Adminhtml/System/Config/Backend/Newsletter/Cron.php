<?php

/**
 * Newsletter Subscribers Synchronization Cron Backend Model
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Adminhtml_System_Config_Backend_Newsletter_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/subscribers_synchronization/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/subscribers_synchronization/run/model';

    /**
     * Cron settings after save
     *
     * @throws Mage_Core_Exception
     */
    protected function _afterSave()
    {
        $enabled = $this->getData('groups/subscribers_synchronization/fields/enabled/value');
        $time = $this->getData('groups/subscribers_synchronization/fields/time/value');
        $frequency = $this->getData('groups/subscribers_synchronization/fields/frequency/value');
        $errorEmail = $this->getData('groups/subscribers_synchronization/fields/error_email/value');

        $frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronDayOfWeek = date('N');
            $cronExprArray = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',       # Day of the Month
                '*',                                                # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',        # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string)Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('newsman_newsletter')->__('Unable to save the cron expression.'));
        }
    }
}
