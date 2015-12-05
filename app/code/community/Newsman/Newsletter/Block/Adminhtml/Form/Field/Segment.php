<?php

/**
 * HTML select element block with segments options
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Block_Adminhtml_Form_Field_Segment extends Newsman_Newsletter_Block_Html_Select
{
    /**
     * Segments cache
     *
     * @var array
     */
    private $_segments;

    /**
     * Retrieve allowed segments
     *
     * @param null $segmentId return name by segment id
     * @return array|null
     */
    protected function _getSegments($segmentId = null)
    {
        if (is_null($this->_segments)) {
            $this->_segments = array();
            $listId =  Mage::helper('newsman_newsletter')->getListId();
            $segments = Mage::getModel('newsman_newsletter/api_segment')->getAll($listId);
            foreach ($segments as $segment) {
                $key = "{$segment['segment_id']}";
                $name = "{$segment['segment_name']}";
                $this->_segments[$key] = $name;
            }
        }
        if (!is_null($segmentId)) {
            return isset($this->_segments[$segmentId]) ? $this->_segments[$segmentId] : null;
        }
        return $this->_segments;
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
            foreach ($this->_getSegments() as $key => $segmentName) {
                $this->addOption($key, addslashes($segmentName));
            }
        }
        return parent::_toHtml();
    }
}
