<?php

class Magazento_Productoptions_Model_Mysql4_Prodinset extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('productoptions/prodinset', 'prodinset_id');
    }

    public function getSetsProducts($setId) {
        $needsfields = array('product_id');

        $stmt = $this->_getReadAdapter()->select()
                ->from($this->getTable('productoptions/prodinset'), $needsfields)
                ->where('set_id = ?', $setId);

        return $this->_getReadAdapter()->fetchCol($stmt);
    }

    public function getSetslist($productId) {
        $query = $this->_getReadAdapter()->select()
                ->from(array('main_table'=>$this->getTable('productoptions/prodinset')), array('set_id'))
                ->join( array('table_alias'=>$this->getTable('productoptions/productoptions_stores')), 'main_table.set_id = table_alias.productoptions_id')
                ->where('product_id = ?', $productId)
                ->where('store_id= ?',Mage::app()->getStore()->getId());
        
        return $this->_getReadAdapter()->fetchCol($query);
    }

    public function addAssoc($setId, $productId) {
        $this->_getWriteAdapter()->insert(
                $this->getTable('productoptions/prodinset'), array(
            'product_id' => $productId,
            'set_id' => $setId,
//    	        'sort_order'       => $aData['sort_order'],
#    	        'required_options' => $oTemplate->getData('required_options'),
                )
        );
    }

    public function deleteSetsProducts($setId, $productIds) {

        $this->_getWriteAdapter()->delete(
                $this->getTable('productoptions/prodinset'), 'set_id = "' . intval($setId) . '" AND product_id IN (' . implode(',', $productIds) . ')'
        );
    }

    public function deleteSetsAssoc($setId) {
       Mage::log(intval($setId));
        $this->_getWriteAdapter()->delete(
                $this->getTable('productoptions/prodinset'), 'set_id =' . intval($setId));
       
    }
}

?>
