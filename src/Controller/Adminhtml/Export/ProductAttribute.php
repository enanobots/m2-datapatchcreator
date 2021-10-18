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

class ProductAttribute extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getDataIdentifiers(): array
    {
        return ['attribute_id' => (int)$this->getRequest()->getParam('attribute_id')];
    }
}
