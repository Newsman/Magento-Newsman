<?php

/**
 * Used in creating options for Newsman list config value selection
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Adminhtml_System_Config_Source_List
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        try {
            $lists = Mage::getModel('newsman_newsletter/api_list')
                ->getAll();

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return array();
        }

        $options = array();
        if (is_array($lists)) {
            $options[] = array(
                'value' => '',
                'label' => Mage::helper('newsman_newsletter')->__('Select a list ...')
            );
            foreach ($lists as $list) {
                $options[] = array(
                    'value' => $list['list_id'],
                    'label' => $list['list_name']
                );
            }
        }

        return $options;
    }

}