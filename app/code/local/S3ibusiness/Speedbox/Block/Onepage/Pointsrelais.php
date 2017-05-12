<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Block_Onepage_Pointsrelais extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function getPointsRelais($customer_data)
    {

        return Mage::helper('speedbox')->get_speedbox_points_relais($customer_data);

    }
    public function getInfosForSearch()
    {
        $customer_data   = array();
        $shippingAddress = $this->getQuote()->getShippingAddress();

        $customer_data['country']  = $shippingAddress->getCountryId();
        $customer_data['city']     = $shippingAddress->getCity();
        $customer_data['postcode'] = $shippingAddress->getPostcode();
        return $customer_data;

    }
}
