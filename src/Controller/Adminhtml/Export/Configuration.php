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

class Configuration extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getDataIdentifiers(): array
    {
        return [
            'section_id' => $this->getRequest()->getParam('section'),
            'store_id' => $this->getRequest()->getParam('store')
        ];
    }
}
