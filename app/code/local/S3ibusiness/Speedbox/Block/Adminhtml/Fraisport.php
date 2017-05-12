<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Block_Adminhtml_Fraisport extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

        $this->_controller     = "adminhtml_fraisport";
        $this->_blockGroup     = "speedbox";
        $this->_headerText     = Mage::helper("speedbox")->__("Shipping costs Manager");
        $this->_addButtonLabel = Mage::helper("speedbox")->__("Add New Item");
        parent::__construct();

    }

}
