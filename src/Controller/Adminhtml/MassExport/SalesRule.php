<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 */

namespace Nanobots\DataPatchCreator\Controller\Adminhtml\MassExport;

class SalesRule extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        $ids = $this->getRequest()->getParam('rule_id') ?? $this->dataPatchExport->getAllIdentifiers();
        $identifiersArray = [];
        foreach ($ids as $id) {
            $identifiersArray[] = [
                'rule_id' => $id
            ];
        }
        return $identifiersArray;
    }
}
