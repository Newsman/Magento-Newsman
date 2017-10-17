<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Adminhtml_System_Config_Source_Importfilter
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
				'value' => '1',
				'label' => 'Import Newsletter Subscribers'
			),
			array(
				'value' => '2',
				'label' => 'Import customers who ordered and newsletter subscribers'
			)
		);
	}

}