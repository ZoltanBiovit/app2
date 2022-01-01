/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_PromotionBar
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
        'jquery',
        'Magento_Customer/js/customer-data'
    ],
    function($, customerData) {
        return function(config){
            $(document).ready(function() {
                $("div.messages").load(function(e) {
                    $.cookieStorage.set('mage-messages', null);
                });
                
                window.onbeforeunload = function(e) {
                    $.cookieStorage.set('mage-messages', null);
                };
            });
        }
    }
);