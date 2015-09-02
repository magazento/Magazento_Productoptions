<?php

class Magazento_Productoptions_Model_Productoptions extends Mage_Catalog_Model_Product {

    protected $_canAffectOptions = false;

    /**
     * Product type instance
     *
     * @var Mage_Catalog_Model_Product_Type_Abstract
     */
    protected $_typeInstance = null;

    /**
     * Product type instance as singleton
     */
    protected $_typeInstanceSingleton = null;

    /**
     * Product link instance
     *
     * @var Mage_Catalog_Model_Product_Link
     */
    protected $_linkInstance;

    /**
     * Product object customization (not stored in DB)
     *
     * @var array
     */
    protected $_customOptions = array();

    /**
     * Product Url Instance
     *
     * @var Mage_Catalog_Model_Product_Url
     */
    protected $_urlModel = null;
    protected static $_url;
    protected static $_urlRewrite;
    protected $_errors = array();
    protected $_optionInstance;
    protected $_options = array();

    public function _construct() {
        parent::_construct();
        $this->_init('productoptions/productoptions');
    }

    protected function _beforeSave() {

        $this->setTypeHasOptions(false);
        $this->setTypeHasRequiredOptions(false);
        $hasOptions = false;
        $hasRequiredOptions = false;

        /**
         * $this->_canAffectOptions - set by type instance only
         * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
         * or in type instance as well
         */
        $this->canAffectOptions($this->_canAffectOptions && $this->getCanSaveCustomOptions());
        if ($this->getCanSaveCustomOptions()) {
            $options = $this->getProductOptions();
            if (is_array($options)) {
            //    var_dump($options);
                $this->setIsCustomOptionChanged(true);
                foreach ($this->getProductOptions() as $option) {
                    $this->getOptionInstance()->addOption($option);
                    if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
                        $hasOptions = true;
                    }
                }
                foreach ($this->getOptionInstance()->getOptions() as $option) {
                    if ($option['is_require'] == '1') {
                        $hasRequiredOptions = true;
                        break;
                    }
                }
            }
        }

