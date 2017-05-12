<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Block_Adminhtml_Zones_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("speedbox_form", array("legend" => Mage::helper("speedbox")->__("Item information")));

        $fieldset->addField("nom", "text", array(
            "label"    => Mage::helper("speedbox")->__("Nom"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "nom",
        ));

        $fieldset->addField('villes', 'multiselect', array(
            'label'    => Mage::helper('speedbox')->__('Villes'),
            'values'   => Mage::helper('speedbox')->getCitiesValues(),
            'name'     => 'villes',
            "class"    => "required-entry",
            "required" => true,
        ));

        if (Mage::getSingleton("adminhtml/session")->getZonesData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getZonesData());
            Mage::getSingleton("adminhtml/session")->setZonesData(null);
        } elseif (Mage::registry("zones_data")) {
            $form->setValues(Mage::registry("zones_data")->getData());
        }
        return parent::_prepareForm();
    }
}
