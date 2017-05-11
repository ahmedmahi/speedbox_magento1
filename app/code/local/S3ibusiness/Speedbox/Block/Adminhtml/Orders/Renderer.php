<?php

class S3ibusiness_Speedbox_Block_Adminhtml_Orders_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($this->getColumn()->getIndex() == 'speedbox_selected_relais_infos') {
            $relais_infos = json_decode($value, true);
            if ($relais_infos) {
                if (isset($relais_infos['shop_name'])) {
                    return '<div>' . $relais_infos['shop_name'] . '<br />' . $relais_infos['address'] . '<br />' . $relais_infos['city'] . '</div>';

                }
            }
        }return $value;

    }

    // return '<span style="color: red;">' . $value . '</span>';

}
