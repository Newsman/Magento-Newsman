<?php
class Newsman_Newsletter_Block_Adminhtml_System_Config_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonHtml = '<a href="#" id="newsman_action_button" class="btn btn-primary">NewsMAN OAuth</a>';
        $buttonHtml .= '<style>#newsman_action_button { margin-bottom: 10px; }</style>';
        return $buttonHtml;
    }
}
?>