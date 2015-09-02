<?php

class Magazento_Productoptions_Block_Developer extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $content = '<p></p>';
        $content.= '<style>
                    .developer {
                            background: #FAFAFA;
                            border: 1px solid #CCCCCC;
                            margin-bottom: 10px;
                            padding: 10px;
                            height: auto;
                    }

                    .developer h3 {
                            color: #444;
                    }

                    .contact-type {
                            color: #EA7601;
                            font-weight: bold;
                    }

                    .developer img {
                            float: left;
                    }

                    .developer .info {
                            background: #E7EFEF;
                            padding: 5px 10px 0 5px;
                            margin-left: 210px;
                            height: 195px;
                    }
                    </style>

                    <div class="developer">
                            <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/navigation-menu-megamenu"
                                    target="_blank"><img
                                    src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/base/default/magazento/mostpopular/menu_box.jpg"
                                    >
                            </a>
                            
                            <div class="info">
                                <h3>MEGAMENU - Band new Magento navigation menu</h3>
                                <p>
                                Menu - is a key navigation element for customers on your website. It is particularly important that the menu would contain a maximum of all information, 
                                namely that which is needed by your potential customers. Alas, based on analysis of user behavior on large portals, the attention of given only for the first 
                                10% - 20% of all categories of the store directories.<br>
                                The remaining elements are simply not visible to users and are not used. Store can increase profits by changing the sitemenu - users should see all items of your catalog products immediately.
                                </p>
                            
                                <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/navigation-menu-megamenu" target="_blank"><img src="http://magazento.com/promo/extension_page.png" alt=""></a>
                                <br>
                            </div>                            
                            
                    </div>
                    
                    <div class="developer">
                            <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/magento-one-step-page-checkout"
                                    target="_blank"><img
                                    src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/base/default/magazento/mostpopular/logo_onecheckout.jpg"
                                    >
                            </a>
                            
                            <div class="info">
                                <h3>ONE STEP CHECKOUT</h3>
                                <p>
                                    Complicated checkout process with many forms to fill in can make customers run away and increase cart abandonment. Magento One Step Checkout offers simplified checkout for your customers\' positive shopping experience. The module shortens the process into one single page. Logged in customers already have their information on the page; they need to make just a few clicks to confirm the order.
                                </p>
                            
                                <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/magento-one-step-page-checkout" target="_blank"><img src="http://magazento.com/promo/extension_page.png" alt=""></a>
                                <br>
                            </div>                            
                    </div>
                    
                    <div class="developer">
                            <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/pdf-export"
                                    target="_blank"><img
                                    src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/base/default/magazento/mostpopular/logo_pdf.jpg"
                                    >
                            </a>
                            
                            <div class="info">
                                <h3>PDF CATALOG</h3>
                                <p>
                                    PDF CATALOG for Magento is a professional solution that lets you generate printable copy of all store products! It also lets your customers to generate PDF\'s of products and categories they view directly from your website without other 3rd party website or applications. All store products in PDF in few clicks!                                
                                </p>
                            
                                <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/pdf-export" target="_blank"><img src="http://magazento.com/promo/extension_page.png" alt=""></a>
                                <br>
                            </div>
                    </div>
                    
                    <div class="developer">
                            <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/extension-html-sitemap"
                                    target="_blank"><img
                                    src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/base/default/magazento/mostpopular/logo_sitemap.jpg"
                                    >
                            </a>
                            
                            <div class="info">
                                <h3>SITEMAP</h3>
                                <p>
                                    With Sitemap you can easily create the site map page on your site with the clear hierarchical structure of all the products, categories and pages. Sure that such a page will be highly appreciated by those of your customers who would like not to spend a lot of time to find the product they are interested in. And the search spiders and crawlers will get through this magento sitemap the clear direction to all your site pages, no matter how numerous they are. Moreover, with Magento Html Sitemap extension you will receive the bundle of bright additional advantages making the google sitemap creation even easier and more convenient.
                                </p>
                            
                                <a href="http://www.magazento.com/english/magento-ext/magazento-extensions/extension-html-sitemap" target="_blank"><img src="http://magazento.com/promo/extension_page.png" alt=""></a>
                                <br>
                            </div>
                    </div>'
                
                
                
                
                
                ;

        return $content;


    }


}
