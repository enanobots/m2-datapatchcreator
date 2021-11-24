<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Api\Data\RuleInterface;
use Magento\CatalogRule\Model\CatalogRuleRepository;
use Magento\CatalogRule\Model\ResourceModel\Rule as CatalogRuleResource;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\CatalogRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Nanobots\DataPatchCreator\Converters\ObjectToString;
use Nanobots\DataPatchCreator\Helper\Output;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync;

class CatalogRule extends AbstractExport
{
    /** @var mixed */
    protected $_ruleId;

    /** @var RuleInterface */
    protected $catalogRule;

    /** @var RuleFactory  */
    protected $ruleFactory;

    /** @var CatalogRuleResource  */
    protected $catalogRuleResource;

    /** @var RuleCollectionFactory  */
    protected $catalogRuleCollectionFactory;

    /**
     * @param RuleCollectionFactory $catalogRuleCollectionFactory
     * @param RuleFactory $ruleFactory
     * @param CatalogRuleResource $catalogRuleResource
     * @param Filesystem $filesystem
     * @param ImageSync $imageSync
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectToString $objectToString
     * @param Output $outputHelper
     */
    public function __construct(
        RuleCollectionFactory $catalogRuleCollectionFactory,
        RuleFactory $ruleFactory,
        CatalogRuleResource $catalogRuleResource,
        Filesystem $filesystem,
        ImageSync $imageSync,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        ObjectToString $objectToString,
        Output $outputHelper
    ) {
        $this->catalogRuleCollectionFactory = $catalogRuleCollectionFactory;
        $this->catalogRuleResource = $catalogRuleResource;
        $this->ruleFactory = $ruleFactory;
        parent::__construct($filesystem, $imageSync, $resourceConnection, $scopeConfig, $objectToString, $outputHelper);
    }

    /**
     * Get RuleModel
     *
     * @return BlockInterface|DataObject
     */
    private function getCatalogRule(): RuleInterface
    {
        if (!$this->catalogRule || $this->isMassExport()) {
            $this->catalogRule = $this->ruleFactory->create();
            $this->catalogRuleResource->load($this->catalogRule, $this->_ruleId);
        }
        return $this->catalogRule;
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
        return str_replace("-", "_", $this->getCatalogRule()->getRuleId());
    }

    /**
     * @inheritDoc
     */
    public function prepareObjectArray(): array
    {
        $catalogRuleData = $this->getCatalogRule()->getData();

        return [
            'ruleData' => $this->objectToString->convertDataArrayToString($catalogRuleData),
            'ruleId' => $this->getCatalogRule()->getRuleId(),
            'name' => $this->getCatalogRule()->getName(),
            'images' => [],
            'imgArray' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAllIdentifiers(): array
    {
        /** @var RuleCollection $catalogRuleCollection */
        $catalogRuleCollection = $this->catalogRuleCollectionFactory->create();

        $catalogRuleIds = [];
        /** @var \Magento\CatalogRule\Model\Rule $catalogRule */
        foreach ($catalogRuleCollection as $catalogRule) {
            $catalogRuleIds[] = $catalogRule->getRuleId();
        }

        return $catalogRuleIds;
    }
}
