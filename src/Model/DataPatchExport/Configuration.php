<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

class Configuration extends AbstractExport
{
    /** @var string  */
    private $_configSection = '';

    /** @var string  */
    private $_storeId = '';

    /**
     * @inheritDoc
     */
    public function setDataIdentifiers(array $dataIdentifiers): DataPatchExportInterface
    {
        $this->_configSection = $dataIdentifiers['section_id'];
        $this->_storeId = $dataIdentifiers['store_id'];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->_configSection . sprintf('StoreId%s', $this->_storeId);
    }

    /**
     * @inheritDoc
     */
    public function prepareObjectArray(): array
    {
        $query = $this->connection->select()
            ->from($this->connection->getTableName('core_config_data'))
            ->where('path like ?', sprintf('%s/%%', $this->_configSection))
            ->where('scope_id = ?', $this->_storeId);

        return [
            'configSections' => $this->connection->fetchAssoc($query),
            'sectionName' => $this->getIdentifier(),
            'images' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAllIdentifiers(): array
    {
        return [];
    }
}
