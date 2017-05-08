<?php

class S3ibusiness_Speedbox_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'speedbox';
        $this->_controller = 'adminhtml_orders';
        $this->_headerText = Mage::helper('speedbox')->__('Orders management');
        parent::__construct();
        $this->_removeButton('add');
    }
}
