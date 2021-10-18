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

class ExportType implements OptionSourceInterface
{
    /** @var int  */
    public const EXPORT_TYPE_DOWNLOAD = 0;

    /** @var int  */
    public const EXPORT_TYPE_LOCAL_FILE = 1;

    /** @var string  */
    public const XPATH_EXPORT_TYPE = 'nanobots_datapatchcreator/export/type';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::EXPORT_TYPE_DOWNLOAD, 'label' => __('Download dataPatch files')],
            ['value' => self::EXPORT_TYPE_LOCAL_FILE, 'label' => __('Save file(s) locally')]
        ];
    }
}
