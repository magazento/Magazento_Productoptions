<?php
class Magazento_Productoptions_Block_Adminhtml_Productoptions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productoptions';
    $this->_blockGroup = 'productoptions';
    $this->_headerText = Mage::helper('productoptions')->__('Manage Options Packs');
    $this->_addButtonLabel = Mage::helper('productoptions')->__('Add Options Pack');
    parent::__construct();
  }
}