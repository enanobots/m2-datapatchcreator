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

namespace Nanobots\DataPatchCreator\Block\Adminhtml\Block\Edit;

use Magento\Cms\Block\Adminhtml\Block\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class ExportToDataPatch extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($blockId = $this->getBlockId()) {
            $data = [
                'label' => __('Export To DataPatch'),
                'on_click' => sprintf("location.href = '%s?block_id=%s';", $this->getBackUrl(), $blockId),
                'class' => 'action-secondary export-to-data-patch',
                'sort_order' => 100
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('dataPatchCreator/export/cmsBlock');
    }
}
