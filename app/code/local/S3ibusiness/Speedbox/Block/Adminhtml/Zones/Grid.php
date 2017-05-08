<?php

class S3ibusiness_Speedbox_Block_Adminhtml_Zones_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId("zonesGrid");
        $this->setDefaultSort("id_zone");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("speedbox/zones")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {

        $this->addColumn("id_zone", array(
            "header" => Mage::helper("speedbox")->__("ID"),
            "align"  => "right",
            "width"  => "50px",
            "type"   => "number",
            "index"  => "id_zone",
        ));

        $this->addColumn("nom", array(
            "header" => Mage::helper("speedbox")->__("Nom"),
            "index"  => "nom",
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id_zone');
        $this->getMassactionBlock()->setFormFieldName('id_zones');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_zones', array(
            'label'   => Mage::helper('speedbox')->__('Remove Zones'),
            'url'     => $this->getUrl('*/adminhtml_zones/massRemove'),
            'confirm' => Mage::helper('speedbox')->__('Are you sure?'),
        ));
        return $this;
    }

    public static function getOptionArray1()
    {
        $data_array    = array();
        $data_array[0] = 'Casablanca';
        $data_array[1] = 'Rabat';
        $data_array[2] = 'Marrakech';
        $data_array[3] = 'Agadir';
        return ($data_array);
    }
    public static function getValueArray1()
    {
        $data_array = array();
        foreach (S3ibusiness_Speedbox_Block_Adminhtml_Zones_Grid::getOptionArray1() as $k => $v) {
            $data_array[] = array('value' => $k, 'label' => $v);
        }
        return ($data_array);

    }

}