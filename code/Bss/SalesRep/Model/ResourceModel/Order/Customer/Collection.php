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
 * @package    Bss_SalesRep
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SalesRep\Model\ResourceModel\Order\Customer;

use Bss\SalesRep\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Helper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Authorization;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Validator\UniversalFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * @package Bss\SalesRep\Model\ResourceModel\Order\Customer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Customer\Collection
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Data
     */
    protected $helper;

    protected $authorization;

    /**
     * Collection constructor.
     * @param Session $session
     * @param Data $helper
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Config $eavConfig
     * @param ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param Helper $resourceHelper
     * @param UniversalFactory $universalFactory
     * @param Snapshot $entitySnapshot
     * @param \Magento\Framework\DataObject\Copy\Config $fieldsetConfig
     * @param AdapterInterface|null $connection
     * @param string $modelName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Authorization $authorization,
        Session $session,
        Data $helper,
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        Snapshot $entitySnapshot,
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        AdapterInterface $connection = null,
        $modelName = \Magento\Customer\Model\ResourceModel\Customer\Collection::CUSTOMER_MODEL_NAME
    ) {
        $this->authorization = $authorization;
        $this->helper = $helper;
        $this->session = $session;
        \Magento\Customer\Model\ResourceModel\Customer\Collection::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $entitySnapshot,
            $fieldsetConfig,
            $connection,
            $modelName
        );
    }

    /**
     * Customer Collection
     *
     * @return $this|\Magento\Sales\Model\ResourceModel\Order\Customer\Collection
     * @throws LocalizedException
     */
    protected function _initSelect()
    {
        $customerAllowed = $this->authorization->isAllowed('Magento_Sales::sales');
        $userId = $this->session->getUser()->getId();
        parent::_initSelect();
        if ($this->helper->isEnable() && $this->helper->checkUserIsSalesRep() && !$customerAllowed) {
            $this->addAttributeToFilter('bss_sales_representative', $userId);
            return $this;
        }
        return $this;
    }
}
