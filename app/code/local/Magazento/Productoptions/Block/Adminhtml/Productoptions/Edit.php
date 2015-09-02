<?php

class Magazento_Productoptions_Block_Adminhtml_Productoptions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'productoptions';
        $this->_controller = 'adminhtml_productoptions';

        $this->_updateButton('save', 'label', Mage::helper('productoptions')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('productoptions')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\'' . $this->getSaveAndContinueUrl() . '\')',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productoptions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productoptions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productoptions_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('productoptions_data') && Mage::registry('productoptions_data')->getId()) {
            return Mage::helper('productoptions')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('productoptions_data')->getTitle()));
        } else {
            return Mage::helper('productoptions')->__('Add Item');
        }
    }

    public function getSaveAndContinueUrl() {
        return $this->getUrl('*/*/save', array(
                    '_current' => true,
                    'back' => 'edit',
                    'tab' => '{{tab_id}}',
                    'active_tab' => null
                ));
    }

}