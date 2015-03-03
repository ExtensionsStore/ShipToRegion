<?php

/**
 * Regions
 *
 * @category   Aydus
 * @package	   Aydus_ShipToRegion
 * @author     Aydus Consulting <davidt@aydus.com>
 */

class Aydus_ShipToRegion_Model_Adminhtml_System_Config_Source_Region
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            
            $collection = Mage::getResourceModel('directory/region_collection');
                        
            $allowedCountries = Mage::getStoreConfig("general/country/allow");
            $countryIds = explode(',',$allowedCountries);
            if (is_array($countryIds) && count($countryIds)>0 && $countryIds[0]){
                $collection->addFieldToFilter('country_id',array('in'=>$countryIds));
            }
                        
            $this->_options = $collection->loadData()->toOptionArray(false);
            
        }
        
        $options = $this->_options;
        
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }
    
        return $options;
    }    
    

}
