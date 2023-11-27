<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 * @author      Wojtek Wnuk <wojtek@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\ImagesSync;

class ImageSync
{
    private const NO_SYNC_MODEL = 'NoSync';
    /*** @var ImageSyncInterface[] */
    protected $syncModels;

    /**
     * @param array $syncModels
     */
    public function __construct(
        array $syncModels
    ) {
        $this->syncModels = $syncModels;
    }

    /**
     * Gets sync objects
     *
     * @return array|ImageSyncInterface[]
     */
    public function getSyncModels(): array
    {
        return $this->syncModels;
    }

    /**
     * Get ImageSyncInterface
     *
     * @param string|null $syncModel
     * @return ImageSyncInterface
     */
    public function getSyncModel(?string $syncModel): ImageSyncInterface
    {
        if (empty($syncModel)
            || !is_array($this->syncModels)
            || empty($this->syncModels)
            || !array_key_exists($syncModel, $this->syncModels)) {
            return $this->syncModels[self::NO_SYNC_MODEL];
        }
        return $this->syncModels[$syncModel];
    }
}
