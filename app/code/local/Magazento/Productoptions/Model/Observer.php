<?php

class Magazento_Productoptions_Model_Observer {

    public function Add_ProductOptions($observer) {
        $product = $observer->getEvent()->getProduct();
        if ($product->getParentItem()) {
            $product = $product->getParentItem();
        }
        
        $prodinset = Mage::getResourceModel('productoptions/prodinset');
        $setslist = $prodinset->getSetslist($product->getId());
        if ($setslist) {
            
            $product->setHasOptions(1);
            foreach ($setslist as $setId) {
                $modeloplist = Mage::getModel('productoptions/productoptions');
                $set = $modeloplist->load($setId);
                if ($set->getStatus() == '1') {
                    foreach ($set->getOptions() as $option) {
                        $option->setProduct($product);
                        $product->addOption($option);
                    }
                }
            }
        }
    }

    public function Add_ProductOptionsToCard($observer) {
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if ($product->getParentItem()) {
            $product = $product->getParentItem();
        }
        $prodinset = Mage::getResourceModel('productoptions/prodinset');
        if ($setslist = $prodinset->getSetslist($product->getId())) {
            $infoArr = array();
            if ($info = $item->getProduct()->getCustomOption('info_buyRequest')) {
                $infoArr = unserialize($info->getValue());
            }

            $specialPrice = 0;
            foreach ($setslist as $setId) {
                $modeloplist = Mage::getModel('productoptions/productoptions');
                $set = $modeloplist->load($setId);
                if ($set->getStatus() == '1') {
                    foreach ($set->getOptions() as $option) {
                        $option->setProduct($product);
                        $value = '';
                        if (isset($infoArr['options'][$option->getId()])) {
                            switch ($option->getGroupByType()) {
                                case 'select': {
                                        if (is_array($infoArr['options'][$option->getId()])) {
                                            foreach ($infoArr['options'][$option->getId()] as $elem) {
                                                $value .= $option->getValueById($elem)->getTitle() . ' ';
                                                $specialPrice+=$option->getValueById($elem)->getPrice(true);
                                            }
                                        } else {

                                            $value = $option->getValueById($infoArr['options'][$option->getId()])->getTitle();
                                            $specialPrice+=$option->getValueById($infoArr['options'][$option->getId()])->getPrice(true);
                                        }
                                        break;
                                    }
                                case 'date': {
                                        switch ($option->getType()) {
                                            case 'date': {
                                                    $value = implode('.', $infoArr['options'][$option->getId()]);
                                                    $print_value = $value;
                                                    break;
                                                }
                                            case 'time': {
                                                    $input = $infoArr['options'][$option->getId()];
                                                    if ($input['day_part'] == 'pm')
                                                        $input['hour']+=12; // +12 часов
                                                    $value = sprintf("%02d:%02d", $input['hour'], $input['minute']);
                                                    $print_value = $value;
                                                    break;
                                                }
                                            default: {
                                                    $input = $infoArr['options'][$option->getId()];
                                                    if ($input['day_part'] == 'pm') {
                                                        $input['hour']+=12; // +12 часов
                                                        if ($input['hour'] == 24)
                                                            $input['hour'] = 0;
                                                    }

                                                    $value = sprintf("%02d.%02d.%04d %02d:%02d", $input['day'], $input['month'], $input['year'], $input['hour'], $input['minute']);
                                                    $print_value = $value;
                                                }
                                        }
                                        $specialPrice+=$option->getPrice(true);
                                        break;
                                    }
                                default: {
                                        $value = $infoArr['options'][$option->getId()];
                                        $specialPrice+=$option->getPrice(true);
                                    }
                                //   break;
                            }



                            $additionalOptions[] = array(
                                'code' => 'additional_options',
                                'label' => $option->getTitle(),
                                'value' => $value,
                                'print_value' => $print_value,
                                'option_type' => $option->getType(),
                                'custom_view' => true,
                                'option_id' => $option->getId(),
                            );
                            //         Mage::log($specialPrice);
                            $option->setProduct($product);
                        }
                    }
                }
            }

            $item->addOption(array(
                'code' => 'additional_options',
                'value' => serialize($additionalOptions),
            ));
            if (($item->getProduct()->getTierPrice($item->getQty())) <> $item->getProduct()->getPrice()) {
                $specialPrice += $item->getProduct()->getTierPrice($item->getQty());
                if ($optionIds = $product->getCustomOption('option_ids')) {
                    $optionPartPrice = $item->getProduct()->getFinalPrice() - $item->getProduct()->getPrice();
                    $specialPrice+=$optionPartPrice;
                }
            }
            else
                $specialPrice += $item->getProduct()->getFinalPrice();
            $item->setCustomPrice($specialPrice);
            $item->setOriginalCustomPrice($specialPrice);
            $item->getProduct()->setIsSuperMode(true);

            if ($quote->hasProductId($product->getId())) {
                Mage::log('has product id');
                foreach ($quote->getAllVisibleItems() as $proditem) {
                    if (($item->compare($proditem)) && ($proditem->getId() <> $item->getId())) {
                        Mage::log('compare true');
                        $oldQty = $proditem->getQty();
                        $item->setQty($proditem->getQty() + $item->getQty());
                        //       $item->isDeleted(TRUE);
                        $cart = Mage::getModel('checkout/cart');
                        //        MAge::log($item);
                        //   $item->save();
                        MAge::register('need_to_del', 1);
                        //    $item->remove();
                        //   $quote->removeItem($item->getId());
                        //     $cart->removeItem($item->getId());

                        if (($proditem->getProduct()->getTierPrice($proditem->getQty())) <> $proditem->getProduct()->getPrice()) {
                            $price = $proditem->getCustomPrice();
                            if ($proditem->getProduct()->getTierPrice($oldQty) <> $proditem->getProduct()->getTierPrice($proditem->getQty())) {

                                $optionPrice = $proditem->getCustomPrice() - $proditem->getProduct()->getPrice();

                                $specialPrice = $proditem->getProduct()->getTierPrice($proditem->getQty()) + $optionPrice;

                                $proditem->setCustomPrice($specialPrice);
                                $proditem->setOriginalCustomPrice($specialPrice);
                                $item->setCustomPrice($specialPrice);
                                $item->setOriginalCustomPrice($specialPrice);
                                $proditem->getProduct()->setIsSuperMode(true);
                                $proditem->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function Update_ProductOptionsInCard($observer) {
        $item = $observer->getItem();
        if ($item->getParentItem()) {
            $item = $item->getParentItem();
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $product = $item->getProduct();
        $prodinset = Mage::getResourceModel('productoptions/prodinset');
        //    $setslist = $prodinset->getSetslist($product->getId());
        if ($setslist = $prodinset->getSetslist($product->getId())) {
            $infoArr = array();
            if ($info = $item->getProduct()->getCustomOption('info_buyRequest')) {
                $infoArr = unserialize($info->getValue());
            }
            if (($item->getProduct()->getTierPrice($item->getQty())) <> $item->getProduct()->getPrice()) {
                $specialPrice = $item->getProduct()->getTierPrice($item->getQty());
                if ($optionIds = $product->getCustomOption('option_ids')) {
                    $optionPartPrice = $item->getProduct()->getFinalPrice() - $item->getProduct()->getPrice();
                    $specialPrice+=$optionPartPrice;
                }
            }
            else
                $specialPrice = $item->getProduct()->getFinalPrice();
            $additionalOptions = array();
            foreach ($setslist as $setId) {
                $modeloplist = Mage::getModel('productoptions/productoptions');
                $set = $modeloplist->load($setId);
                if ($set->getStatus() == '1') {
                    foreach ($set->getOptions() as $option) {
                        $value = '';
                        switch ($option->getGroupByType()) {
                            case 'select': {
                                    if (is_array($infoArr['options'][$option->getId()])) {
                                        foreach ($infoArr['options'][$option->getId()] as $elem) {
                                            $value .= $option->getValueById($elem)->getTitle() . ' ';
                                            $specialPrice+=$option->getValueById($elem)->getPrice();
                                        }
                                    } else {
                                        $value = $option->getValueById($infoArr['options'][$option->getId()])->getTitle();
                                        $specialPrice+=$option->getValueById($infoArr['options'][$option->getId()])->getPrice();
                                    }
                                    break;
                                }
                            case 'date': {
                                    switch ($option->getType()) {
                                        case 'date': {
                                                $value = implode('.', $infoArr['options'][$option->getId()]);
                                                $print_value = $value;
                                                break;
                                            }
                                        case 'time': {
                                                $input = $infoArr['options'][$option->getId()];
                                                if ($input['day_part'] == 'pm')
                                                    $input['hour']+=12; // +12 часов
                                                $value = sprintf("%02d:%02d", $input['hour'], $input['minute']);
                                                $print_value = $value;
                                                break;
                                            }
                                        default: {
                                                $input = $infoArr['options'][$option->getId()];
                                                if ($input['day_part'] == 'pm') {
                                                    $input['hour']+=12; // +12 часов
                                                    if ($input['hour'] == 24)
                                                        $input['hour'] = 0;
                                                }

                                                $value = sprintf("%02d.%02d.%04d %02d:%02d", $input['day'], $input['month'], $input['year'], $input['hour'], $input['minute']);
                                                $print_value = $value;
                                            }
                                    }
                                    $specialPrice+=$option->getPrice();
                                    break;
                                }
                            default: {
                                    $value = $infoArr['options'][$option->getId()];
                                    $specialPrice+=$option->getPrice();
                                }
                            //   break;
                        }
                        $additionalOptions[] = array(
                            'code' => 'additional_options',
                            'label' => $option->getTitle(),
                            'value' => $value,
                            'print_value' => $print_value,
                            'option_type' => $option->getType(),
                            'custom_view' => true,
                            'option_id' => $option->getId(),
                        );


                        $option->setProduct($product);
                    }
                }
            }

            $item->addOption(array(
                'code' => 'additional_options',
                'value' => serialize($additionalOptions),
            ));
            $item->setCustomPrice($specialPrice);
            $item->setOriginalCustomPrice($specialPrice);
            $item->getProduct()->setIsSuperMode(true);
            $item->save();
            if ($quote->hasProductId($product->getId())) {
                Mage::log('has product id');
                foreach ($quote->getAllVisibleItems() as $proditem) {
                    if (($item->compare($proditem)) && ($proditem->getId() <> $item->getId())) {
                        Mage::log('compare true');
                        $proditem->setQty($proditem->getQty() + $item->getQty());
                        if (($proditem->getProduct()->getTierPrice($proditem->getQty())) <> $proditem->getProduct()->getPrice()) {
                            $price = $proditem->getCustomPrice();
                            MAge::log($price);
                            $optionPrice = $proditem->getCustomPrice() - $proditem->getProduct()->getPrice();
                            MAge::log($optionPrice);
                            $specialPrice = $proditem->getProduct()->getTierPrice($proditem->getQty()) + $optionPrice;
                            MAge::log($specialPrice);
                            $proditem->setCustomPrice($specialPrice);
                            $proditem->setOriginalCustomPrice($specialPrice);
                        }
                        $cart = Mage::getModel('checkout/cart');
                        $cart->removeItem($item->getId())->save();
                        $quote->removeItem($item->getId())->save();
                    }
                }
            }
        }
    }

    public function SaveAfter($observer) {
        if (MAge::registry('need_to_del') == 1) {

            $cart = Mage::getModel('checkout/cart');
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $model = Mage::getResourceModel('productoptions/obs');
            $itemId = $model->getCurrentCartItem();

            $cart->removeItem($itemId[0]);
            Mage::unregister('need_to_del');
        }
    }

    public function OrderSaveBefore($observer) {
        $item = $observer->getItem();
        $orderItem = $observer->getOrderItem();
        $options = $orderItem->getProductOptions();
        $product = $item->getProduct();
        $prodinset = Mage::getResourceModel('productoptions/prodinset');
        $setslist = $prodinset->getSetslist($product->getId());
      //  var_dump($setslist);
        $infoArr = array();
        if ($info = $item->getProduct()->getCustomOption('info_buyRequest')) {
            $infoArr = unserialize($info->getValue());
        }

        $specialPrice = $item->getProduct()->getFinalPrice();
        $additionalOptions = array();
        foreach ($setslist as $setId) {
            $modeloplist = Mage::getModel('productoptions/productoptions');
            $set = $modeloplist->load($setId);
            if ($set->getStatus() == '1') {
                foreach ($set->getOptions() as $option) {
                    $value = '';
                    switch ($option->getGroupByType()) {
                        case 'select': {
                                if (is_array($infoArr['options'][$option->getId()])) {
                                    foreach ($infoArr['options'][$option->getId()] as $elem) {
                                        $value .= $option->getValueById($elem)->getTitle();
                                        $specialPrice+=$option->getValueById($elem)->getPrice();
                                    }
                                } else {
                                    $value = $option->getValueById($infoArr['options'][$option->getId()])->getTitle();
                                    $specialPrice+=$option->getValueById($infoArr['options'][$option->getId()])->getPrice();
                                }
                                break;
                            }
                        case 'date': {
                                switch ($option->getType()) {
                                    case 'date': {
                                            $value = implode('.', $infoArr['options'][$option->getId()]);
                                            $print_value = $value;
                                            break;
                                        }
                                    case 'time': {
                                            $input = $infoArr['options'][$option->getId()];
                                            if ($input['day_part'] == 'pm')
                                                $input['hour']+=12; // +12 часов
                                            $value = sprintf("%02d:%02d", $input['hour'], $input['minute']);
                                            $print_value = $value;
                                            break;
                                        }
                                    default: {
                                            $input = $infoArr['options'][$option->getId()];
                                            if ($input['day_part'] == 'pm') {
                                                $input['hour']+=12; // +12 часов
                                                if ($input['hour'] == 24)
                                                    $input['hour'] = 0;
                                            }

                                            $value = sprintf("%02d.%02d.%04d %02d:%02d", $input['day'], $input['month'], $input['year'], $input['hour'], $input['minute']);
                                            $print_value = $value;
                                        }
                                }
                                $specialPrice+=$option->getPrice();
                                break;
                            }
                        default: {
                                $value = $infoArr['options'][$option->getId()];
                                $specialPrice+=$option->getPrice();
                            }
                        //   break;
                    }


                    $options['additional_options'][] = array(
                        'label' => $option->getTitle(),
                        'value' => $value,
                        'print_value' => $value,
                    );

                    $option->setProduct($product);
                }
            }
        }

        $orderItem->setProductOptions($options);
    }

}

?>
