<?php
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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\QuoteExtension\Helper;

use Bss\QuoteExtension\Model\QuoteExtension as CustomerQuoteExtension;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Bss\QuoteExtension\Model\ManageQuoteFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class QuoteExtensionPlace
 *
 * @package Bss\QuoteExtension\Helper
 */
class QuoteExtensionPlace extends AbstractHelper
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var
     */
    protected $idQuoteExtensionPlace;

    /**
     * @var ManageQuoteFactory
     */
    protected $manageQuote;

    /**
     * Model constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param RequestInterface $request
     * @param ManageQuoteFactory $manageQuote
     * @param Context $context
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        RequestInterface $request,
        ManageQuoteFactory $manageQuote,
        Context $context
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->manageQuote = $manageQuote;
        parent::__construct($context);
    }

    /**
     * Check quote extension place
     *
     * @return string
     */
    public function checkQuoteExtensionPlace() {
        $urlCurrent = "";
        if (strpos($this->request->getServer('HTTP_REFERER'), 'quoteextension/index/index') !== false) {
            $urlCurrent = $this->request->getServer('HTTP_REFERER');
        } else if(strpos($this->request->getServer('REDIRECT_URL'), 'quoteextension/index/index') !== false) {
            $urlCurrent = $this->request->getServer('REDIRECT_URL');
        }
        if (strpos($this->request->getServer('REDIRECT_URL'), 'customer/section/load') !== false) {
            return "";
        }
        return $urlCurrent;
    }
    /**
     * Get id quote when place order quote extension
     *
     * @return null|int
     */
    public function getIdQuoteExtensionPlace() {
        $urlCurrent = $this->checkQuoteExtensionPlace();
        if ($urlCurrent) {
            if (!$this->idQuoteExtensionPlace) {
                $positionStartQuoteIdExtension = strpos(
                        $urlCurrent,
                        '/quote/',
                        strpos($urlCurrent, 'quoteextension/index/index')
                    ) + strlen("/quote/");
                $positionEndQuoteIdExtension = strpos(
                    $urlCurrent,
                    '/',
                    $positionStartQuoteIdExtension
                );
                $quoteExtensionId = substr($urlCurrent, $positionStartQuoteIdExtension, $positionEndQuoteIdExtension - $positionStartQuoteIdExtension);
                $this->idQuoteExtensionPlace = $this->getQuoteIdByIdQE($quoteExtensionId);
                $this->checkoutSession->setQuoteIdPayPal($this->idQuoteExtensionPlace);
                $this->checkoutSession->setIsQuoteExtension($this->idQuoteExtensionPlace);
            }
            return $this->idQuoteExtensionPlace;
        } else if ($this->getQuoteQuoteIdPayPal()) {
            return $this->getQuoteQuoteIdPayPal();
        }
        $this->checkoutSession->setQuoteIdPayPal(null);
        return null;
    }

    /**
     * Get quote by id quote extension
     *
     * @param $quoteExtensionId
     * @return null|int
     */
    public function getQuoteIdByIdQE($quoteExtensionId) {
        if (is_numeric($quoteExtensionId)) {
            $manageQuote = $this->manageQuote->create()->load($quoteExtensionId);
            return $manageQuote->getQuoteId();
        }
        return null;
    }

    /**
     * Return quote id of quote extension when place order use payment PayPal
     *
     * @return null|int
     */
    public function getQuoteQuoteIdPayPal() {
        if ($this->checkoutSession->getQuoteIdPayPal() &&
            strpos($this->request->getServer('REDIRECT_URL'), '/paypal/express/return/') !== false
        ) {
            return $this->checkoutSession->getQuoteIdPayPal();
        }
        return null;
    }
}
