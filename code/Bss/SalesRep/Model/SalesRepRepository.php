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
namespace Bss\SalesRep\Model;

use Bss\SalesRep\Api\Data\SalesRepInterface;
use Bss\SalesRep\Api\SalesRepRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SalesRepRepository
 *
 * @package Bss\SalesRep\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SalesRepRepository implements SalesRepRepositoryInterface
{
    /**
     * @var SalesRep
     */
    protected $salesRep;

    /**
     * @var SearchResultsInterface
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessor
     */
    protected $collectionProcessor;

    /**
     * @var ResourceModel\SalesRep\CollectionFactory
     */
    protected $collection;

    /**
     * @var ResourceModel\SalesRep
     */
    protected $salesRepResource;

    /**
     * @var SalesRepFactory
     */
    protected $salesRepFactory;

    /**
     * SalesRepRepository constructor.
     * @param SalesRepFactory $salesRepFactory
     * @param ResourceModel\SalesRep $salesRepResource
     * @param ResourceModel\SalesRep\CollectionFactory $collection
     * @param SearchResultsInterface $searchResultsFactory
     * @param CollectionProcessor $collectionProcessor
     * @param SalesRep $salesRep
     */
    public function __construct(
        \Bss\SalesRep\Model\SalesRepFactory $salesRepFactory,
        \Bss\SalesRep\Model\ResourceModel\SalesRep $salesRepResource,
        \Bss\SalesRep\Model\ResourceModel\SalesRep\CollectionFactory $collection,
        SearchResultsInterface $searchResultsFactory,
        CollectionProcessor $collectionProcessor,
        \Bss\SalesRep\Model\SalesRep $salesRep
    ) {
        $this->salesRepFactory = $salesRepFactory;
        $this->salesRepResource = $salesRepResource;
        $this->salesRep = $salesRep;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->collection = $collection;
    }

    /**
     * Get Sales Rep by id
     *
     * @param int $repId
     * @return SalesRep
     * @throws NoSuchEntityException
     */
    public function getById($repId)
    {
        try {
            return $this->salesRep->load($repId, 'rep_id');
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__('Sales Rep with id "%1" does not exist.', $repId));
        }
    }

    /**
     * Get Sales Rep by user id
     *
     * @param int $userId
     * @return SalesRep
     * @throws NoSuchEntityException
     */
    public function getByUserId($userId)
    {
        try {
            return $this->salesRep->load($userId, 'user_id');
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__('Sales Rep with user id "%1" does not exist.', $userId));
        }
    }

    /**
     * Get list Sales Rep
     *
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResult = $this->searchResultsFactory->create();
        $collection = $this->collection->create();
        $this->collectionProcessor->process($criteria, $collection);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * Delelte Sales Rep
     *
     * @param SalesRepInterface $salesRep
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function delete(SalesRepInterface $salesRep)
    {
        try {
            return $this->salesRepResource->delete($salesRep);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }

    /**
     * Delete Sales Rep by id
     *
     * @param int $id
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function deleteById($id)
    {
        try {
            $salesRep = $this->salesRepFactory->create();
            $this->salesRepResource->load($salesRep, $id);

            return $this->delete($salesRep);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }
}
