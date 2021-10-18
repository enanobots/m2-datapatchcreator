<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\Framework\Exception\LocalizedException;

interface DataPatchExportInterface
{
    /**
     * Sets Identifies in data processing
     *
     * @param array $dataIdentifiers
     * @return $this
     */
    public function setDataIdentifiers(array $dataIdentifiers): self;

    /**
     * Gets an Object identifiers
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Prepares array from an object which will be passed to template variables
     *
     * @return array
     */
    public function prepareObjectArray(): array;

    /**
     * Creates a single DataPatch File
     *
     * @return array
     */
    public function prepareDataPatch(): array;

    /**
     * Return a DataPatch File name based on object class and Identifiers
     *
     * @return string
     */
    public function getDataPatchClassName(): string;

    /**
     * Sets Mass Export
     *
     * @param bool $massExport
     * @return $this
     */
    public function setMassExport(bool $massExport): self;

    /**
     * Is Mass export set
     *
     * @return bool
     */
    public function isMassExport(): bool;

    /**
     * Prepare Mass Patch files / zip package and return file array
     *
     * @param array $patchIds
     * @return array
     */
    public function prepareMassPatches(array $patchIds): array;

    /**
     * Get all Identifiers (IDs) for data pach
     *
     * @return array
     */
    public function getAllIdentifiers(): array;
}
