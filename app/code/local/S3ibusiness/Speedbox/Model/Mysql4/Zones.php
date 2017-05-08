<?php
class S3ibusiness_Speedbox_Model_Mysql4_Zones extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("speedbox/zones", "id_zone");
    }
}