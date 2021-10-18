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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Nanobots\DataPatchCreator\Model\Config\Source\ExportType;
use Nanobots\DataPatchCreator\Model\DataPatchExport\DataPatchExportInterface;

abstract class AbstractController extends Action
{
    /** @var DataPatchExportInterface  */
    protected $dataPatchExport;

    /** @var FileFactory */
    protected $fileFactory;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param FileFactory $fileFactory
     * @param Context $context
     * @param DataPatchExportInterface $dataPatchExport
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        FileFactory $fileFactory,
        Context $context,
        DataPatchExportInterface $dataPatchExport
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->fileFactory = $fileFactory;
        $this->dataPatchExport = $dataPatchExport;
        parent::__construct($context);
    }

    /**
     * Gets identifiers
     *
     * @return array
     */
    abstract public function getIdentifiers(): array;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $identifiersArray = $this->getIdentifiers();
        $patchesData = $this->dataPatchExport
            ->setMassExport(true)
            ->prepareMassPatches($identifiersArray);
        try {
            switch ($this->scopeConfig->getValue(ExportType::XPATH_EXPORT_TYPE)) {
                case ExportType::EXPORT_TYPE_DOWNLOAD: {
                    return $this->fileFactory->create(
                        $patchesData['fileName'],
                        $patchesData['content'],
                        DirectoryList::ROOT,
                        'application/zip'
                    );
                }
                case ExportType::EXPORT_TYPE_LOCAL_FILE: {
                    $this->messageManager->addSuccessMessage(
                        __(
                            '%1 Data Patch file(s) have been created, location: %2',
                            $patchesData['patchCount'],
                            $patchesData['patchLocation'],
                        )
                    );
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
                default: {
                    throw new LocalizedException(__('Unsupported export type'));
                }
            }
        } catch (LocalizedException|Exception $e) {
            $this->messageManager->addErrorMessage(
                __(
                    'Could not create a data patches, error: %1',
                    $e->getMessage(),
                )
            );
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
