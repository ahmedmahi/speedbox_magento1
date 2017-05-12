<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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
