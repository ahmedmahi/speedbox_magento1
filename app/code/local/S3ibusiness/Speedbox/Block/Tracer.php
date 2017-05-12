<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Block_Tracer extends Mage_Core_Block_Template
{

    public function getTrace()
    {
        $trakcmessage = '';

        try {

            $incrementId = $this->getRequest()->getParam("trackingnumber");

            $order              = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
            $shipping_method_id = $order->getShippingMethod();

            $colis_numero_speedbox = $order->getSpeedboxNumeroColis();
            if ($shipping_method_id == 'speedbox_speedbox' && $colis_numero_speedbox) {

                $track = Mage::helper('speedbox')->get_api()->colis->track($colis_numero_speedbox);

                if (isset($track['numero_prise_en_charge']) && $track['numero_prise_en_charge']) {

                    $track['Statut']                 = Mage::helper('speedbox')->getStatus($track['statut']);
                    $track['Historique des statuts'] = implode("=>", Mage::helper('speedbox')->getStatutHistorique($track['statut_historique']));
                    $track['Dernière mise à jour'] = date('d/m/Y H:i', $track['last_updated_timestamp']);
                    unset($track['last_updated_timestamp']);
                    unset($track['statut']);
                    unset($track['statut_historique']);
                }

                $message    = $this->__('');
                $post_metas = array('speedbox_statut_colis' => $track['Statut']);

                if (is_array($track) && $track['resultat'] == 'ok') {

                    $initial = $order->getData('speedbox_statut_colis');
                    if ($initial != $track['Statut']) {
                        $order->setData('speedbox_statut_colis', $track['Statut']);
                        $order->save();
                    }

                    unset($track['resultat']);
                    $trakcmessage = ($this->__('Commande ' . $order->getIncrementID() . ' : ') . $message . Mage::helper('speedbox')->html_show_array($track));
                } else {
                    $trakcmessage = ($this->__('Commande ' . $order->getIncrementID() . ' : ' . $track));

                }

            }

        } catch (Exception $e) {
            echo $e;
        }
        return $trakcmessage;

    }

}
