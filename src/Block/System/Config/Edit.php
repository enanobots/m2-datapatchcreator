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

namespace Nanobots\DataPatchCreator\Block\System\Config;

use Magento\Framework\View\Element\AbstractBlock;

class Edit extends \Magento\Config\Block\System\Config\Edit
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout(): AbstractBlock
    {
        if ($section = $this->getRequest()->getParam('section')) {
            $storeId = $this->getRequest()->getParam('store') ?? 0;
            $this->getToolbar()->addChild(
                'export_to_data_patch',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'id' => 'export_to_data_patch',
                    'label' => __('Export To DataPatch'),
                    'class' => 'action-secondary export_to_data_patch',
                    'on_click' => sprintf(
                        "location.href = '%s?section=%s&store=%s';",
                        $this->getBackUrl(),
                        $section,
                        $storeId
                    )
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @inheritDoc
     */
    protected function getBackUrl(): string
    {
        return $this->getUrl('dataPatchCreator/export/configuration');
    }
}
