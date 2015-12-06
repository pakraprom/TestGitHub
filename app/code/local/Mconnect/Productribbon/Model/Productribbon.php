<?php

class Mconnect_Productribbon_Model_Productribbon extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productribbon/productribbon');
    }
}