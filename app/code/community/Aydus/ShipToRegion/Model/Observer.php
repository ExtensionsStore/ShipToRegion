<?php

/**
 * Observer
 *
 * @category   Aydus
 * @package	   Aydus_ShipToRegion
 * @author     Aydus Consulting <davidt@aydus.com>
 */

class Aydus_ShipToRegion_Model_Observer 
{
    /**
     * Add specificregion field to all carrier groups
     *
     * @param Mage_Core_Model_Observer $observer
     * @return Aydus_ShipToRegion_Model_Observer
     */
    public function addShipToRegionFields($observer)
    {
        $config = $observer->getConfig();
    
        $carrierGroups = $config->getNode('sections/carriers/groups');
        
        $usualCarrierGroups = array(
                'flatrate' => 92,
                'freeshipping' => 92,
                'tablerate' => 92,
                'dhl' => 1915,
                'fedex' => 215,
                'ups' => 915,
                'usps' => 195,
                'dhlint' => 1915,
        );
        
        $usualCarrierGroupsKeys = array_keys($usualCarrierGroups);
    
        foreach ($carrierGroups->children() as $group => $element){
            	
            $fields = &$element->fields;
            
            if (!$fields->specificregion){
                
                $specificRegion = $fields->addChild('specificregion');
                
                $specificRegion->addAttribute('translate', 'label');
                $specificRegion->addAttribute('module', 'aydus_shiptoregion');
                
                $specificRegion->addChild('label', 'Ship to Specific Region');
                $specificRegion->addChild('frontend_type', 'multiselect');
                $specificRegion->addChild('source_model', 'aydus_shiptoregion/adminhtml_system_config_source_shipping_region');
                $sortOrder = (in_array($group, $usualCarrierGroupsKeys)) ? $usualCarrierGroups[$group] : 500;
                $specificRegion->addChild('sort_order', $sortOrder);
                $specificRegion->addChild('show_in_default', 1);
                $specificRegion->addChild('show_in_website', 1);
                $specificRegion->addChild('show_in_store', 0);
                $specificRegion->addChild('can_be_empty', 1);
                
            }
            	
        }
    
        return $this;
    }
        
    /**
     * 
     * Disable carrier if carrier region disabled
     * 
     * @param Varien_Event_Observer $observer
     * @return Aydus_ShipToRegion_Model_Observer
     */
    public function checkShipToRegion($observer)
    {
        $quoteAddress = $observer->getQuoteAddress();
        
        if ($quoteAddress->getAddressType()=='shipping'){
            
            $shippingAddress = $quoteAddress;
            
            $quote = $shippingAddress->getQuote();
            $quoteId = $quote->getId();
            
            $store = Mage::app()->getStore($quote->getStoreId());
            $carriers = $store->getConfig('carriers');
            
            foreach ($carriers as $code => $configAr){
                
                if ($configAr['active']){
                    
                    $specificRegion = $configAr['specificregion'];
                    
                    $regionIds = explode(',',$specificRegion);
                    
                    if (is_array($regionIds) && count($regionIds) && (int)$regionIds[0] > 0){
                        
                        $regionId = (int)$shippingAddress->getRegionId();
                        
                        if ($regionId){
                            
                            if (!in_array($regionId, $regionIds)){
                                
                                $store->setConfig('carriers/'.$code.'/active', 0);
                            }
                            
                        }
                        
                    }
                                        
                }
                
            }
            
        }
        
        return $this;
    }
    
}