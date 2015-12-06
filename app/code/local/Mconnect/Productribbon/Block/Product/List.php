<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mconnect_Productribbon_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    
    
    public function stripTags($data, $allowableTags = null, $escape = false)
    {
        $result = strip_tags($data, $allowableTags);
        return $escape ? $this->escapeHtml($result, $allowableTags) : $result;
    }
    
    public function escapeHtml($data, $allowedTags = null)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item);
            }
        } else {
            // process single item
            if (strlen($data)) {
                if (is_array($allowedTags) and !empty($allowedTags)) {
                    $allowed = implode('|', $allowedTags);
                    $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
                    $result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
                    $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
                } else {
                    $result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
                }
            } else {
                $result = $data;
            }
        }
        return $result;
    }
    
    
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        /*$toolbar = $this->getLayout()->createBlock('catalog/product_list_toolbar', microtime());
        if ($toolbarTemplate = $this->getToolbarTemplate()) {
            $toolbar->setTemplate($toolbarTemplate);
        }*/
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to tollbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();
        Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }
    
    //custom function for the new product ribbon
    public function NewProductImageOnProduct($listSku,$modelSku,$Npc,$currntDate,$adminDayFornewproduct,$currntDateintime,$getnewproductsimagename,$adminStyleNew,$getpositionforNewproduct,$listmode){
    if(in_array($listSku, $Npc)){
                        
                        //save sku for new product
                        if(!in_array($listSku, $modelSku)){
                        $savenewproduct0 = Mage::getModel('productribbon/productribbon')
                        ->setSku($listSku)       
                        ->setProductDate($currntDate)
                        ->save();
                        }
                      
                    //get date from model for new products
                    $newproductsdate = Mage::getModel('productribbon/productribbon')->getCollection();
                    $filtertoSku = $newproductsdate->addFieldToFilter('sku', $listSku);
                    $Datacount = $filtertoSku->count();

                    if($Datacount > 0){
                        $getFilterData = $newproductsdate->getData();
                        
                        if(isset($getFilterData[0])){
                        $productDate = $getFilterData[0]['product_date']; 
                        $productDatetoTime = strtotime($productDate);
                        
                         //admin day convert to time for new products
                        $newproductday = (60*60*24*$adminDayFornewproduct);
                        $additionofDaysForNew = $productDatetoTime+$newproductday;
                 ?>
        <?php if($currntDateintime >= $productDatetoTime && $currntDateintime <= $additionofDaysForNew){ ?>
                    <?php if($listmode == 'list'){ 
                          if($getpositionforNewproduct == 'top-left'){ ?>
                            <div class="label-new-top-left-list">
                        <?php } ?> 
                      
                        <?php if($getpositionforNewproduct == 'top-right'){ ?>
                                <div class="label-new-top-right-list" style="position: absolute;width: inherit">
                            
                        <?php } ?>
                    
                        <?php if($getpositionforNewproduct == 'bottom-left'){ ?>
                                <div class="label-new-bottom-left-list" style="position: absolute;width: inherit; height: inherit">
                            
                        <?php } ?>
                                    
                        <?php if($getpositionforNewproduct == 'bottom-right'){ ?>
                                <div class="label-new-bottom-right-list" style="position: absolute;width: inherit; height: inherit">
                            
                        <?php }
                    
                    } ?>
                    <?php if($listmode == 'grid'){
                            if($getpositionforNewproduct == 'top-left'){ ?>
                            <div class="label-new-top-left-grid">
                            <?php } ?> 
                               
                            <?php if($getpositionforNewproduct == 'top-right'){ ?>
                                <div class="label-new-top-right-grid" style="position: absolute;width: inherit">
                            
                            <?php } ?>
                                
                            <?php if($getpositionforNewproduct == 'bottom-left'){ ?>
                                <div class="label-new-bottom-left-grid" style="position: absolute;width: inherit; height: inherit">
                            
                            <?php } ?>
                                    
                            <?php if($getpositionforNewproduct == 'bottom-right'){ ?>
                                <div class="label-new-bottom-right-grid" style="position: absolute;width: inherit; height: inherit">
                            
                            <?php }
                    
                            
                    } ?>    
                        <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getnewproductsimagename ?>" style="<?php if($adminStyleNew != ''){ echo $adminStyleNew; }else{ ?>height: 50px;width: 50px;<?php }?>">
                   
                        </div>    
                    <?php 
           }
          }
         }
        }
       }
       
       //custom function for Best seller product
       public function BestProductImageOnProduct($listSku,$Bpc,$modelSku1,$currntDate,$adminDayForBestproduct,$currntDateintime,$getbestproductsimagename,$adminStyleBest,$getpositionforBestproduct,$listmode){
       if(in_array($listSku, $Bpc)){ 
                       //save sku for best product
                       if(!in_array($listSku, $modelSku1)){
                        $savenewproduct1 = Mage::getModel('productribbon/productribbonbest')
                        ->setSku($listSku)       
                        ->setProductDate($currntDate)
                        ->save();
                        }

                    //get model for best seller products
                    $bestproductsdate = Mage::getModel('productribbon/productribbonbest')->getCollection();
                    $filtertoSkubest = $bestproductsdate->addFieldToFilter('sku', $listSku);
                    $Datacountbest = $filtertoSkubest->count();
                    if($Datacountbest > 0){
                        $getFilterDataBest = $bestproductsdate->getData();
                        if(isset($getFilterDataBest[0])){
                        $productDateBest = $getFilterDataBest[0]['product_date']; 
                        $BestproductDatetoTime = strtotime($productDateBest);
                    
                        $bestproductday = (60*60*24*$adminDayForBestproduct);
                        $additionofDaysForBest = $BestproductDatetoTime+$bestproductday;   
                  ?>
                        <?php if($currntDateintime >= $BestproductDatetoTime && $currntDateintime <= $additionofDaysForBest){ ?>
                                    
                                   <?php if($listmode == 'list'){ 
                                            if($getpositionforBestproduct == 'top-left'){ ?>
                                              <div class="label-new-top-left-list">
                                          <?php } ?> 

                                          <?php if($getpositionforBestproduct == 'top-right'){ ?>
                                                  <div class="label-new-top-right-list" style="position: absolute;width: inherit">

                                          <?php } ?>

                                          <?php if($getpositionforBestproduct == 'bottom-left'){ ?>
                                                  <div class="label-new-bottom-left-list" style="position: absolute;width: inherit; height: inherit">

                                          <?php } ?>

                                          <?php if($getpositionforBestproduct == 'bottom-right'){ ?>
                                                  <div class="label-new-bottom-right-list" style="position: absolute;width: inherit; height: inherit">

                                          <?php }

                                  } ?>
                                  <?php if($listmode == 'grid'){
                                          if($getpositionforBestproduct == 'top-left'){ ?>
                                          <div class="label-new-top-left-grid">
                                          <?php } ?> 

                                          <?php if($getpositionforBestproduct == 'top-right'){ ?>
                                              <div class="label-new-top-right-grid" style="position: absolute;width: inherit">

                                          <?php } ?>

                                          <?php if($getpositionforBestproduct == 'bottom-left'){ ?>
                                              <div class="label-new-bottom-left-grid" style="position: absolute;width: inherit; height: inherit">

                                          <?php } ?>

                                          <?php if($getpositionforBestproduct == 'bottom-right'){ ?>
                                              <div class="label-new-bottom-right-grid" style="position: absolute;width: inherit; height: inherit">

                                          <?php }


                                  } ?>     
                                    
                        <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getbestproductsimagename ?>" style="<?php if($adminStyleBest != ''){ echo $adminStyleBest; }else{ ?>height: 50px;width: 50px;<?php }?>">
                          </div>
                <?php
            }
           }
          }
         }  
        }
    
    // custom function for sales product    
    public function SalesProductImageOnProduct($specialSpricelabel,$getsalesproductsimagename,$adminStyleSales,$getpositionforSalesproduct,$listmode){
    if($specialSpricelabel != ''){ ?>
                                                  
             <?php if($listmode == 'list'){ 
                          if($getpositionforSalesproduct == 'top-left'){ ?>
                            <div class="label-new-top-left-list">
                        <?php } ?> 
                      
                        <?php if($getpositionforSalesproduct == 'top-right'){ ?>
                                <div class="label-new-top-right-list" style="position: absolute;width: inherit">
                            
                        <?php } ?>
                    
                        <?php if($getpositionforSalesproduct == 'bottom-left'){ ?>
                                <div class="label-new-bottom-left-list" style="position: absolute;width: inherit; height: inherit">
                            
                        <?php } ?>
                                    
                        <?php if($getpositionforSalesproduct == 'bottom-right'){ ?>
                                <div class="label-new-bottom-right-list" style="position: absolute;width: inherit; height: inherit">
                            
                        <?php }
                    
                    } ?>
                    <?php if($listmode == 'grid'){
                            if($getpositionforSalesproduct == 'top-left'){ ?>
                            <div class="label-new-top-left-grid">
                            <?php } ?> 
                               
                            <?php if($getpositionforSalesproduct == 'top-right'){ ?>
                                <div class="label-new-top-right-grid" style="position: absolute;width: inherit">
                            
                            <?php } ?>
                                
                            <?php if($getpositionforSalesproduct == 'bottom-left'){ ?>
                                <div class="label-new-bottom-left-grid" style="position: absolute;width: inherit; height: inherit">
                            
                            <?php } ?>
                                    
                            <?php if($getpositionforSalesproduct == 'bottom-right'){ ?>
                                <div class="label-new-bottom-right-grid" style="position: absolute;width: inherit; height: inherit">
                            
                            <?php }
                    
                            
                    } ?>                                                     
          <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getsalesproductsimagename ?>" style="<?php if($adminStyleSales != ''){ echo $adminStyleSales; }else{ ?>height: 50px;width: 50px;<?php }?>">
                                </div>
              <?php
      }
    }
    
    //custom function for most reviewed product
    public function ReviewProductImageOnProduct($listSku,$Rpc,$modelSku2,$currntDate,$adminDayForReviewproduct,$currntDateintime,$getreviewproductsimagename,$adminStyleReview,$getpositionforMostRiviewedproduct,$listmode){
     if(in_array($listSku, $Rpc)){ 
                    
                       //save sku for best product
                       if(!in_array($listSku, $modelSku2)){
                        $savenewproduct2 = Mage::getModel('productribbon/productribbonrivewed')
                        ->setSku($listSku)       
                        ->setProductDate($currntDate)
                        ->save();
                        }

                    //get model for most Reviewed products
                    $reviewproductsdate = Mage::getModel('productribbon/productribbonrivewed')->getCollection();
                    $filtertoSkuReview = $reviewproductsdate->addFieldToFilter('sku', $listSku);
                    $DatacountReview = $filtertoSkuReview->count();
                    if($DatacountReview > 0){
                        $getFilterDataReview = $reviewproductsdate->getData();
                        if(isset($getFilterDataReview[0])){
                        $productDateReview = $getFilterDataReview[0]['product_date']; 
                        $ReviewproductDatetoTime = strtotime($productDateReview);
                    
                        $Reviewproductday = (60*60*24*$adminDayForReviewproduct);
                        $additionofDaysForReview = $ReviewproductDatetoTime+$Reviewproductday;   
                  ?>
                        <?php if($currntDateintime >= $ReviewproductDatetoTime && $currntDateintime <= $additionofDaysForReview){ ?>
                                    
                            <?php if($listmode == 'list'){ 
                                  if($getpositionforMostRiviewedproduct == 'top-left'){ ?>
                                    <div class="label-new-top-left-list">
                                <?php } ?> 

                                <?php if($getpositionforMostRiviewedproduct == 'top-right'){ ?>
                                        <div class="label-new-top-right-list" style="position: absolute;width: inherit">

                                <?php } ?>

                                <?php if($getpositionforMostRiviewedproduct == 'bottom-left'){ ?>
                                        <div class="label-new-bottom-left-list" style="position: absolute;width: inherit; height: inherit">

                                <?php } ?>

                                <?php if($getpositionforMostRiviewedproduct == 'bottom-right'){ ?>
                                        <div class="label-new-bottom-right-list" style="position: absolute;width: inherit; height: inherit">

                                <?php }

                            } ?>
                            <?php if($listmode == 'grid'){
                                    if($getpositionforMostRiviewedproduct == 'top-left'){ ?>
                                    <div class="label-new-top-left-grid">
                                    <?php } ?> 

                                    <?php if($getpositionforMostRiviewedproduct == 'top-right'){ ?>
                                        <div class="label-new-top-right-grid" style="position: absolute;width: inherit">

                                    <?php } ?>

                                    <?php if($getpositionforMostRiviewedproduct == 'bottom-left'){ ?>
                                        <div class="label-new-bottom-left-grid" style="position: absolute;width: inherit; height: inherit">

                                    <?php } ?>

                                    <?php if($getpositionforMostRiviewedproduct == 'bottom-right'){ ?>
                                        <div class="label-new-bottom-right-grid" style="position: absolute;width: inherit; height: inherit">

                                    <?php }


                            } ?>    
                                    
                        <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getreviewproductsimagename ?>" style="<?php if($adminStyleReview != ''){ echo $adminStyleReview; }else{ ?>height: 50px;width: 50px;<?php }?>">
                                </div>
                            <?php
        }
       }
      }
     }
    }

    
    
}
