<?php

class Mconnect_Productribbon_Model_Ribbonposition
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'top-left', 'label'=>Mage::helper('adminhtml')->__('top-left')),
            array('value' => 'top-right', 'label'=>Mage::helper('adminhtml')->__('top-right')),
            array('value' => 'bottom-left', 'label'=>Mage::helper('adminhtml')->__('bottom-left')),
            array('value' => 'bottom-right', 'label'=>Mage::helper('adminhtml')->__('bottom-right')),
        );
    }
}