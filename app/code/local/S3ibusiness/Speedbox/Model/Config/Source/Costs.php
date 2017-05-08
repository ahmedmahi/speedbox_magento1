<?php
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
