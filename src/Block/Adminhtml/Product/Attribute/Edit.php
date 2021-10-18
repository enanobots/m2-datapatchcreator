<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Block\Adminhtml\Product\Attribute;

use Magento\Eav\Model\Attribute;

class Edit extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        /** @var Attribute $attribute */
        $attribute = $this->_coreRegistry->registry('entity_attribute');

        if ($attributeId = $attribute->getId()) {
            $this->addButton(
                'export_to_data_patch',
                [
                    'label' => __('Export To DataPatch'),
                    'on_click' => sprintf("location.href = '%s?attribute_id=%s';", $this->getExportUrl(), $attributeId),
                    'class' => 'action-secondary export-to-data-patch',
                    'sort_order' => 100
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getExportUrl(): string
    {
        return $this->getUrl('dataPatchCreator/export/productAttribute');
    }
}
