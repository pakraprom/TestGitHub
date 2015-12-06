<?php
class Mconnect_Productribbon_Block_Productribbon extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getProductribbon()     
     { 
        if (!$this->hasData('productribbon')) {
            $this->setData('productribbon', Mage::registry('productribbon'));
        }
        return $this->getData('productribbon');
        
    }
     //custom function for the new product ribbon to media file
    public function MediaNewProduct($adminDayFornewproduct,$productViewSku,$currntDateintimeView,$getnewproductsimagenameview,$adminStyleNew,$getpositionforNewproduct){
        //get model for each sku date
             $newProductDate = Mage::getModel('productribbon/productribbon')->getCollection();
             $newProductDate->addFieldToFilter('view_status', 1);
             $filterSku = $newProductDate->addFieldToFilter('sku', $productViewSku);
             $countView = $filterSku->count();
             if($countView > 0){
             $filterDateView = $newProductDate->getData();
             if(isset($filterDateView[0])){
             $ecahproductDate = $filterDateView[0]['product_date'];   
             $NewecahproductDateTime = strtotime($ecahproductDate);
             
            //get model for collection of sku    
            $newProductView = Mage::getModel('productribbon/productribbon')
            ->getCollection();
            $newProductView->addFieldToFilter('view_status', 1);
            $newProductView->getData();
            $NpvSku = array();
            foreach($newProductView as $Npv){
                $NpvSku[] = $Npv['sku'];
            }
            
            //admin day convert to time for new products
            $newproductday = (60*60*24*$adminDayFornewproduct);
            $additionofDaysForNew = $NewecahproductDateTime+$newproductday;
             
            if(in_array($productViewSku, $NpvSku)){?>
            <?php if($currntDateintimeView >= $NewecahproductDateTime && $currntDateintimeView <= $additionofDaysForNew){ ?>
                
                      <?php  if($getpositionforNewproduct == 'top-left'){ ?>
                            <span class="label-new-top-left-list-media">  
                        <?php } ?> 
                      
                        <?php if($getpositionforNewproduct == 'top-right'){ ?>
                                <span class="label-new-top-right-list-meida">
                            
                        <?php } ?>
                    
                        <?php if($getpositionforNewproduct == 'bottom-left'){ ?>
                                <span class="label-new-bottom-left-list-media">
                            
                        <?php } ?>
                                    
                        <?php if($getpositionforNewproduct == 'bottom-right'){ ?>
                                <span class="label-new-bottom-right-list-media">
                            
                        <?php } ?>

            <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getnewproductsimagenameview ?>" style="<?php if($adminStyleNew != ''){ echo $adminStyleNew; }else{ ?>height: 50px;width: 50px;<?php }?>">
                               
            </span>  
         
                         
           <?php
               } 
              } 
             }
            } 
    }
    
    //custom function for Best seller product to media file
    public function MediaBestProduct($productViewSku,$adminDayForBestproduct,$currntDateintimeView,$getbestproductsimagename,$adminStyleBest,$getpositionforBestproduct){
         $betsProductView = Mage::getModel('productribbon/productribbonbest')
                    ->getCollection();
                    $betsProductView->addFieldToFilter('view_status', 1);
                    $betsProductView->getData();
                    $BpvSku = array();
                    foreach($betsProductView as $Bpv){
                        $BpvSku[] = $Bpv['sku'];
                    }

                    //get model for each products date
                    $bestproductsdate = Mage::getModel('productribbon/productribbonbest')->getCollection();
                    $bestproductsdate->addFieldToFilter('view_status', 1);
                    $filtertoSkubest = $bestproductsdate->addFieldToFilter('sku', $productViewSku);
                    $Datacountbest = $filtertoSkubest->count();
                    if($Datacountbest > 0){
                        $getFilterDataBest = $bestproductsdate->getData();
                        if(isset($getFilterDataBest[0])){
                        $productDateBest = $getFilterDataBest[0]['product_date']; 
                        $BestproductDatetoTime = strtotime($productDateBest);
                    
                        $bestproductday = (60*60*24*$adminDayForBestproduct);
                        $additionofDaysForBest = $BestproductDatetoTime+$bestproductday;   
              ?>
                        <?php if(in_array($productViewSku, $BpvSku)); ?>
                        <?php if($currntDateintimeView >= $BestproductDatetoTime && $currntDateintimeView <= $additionofDaysForBest){ ?>
                                    
                        <?php  if($getpositionforBestproduct == 'top-left'){ ?>
                            <span class="label-new-top-left-list-media">  
                        <?php } ?> 
                      
                        <?php if($getpositionforBestproduct == 'top-right'){ ?>
                                <span class="label-new-top-right-list-meida">
                            
                        <?php } ?>
                    
                        <?php if($getpositionforBestproduct == 'bottom-left'){ ?>
                                <span class="label-new-bottom-left-list-media">
                            
                        <?php } ?>
                                    
                        <?php if($getpositionforBestproduct == 'bottom-right'){ ?>
                                <span class="label-new-bottom-right-list-media">
                            
                        <?php } ?>
                        <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getbestproductsimagename ?>" style="<?php if($adminStyleBest != ''){ echo $adminStyleBest; }else{ ?>height: 50px;width: 50px;<?php }?>">
                                </span> 
                       <?php
                        }
                       }
                      }
    }
    
    // custom function for sales product to media file
    public function MediaSalesProduct($specialSpricelabel,$getsalesproductsimagename,$adminStyleSales,$getpositionforSalesproduct){
        if($specialSpricelabel != ''){ ?>
                                    
                         <?php  if($getpositionforSalesproduct == 'top-left'){ ?>
                            <span class="label-new-top-left-list-media">  
                        <?php } ?> 
                      
                        <?php if($getpositionforSalesproduct == 'top-right'){ ?>
                                <span class="label-new-top-right-list-meida">
                            
                        <?php } ?>
                    
                        <?php if($getpositionforSalesproduct == 'bottom-left'){ ?>
                                <span class="label-new-bottom-left-list-media">
                            
                        <?php } ?>
                                    
                        <?php if($getpositionforSalesproduct == 'bottom-right'){ ?>
                                <span class="label-new-bottom-right-list-media">
                            
                        <?php } ?>
                                    
                        <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getsalesproductsimagename ?>" style="<?php if($adminStyleSales != ''){ echo $adminStyleSales; }else{ ?>height: 50px;width: 50px;<?php }?>">
                                </span>
                        <?php
         }
    }
    
    //custom function for most reviewed product to media file
    public function MediaReviewProduct($productViewSku,$adminDayForReviewproduct,$currntDateintimeView,$getreviewproductsimagename,$adminStyleReview,$getpositionforMostRiviewedproduct){
     
         $ReviewedProductSku = Mage::getModel('productribbon/productribbonrivewed')
                ->getCollection();
                $ReviewedProductSku->addFieldToFilter('view_status', 1);
                $ReviewedProductSku->getData();
                $filterViewSku = array();
                foreach($ReviewedProductSku as $Rps){
                    $filterViewSku[] = $Rps['sku'];
                }
                
                $reviewproductsdate = Mage::getModel('productribbon/productribbonrivewed')->getCollection();
                $reviewproductsdate->addFieldToFilter('view_status', 1);
                    $filtertoSkuReview = $reviewproductsdate->addFieldToFilter('sku', $productViewSku);
                    $DatacountReview = $filtertoSkuReview->count();
                    if($DatacountReview > 0){
                        $getFilterDataReview = $reviewproductsdate->getData();
                        if(isset($getFilterDataReview[0])){
                        $productDateReview = $getFilterDataReview[0]['product_date']; 
                        $ReviewproductDatetoTime = strtotime($productDateReview);
                    
                        $Reviewproductday = (60*60*24*$adminDayForReviewproduct);
                        $additionofDaysForReview = $ReviewproductDatetoTime+$Reviewproductday;   
            ?>
                        <?php if(in_array($productViewSku, $filterViewSku)){ ?>
                        <?php if($currntDateintimeView >= $ReviewproductDatetoTime && $currntDateintimeView <= $additionofDaysForReview){ ?>
                                    
                        <?php  if($getpositionforMostRiviewedproduct == 'top-left'){ ?>
                            <span class="label-new-top-left-list-media">  
                        <?php } ?> 
                      
                        <?php if($getpositionforMostRiviewedproduct == 'top-right'){ ?>
                                <span class="label-new-top-right-list-meida">
                            
                        <?php } ?>
                    
                        <?php if($getpositionforMostRiviewedproduct == 'bottom-left'){ ?>
                                <span class="label-new-bottom-left-list-media">
                            
                        <?php } ?>
                                    
                        <?php if($getpositionforMostRiviewedproduct == 'bottom-right'){ ?>
                                <span class="label-new-bottom-right-list-media">
                            
                        <?php } ?>
                                    
                        <img src="<?php echo Mage::getBaseUrl('media').'m-connectRibbon/'.$getreviewproductsimagename ?>" style="<?php if($adminStyleReview != ''){ echo $adminStyleReview; }else{ ?>height: 50px;width: 50px;<?php }?>">
                                </span>
                        <?php
                         }
                        }
                       }
                      }
        
        
    }
}