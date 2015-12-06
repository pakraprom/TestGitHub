<?php

class Mconnect_Productribbon_Model_Mysql4_Productribbonrivewed_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productribbon/productribbonrivewed');
    }
}