<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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
        }
        if ($this->getColumn()->getIndex() == 'speedbox_statut_colis') {

            if ($value == '-') {
                return '<span style="color:#d51f4f;font-weight: bold;">' . Mage::helper('speedbox')->__('Non traité') . '</span>';
            }
        }
        return $value;

    }

}
