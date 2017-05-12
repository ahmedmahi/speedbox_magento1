<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function get_api()
    {

        $options = array(
            'validate_url' => false,
            'timeout'      => 30,
            'ssl_verify'   => false,
        );

        $arg = array(
            'store_url'       => Mage::getStoreConfig('carriers/speedbox/speedbox_relais_url'),
            'consumer_key'    => Mage::getStoreConfig('carriers/speedbox/api_token'),
            'consumer_secret' => Mage::getStoreConfig('carriers/speedbox/speedbox_relais_security_key'),
            'options'         => $options,
        );

        return Mage::getModel('speedbox/api', $arg);

    }

    public function get_speedbox_points_relais($customer_data)
    {

        $speedbox_relais_points = array();
        $ville_proche           = array();
        $country                = $this->stripAccents($customer_data['country']);
        $city                   = $this->stripAccents($customer_data['city']);
        $zipcode                = $customer_data['postcode'];
        $points_relais          = $this->get_api()->points_relais->get_by_city($city);
        $city_data              = $this->get_city_from_data($city/*, $zipcode*/);
        if ($country != 'MA') {
            $speedbox_relais_points = array('error' => $this->__('This shipping method is only available in Morocco .'));
        } else if (is_array($points_relais) && !empty($points_relais) && !isset($points_relais['error'])) {
            $speedbox_relais_points           = $points_relais;
            $ville_proche['min_city']['city'] = $city;
            $ville_proche['distance']         = 1000;

        } else if (!empty($city_data)) {
            // desactivation du recherche basé sur les codes postales
            $cities                 = $this->get_api()->villes->get();
            $ville_proche           = $this->min_circle_distance($city_data, $cities);
            $speedbox_relais_points = $this->get_api()->points_relais->get_by_city($ville_proche['min_city']['city']);

        } else {
            $speedbox_relais_points = array('error' => $this->__('There are no Pickup points near this address, please modify it.'));
        }

        try {

            if (!isset($speedbox_relais_points['error'])) {
                foreach ($speedbox_relais_points as $pr => $item) {
                    $point = array();
                    $item  = (array) $item;

                    $point['relay_id']  = $item['id'];
                    $point['shop_name'] = $this->stripAccents($item['nom']);
                    $point['address']   = $this->stripAccents($item['adresse']);

                    $point['city']     = $this->stripAccents($ville_proche['min_city']['city']);
                    $point['distance'] = number_format($ville_proche['distance'] / 1000, 2);
                    if ($point['distance'] == 0) {
                        $point['distance'] = 1;
                    }

                    $point['coord_lat']  = (float) strtr($item['gps_lat'], ',', '.');
                    $point['coord_long'] = (float) strtr($item['gps_lng'], ',', '.');
                    $point['images']     = str_replace('localhost:8000', 'api.speedbox.ma', $item['images']);
                    $point['postcode']   = $this->get_postalcode($point['city']);

                    if (isset($city_data['region'])) {

                        $point['state'] = $city_data['region'];
                    } else {
                        $p_city         = $this->get_city_from_data($point['city']);
                        $point['state'] = $p_city['region'];
                    }
                    $point['state'] = $this->speedbox_get_state($point['state']);

                    $days = array(0 => 'monday', 1 => 'tuesday', 2 => 'wednesday', 3 => 'thursday', 4 => 'friday', 5 => 'saturday', 6 => 'sunday');
                    if (count($item['horaires']) > 0) {
                        foreach ($item['horaires'] as $k => $oh_item) {
                            $point[$days[$k]][] = gmdate("H:i", $oh_item[0]['ouverture']) . ' - ' . gmdate("H:i", $oh_item[0]['fermeture']);
                        }
                    }

                    if (empty($point['monday'])) {$h1 = $this->__('Closed');} else {
                        if (empty($point['monday'][1])) {$h1 = $point['monday'][0];} else { $h1 = $point['monday'][0] . ' & ' . $point['monday'][1];}}

                    if (empty($point['tuesday'])) {$h2 = $this->__('Closed');} else {
                        if (empty($point['tuesday'][1])) {$h2 = $point['tuesday'][0];} else { $h2 = $point['tuesday'][0] . ' & ' . $point['tuesday'][1];}}

                    if (empty($point['wednesday'])) {$h3 = $this->__('Closed');} else {
                        if (empty($point['wednesday'][1])) {$h3 = $point['wednesday'][0];} else { $h3 = $point['wednesday'][0] . ' & ' . $point['wednesday'][1];}}

                    if (empty($point['thursday'])) {$h4 = $this->__('Closed');} else {
                        if (empty($point['thursday'][1])) {$h4 = $point['thursday'][0];} else { $h4 = $point['thursday'][0] . ' & ' . $point['thursday'][1];}}

                    if (empty($point['friday'])) {$h5 = $this->__('Closed');} else {
                        if (empty($point['friday'][1])) {$h5 = $point['friday'][0];} else { $h5 = $point['friday'][0] . ' & ' . $point['friday'][1];}}

                    if (empty($point['saturday'])) {$h6 = $this->__('Closed');} else {
                        if (empty($point['saturday'][1])) {$h6 = $point['saturday'][0];} else { $h6 = $point['saturday'][0] . ' & ' . $point['saturday'][1];}}

                    if (empty($point['sunday'])) {$h7 = $this->__('Closed');} else {
                        if (empty($point['sunday'][1])) {$h7 = $point['sunday'][0];} else { $h7 = $point['sunday'][0] . ' & ' . $point['sunday'][1];}}

                    $point['opening_hours'] = array('monday' => $h1, 'tuesday' => $h2, 'wednesday' => $h3, 'thursday' => $h4, 'friday' => $h5, 'saturday' => $h6, 'sunday' => $h7);
                    unset($speedbox_relais_points[$pr]);
                    $speedbox_relais_points[] = $point;

                }
            }

        } catch (Exception $e) {
            $speedbox_relais_points['error'] = $this->__('Speedbox Relais is not available at the moment, please try again shortly.');
        }
        return $speedbox_relais_points;

    }

    public function getZonesOptions()
    {
        $zones   = Mage::getModel('speedbox/zones')->getCollection();
        $options = array();
        foreach ($zones as $zone) {
            $id           = $zone->getId();
            $options[$id] = $zone->getNom();
        }
        return $options;
    }
    public function getZonesValues()
    {
        $values = array();
        foreach ($this->getZonesOptions() as $value => $label) {
            $values[] = array('value' => $value, 'label' => $label);
        }
        return $values;

    }
    public function getCitiesOptions()
    {
        $options    = array();
        $all_cities = $this->get_all_cities_from_data();
        foreach ($all_cities['cities'] as $key => $val) {

            $options[$key] = $val['city'];

        }
        return $options;
    }
    public function getCitiesValues()
    {
        $values = array();
        foreach ($this->getCitiesOptions() as $value => $label) {
            $values[] = array('value' => $value, 'label' => $label);
        }
        return $values;

    }
    public function get_available_table_rates($request)
    {
        $available_zones       = $this->get_available_zones($request);
        $available_table_rates = array();
        $table_rates           = Mage::getModel('speedbox/fraisport')->getCollection();
        /*
        `id` int(11) unsigned NOT NULL auto_increment,
        `id_zone`  int(11) NOT NULL default '0' ,
        `condition` varchar(255) NOT NULL default '',
        `min` varchar(255) NOT NULL default'',
        `max` varchar(255) NOT NULL  default'',
        `cout` varchar(255) NOT NULL default'',
         */
        foreach ($table_rates as $table_rate) {

            // Is table_rate for an available zone?
            $zone_pass = (in_array($table_rate->getIdZone(), $available_zones));

            // Is table_rate valid for basket weight?
            if ($table_rate->getCondition() == 0) {
                $weight      = $this->cart_contents_weight();
                $weight_pass = (($weight >= $table_rate->getMin()) && ($this->is_less_than($weight, $table_rate->getMax())));
            } else {
                $weight_pass = true;
            }

            // Is table_rate valid for basket total?
            if ($table_rate->getCondition() == 1) {
                $total      = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
                $total_pass = (($total >= $table_rate->getMin()) && ($this->is_less_than($total, $table_rate->getMax())));
            } else {
                $total_pass = true;
            }

            // Accept table_rate if passes all tests
            if ($zone_pass && $weight_pass && $total_pass) {
                $available_table_rates[] = $table_rate->getData();
            }

        }
        return $available_table_rates;
    }

    public function get_available_zones($request)

    /*
    $request->getDestCountryId(); //for country code
    $request->getDestRegionCode(); //for region id (if the country supports it
    $request->getDestPostcode();//postal code
    $request->getDestCity();//city
    $request->getDestStreet();//street

    `id_zone` int(11) unsigned NOT NULL auto_increment,
    `nom` varchar(255) NOT NULL default'',
    `villes` varchar(255) NOT NULL default'',

     */
    {

        $zones = Mage::getModel('speedbox/zones')->getCollection();

        $destination_country = $request->getDestCountryId();

        $destination_city = $this->get_city_from_data($request->getDestCity());
        //$cities=$this->speedbox_helper->get_cities_from_data();

        $available_zones = array();

        foreach ($zones as $zone):
            $villes = explode(",", $zone->getVilles());
            if ($destination_country == 'MA' && (isset($destination_city['ID']) && in_array($destination_city['ID'], $villes))) {
                $available_zones[] = $zone->getId();
            }
        endforeach;

        if (empty($available_zones)) {
            if ($destination_country == 'MA') {
                $available_zones[] = '0'; // "All of Morocco"
            }
        }
        return $available_zones;
    }

    public function cart_contents_weight()
    {

        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();

        $weight = 0;
        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }

        return $weight;
    }

    /* Return true if value less than max, incl. "*" */
    public function is_less_than($value, $max)
    {
        if ($max == '*') {
            return true;
        } else {
            return ($value <= $max);
        }

    }
    /* Retrieves cheapest rate from a list of table_rates. */
    public function pick_cheapest_table_rate($table_rates)
    {
        $cheapest = false;
        foreach ($table_rates as $table_rate):
            if ($cheapest == false) {
                $cheapest = $table_rate;
            } else {
                if ($table_rate['cout'] < $cheapest['cout']) {
                    $cheapest = $table_rate;
                }

            }
        endforeach;
        return $cheapest;
    }
    public function get_all_cities_from_data()
    {
        $city_data        = Mage::getBaseDir('media') . '/speedbox/data/city.json';
        $cities_json_data = file_get_contents($city_data);
        return json_decode($cities_json_data, true);

    }
    public function get_all_postalcodes_from_data()
    {
        $postalcodes           = Mage::getBaseDir('media') . '/speedbox/data/postalcodes.json';
        $postalcodes_json_data = file_get_contents($postalcodes);
        return json_decode($postalcodes_json_data, true);

    }

    public function get_postalcode($city)
    {

        $all_postalcodes = $this->get_all_postalcodes_from_data();

        foreach ($all_postalcodes['postalcodes'] as $pkey => $pval) {
            foreach ($pval as $key => $val) {
                if ($city == $key) {
                    return $val;
                }
            }

        }

        return '';

    }
    public function get_cities_values()
    {
        $cities = array();
        foreach ($this->getCitiesOptions() as $key => $val) {

            $cities[$val] = $val;

        }
        return $cities;

    }

    public function get_city_from_data($city, $zipcode = 0)
    {
        $all_cities = $this->get_all_cities_from_data();
        foreach ($all_cities['cities'] as $key => $val) {
            if (strcasecmp($city, $this->stripAccents($val['city'])) == 0) {
                $all_cities['cities'][$key]['ID'] = $key;
                return ($all_cities['cities'][$key]);
            }
        }
        if ($zipcode) {
            $all_postalcodes = $this->get_all_postalcodes_from_data();
            foreach ($all_postalcodes['postalcodes'] as $pkey => $pval) {
                foreach ($pval as $key => $val) {
                    if ($val == $zipcode) {
                        return $this->get_city_from_data($this->stripAccents($key));
                    }
                }

            }
        }

        return array();
    }
    public function circleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a        = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }

    public function min_circle_distance($city, $cities)
    {
        $min = array('distance' => 999999999999999999999999, 'min_city' => array());
        foreach ($cities as $speedbox_city) {
            $city_data = $this->get_city_from_data($speedbox_city);
            if (!empty($city_data)) {

                $distance = $this->circleDistance(
                    $city_data['latitude'], $city_data['longitude'], $city['latitude'], $city['longitude']);
                if ($distance < $min['distance']) {
                    $min['distance'] = $distance;
                    $min['min_city'] = $city_data;
                }

            }
        }

        return $min;

    }
    public function stripAccents($str)
    {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),

            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

    public function generate_token()
    {
        $id_length = 9;

        $alfa  = "abcdefghijklmnopqrstuvwxyz1234567890";
        $token = "";
        for ($i = 1; $i < $id_length; $i++) {

            @$token .= $alfa[rand(1, strlen($alfa))];

        }
        return $token;
    }
    public static function formatTel($gsm_dest)
    {

        $gsm_dest = str_replace(array(' ', '.', '-', ',', ';', '/', '\\', '(', ')'), '', $gsm_dest);

        if (substr($gsm_dest, 0, 1) == 0) {
            // Chrome autofill fix
            $gsm_dest = substr_replace($gsm_dest, '+212', 0, 1);
        } else {
            $gsm_dest = '+212' . $gsm_dest;
        }
        if ((substr($gsm_dest, 4, 1) == 6 || substr($gsm_dest, 4, 1) == 5) && strlen($gsm_dest) == 13) {
            return $gsm_dest;
        } else {
            return '+212600000000';
        }

    }

    public function getStatus($code)
    {

        $status = array(
            '100' => 'STATUT_DEMANDE_DE_PRISE_EN_CHARGE',
            '110' => 'STATUT_PRISE_EN_CHARGE_ID',
            '120' => 'STATUT_RAMASSAGE',
            '130' => 'STATUT_PRISE_EN_CHARGE_HUB',
            '140' => 'STATUT_CENTRE_DE_TRI',
            '150' => 'STATUT_TRIE',
            '160' => 'STATUT_MIS_EN_SAC',
            '170' => 'STATUT_EN_COURS_DE_LIVRAISON',
            '1'   => 'STATUT_EN_ATTENTE',
            '2'   => 'STATUT_RECU',
            '14'  => 'STATUT_DEVOYE',
            '3'   => 'STATUT_RECU_NON_CONFORME',
            '12'  => 'STATUT_REFUS_CLIENT',
            '4'   => 'STATUT_ENCAISSE',
            '8'   => 'STATUT_TRANSFERE',
            '13'  => 'STATUT_DELAIS_DE_GARDE_DEPASSE',
            '9'   => 'STATUT_ANNULE',

        );

        return $status[$code];
    }
    public function speedbox_get_state($code)
    {
        $states = array(
            '45' => 'Grand Casablanca',
            '50' => 'Chaouia-Ouardigha',
            '51' => 'Doukkala-Abda',
            '46' => 'Fès-Boulemane',
            '52' => 'Gharb-Chrarda-Beni Hssen',
            '53' => 'Guelmim-Es Semara',
            '47' => 'Marrakech-Tensift-Al Haouz',
            '48' => 'Meknès-Tafilalet',
            '54' => 'l\'Oriental',
            '49' => 'Rabat-Salé-Zemmour-Zaër',
            '55' => 'Souss-Massa-Draâ',
            '56' => 'Tadla-Azilal',
            '57' => 'Tanger-Tétouan',
            '58' => 'Taza-Al Hoceïma-Taounate',
            '59' => 'Laayoune-Boujdour-Sakia-Hamra',
            '60' => 'Oued-Eddahab-Lagouira',

        );
        return isset($states[$code]) ? $states[$code] : '';
    }
    public function getStatutHistorique($statut_historique)
    {
        foreach ($statut_historique as $key => $value) {
            $statut_historique[$key] = $this->getStatus($value);
        }
        return $statut_historique;

    }

    public function do_offset($level)
    {
        $offset = "";
        for ($i = 1; $i < $level; $i++) {
            $offset = $offset . "<td></td>";
        }
        return $offset;
    }

    public function show_array($array, $level, $sub)
    {
        $html = '';
        if (is_array($array) == 1) {
            // check if input is an array
            foreach ($array as $key_val => $value) {
                $offset = "";
                if (is_array($value) == 1) {
                    // array is multidimensional
                    $html .= "<tr>";
                    $offset = $this->do_offset($level);
                    $html .= $offset . "<td>" . $key_val . "</td>";
                    $html .= $this->show_array($value, $level + 1, 1);
                } else {
                    // (sub)array is not multidim
                    if ($sub != 1) {
                        // first entry for subarray
                        $html .= "<tr nosub>";
                        $offset = $this->do_offset($level);
                    }
                    $sub = 0;
                    $html .= $offset . "<td main " . $sub . " >" . $key_val .
                        "</td><td>" . $value . "</td>";
                    $html .= "</tr>\n";
                }
            } //foreach $array
        } else {
            // argument $array is not an array
            return;
        }
        return $html;
    }

    public function html_show_array($array)
    {
        $html = "<table>\n";
        $html .= $this->show_array($array, 1, 0);
        $html .= "</table>\n";
        return $html;
    }

}
