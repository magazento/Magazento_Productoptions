<?php

class Magazento_Productoptions_Model_Product_Option_Type_File extends  Mage_Catalog_Model_Product_Option_Type_File //extends Mage_Catalog_Model_Product_Option_Type_Default
{
//    public function getFormattedOptionValue($optionValue) {
//        if ($this->_formattedOptionValue === null) {
//            try {
//                $value = unserialize($optionValue);
//
//                $customOptionUrlParams = $this->getCustomOptionUrlParams()
//                        ? $this->getCustomOptionUrlParams() : array(
//                    'id' => $this->getConfigurationItemOption()->getId(),
//                    'key' => $value['secret_key']
//                        );
//
//                $value['url'] = array('route' => $this->_customOptionDownloadUrl, 'params' => $customOptionUrlParams);
//                //  Mage::log( $value['url'],null,'magento.log');
//                $this->_formattedOptionValue = $this->_getOptionHtml($value);
//             //   $this->getConfigurationItemOption()->setValue(serialize($value));
//                //   Mage::log($this->_formattedOptionValue, null, 'magento.log');
//                return $this->_formattedOptionValue;
//           } catch (Exception $e) {
//                // Mage::log()
//               // var_dump($e);
//               // return $optionValue;
//            //   return $this->_formattedOptionValue;
//                //   return $e;
//           }
//        }
//        return $this->_formattedOptionValue;
//    }
//
}
