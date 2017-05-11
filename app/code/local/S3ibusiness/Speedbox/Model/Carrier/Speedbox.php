<?php
class S3ibusiness_Speedbox_Model_Carrier_Speedbox extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'speedbox';

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        /*   $speedboxApi = Mage::helper('speedbox')->get_api();

        Mage::log($speedboxApi->villes->get());
        Mage::log($speedboxApi->points_relais->get_by_city('Tanger'));

        $coli = array(
        'date_de_commande' => date('d/m/Y', strtotime(now())),
        'numero_colis'     => Mage::helper('speedbox')->generate_token(),
        'pointrelais'      => 'PR001222',
        'nom_du_client'    => 'Ahmed' . ' ' . 'MAHI',
        'email_du_client'  => '1hmedmahi@gmail.com',
        'numero_du_client' => Mage::helper('speedbox')->formatTel('0663745054'),
        'cash_due'         => '0',
        'poids'            => '1',
        );
        Mage::log($speedboxApi->colis->create($coli));*/

        if (!$this->getConfigData('enabled') || $request->getDestCountryId() != 'MA') {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $price = $this->calculateShippingCost($request);
        $method->setPrice($price);
        $method->setCost($price);
        $result->append($method);
        return $result;
    }

    public function calculateShippingCost($package = array())
    {

        try {
            $cost = 0;
            if ($this->getConfigData('gestion_frais_api') == 2) {

                $available_table_rates = Mage::helper('speedbox')->get_available_table_rates($package);
                $table_rate            = Mage::helper('speedbox')->pick_cheapest_table_rate($available_table_rates);

                if ($table_rate != false) {
                    $cost = $table_rate['cout'];
                }

            } else {
                $cost        = $this->getConfigData('default_price_api');
                $session     = Mage::getSingleton('core/session');
                $point_relai = $session->getSpeedboxSelectedRelaisId();
                if ($point_relai) {
                    $cout_temps = Mage::helper('speedbox')->get_api()->colis->coutTemps($point_relai);
                    if (isset($cout_temps['frais'])) {
                        $cost = $this->getConfigData('supp_api') + (double) $cout_temps['frais'];
                    }

                }

            }

            return $cost;
        } catch (Exception $e) {
            $this->log->lwrite_and_lclose(($e->getMessage()));
        }

    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }
}
