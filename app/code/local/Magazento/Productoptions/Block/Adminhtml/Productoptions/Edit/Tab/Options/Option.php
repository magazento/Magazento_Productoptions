<?php

class Magazento_Productoptions_Block_Adminhtml_Productoptions_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('magazento_productoptions/options/option.phtml');
        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    protected function _prepareLayout() {
        $this->setChild('delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Delete Option'),
                            'class' => 'delete delete-product-option '
                        ))
        );

        $path = 'global/catalog/product/options/custom/groups';

        foreach (Mage::getConfig()->getNode($path)->children() as $group) {
            $this->setChild($group->getName() . '_option_type', $this->getLayout()->createBlock(
                            'productoptions/adminhtml_productoptions_edit_tab_options_type_' . $group->getName()
                    )
            );
        }

        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }

    public function getAddButtonId() {
        $buttonId = $this->getLayout()
                        ->getBlock('tab.options')
                        ->getChild('add_button')->getId();
        return $buttonId;
    }

    public function setSet($set) {
        $this->_productInstance = $set;
        return $this;
    }

    public function getSet() {
        $sId = Mage::app()->getFrontController()->getRequest()->getParams();
        if (isset($sId)) {
            $model = Mage::getModel('productoptions/productoptions');

            return $model->load($sId['id']);
        } else {
            if (!$this->_productInstance) {
                if ($product = Mage::registry('product')) {
                    $this->_productInstance = $product;
                } else {
                    $this->_productInstance = Mage::getSingleton('catalog/product');
                }
            }

            return $this->_productInstance;
        }
    }

    public function getOptionValues() {

        $optionsArr = array_reverse($this->getSet()->getOptions(), true);

        if (!$this->_values) {
            $showPrice = $this->getCanReadPrice();
            $values = array();
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            foreach ($optionsArr as $option) {
                /* @var $option Mage_Catalog_Model_Product_Option */

                $this->setItemCount($option->getOptionId());

                $value = array();

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $this->htmlEscape($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['can_edit_price'] = $this->getCanEditPrice();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'title', is_null($option->getStoreTitle()));
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle()) ? 'disabled' : null;
                }

                if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {

                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        /* @var $_value Mage_Catalog_Model_Product_Option_Value */
                        $value['optionValues'][$i] = array(
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $this->htmlEscape($_value->getTitle()),
                            'price' => ($showPrice) ? $this->getPriceValue($_value->getPrice(), $_value->getPriceType()) : '',
                            'price_type' => ($showPrice) ? $_value->getPriceType() : 0,
                            'sku' => $this->htmlEscape($_value->getSku()),
                            'sort_order' => $_value->getSortOrder(),
                        );

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                    $_value->getOptionId(), 'title', is_null($_value->getStoreTitle()), $_value->getOptionTypeId());
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($_value->getStoreTitle()) ? 'disabled' : null;
                            if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                        $_value->getOptionId(), 'price', is_null($_value->getstorePrice()), $_value->getOptionTypeId());
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($_value->getStorePrice()) ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = ($showPrice) ? $this->getPriceValue($option->getPrice(), $option->getPriceType()) : '';
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->htmlEscape($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0' &&
                            $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'price', is_null($option->getStorePrice()));
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice()) ? 'disabled' : null;
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }
     public function getTypeSelectHtml() {
         $list=Mage::getSingleton('adminhtml/system_config_source_product_options_type')->toOptionArray();
         $i=0;
         foreach ($list as $opType)
         {
//             if ($opType['label']=='File') {unset($list[$i]);}
             $i++;
         }
    
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData(array(
                    'id' => $this->getFieldId() . '_{{id}}_type',
                    'class' => 'select select-product-option-type required-option-select'
                ))
                ->setName($this->getFieldName() . '[{{id}}][type]')
                ->setOptions($list);

        return $select->getHtml();
    }

}

?>
