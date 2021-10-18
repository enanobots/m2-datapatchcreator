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

interface ImageSyncInterface
{
    /**
     * Method to move / upload / push image to designated source
     *
     * @param string $imagePath
     */
    public function dumpImage(string $imagePath): void;

    /**
     * Gets Image from Sync Model
     *
     * @param string $imagePath
     */
    public function fetchImage(string $imagePath): void;
}
