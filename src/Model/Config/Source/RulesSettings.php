<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 */

namespace Nanobots\DataPatchCreator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RulesSettings implements OptionSourceInterface
{
    /** @var int  */
    public const REPLACE_BY_ID = 0;

    /** @var string  */
    public const ADD_NEW_AND_DISABLE = 1;

    /** @var int  */
    public const NO_ACTION = 2;

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::REPLACE_BY_ID, 'label' => __('Overwrite Catalog/Sales Rule by ID')],
            ['value' => self::ADD_NEW_AND_DISABLE, 'label' => __('Create New Catalog/Sales Rule')],
            ['value' => self::NO_ACTION, 'label' => __('Do not overwrite Catalog/Sales Rule (by ID)')]
        ];
    }
}
