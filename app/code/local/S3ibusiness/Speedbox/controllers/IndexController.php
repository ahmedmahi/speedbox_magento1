<?php
class S3ibusiness_Speedbox_IndexController extends Mage_Core_Controller_Front_Action
{

    public function CitiesAction()
    {

        $sous_city = $this->getRequest()->getParam("sous_city");
        if ($sous_city) {

            $all_cities = Mage::helper('speedbox')->get_all_cities_from_data();
            $cities     = $all_cities['cities'];
            foreach ($all_cities['cities'] as $key => $val) {
                if (!preg_match('/' . $sous_city . '/i', $val['city'])) {
                    unset($all_cities['cities'][$key]);
                }
            }
            echo json_encode($all_cities);
        }

    }

    public function PointsrelaisAction()
    {

        try {
            $city     = $this->getRequest()->getParam("city");
            $PR_id    = $this->getRequest()->getParam("PR_id");
            $PR_infos = $this->getRequest()->getParam("PR_infos");
            if ($city) {
                $block = $this->getLayout()->createBlock('speedbox/onepage_pointsrelais')->setTemplate('speedbox/onepage/pointsrelais.phtml');
                echo $block->toHtml();
            } elseif ($PR_id && $PR_infos) {
                $session = Mage::getSingleton('core/session');
                $session->setSpeedboxSelectedRelaisId($PR_id);
                $session->setSpeedboxSelectedRelaisInfos($PR_infos);
                $PR_infos = json_decode($PR_infos, true);
                echo $PR_infos['shop_name'] . ' ( ' . $PR_id . ' )';

            }

        } catch (Exception $e) {
            echo $e;
        }

    }

    public function tracerAction()
    {

        $this->loadLayout();

        $this->renderLayout();
    }

    public function getSession()
    {

        return Mage::getSingleton('core/session');
    }

}
