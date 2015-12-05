<?php
/**
 * HTML select element block with customer groups options
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Block_Adminhtml_Form_Field_Customergroup extends Newsman_Newsletter_Block_Html_Select
{
    /**
     * Customer groups cache
     *
     * @var array
     */
    private $_customerGroups;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addGroupAllOption = true;

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId  return name by customer group id
     * @return array|string
     */
    protected function _getCustomerGroups($groupId = null)
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = array();
            $collection = Mage::getModel('customer/group')->getCollection();
            foreach ($collection as $item) {
                /* @var $item Mage_Customer_Model_Group */
                $this->_customerGroups[$item->getId()] = $item->getCustomerGroupCode();
            }
        }
        if (!is_null($groupId)) {
            return isset($this->_customerGroups[$groupId]) ? $this->_customerGroups[$groupId] : null;
        }
        return $this->_customerGroups;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            if ($this->_addGroupAllOption) {
                $this->addOption(Mage_Customer_Model_Group::CUST_GROUP_ALL, Mage::helper('customer')->__('ALL GROUPS'));
            }
            foreach ($this->_getCustomerGroups() as $groupId => $groupLabel) {
                $this->addOption($groupId, $groupLabel);
            }
        }
        return parent::_toHtml();
    }
}
