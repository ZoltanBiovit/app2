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
        'Bss_PromotionBar/js/bxslider'
    ],
    function($) {
        return function(config){
            $(document).ready(function(){
                $(".bssClass").removeClass('displayNone');

                $('.promotionbar_slide').bxSlider({
                    auto: true,
                    mode: 'fade',
                    pager: config.pager,
                    controls: config.controls,
                    pause: config.pause,
                    adaptiveHeight: true
                });
                var promotionbar_width = $(".promotionbar_wrapper").width();
                $(".a_promotion_bar").css("width",(promotionbar_width+"px"));
            });
        }
    }
);