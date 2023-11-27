<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 * @author      Wojtek Wnuk <wojtek@qsolutionsstudio.com>
 * @author      Lukasz Owczarczuk <lukasz@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Model\ImagesSync;

class NoSync implements ImageSyncInterface
{
    /**
     * @inheritDoc
     */
    public function dumpImage(string $imagePath): void
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function fetchImage(string $imagePath): void
    {
        return;
    }
}
