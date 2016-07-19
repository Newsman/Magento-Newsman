<?php

class Newsman_Newsletter_Model_Adminhtml_System_Config_Source_CmsBlock
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $blockCollection = Mage::getResourceModel('cms/block_collection')
                ->load();

            $this->_options[] = array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__('--Please Select--')
            );
            foreach ($blockCollection as $block) {
                $this->_options[] = array(
                    'value' => $block->getData('identifier'),
                    'label' => $block->getData('title'),
                );
            }

        }
        return $this->_options;
    }

}