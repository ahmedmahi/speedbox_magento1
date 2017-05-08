<?php
class S3ibusiness_Speedbox_Block_Adminhtml_Fraisport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("speedbox_form", array("legend" => Mage::helper("speedbox")->__("Item information")));

        $fieldset->addField('id_zone', 'select', array(
            'label'    => Mage::helper('speedbox')->__('Zone'),
            'values'   => Mage::helper('speedbox')->getZonesValues(),
            'name'     => 'id_zone',
            "class"    => "required-entry",
            "required" => true,
        ));
        $fieldset->addField('condition', 'select', array(
            'label'    => Mage::helper('speedbox')->__('Condition'),
            'values'   => S3ibusiness_Speedbox_Block_Adminhtml_Fraisport_Grid::getValueCondition(),
            'name'     => 'condition',
            "class"    => "required-entry",
            "required" => true,
        ));
        $fieldset->addField("min", "text", array(
            "label"    => Mage::helper("speedbox")->__("Min"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "min",
        ));

        $fieldset->addField("max", "text", array(
            "label"    => Mage::helper("speedbox")->__("Max"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "max",
        ));

        $fieldset->addField("cout", "text", array(
            "label"    => Mage::helper("speedbox")->__("Coût"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "cout",
        ));

        if (Mage::getSingleton("adminhtml/session")->getFraisportData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getFraisportData());
            Mage::getSingleton("adminhtml/session")->setFraisportData(null);
        } elseif (Mage::registry("fraisport_data")) {
            $form->setValues(Mage::registry("fraisport_data")->getData());
        }
        return parent::_prepareForm();
    }
}
