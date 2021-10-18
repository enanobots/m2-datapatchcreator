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

class CmsPage extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getDataIdentifiers(): array
    {
        return [
            'page_id' => $this->getRequest()->getParam('page_id')
        ];
    }
}
