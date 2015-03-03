<?php

/**
 * Directory data helper
 *
 * @category   Aydus
 * @package    Aydus_ShipToRegion
 * @author     Aydus Consulting <davidt@aydus.com>
 */

class Aydus_ShipToRegion_Helper_Directory_Data extends Mage_Directory_Helper_Data
{

    /**
     * Get Regions for specific Countries
     * @param string $storeId
     * @return array|null
     */
    protected function _getRegions($storeId)
    {
        $countryIds = array();

        $countryCollection = $this->getCountryCollection()->loadByStore($storeId);
        foreach ($countryCollection as $country) {
            $countryIds[] = $country->getCountryId();
        }

        /** @var $regionModel Mage_Directory_Model_Region */
        $regionModel = $this->_factory->getModel('directory/region');
        /** @var $collection Mage_Directory_Model_Resource_Region_Collection */
        $collection = $regionModel->getResourceCollection()
            ->addCountryFilter($countryIds);
        
        //start filter by allowed regions - Aydus_ShipToRegion
        $allowedRegions = Mage::getStoreConfig('general/region/allow');
        $allowedRegionsAr = explode(',',$allowedRegions);

        if (is_array($allowedRegionsAr) && count($allowedRegionsAr)>0 && (int)$allowedRegionsAr[0]){
            $collection->addFieldToFilter('main_table.region_id', array('in'=>$allowedRegionsAr));
        }
        //end filter - Aydus_ShipToRegion
        $collection->load();
        
        $regions = array(
            'config' => array(
                'show_all_regions' => $this->getShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired()
            )
        );
        foreach ($collection as $region) {
            if (!$region->getRegionId()) {
                continue;
            }
            $regions[$region->getCountryId()][$region->getRegionId()] = array(
                'code' => $region->getCode(),
                'name' => $this->__($region->getName())
            );
        }
        return $regions;
    }


}
