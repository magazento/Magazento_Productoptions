<?php

class Magazento_Productoptions_Block_Adminhtml_Productoptions_Edit_Tab_Options extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
public function __construct()
    {
        parent::__construct();
        $this->setTemplate('magazento_productoptions/options.phtml');
    }


    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add New Option'),
                    'class' => 'add',
                    'id'    => 'add_new_defined_option'
                ))
        );

       $this->setChild('options_box',
           $this->getLayout()->createBlock('productoptions/adminhtml_productoptions_edit_tab_options_option')
               
       );

        return parent::_prepareLayout();
    }

   public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getOptionsBoxHtml()
    {
        return  $this->getChildHtml('options_box');
    }

}
   


?>
