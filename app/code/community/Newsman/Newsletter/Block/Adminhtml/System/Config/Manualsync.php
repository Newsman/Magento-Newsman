<?php
/**
 * Adminhtml Manual Subscribers Sync block
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Block_Adminhtml_System_Config_Manualsync extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Set template to itself
     *
     * @return Newsman_Newsletter_Block_Adminhtml_System_Config_Manualsync
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('newsman/system/config/manualsync.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = isset($originalData['button_label']) ? $originalData['button_label'] : 'Manual Sync';
        $this->addData(array(
            'button_label' => Mage::helper('customer')->__($buttonLabel),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/newsletter_system_config_synchronization/manualsync')
        ));

        return $this->_toHtml();
    }
}
