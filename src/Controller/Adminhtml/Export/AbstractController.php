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

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Nanobots\DataPatchCreator\Model\Config\Source\ExportType;
use Nanobots\DataPatchCreator\Model\DataPatchExport\DataPatchExportInterface;

abstract class AbstractController extends Action
{
    /** @var FileFactory  */
    protected $fileFactory;

    /** @var DataPatchExportInterface  */
    protected $dataPatchExport;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param FileFactory $fileFactory
     * @param MessageManagerInterface $messageManager
     * @param DataPatchExportInterface $dataPatchExport
     * @param Context $context
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        FileFactory $fileFactory,
        MessageManagerInterface $messageManager,
        DataPatchExportInterface $dataPatchExport,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->dataPatchExport = $dataPatchExport;
        parent::__construct($context);
    }

    /**
     * Set data Identifiers for rest of the classes
     *
     * @return array
     */
    abstract public function getDataIdentifiers(): array;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->dataPatchExport->setDataIdentifiers($this->getDataIdentifiers());
        $fileData = $this->dataPatchExport->prepareDataPatch();
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            switch ($this->scopeConfig->getValue(ExportType::XPATH_EXPORT_TYPE)) {
                case ExportType::EXPORT_TYPE_DOWNLOAD: {
                    return $this->fileFactory->create(
                        $fileData['fileName'],
                        $fileData['content'],
                    );
                }
                case ExportType::EXPORT_TYPE_LOCAL_FILE: {
                    $this->messageManager->addSuccessMessage(
                        __('File %1 been created, file path: %2', $fileData['fileName'], $fileData['fullPath'])
                    );
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
                default: {
                    throw new LocalizedException(__('Unsupported export type'));
                }

            }
        } catch (LocalizedException | Exception $e) {
            $this->messageManager->addErrorMessage(
                __(
                    'Could not create a data patch %1, error: %2',
                    $fileData['fileName'],
                    $e->getMessage(),
                )
            );
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
