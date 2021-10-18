<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\Eav\Model\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Nanobots\DataPatchCreator\Converters\ObjectToString;
use Nanobots\DataPatchCreator\Helper\Output;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync;

class ProductAttribute extends AbstractExport
{
    /** @var string[]  */
    public const REVERSE_ATTRIBUTE_MAP = [
        'backend_model' => 'backend' ,
        'backend_type' => 'type',
        'backend_table' => 'table',
        'frontend_model' => 'frontend',
        'frontend_input' => 'input',
        'frontend_label' => 'label',
        'source_model' => 'source',
        'is_required' => 'required',
        'is_user_defined' => 'user_defined',
        'default_value' => 'default',
        'is_unique' => 'unique',
        'is_global' => 'global',
        'is_visible' => 'visible',
        'is_searchable' => 'searchable',
        'is_filterable' => 'filterable',
        'is_comparable' => 'comparable',
        'is_visible_on_front' => 'visible_on_front',
        'is_wysiwyg_enabled' => 'wysiwyg_enabled',
        'is_visible_in_advanced_search' => 'visible_in_advanced_search',
        'is_filterable_in_search' => 'filterable_in_search',
        'is_used_for_promo_rules' => 'used_for_promo_rules',
    ];

    /** @var int  */
    private $_attributeId = 0;

    /** @var Attribute */
    private $_attributeModel;

    /** @var AttributeRepository  */
    protected $attributeRepository;

    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeResource  */
    protected $attributeResource;

    /** @var DirectoryList  */
    protected $directoryList;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeResource
     * @param AttributeRepository $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filesystem $filesystem
     * @param ImageSync $imageSync
     * @param DirectoryList $directoryList
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectToString $objectToString
     * @param Output $outputHelper
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeResource,
        AttributeRepository $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filesystem $filesystem,
        ImageSync $imageSync,
        DirectoryList $directoryList,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        ObjectToString $objectToString,
        Output $outputHelper
    ) {
        $this->directoryList = $directoryList;
        $this->attributeResource = $attributeResource;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($filesystem, $imageSync, $resourceConnection, $scopeConfig, $objectToString, $outputHelper);
    }

    /**
     * @inheritDoc
     */
    public function setDataIdentifiers(array $dataIdentifiers): DataPatchExportInterface
    {
        $this->_attributeId = (int)$dataIdentifiers['attribute_id'] ?? null;
        return $this;
    }

    /**
     * Get Attribute ID
     *
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->_attributeId;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->getAttribute($this->getAttributeId())->getAttributeCode();
    }

    /**
     * Gets attribute model based on attribute Id
     *
     * @param int $attributeId
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute|Attribute
     */
    public function getAttribute(int $attributeId)
    {
        if (!$this->_attributeModel || $this->isMassExport()) {
            $this->_attributeModel = $this->attributeResource->setEntityTypeId(4)
                ->load($attributeId);
        }
        return $this->_attributeModel;
    }

    /**
     * @inheritDoc
     */
    public function prepareObjectArray(): array
    {
        $attributeId = $this->getAttributeId();

        /** @var Attribute $attributeModel */
        $attributeModel = $this->getAttribute($attributeId);
        $attributeData = $attributeModel->getData();
        $attributeAdditionalData = $attributeData['additional_data'];
        unset($attributeData['additional_data']); //
        $this->_reverseAttributeArrayMap($attributeData);

        return [
            'attributeCode' => $attributeModel->getAttributeCode(),
            'attributeData' => $this->objectToString->convertDataArrayToString($attributeData, 16),
            'storeLabels' => $this->objectToString->convertDataArrayToString($attributeModel->getStoreLabels()),
            'attributeOptions' => $this->objectToString->convertDataArrayToString(
                $this->_attributeOptionsToDataPatch($this->_getOptionsIds($attributeId))
            ),
            // this is on purpose as ['additional_data'] is not processed on attribute creation
            'additionalData' => var_export($attributeAdditionalData, true),
            'swatchImages' => $this->objectToString->convertDataArrayToString($this->base64Images),
            'imageSync' => $this->isImageSyncEnabled(),
            'images' => [],
        ];
    }

    /**
     * Due to Magento 2 legacy code, some array data needs to be reversed to different set of array assoc keys
     *
     * @param array $attributeData
     * @return void
     */
    private function _reverseAttributeArrayMap(array &$attributeData): void
    {
        foreach (self::REVERSE_ATTRIBUTE_MAP as $dbKey => $reverseKey) {
            $attributeData[$reverseKey] = $attributeData[$dbKey];
            unset($attributeData[$dbKey]);
        }

        // we don't need this data
        unset(
            $attributeData['additional_data'],
            $attributeData['attribute_id'],
            $attributeData['attribute_code'],
            $attributeData['entity_type_id']
        );
    }

    /**
     * Return attribute option Ids from database
     *
     * @param int $attributeId
     * @return array
     */
    private function _getOptionsIds(int $attributeId): array
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName('eav_attribute_option'), 'option_id')
            ->where('attribute_id = ?', $attributeId)
            ->order('sort_order ASC');

        return $this->connection->fetchCol($select);
    }

    /**
     * Get option values for attribute and for swatches
     *
     * @param mixed $optionId
     * @param string $table
     * @param array $columns
     * @return array
     */
    private function _getOptionValues($optionId, string $table, array $columns): array
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName($table), $columns)
            ->where('option_id = ?', $optionId)
            ->order('store_id asc');

        return $this->connection->fetchAll($select);
    }

    /**
     * Check if swatches export is enabled
     *
     * @return bool
     */
    private function _isSwatchExportEnabled(): bool
    {
        return $this->scopeConfig->getValue(self::XPATH_CONFIG_EXPORT_SWATCHES) == 1;
    }

    /**
     * Data which will be inserted to database
     *
     * @param array $optionIds
     * @return string[]
     * @throws FileSystemException
     */
    private function _attributeOptionsToDataPatch(array $optionIds): array
    {
        $optionTable = [];
        foreach ($optionIds as $optionId) {
            $swatches = $this->_getOptionValues(
                $optionId,
                'eav_attribute_option_swatch',
                ['store_id', 'type', 'value']
            );

            $optionTable[] = [
                'labels' => $this->_getOptionValues(
                    $optionId,
                    'eav_attribute_option_value',
                    ['store_id', 'value']
                ),
                'swatches' => $swatches
            ];

            if ($this->_isSwatchExportEnabled()) {
                foreach ($swatches as $swatch) {
                    if ((int)$swatch['type'] === 2) {
                        $this->base64Images[$swatch['value']] =
                            $this->imageToBase64($this->getSwatchesDirectory() . $swatch['value']);
                    }
                }
            }
        }

        return $optionTable;
    }

    /**
     * Get full path to images swatches dir
     *
     * @return string
     * @throws FileSystemException
     */
    public function getSwatchesDirectory(): string
    {
        return $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) .
            DIRECTORY_SEPARATOR . \Magento\Swatches\Helper\Media::SWATCH_MEDIA_PATH ;
    }

    /**
     * @inheritDoc
     */
    public function getAllIdentifiers(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $attributes = $this->attributeRepository->getList($searchCriteria)->getItems();

        $attributeIds = [];
        foreach ($attributes as $attribute) {
            $attributeIds[] = $attribute->getAttributeId();
        }

        return $attributeIds;
    }
}
