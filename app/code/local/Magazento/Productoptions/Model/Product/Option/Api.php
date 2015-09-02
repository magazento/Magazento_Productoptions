<?php
class Magazento_Productoptions_Model_Product_Option_Api extends Mage_Catalog_Model_Product_Option_Api //extends Mage_Catalog_Model_Api_Resource
{

    /**
     * Update product custom option data
     *
     * @param string $optionId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function update($optionId, $data, $store = null)
    {
        /** @var $option Mage_Catalog_Model_Product_Option */
        $option = Mage::getModel('productoptions/product_option')->load($optionId);
        if (!$option->getId()) {
            $this->_fault('option_not_exists');
        }
        $product = $this->_getProduct($option->getProductId(), $store, null);
        $option = $product->getOptionById($optionId);
        if (isset($data['type']) and !$this->_isTypeAllowed($data['type'])) {
            $this->_fault('invalid_type');
        }
        if (isset($data['additional_fields'])) {
            $this->_prepareAdditionalFields(
                $data,
                $option->getGroupByType()
            );
        }
        foreach ($option->getValues() as $valueId => $value) {
            if(isset($data['values'][$valueId])) {
                $data['values'][$valueId] = array_merge($value->getData(), $data['values'][$valueId]);
            }
        }
        $data = array_merge($option->getData(), $data);
        $this->_saveProductCustomOption($product, $data);
        return true;
    }

 
    /**
     * Get full information about custom option in product
     *
     * @param int|string $optionId
     * @param  int|string|null $store
     * @return array
     */
    public function info($optionId, $store = null)
    {
        /** @var $option Mage_Catalog_Model_Product_Option */
        $option = Mage::getModel('productoptions/product_option')->load($optionId);
        if (!$option->getId()) {
            $this->_fault('option_not_exists');
        }
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct($option->getProductId(), $store, null);
        $option = $product->getOptionById($optionId);
        $result = array(
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'is_require' => $option->getIsRequire(),
            'sort_order' => $option->getSortOrder(),
            // additional_fields should be two-dimensional array for all option types
            'additional_fields' => array(
                array(
                    'price' => $option->getPrice(),
                    'price_type' => $option->getPriceType(),
                    'sku' => $option->getSku()
                )
            )
        );
        // Set additional fields to each type group
        switch ($option->getGroupByType()) {
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_TEXT:
                $result['additional_fields'][0]['max_characters'] = $option->getMaxCharacters();
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_FILE:
                $result['additional_fields'][0]['file_extension'] = $option->getFileExtension();
                $result['additional_fields'][0]['image_size_x'] = $option->getImageSizeX();
                $result['additional_fields'][0]['image_size_y'] = $option->getImageSizeY();
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT:
                $result['additional_fields'] = array();
                foreach ($option->getValuesCollection() as $value) {
                    $result['additional_fields'][] = array(
                        'value_id' => $value->getId(),
                        'title' => $value->getTitle(),
                        'price' => $value->getPrice(),
                        'price_type' => $value->getPriceType(),
                        'sku' => $value->getSku(),
                        'sort_order' => $value->getSortOrder()
                    );
                }
                break;
            default:
                break;
        }

        return $result;
    }

  
    /**
     * Remove product custom option
     *
     * @param string $optionId
     * @return boolean
     */
    public function remove($optionId)
    {
        /** @var $option Mage_Catalog_Model_Product_Option */
        $option = Mage::getModel('productoptions/product_option')->load($optionId);
        if (!$option->getId()) {
            $this->_fault('option_not_exists');
        }
        try {
            $option->getValueInstance()->deleteValue($optionId);
            $option->deletePrices($optionId);
            $option->deleteTitles($optionId);
            $option->delete();
        } catch (Exception $e){
            $this->fault('delete_option_error');
        }
        return true;
    }

    
}
