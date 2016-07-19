<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Adminhtml_System_Config_Source_Ssltype
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'ssl',
                'label' => 'ssl'
            ),
            array(
                'value' => 'tls',
                'label' => 'tls'
            )
        );
    }

}