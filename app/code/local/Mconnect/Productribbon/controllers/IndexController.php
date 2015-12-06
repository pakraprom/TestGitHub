<?php
class Mconnect_Productribbon_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/productribbon?id=15 
    	 *  or
    	 * http://site.com/productribbon/id/15 	
    	 */
    	/* 
		$productribbon_id = $this->getRequest()->getParam('id');

  		if($productribbon_id != null && $productribbon_id != '')	{
			$productribbon = Mage::getModel('productribbon/productribbon')->load($productribbon_id)->getData();
		} else {
			$productribbon = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($productribbon == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$productribbonTable = $resource->getTableName('productribbon');
			
			$select = $read->select()
			   ->from($productribbonTable,array('productribbon_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$productribbon = $read->fetchRow($select);
		}
		Mage::register('productribbon', $productribbon);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    
    
}