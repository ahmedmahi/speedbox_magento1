<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Model_Config_Source_Costs
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(

            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Use of Speedbox rates (via API) + supplement')),
            array('value' => 2, 'label' => Mage::helper('adminhtml')->__('Specify shipping costs')),
        );
    }

}
