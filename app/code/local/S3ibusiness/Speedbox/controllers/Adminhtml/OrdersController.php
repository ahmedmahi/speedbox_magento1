<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action
{

    public function envoiAction()
    {

        $orderIds = $this->getRequest()->getPost('order_ids');

        if (!empty($orderIds)) {

            $numero_prise_en_charge = Mage::helper('speedbox')->generate_token();
            foreach ($orderIds as $order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                $pointrelaist          = $order->getSpeedboxSelectedRelaisId();
                $colis_numero_speedbox = $order->getSpeedboxNumeroColis();
                if ($order->getShippingMethod() == 'speedbox_speedbox' && $pointrelaist && !$order->getSpeedboxNumeroColis()) {
                    $numero_colis = ($this->hasShipment($order)) ? ($this->hasShipment($order)) : $this->creatShipment($order); //Mage::helper('speedbox')->generate_token();
                    $weights      = explode("-", $this->getRequest()->getPost('weight_' . $order_id));
                    $poids        = number_format($weights[1], 2, '.', '');
                    $coli         = array(
                        'date_de_commande' => date('d/m/Y', strtotime($order->getCreatedAt())),
                        'numero_colis'     => $numero_colis,
                        'pointrelais'      => $pointrelaist,
                        'nom_du_client'    => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                        'email_du_client'  => $order->getCustomerEmail(),
                        'numero_du_client' => Mage::helper('speedbox')->formatTel($order->getBillingAddress()->getTelephone()),
                        'cash_due'         => (Mage::getStoreConfig('carriers/speedbox/cash_delivery_active') && 'cashondelivery' == $order->getPayment()->getMethod()) ? number_format($order->getGrandTotal(), 2, '.', '') : '0',
                        'poids'            => $poids,
                    );
                    //echo '<pre>' . print_r($coli, true) . '</pre>';
                    $result = Mage::helper('speedbox')->get_api()->colis->create($coli);

                    if (is_array($result) && $result['result'] == 'ok') {

                        $order->setSpeedboxNumeroColis($result['numero_speedbox']);
                        $order->setSpeedboxCodeBarreColis($result['code_barre']);
                        $order->setSpeedboxStatutColis(Mage::helper('speedbox')->getStatus($result['statut']));
                        $order->save();

                        $this->apiPriseEnCharge($numero_prise_en_charge, $result['numero_speedbox'], $order);
                        $this->apiTracker($result['numero_speedbox'], $order, true);

                    } else {
                        $this->_getSession()->addError($this->__('Commande ID :' . $order->getIncrementID() . ' ' . $result));
                    }

                } elseif ($colis_numero_speedbox) {

                    $track = $this->apiTracker($colis_numero_speedbox, $order, true);
                    if (!$track['numero_prise_en_charge']) {
                        $this->apiPriseEnCharge($numero_prise_en_charge, $colis_numero_speedbox, $order);
                    }

                }

            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");

    }
    protected function hasShipment($order)
    {
        $shipment            = $order->getShipmentsCollection()->getFirstItem();
        $shipmentIncrementId = $shipment->getIncrementId();
        return $shipmentIncrementId;

    }

    public function creatShipment($order)
    {

        $incrementId = $order->getIncrementID();

        $trackingTitle  = 'Speedbox';
        $sendEmail      = 1;
        $url            = str_replace('admin_speedbox', 'speedbox', Mage::getUrl('speedbox/index/tracer/', array('trackingnumber' => $incrementId)));
        $comment        = 'Cher client, vous pouvez suivre l\'acheminement de votre colis par Speedbox en cliquant sur le lien ci-contre : ' . '<a target="_blank" href="' . $url . '">Suivre ce colis </a>';
        $includeComment = 1;

        if (!$order->canShip()) {
            $this->_getSession()->addError($this->__('La commande %s ne peut pas être expédiée, ou a déjà été expédiée.', $order->getRealOrderId()));
            return 0;
        }

        $convertor = Mage::getModel('sales/convert_order');
        $shipment  = $convertor->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            $item = $convertor->itemToShipmentItem($orderItem);
            $qty  = $orderItem->getQtyToShip();
            $item->setQty($qty);

            $shipment->addItem($item);
        } //foreach

        $shipment->register();
        $carrierCode = stristr($order->getShippingMethod(), '_', true);

        $track = Mage::getModel('sales/order_shipment_track')
            ->setNumber($incrementId)
            ->setCarrierCode($carrierCode)
            ->setTitle($trackingTitle)
            ->setUrl($url)
            ->setStatus('<a target="_blank" href="' . $url . '">' . __('Suivre ce colis') . '</a>');

        $shipment->addTrack($track);
        //$shipment->addComment($comment, $sendEmail && $includeComment);
        $shipment->getOrder()->setIsInProcess(true);

        if ($sendEmail) {
            $shipment->setEmailSent(true);
        }

        try {

            $shipment->save();

            // $shipment->sendEmail($sendEmail, ($includeComment ? $comment : ''));

            $shipment->getOrder()->addStatusHistoryComment($comment, $shipment->getOrder()->getStatus())
                ->setIsVisibleOnFront(1)
                ->setIsCustomerNotified($sendEmail && $includeComment);

            $shipment->getOrder()->save();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__('Erreur pendant la création de l\'expédition %s : %s', $order->getId(), $e->getMessage()));
            return 0;
        }

        $shipmentId = $shipment->getIncrementId();

        if ($shipmentId != 0) {
            $this->_getSession()->addSuccess($this->__('Livraison %s créée pour la commande %s, statut mis à jour', $shipmentId, $incrementId));
        }
        return $shipmentId;

    }

    public function trackerAction()
    {

        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {

            foreach ($orderIds as $order_id) {

                $order              = Mage::getModel('sales/order')->load($order_id);
                $shipping_method_id = $order->getShippingMethod();

                $colis_numero_speedbox = $order->getSpeedboxNumeroColis();
                if ($shipping_method_id == 'speedbox_speedbox' && $colis_numero_speedbox) {

                    $this->apiTracker($colis_numero_speedbox, $order);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }

    public function deliveredAction()
    {

        $orderIds = $this->getRequest()->getPost('order_ids');

        if (!empty($orderIds)) {

            foreach ($orderIds as $order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                $shipping_method_id    = $order->getShippingMethod();
                $colis_numero_speedbox = $order->getSpeedboxNumeroColis();
                if ($shipping_method_id == 'speedbox_speedbox') {
                    if ($colis_numero_speedbox) {
                        $data = array('speedbox_statut_colis' => 'STATUT_RECU');
                        $this->valdatePrintMessage(array('resultat' => 'ok'), $order, $this->__('Delivered order status was updated'), $data);

                    } else {
                        $this->valdatePrintMessage($this->__('Package should be treated first'), $order);
                    }
                }
            }

        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }
    public function cancelAction()
    {

        $orderIds = $this->getRequest()->getPost('order_ids');

        if (!empty($orderIds)) {

            foreach ($orderIds as $order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                $shipping_method_id    = $order->getShippingMethod();
                $colis_numero_speedbox = $order->getSpeedboxNumeroColis();
                if ($shipping_method_id == 'speedbox_speedbox') {
                    $result     = Mage::helper('speedbox')->get_api()->colis->cancel($colis_numero_speedbox);
                    $post_metas = array('speedbox_statut_colis' => 'STATUT_ANNULE');
                    $this->valdatePrintMessage($result, $order, $this->__('Package well removed'), $post_metas);

                }
            }

        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }

    public function apiPriseEnCharge($numero_prise_en_charge, $numero_speedbox, $order)
    {
        $infos_depc = array(
            'numero_prise_en_charge' => $numero_prise_en_charge,
            'numero_speedbox'        => $numero_speedbox,

        );

        $result_depc = Mage::helper('speedbox')->get_api()->colis->demandePriseEnCharge($infos_depc);
        $this->valdatePrintMessage($result_depc, $order, $this->__('Support well sent:'));

    }

    public function apiTracker($colis_numero_speedbox, $order, $dajatraite = false)
    {

        $track = Mage::helper('speedbox')->get_api()->colis->track($colis_numero_speedbox);

        if (isset($track['numero_prise_en_charge']) && $track['numero_prise_en_charge']) {

            $track['Statut']                 = Mage::helper('speedbox')->getStatus($track['statut']);
            $track['Historique des statuts'] = implode("=>", Mage::helper('speedbox')->getStatutHistorique($track['statut_historique']));
            $track['Dernière mise à jour'] = date('d/m/Y H:i', $track['last_updated_timestamp']);
            unset($track['last_updated_timestamp']);
            unset($track['statut']);
            unset($track['statut_historique']);
        }

        $message    = ($dajatraite ? $this->__('Parcels already treated here is the information:') : '');
        $post_metas = array('speedbox_statut_colis' => $track['Statut']);

        $this->valdatePrintMessage($track, $order, $message, $post_metas);

        return $track;
    }

    public function valdatePrintMessage($result, $order, $message = '', $post_metas = array())
    {
        if (is_array($result) && $result['resultat'] == 'ok') {
            foreach ($post_metas as $key => $value) {
                $initial = $order->getData($key);
                if ($initial != $value) {
                    $order->setData($key, $value);
                }
            }
            $order->save();
            unset($result['resultat']);
            $this->_getSession()->addSuccess($this->__('Commande ' . $order->getIncrementID() . ' : ') . $message . Mage::helper('speedbox')->html_show_array($result));
        } else {
            $this->_getSession()->addError($this->__('Commande ' . $order->getIncrementID() . ' : ' . $result));

        }

    }

    public function indexAction()
    {

        $this->loadLayout()
            ->_setActiveMenu('speedbox/speedboxorder')
            ->_addContent($this->getLayout()->createBlock('speedbox/adminhtml_orders'))
            ->renderLayout();
    }

}
