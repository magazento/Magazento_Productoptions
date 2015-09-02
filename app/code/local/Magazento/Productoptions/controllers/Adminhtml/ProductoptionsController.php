<?php

class Magazento_Productoptions_Adminhtml_ProductoptionsController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('productoptions/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('productoptions/productoptions')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('productoptions_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('productoptions/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('productoptions/adminhtml_productoptions_edit'))
                    ->_addLeft($this->getLayout()->createBlock('productoptions/adminhtml_productoptions_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productoptions')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('productoptions/productoptions');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            $productData = $this->getRequest()->getPost('product');

            if (isset($productData['options'])) {
                $model->setProductOptions($productData['options']);
                $model->setCanSaveCustomOptions(true);
            }

            try {
                $model->save();

                $productinset = Mage::getResourceModel('productoptions/prodinset');
                $oldprodinset = $productinset->getSetsProducts($this->getRequest()->getParam('id'));
                if ($oldprodinset) {
                    $productinset->deleteSetsProducts($model->getId(), $oldprodinset);
                }
                if (isset($data['links']['applyto'])) {
                    $link_Data = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['applyto']);

                    if ($link_Data) {
                        foreach ($link_Data as $prodId) {
                            $productinset->addAssoc($model->getId(), $prodId);
                        }
                    }
                }

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productoptions')->__('Unable to find item to save'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('productoptions/productoptions');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $productoptionsIds = $this->getRequest()->getParam('productoptions');
        if (!is_array($productoptionsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($productoptionsIds as $productoptionsId) {
                    $productoptions = Mage::getModel('productoptions/productoptions')->load($productoptionsId);
                    $productoptions->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($productoptionsIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $productoptionsIds = $this->getRequest()->getParam('productoptions');
        if (!is_array($productoptionsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productoptionsIds as $productoptionsId) {
                    $productoptions = Mage::getSingleton('productoptions/productoptions')
                            ->load($productoptionsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($productoptionsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function applytoAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('applyto.grid')
                ->setApplytoProdlist($this->getRequest()->getPost('applyto_prodlist', null));
        $this->renderLayout();
    }

    public function applytogridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('applyto.grid')
                ->setApplytoProdlist($this->getRequest()->getPost('applyto_prodlist', null));
        $this->renderLayout();
    }

    public function optionsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('tab.options');
        $this->renderLayout();
    }

}