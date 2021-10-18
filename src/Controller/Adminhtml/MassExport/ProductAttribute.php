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

namespace Nanobots\DataPatchCreator\Controller\Adminhtml\MassExport;

class ProductAttribute extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        $ids = $this->getRequest()->getParam('attribute') ?? $this->dataPatchExport->getAllIdentifiers();
        $identifiersArray = [];
        foreach ($ids as $id) {
            $identifiersArray[] = [
                'attribute_id' => $id
            ];
        }
        return $identifiersArray;
    }
}
