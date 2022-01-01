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
 * @category  BSS
 * @package   Bss_QuoteExtension
 * @author    Extension Team
 * @copyright Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
define(
    [
        "jquery",
        'mage/url',
    ],
    function ($, urlBuilder) {
        $(document).on('click',".re-send-quote", function(e) {
            var url = urlBuilder.build("quoteextension/quote/resendemail");
            $(this).parent().parent().attr("action", url);
        })
    }
);
