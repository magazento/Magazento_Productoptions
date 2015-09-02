<?php

class Magazento_Productoptions_Model_Mysql4_Productoptions extends Mage_Core_Model_Mysql4_Abstract //class Magazento_Productoptions_Model_Mysql4_Productoptions extends Mage_Core_Model_Mysql4_Abstract 
 {
    public function _construct()
    {    
        // Note that the productoptions_id refers to the key field in your database table.
        $this->_init('productoptions/productoptions', 'set_id');
    }
     protected function _afterSave(Mage_Core_Model_Abstract $object) {
          $this->__saveToStoreTable($object);
        return parent::_afterSave($object);
     }
     
      private function __saveToStoreTable(Mage_Core_Model_Abstract $object) {
        if (!$object->getData('stores')) {
            $condition = $this->_getWriteAdapter()->quoteInto('productoptions_id = ?', $object->getId());
            $this->_getWriteAdapter()->delete($this->getTable('productoptions/productoptions_stores'), $condition);

            $storeArray = array(
                'productoptions_id' => $object->getId(),
                'store_id' => '0');
            $this->_getWriteAdapter()->insert(
                    $this->getTable('productoptions/productoptions_stores'), $storeArray);
            return true;
        }

        $condition = $this->_getWriteAdapter()->quoteInto('productoptions_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('productoptions/productoptions_stores'), $condition);
        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array(
                'productoptions_id' => $object->getId(),
                'store_id' => $store);
            $this->_getWriteAdapter()->insert(
                    $this->getTable('productoptions/productoptions_stores'), $storeArray);
        }
    }
protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        if (!$object->getIsMassDelete()) {
            $object = $this->__loadStore($object);
            
        }

        return parent::_afterLoad($object);
    }
     private function __loadStore(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('productoptions/productoptions_stores'))
                ->where('productoptions_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('store_id', $array);
        }
        return $object;
    }
    
}