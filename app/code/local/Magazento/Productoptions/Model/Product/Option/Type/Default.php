<?php

class Magazento_Productoptions_Model_Product_Option_Type_Default extends Mage_Catalog_Model_Product_Option_Type_Default //extends Varien_Object
{
    /**
     * Getter for Configuration Item Option
     *
     * @return Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
     */
    public function getConfigurationItemOption() {//MAge::log($this->_getData('quote_item_option'));
        if ($this->_getData('configuration_item_option') instanceof Mage_Catalog_Model_Product_Configuration_Item_Option_Interface) {
            return $this->_getData('configuration_item_option');
        }

        // Back compatibility with quote specific keys to set configuration item options
        if ($this->_getData('quote_item_option') instanceof Mage_Sales_Model_Quote_Item_Option) {
            return $this->_getData('quote_item_option');
        }

  //      Mage::throwException(Mage::helper('catalog')->__('Wrong configuration item option instance in options group.'));
    }

}
