<?php


class S3ibusiness_Speedbox_Block_Adminhtml_Fraisport extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_fraisport";
	$this->_blockGroup = "speedbox";
	$this->_headerText = Mage::helper("speedbox")->__("Shipping costs Manager");
	$this->_addButtonLabel = Mage::helper("speedbox")->__("Add New Item");
	parent::__construct();
	
	}

}