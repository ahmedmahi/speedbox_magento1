<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class S3ibusiness_Speedbox_Adminhtml_FraisportController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('speedbox/fraisport');
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("speedbox/fraisport")->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping costs Manager"), Mage::helper("adminhtml")->__("Fraisport Manager"));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Speedbox"));
        $this->_title($this->__("Shipping costs Manager"));

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
        $this->_title($this->__("Fraisport"));
        $this->_title($this->__("Edit Item"));

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("speedbox/fraisport")->load($id);
        if ($model->getId()) {
            Mage::register("fraisport_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("speedbox/fraisport");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping costs Manager"), Mage::helper("adminhtml")->__("Shipping costs Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping costs Description"), Mage::helper("adminhtml")->__("Shipping costs Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("speedbox/adminhtml_fraisport_edit"))->_addLeft($this->getLayout()->createBlock("speedbox/adminhtml_fraisport_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("speedbox")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {

        $this->_title($this->__("Speedbox"));
        $this->_title($this->__("Fraisport"));
        $this->_title($this->__("New Item"));

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("speedbox/fraisport")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("fraisport_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("speedbox/fraisport");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Fraisport Manager"), Mage::helper("adminhtml")->__("Fraisport Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Fraisport Description"), Mage::helper("adminhtml")->__("Fraisport Description"));

        $this->_addContent($this->getLayout()->createBlock("speedbox/adminhtml_fraisport_edit"))->_addLeft($this->getLayout()->createBlock("speedbox/adminhtml_fraisport_edit_tabs"));

        $this->renderLayout();

    }
    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();

        if ($post_data) {

            try {

                $model = Mage::getModel("speedbox/fraisport")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Fraisport was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setFraisportData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setFraisportData($this->getRequest()->getPost());
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
                $model = Mage::getModel("speedbox/fraisport");
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
            $ids = $this->getRequest()->getPost('ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("speedbox/fraisport");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'fraisport.csv';
        $grid     = $this->getLayout()->createBlock('speedbox/adminhtml_fraisport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'fraisport.xml';
        $grid     = $this->getLayout()->createBlock('speedbox/adminhtml_fraisport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
