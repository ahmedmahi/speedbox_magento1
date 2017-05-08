<?php

class S3ibusiness_Speedbox_Adminhtml_ZonesController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('speedbox/zones');
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("speedbox/zones")->_addBreadcrumb(Mage::helper("adminhtml")->__("Zones  Manager"), Mage::helper("adminhtml")->__("Zones Manager"));
        return $this;
    }
    public function indexAction()
    {

        $this->_title($this->__("Speedbox"));
        $this->_title($this->__("Manager Zones"));

        $this->_initAction();
        $this->renderLayout();
        if (Mage::getStoreConfig('carriers/speedbox/gestion_frais_api') != 2) {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("speedbox")->__("Please proceed to the configuration of the Speedbox extension via System > Configuration > Sales > Shipping Methods > Livraison SpeedBox Relais. the value of the 'Delivery Fee Management' option must be equal to 'Specify shipping costs'"));
            $this->_redirect("adminhtml/system_config/index");
        }
    }
    public function editAction()
    {
        $this->_title($this->__("Speedbox"));
        $this->_title($this->__("Zones"));
        $this->_title($this->__("Edit Item"));

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("speedbox/zones")->load($id);
        if ($model->getId()) {
            Mage::register("zones_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("speedbox/zones");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Zones Manager"), Mage::helper("adminhtml")->__("Zones Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Zones Description"), Mage::helper("adminhtml")->__("Zones Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("speedbox/adminhtml_zones_edit"))->_addLeft($this->getLayout()->createBlock("speedbox/adminhtml_zones_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("speedbox")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {

        $this->_title($this->__("Speedbox"));
        $this->_title($this->__("Zones"));
        $this->_title($this->__("New Item"));

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("speedbox/zones")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("zones_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("speedbox/zones");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Zones Manager"), Mage::helper("adminhtml")->__("Zones Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Zones Description"), Mage::helper("adminhtml")->__("Zones Description"));

        $this->_addContent($this->getLayout()->createBlock("speedbox/adminhtml_zones_edit"))->_addLeft($this->getLayout()->createBlock("speedbox/adminhtml_zones_edit_tabs"));

        $this->renderLayout();

    }
    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();

        if ($post_data) {

            try {

                $post_data['villes'] = implode(',', $post_data['villes']);

                $model = Mage::getModel("speedbox/zones")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Zones was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setZonesData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setZonesData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }

        }
        $this->_redirect("*/*/");
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = Mage::getModel("speedbox/zones");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }

    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('id_zones', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("speedbox/zones");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

}
