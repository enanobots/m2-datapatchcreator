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

class CouponSettings implements OptionSourceInterface
{
    /** @var int  */
    public const ADD_TO_NEW_RULE = 0;

    /** @var int  */
    public const NO_ACTION = 2;

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ADD_TO_NEW_RULE, 'label' => __('Move Coupon to New Rule (when created)')],
            ['value' => self::NO_ACTION, 'label' => __('Do not change coupon settings')],
        ];
    }
}
