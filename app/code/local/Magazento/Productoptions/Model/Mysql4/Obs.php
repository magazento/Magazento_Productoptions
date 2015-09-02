<?php
class Magazento_Productoptions_Model_Mysql4_Obs extends Mage_Core_Model_Mysql4_Abstract{
     
    public function _construct() {
          $this->_init('sales/quote_item', 'item_id');
    }
    public function getCurrentCartItem() {
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
Mage::log($quoteId);
        $select = $this->_getReadAdapter()->select()
                ->from(array('qo' => $this->getTable('sales/quote_item')), array('max(item_id) as mitem_id'))
                ->where('qo.quote_id=?', $quoteId);
        return $this->_getReadAdapter()->fetchCol($select);
    }
}

?>
