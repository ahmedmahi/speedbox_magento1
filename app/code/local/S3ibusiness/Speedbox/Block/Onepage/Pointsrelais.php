<?php
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
