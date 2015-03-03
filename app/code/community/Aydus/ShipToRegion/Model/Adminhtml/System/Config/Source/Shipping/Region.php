<?php

/**
 * Regions
 *
 * @category   Aydus
 * @package	   Aydus_ShipToRegion
 * @author     Aydus Consulting <davidt@aydus.com>
 */

class Aydus_ShipToRegion_Model_Adminhtml_System_Config_Source_Shipping_Region extends Varien_Object
{

    public function toOptionArray($isMultiselect=false)
    {
        $collection = Mage::getResourceModel('directory/region_collection');
        
        if ($this->getPath()){
            $configPath = explode('/',$this->getPath());
            $carrier = $configPath[1];
            $specificCountry = Mage::getStoreConfig("carriers/$carrier/specificcountry");
            $countryIds = explode(',',$specificCountry);
            if (is_array($countryIds) && count($countryIds)>0 && $countryIds[0]){
                $collection->addFieldToFilter('country_id',array('in'=>$countryIds));
            }
        }
        
        $options = $collection->loadData()->toOptionArray(false);
        
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}
