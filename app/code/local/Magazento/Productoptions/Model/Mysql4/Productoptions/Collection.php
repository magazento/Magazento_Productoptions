<?php

class Magazento_Productoptions_Model_Mysql4_Productoptions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
//class Magazento_Productoptions_Model_Mysql4_Productoptions_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productoptions/productoptions');
    }
    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStoreId($storeId) {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int) $storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId() {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

}