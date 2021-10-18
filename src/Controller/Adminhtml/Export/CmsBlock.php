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

namespace Nanobots\DataPatchCreator\Controller\Adminhtml\Export;

use Magento\Cms\Model\BlockFactory;

class CmsBlock extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getDataIdentifiers(): array
    {
        return [
            'block_id' => $this->getRequest()->getParam('block_id')
        ];
    }
}

