<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Model_Observer
{

    public function changeShipingAdress(Varien_Event_Observer $observer)
    {
        //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
        //$user = $observer->getEvent()->getUser();
        //$user->doSomething();
    }
    public function saveSelectedRelais($observer)
    {

        $order    = $observer->getOrder();
        $session  = Mage::getSingleton('core/session');
        $fieldVal = Mage::app()->getFrontController()->getRequest()->getParams();
        $order->setData('speedbox_selected_relais_id', $session->getSpeedboxSelectedRelaisId());
        $order->setData('speedbox_selected_relais_infos', $session->getSpeedboxSelectedRelaisInfos());
        $order->setData('speedbox_statut_colis', '-');
        $order->save();
    }
    public function addSpeedboxElements($observer)
    {

        $_block = $observer->getBlock();

        $_type = $_block->getType();

        try {
            if ($_type == 'checkout/onepage') {

                $_child = clone $_block;

                $_child->setType('speedbox/onepage');

                $_block->setChild('oldonepage', $_child);

                $_block->setTemplate('speedbox/onepage.phtml');

            } elseif ($_type == 'checkout/onepage_shipping_method') {
                $_child = clone $_block;

                $_child->setType('speedbox/onepage_shippingmethod');

                $_block->setChild('oldshippingmethod', $_child);

                $_block->setTemplate('speedbox/onepage/shippingmethod.phtml');
            } elseif ($_type == 'checkout/onepage_shipping_method_available') {
                $_child = clone $_block;

                $_child->setType('speedbox/onepage_shippingmethod');

                $_block->setChild('oldavailable', $_child);

                $_block->setTemplate('speedbox/onepage/available.phtml');
            } elseif ($_type == 'adminhtml/sales_order_view_info') {
                $_child = clone $_block;

                $_child->setType('speedbox/adminhtml_orderviewinfo');

                $_block->setChild('oldorderviewinfo', $_child);

                $_block->setTemplate('speedbox/orderviewinfo.phtml');
            }
        } catch (Exception $e) {
            Mage::log($e);
        }
    }

}
