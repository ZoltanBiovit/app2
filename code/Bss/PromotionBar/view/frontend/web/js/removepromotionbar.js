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
                var  selector = ".promotionbar_position_"+ config.page + config.position;
                var close = "span#promotion_bar_close_"+ config.page + config.position;
                var endDate = config.endDate;
                if (endDate) {
                    var length = endDate.length;
                    $.each(endDate, function (index, value){
                        var compare_dates = function(date1, date2){
                            if (date2 < date1) {
                                $('#promotion_bar_id_'+ value.bar_id).parent().remove();
                            }
                        }
                        compare_dates(new Date(), new Date(value.end_date))
                    });
                }
                if (config.timeOut != 0) {
                    setTimeout(function() {
                        $(selector).fadeOut();
                    }, config.timeOut);
                }
                $(document).on('click', close, function(event){
                    $(selector).fadeOut();
                });
                $('.promotionbar_slide').bxSlider({
                    auto: true,
                    mode: 'fade',
                    pager: config.pager,
                    controls: config.controls,
                    pause: config.pause,
                    adaptiveHeight: true
                });
                $(".bssClass").removeClass('displayNone');

                var promotionbar_width = $(".promotionbar_wrapper").width();
                $(".a_promotion_bar").css("width",(promotionbar_width+"px"));
            });
        }
    }
);
