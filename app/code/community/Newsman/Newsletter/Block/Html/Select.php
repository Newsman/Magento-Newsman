<?php

/**
 * HTML select element block
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Block_Html_Select extends Mage_Core_Block_Html_Select
{
    /**
     * Return option HTML node
     *
     * @param array $option
     * @param boolean $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }

        return sprintf('<option value="%s"%s>%s</option>',
            $this->htmlEscape($option['value']),
            $selectedHtml,
            $this->htmlEscape($option['label']));
    }

    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }
}