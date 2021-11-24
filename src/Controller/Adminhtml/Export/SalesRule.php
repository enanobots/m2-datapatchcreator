<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 */

namespace Nanobots\DataPatchCreator\Controller\Adminhtml\Export;

use Magento\Cms\Model\BlockFactory;

class SalesRule extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getDataIdentifiers(): array
    {
        return [
            'rule_id' => $this->getRequest()->getParam('rule_id')
        ];
    }
}