        /**
         * Set true, if any
         * Set false, ONLY if options have been affected by Options tab and Type instance tab
         */
        if ($hasOptions || (bool) $this->getTypeHasOptions()) {
            $this->setHasOptions(true);
            if ($hasRequiredOptions || (bool) $this->getTypeHasRequiredOptions()) {
                $this->setRequiredOptions(true);
            } elseif ($this->canAffectOptions()) {
                $this->setRequiredOptions(false);
            }
        } elseif ($this->canAffectOptions()) {
            $this->setHasOptions(false);
            $this->setRequiredOptions(false);
        }
        //   parent::_beforeSave();
    }

    /**
     * Check/set if options can be affected when saving product
     * If value specified, it will be set.
     *
     * @param   bool $value
     * @return  bool
     */
    public function canAffectOptions($value = null) {
        if (null !== $value) {
            $this->_canAffectOptions = (bool) $value;
        }
        return $this->_canAffectOptions;
    }

    /**
     * Saving product type related data and init index
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _afterSave() {
        $this->getOptionInstance()->setOpset($this)
                ->saveOptions();
    }

    protected function _afterLoad() {

        foreach ($this->getProductOptionsCollection() as $option) {
            $option->setOpset($this);
            $this->addOption($option);
        }
        return $this;
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _getResource() {
        return parent::_getResource();
    }

    /**
     * Retrieve option instance
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOptionInstance() {
        if (!$this->_optionInstance) {
            $this->_optionInstance = Mage::getSingleton('productoptions/product_option');
        }
        return $this->_optionInstance;
    }

    /**
     * Retrieve options collection of product
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
     */
    public function getProductOptionsCollection() {
        $collection = $this->getOptionInstance()
                ->getProductOptionCollection($this);

        return $collection;
    }

    /**
     * Add option to array of product options
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return Mage_Catalog_Model_Product
     */
    //   public function addOption(Magazento_Productoptions_Model_Product_Option $option)
    public function addOption(Mage_Catalog_Model_Product_Option $option) {
        $this->_options[$option->getId()] = $option;
        return $this;
    }

    /**
     * Get option from options array of product by given option id
     *
     * @param int $optionId
     * @return Mage_Catalog_Model_Product_Option | null
     */
    public function getOptionById($optionId) {
        if (isset($this->_options[$optionId])) {
            return $this->_options[$optionId];
        }

        return null;
    }

    /**
     * Get all options of product
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Add custom option information to product
     *
     * @param   string $code    Option code
     * @param   mixed  $value   Value of the option
     * @param   int    $product Product ID
     * @return  Mage_Catalog_Model_Product
     */
    public function addCustomOption($code, $value, $product = null) {
        $product = $product ? $product : $this;
        $option = Mage::getModel('catalog/product_configuration_item_option')
                ->addData(array(
            'product_id' => $product->getId(),
            'product' => $product,
            'code' => $code,
            'value' => $value,
                ));
        $this->_customOptions[$code] = $option;
        return $this;
    }

    /**
     * Sets custom options for the product
     *
     * @param array $options Array of options
     * @return void
     */
    public function setCustomOptions(array $options) {
        $this->_customOptions = $options;
    }

    /**
     * Get all custom options of the product
     *
     * @return array
     */
    public function getCustomOptions() {
        return $this->_customOptions;
    }

    /**
     * Get product custom option info
     *
     * @param   string $code
     * @return  array
     */
    public function getCustomOption($code) {
        if (isset($this->_customOptions[$code])) {
            return $this->_customOptions[$code];
        }
        return null;
    }

    /**
     * Checks if there custom option for this product
     *
     * @return bool
     */
    public function hasCustomOptions() {
        if (count($this->_customOptions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prepare product custom options.
     * To be sure that all product custom options does not has ID and has product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function prepareCustomOptions() {
        foreach ($this->getCustomOptions() as $option) {
            if (!is_object($option->getProduct()) || $option->getId()) {
                $this->addCustomOption($option->getCode(), $option->getValue());
            }
        }

        return $this;
    }

    /**
     * Clearing product's data
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _clearData() {
        foreach ($this->_data as $data) {
            if (is_object($data) && method_exists($data, 'reset')) {
                $data->reset();
            }
        }

        $this->setData(array());
        $this->setOrigData();
        $this->_customOptions = array();
        $this->_optionInstance = null;
        $this->_options = array();
        $this->_canAffectOptions = false;
        $this->_errors = array();

        return $this;
    }

    /**
     * Clearing references to product from product's options
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _clearOptionReferences() {
        /**
         * unload product options
         */
        if (!empty($this->_options)) {
            foreach ($this->_options as $key => $option) {
                $option->setProduct();
                $option->clearInstance();
            }
        }

        return $this;
    }

    public function getSetId() {
        return $this->_getData('set_id');
    }

    public function getStoreId() {
        return 0;
    }

    /**
     * Get collection instance
     *
     * @return object
     */
    public function getResourceCollection() {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('catalog')->__('The model collection resource name is not defined.'));
        }
        $collection = Mage::getResourceModel($this->_resourceCollectionName);
        $collection->setStoreId($this->getStoreId());
        return $collection;
    }

    public function delete() {
        Mage_Core_Model_Abstract::delete();
        return $this;
    }

    protected function _afterDelete() {
        
    }

    protected function _beforeDelete() {
        // delete all sets options
        foreach ($this->getProductOptionsCollection() as $option) {
            $option->delete();
        }
        $model = Mage::getResourceModel('productoptions/prodinset');
        $model->deleteSetsAssoc($this->getId());
    }

    public function afterCommitCallback() {
        //     parent::afterCommitCallback();
        return $this;
    }

}