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

namespace Nanobots\DataPatchCreator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Nanobots\DataPatchCreator\Model\ImagesSync\LocalFile;

class SyncMethods implements OptionSourceInterface
{
    /** @var int  */
    public const EXPORT_TYPE_DOWNLOAD = 0;

    /** @var int  */
    public const EXPORT_TYPE_LOCAL_FILE = 1;

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => "LocalFile", 'label' => __('Copy images to designated folder')],
        ];
    }
}
