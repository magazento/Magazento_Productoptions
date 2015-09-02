<?php

class Magazento_Productoptions_Model_Product_Option_Value extends Mage_Catalog_Model_Product_Option_Value {

    protected $_values = array();
    protected $_product;
    protected $_option;

    protected function _construct() {
        $this->_init('productoptions/product_option_value');
    }

    public function addValue($value) {
        $this->_values[] = $value;
        return $this;
    }

    public function getValues() {
        return $this->_values;
    }

    public function setValues($values) {
        $this->_values = $values;
        return $this;
    }

    public function unsetValues() {
        $this->_values = array();
        return $this;
    }

    //public function setOption(Magazento_Productoptions_Model_Product_Option $option) {
    public function setOption(Mage_Catalog_Model_Product_Option $option) {
        $this->_option = $option;
        return $this;
    }

    public function unsetOption() {
        $this->_option = null;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOption() {
        return $this->_option;
    }

    public function setProduct($product) {
        $this->_product = $product;
        return $this;
    }

    public function getProduct() {
        if (is_null($this->_product)) {
            $this->_product = $this->getOption()->getProduct();
        }
        return $this->_product;
    }

    public function saveValues() {
        foreach ($this->getValues() as $value) {
            $this->setData($value)
                    ->setData('option_id', $this->getOption()->getId())
                    ->setData('store_id', $this->getOption()->getStoreId());

            if ($this->getData('option_type_id') == '-1') {//change to 0
                $this->unsetData('option_type_id');
            } else {
                $this->setId($this->getData('option_type_id'));
            }

            if ($this->getData('is_delete') == '1') {
                if ($this->getId()) {
                    $this->deleteValues($this->getId());
                    $this->delete();
                }
            } else {
                $this->save();
            }
        }//eof foreach()
        return $this;
    }

    /**
     * Return price. If $flag is true and price is percent
     *  return converted percent to price
     *
     * @param bool $flag
     * @return decimal
     */
    public function getPrice($flag = false) {

        if ($flag && $this->getPriceType() == 'percent') {
            $basePrice = $this->getOption()->getProduct()->getPrice();
            $price = $basePrice * ($this->_getData('price') / 100);
            return $price;
        }
        return $this->_getData('price');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Value_Collection
     */
    public function getValuesCollection(Mage_Catalog_Model_Product_Option $option) {

        $res_name = 'productoptions/product_option_value_collection';
        if (version_compare(Mage::getVersion(), '1.6.0', '<')) {
            $res_name = 'productoptions/product_option_value_collection2';
        }

        $collection = Mage::getResourceModel($res_name)
                ->addFieldToFilter('option_id', $option->getId())
                ->getValues($option->getStoreId());

        return $collection;
    }

    public function getValuesByOption($optionIds, $option_id, $store_id) {
        $res_name = 'productoptions/product_option_value_collection';
        if (version_compare(Mage::getVersion(), '1.6.0', '<')) {
            $res_name = 'productoptions/product_option_value_collection2';
        }
        $collection = Mage::getResourceModel($res_name)
                ->addFieldToFilter('option_id', $option_id)
                ->getValuesByOption($optionIds, $store_id);

        return $collection;
    }

    public function deleteValue($option_id) {
        $this->getResource()->deleteValue($option_id);
        return $this;
    }

    public function deleteValues($option_type_id) {
        $this->getResource()->deleteValues($option_type_id);
        return $this;
    }

    /**
     * Prepare array of option values for duplicate
     *
     * @return array
     */
    public function prepareValueForDuplicate() {
        $this->setOptionId(null);
        $this->setOptionTypeId(null);
        return $this->__toArray();
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _getResource() {
        if (version_compare(Mage::getVersion(), '1.6.0', '<')) {
            $this->_resourceName = 'productoptions/product_option_value2';
        }
        if (empty($this->_resourceName)) {
            Mage::throwException(Mage::helper('core')->__('Resource is not set.'));
        }
        return Mage::getResourceSingleton($this->_resourceName);
    }

    /**
     * Get collection instance
     *
     * @return object
     */
    public function getResourceCollection() {
        if (version_compare(Mage::getVersion(), '1.6.0', '<')) {
            $this->_resourceCollectionName = 'productoptions/product_option_value_collection2';
        }
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }

        return Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
    }

    /**
     * Duplicate product options value
     *
     * @param int $oldOptionId
     * @param int $newOptionId
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function duplicate($oldOptionId, $newOptionId) {
        $this->getResource()->duplicate($this, $oldOptionId, $newOptionId);
        return $this;
    }

    public function getCollection() {
        return $this->getResourceCollection();
    }

}
