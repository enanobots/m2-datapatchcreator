<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Block\Adminhtml\Product\Attribute;

class Grid extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Grid
{
    /**
     * @inheritDoc
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('attribute_id');
        $this->getMassactionBlock()->setTemplate('Nanobots_DataPatchCreator::product/attribute_mass_action.phtml');
        $this->getMassactionBlock()->setFormFieldName('attribute');

        $this->getMassactionBlock()->addItem(
            'data-patch-export',
            [
                'label' => __('Export to Data Patches'),
                'url' => $this->getUrl('dataPatchCreator/massExport/productAttribute'),
                'confirm' => __('Are you sure?')
            ]
        );

        return $this;
    }
}
