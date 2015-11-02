<?php
/**
 * Newsman Adminhtml system config array field renderer
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Block_Adminhtml_System_Config_Form_Field_Segments extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_groupRenderer;
    /**
     * @var Newsman_Newsletter_Block_Adminhtml_Form_Field_Segment
     */
    protected $_segmentsRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'cataloginventory/adminhtml_form_field_customergroup', '',
                array('is_render_to_js_template' => true)
            );
            $this->_groupRenderer->setClass('customer_group_select');
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Retrieve segment column renderer
     *
     * @return Newsman_Newsletter_Block_Adminhtml_Form_Field_Segment
     */
    protected function _getSegmentsRenderer()
    {
        if (!$this->_segmentsRenderer) {
            $this->_segmentsRenderer = $this->getLayout()->createBlock(
                'newsman_newsletter/adminhtml_form_field_segment', '',
                array('is_render_to_js_template' => true)
            );
            $this->_segmentsRenderer->setClass('segment_select');
            $this->_segmentsRenderer->setExtraParams('style="width:200px"');
        }
        return $this->_segmentsRenderer;
    }


    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('customer_group_id', array(
            'label' => Mage::helper('newsman_newsletter')->__('Customer Group'),
            'renderer' => $this->_getGroupRenderer(),
        ));
        $this->addColumn('segment', array(
            'label' => Mage::helper('newsman_newsletter')->__('List - Segment'),
            'style' => 'width:100px',
            'renderer' => $this->_getSegmentsRenderer()
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('newsman_newsletter')->__('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getSegmentsRenderer()->calcOptionHash($row->getData('segment')),
            'selected="selected"'
        );
    }
}
