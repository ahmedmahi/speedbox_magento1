<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Block_Adminhtml_Zones_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("zones_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("speedbox")->__("Item Information"));
    }
    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
            "label"   => Mage::helper("speedbox")->__("Item Information"),
            "title"   => Mage::helper("speedbox")->__("Item Information"),
            "content" => $this->getLayout()->createBlock("speedbox/adminhtml_zones_edit_tab_form")->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
