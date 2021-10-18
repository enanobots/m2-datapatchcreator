<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\Data\PageSearchResultsInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Cms\Model\ResourceModel\Page as PageResourceModel;
use Magento\Cms\Model\PageFactory as CmsPageFactory;
use Magento\Cms\Model\Page as CmsPageModel;
use Nanobots\DataPatchCreator\Converters\ObjectToString;
use Nanobots\DataPatchCreator\Helper\Output;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync;

class CmsPage extends AbstractExport
{
    /** @var mixed */
    protected $_pageId;

    /** @var PageInterface */
    protected $cmsPage;

    /** @var CmsPageFactory  */
    protected $cmsPageFactory;

    /** @var PageResourceModel  */
    protected $cmsPageResource;

    /** @var PageRepositoryInterface  */
    protected $pageRepository;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /**
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CmsPageFactory $cmsPageFactory
     * @param PageResourceModel $cmsPageResource
     * @param Filesystem $filesystem
     * @param ImageSync $imageSync
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectToString $objectToString
     * @param Output $outputHelper
     */
    public function __construct(
        PageRepositoryInterface $pageRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CmsPageFactory $cmsPageFactory,
        PageResourceModel $cmsPageResource,
        Filesystem $filesystem,
        ImageSync $imageSync,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        ObjectToString $objectToString,
        Output $outputHelper
    ) {
        $this->pageRepository = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cmsPageFactory = $cmsPageFactory;
        $this->cmsPageResource = $cmsPageResource;
        parent::__construct($filesystem, $imageSync, $resourceConnection, $scopeConfig, $objectToString, $outputHelper);
    }

    /**
     * Get CmsPageModel
     *
     * @return CmsPageModel|DataObject
     */
    private function getCmsPage(): PageInterface
    {
        if (!$this->cmsPage || $this->isMassExport()) {
            $this->cmsPage = $this->cmsPageFactory->create();
            $this->cmsPageResource->load($this->cmsPage, $this->_pageId);
        }
        return $this->cmsPage;
    }

    /**
     * @inheritDoc
     */
    public function setDataIdentifiers(array $dataIdentifiers): DataPatchExportInterface
    {
        $this->_pageId = $dataIdentifiers['page_id'];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return str_replace("-", "_", $this->getCmsPage()->getIdentifier());
    }

    /**
     * @inheritDoc
     */
    public function prepareObjectArray(): array
    {
        $pageData = $this->getCmsPage()->getData();
        unset($pageData['page_id']);

        return [
            'pageData' => $this->objectToString->convertDataArrayToString($pageData),
            'pageIdentifier' => $this->getCmsPage()->getIdentifier(),
            'images' => $this->getImagesFromContent($pageData['content']),
            'imgArray' => $this->objectToString->convertDataArrayToString(
                $this->getImagesFromContent($pageData['content']),
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
        $pages = $this->pageRepository->getList($searchCriteria)->getItems();

        $pageIds = [];
        foreach ($pages as $page) {
            $pageIds[] = $page->getId();
        }

        return $pageIds;
    }
}
