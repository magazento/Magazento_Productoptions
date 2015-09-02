<?php

class Magazento_Productoptions_Block_Adminhtml_Productoptions_Edit_Tab_Applyto extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('productoptions_applyto_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');

        $this->setUseAjax(true);
        if ((int) $this->getRequest()->getParam('id', 0)) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('attribute_set_id')
                ->addAttributeToSelect('type_id')
                ->addAttributeToSelect('status');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareColumns() {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            //   'field_name'=> 'applyto_prodlist[]',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'width' => '50px',
            'type' => 'number',
            'index' => 'entity_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
        ));
        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
                'index' => 'custom_name',
            ));
        }

        $this->addColumn('type', array(
            'header' => Mage::helper('catalog')->__('Type'),
            'width' => '60px',
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();

        $this->addColumn('set_name', array(
            'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width' => 130,
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
        ));
        $store = $this->_getStore();
        $this->addColumn('price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index' => 'price',
        ));
        $this->addColumn('mstatus', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '70px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
//        $this->addColumn('position', array(
//            'header'            => Mage::helper('catalog')->__('ID'),
//            'name'              => 'position',
//            'width'             => 60,
//            'type'              => 'number',
//            'validate_class'    => 'validate-number',
//            'index'             => 'position',
//            'editable'          => true,
//            'edit_only'         => true
//            ));
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/applytoGrid', array('_current' => true));
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _getSelectedProducts() {
        $products = $this->getRequest()->getPost('products_related', null);
        if (!is_array($products)) {

            $sId = Mage::app()->getFrontController()->getRequest()->getParams();
            if (isset($sId)) {
                $prodinset = Mage::getResourceModel('productoptions/prodinset');
                $products = $prodinset->getSetsProducts($sId['id']);
            }
        }
        return $products;
    }

    public function getSelectedApplytoProducts() {
        $products = array();

        $sId = Mage::app()->getFrontController()->getRequest()->getParams();
        if (isset($sId)) {
            $prodinset = Mage::getResourceModel('productoptions/prodinset');
            $products = $prodinset->getSetsProducts($sId['id']);
        }
        return $products;
    }

}

?>
