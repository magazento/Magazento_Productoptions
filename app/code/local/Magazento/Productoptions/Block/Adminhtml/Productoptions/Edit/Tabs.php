<?php

class Magazento_Productoptions_Block_Adminhtml_Productoptions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('productoptions_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('productoptions')->__('Item Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('productoptions')->__('Pack Info'),
            'title' => Mage::helper('productoptions')->__('General information'),
            'content' => $this->getLayout()->createBlock('productoptions/adminhtml_productoptions_edit_tab_form')->toHtml(),
        ));
        $this->addTab('rules', array(
            'label' => Mage::helper('productoptions')->__('Options'),
            'title' => Mage::helper('productoptions')->__('Options'),
            'url' => $this->getUrl('*/*/options', array('_current' => true)),
            'class' => 'ajax',
        ));

        $this->addTab('options2', array(
            'label' => Mage::helper('catalog')->__('Apply To'),
            'url' => $this->getUrl('*/*/applyto', array('_current' => true)),
            'class' => 'ajax',
        ));
        return parent::_beforeToHtml();
    }

}