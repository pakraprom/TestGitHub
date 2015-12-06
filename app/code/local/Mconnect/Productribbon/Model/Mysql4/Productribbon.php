<?php

class Mconnect_Productribbon_Model_Mysql4_Productribbon extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productribbon_id refers to the key field in your database table.
        $this->_init('productribbon/productribbon', 'productribbon_id');
    }
}