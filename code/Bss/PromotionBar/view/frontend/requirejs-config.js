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
var config = {
    map: {
        '*': {
            renderpromotionbar: 'Bss_PromotionBar/js/renderpromotionbar',
            removepromotionbar: 'Bss_PromotionBar/js/removepromotionbar',
            promotionslidestart: 'Bss_PromotionBar/js/promotionslidestart',
            clearmess: 'Bss_PromotionBar/js/clearmess',
        }
    },
    paths: {
        'bxslider': 'Bss_PromotionBar/js/bxslider'
    },
    shim: {
        'bxslider':{
            'deps':['jquery']
        }
    }
};