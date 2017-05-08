<?php
class S3ibusiness_Speedbox_Block_Adminhtml_Fraisport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("fraisport_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("speedbox")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("speedbox")->__("Item Information"),
				"title" => Mage::helper("speedbox")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("speedbox/adminhtml_fraisport_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
