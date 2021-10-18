<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Cms\Model\ResourceModel\Block as CmsBlockResourceModel;
use Magento\Cms\Model\BlockFactory as CmsBlockFactory;
use Nanobots\DataPatchCreator\Converters\ObjectToString;
use Nanobots\DataPatchCreator\Helper\Output;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync;

class CmsBlock extends AbstractExport
{
    /** @var mixed */
    protected $_blockId;

    /** @var BlockInterface */
    protected $cmsBlock;

    /** @var CmsBlockFactory  */
    protected $cmsBlockFactory;

    /** @var CmsBlockResourceModel  */
    protected $cmsBlockResource;

    /** @var BlockRepositoryInterface  */
    protected $blockRepository;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CmsBlockFactory $cmsBlockFactory
     * @param CmsBlockResourceModel $cmsBlockResource
     * @param Filesystem $filesystem
     * @param ImageSync $imageSync
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectToString $objectToString
     * @param Output $outputHelper
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CmsBlockFactory $cmsBlockFactory,
        CmsBlockResourceModel $cmsBlockResource,
        Filesystem $filesystem,
        ImageSync $imageSync,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        ObjectToString $objectToString,
        Output $outputHelper
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cmsBlockFactory = $cmsBlockFactory;
        $this->cmsBlockResource = $cmsBlockResource;
        parent::__construct($filesystem, $imageSync, $resourceConnection, $scopeConfig, $objectToString, $outputHelper);
    }

    /**
     * Get CmsPageModel
     *
     * @return BlockInterface|DataObject
     */
    private function getCmsBlock(): BlockInterface
    {
        if (!$this->cmsBlock || $this->isMassExport()) {
            $this->cmsBlock = $this->cmsBlockFactory->create();
            $this->cmsBlockResource->load($this->cmsBlock, $this->_blockId);
        }
        return $this->cmsBlock;
    }

    /**
     * @inheritDoc
     */
    public function setDataIdentifiers(array $dataIdentifiers): DataPatchExportInterface
    {
        $this->_blockId = $dataIdentifiers['block_id'];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return str_replace("-", "_", $this->getCmsBlock()->getIdentifier());
    }

    /**
     * @inheritDoc
     */
    public function prepareObjectArray(): array
    {
        $blockData = $this->getCmsBlock()->getData();
        unset($blockData['block_id']);

        return [
            'blockData' => $this->objectToString->convertDataArrayToString($blockData),
            'blockIdentifier' => $this->getCmsBlock()->getIdentifier(),
            'images' => $this->getImagesFromContent($blockData['content']),
            'imgArray' => $this->objectToString->convertDataArrayToString(
                $this->getImagesFromContent($blockData['content']),
                8
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAllIdentifiers(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $blocks = $this->blockRepository->getList($searchCriteria)->getItems();

        $blockIds = [];
        foreach ($blocks as $block) {
            $blockIds[] = $block->getId();
        }

        return $blockIds;
    }
}
