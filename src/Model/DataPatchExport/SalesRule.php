<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\CouponRepository;
use Magento\SalesRule\Model\RuleRepository;
use Magento\SalesRule\Model\ResourceModel\Rule as SalesRuleResource;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Nanobots\DataPatchCreator\Converters\ObjectToString;
use Nanobots\DataPatchCreator\Helper\Output;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync;

class SalesRule extends AbstractExport
{
    /** @var mixed */
    protected $_ruleId;

    /** @var RuleInterface */
    protected $salesRule;

    /** @var RuleFactory  */
    protected $salesFactory;

    /** @var CouponRepositoryInterface  */
    protected $couponRepository;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /** @var FilterBuilder  */
    protected $filterBuilder;

    /** @var RuleCollectionFactory  */
    protected $salesRuleCollectionFactory;

    /** @var SalesRuleResource  */
    protected $salesRuleResource;

    /** @var RuleFactory  */
    protected $ruleFactory;

    /**
     * @param RuleCollectionFactory $salesRuleCollectionFactory
     * @param RuleFactory $ruleFactory
     * @param SalesRuleResource $salesRuleResource
     * @param CouponRepositoryInterface $couponRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param Filesystem $filesystem
     * @param ImageSync $imageSync
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectToString $objectToString
     * @param Output $outputHelper
     */
    public function __construct(
        RuleCollectionFactory $salesRuleCollectionFactory,
        RuleFactory $ruleFactory,
        SalesRuleResource $salesRuleResource,
        CouponRepositoryInterface $couponRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        Filesystem $filesystem,
        ImageSync $imageSync,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        ObjectToString $objectToString,
        Output $outputHelper
    ) {
        $this->salesRuleCollectionFactory = $salesRuleCollectionFactory;
        $this->salesRuleResource = $salesRuleResource;
        $this->ruleFactory = $ruleFactory;
        $this->couponRepository = $couponRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        parent::__construct($filesystem, $imageSync, $resourceConnection, $scopeConfig, $objectToString, $outputHelper);
    }

    /**
     * Get RuleModel
     *
     * @return \Magento\SalesRule\Model\Rule|DataObject
     */
    private function getSalesRule(): \Magento\SalesRule\Model\Rule
    {
        if (!$this->salesRule || $this->isMassExport()) {
            $this->salesRule = $this->ruleFactory->create();
            $this->salesRuleResource->load($this->salesRule, $this->_ruleId);
        }
        return $this->salesRule;
    }

    /**
     * @inheritDoc
     */
    public function setDataIdentifiers(array $dataIdentifiers): DataPatchExportInterface
    {
        $this->_ruleId = $dataIdentifiers['rule_id'];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return str_replace("-", "_", $this->getSalesRule()->getRuleId());
    }

    /**
     * @inheritDoc
     */
    public function prepareObjectArray(): array
    {
        $salesRuleData = $this->getSalesRule()->getData();
        unset($salesRuleData['coupon_code']);

        return [
            'ruleData' => $this->objectToString->convertDataArrayToString($salesRuleData),
            'ruleId' => $this->getSalesRule()->getRuleId(),
            'ruleName' => $this->getSalesRule()->getName(),
            'couponsData' => $this->getCoupons($this->getSalesRule()),
            'images' => [],
            'imgArray' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAllIdentifiers(): array
    {
        /** @var RuleCollection $salesRuleCollection */
        $salesRuleCollection = $this->salesRuleCollectionFactory->create();

        $salesRuleIds = [];
        /** @var \Magento\SalesRule\Model\Rule $salesRule */
        foreach ($salesRuleCollection as $salesRule) {
            $salesRuleIds[] = $salesRule->getId();
        }

        return $salesRuleIds;
    }

    /**
     * Returns array of coupon codes for data patch creation
     *
     * @param \Magento\SalesRule\Model\Rule $salesRule
     * @return array
     */
    public function getCoupons(\Magento\SalesRule\Model\Rule $salesRule): array
    {
        $couponCodes = [];

        /** @var Filter $filter */
        $filter = $this->filterBuilder->create()
            ->setField('rule_id')
            ->setConditionType('eq')
            ->setValue($salesRule->getId())
        ;

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters([$filter])->create();

        /** @var CouponInterface $coupon */
        foreach ($this->couponRepository->getList($searchCriteria)->getItems() as $coupon) {
            $couponData = $coupon->getData();
            unset($couponData['coupon_id'], $couponData['rule_id']);
            $couponCodes[$coupon->getcode()] = [
                'code' => $coupon->getcode(),
                'data' => $this->objectToString->convertDataArrayToString($couponData),
            ];
        }

        return $couponCodes;
    }
}
