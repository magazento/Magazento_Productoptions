<?php

class Magazento_Productoptions_Block_Adminhtml_Productoptions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productoptions_form', array('legend' => Mage::helper('productoptions')->__('Item information')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('productoptions')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('description', 'text', array(
            'label' => Mage::helper('productoptions')->__('Description'),
            'required' => false,
            'name' => 'description',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('productoptions')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('productoptions')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('productoptions')->__('Disabled'),
                ),
            ),
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'label' => Mage::helper('productoptions')->__('Visible In'),
                'required' => false,
                'name' => 'stores[]',
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                    //   'value'     => $_model->getStoreId()
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('productoptions_data')->setData('stores',Mage::app()->getStore(true)->getId());
        }


        if (Mage::getSingleton('adminhtml/session')->getProductoptionsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getProductoptionsData());
            Mage::getSingleton('adminhtml/session')->setProductoptionsData(null);
        } elseif (Mage::registry('productoptions_data')) {
            $form->setValues(Mage::registry('productoptions_data')->getData());
        }
        return parent::_prepareForm();
    }

}