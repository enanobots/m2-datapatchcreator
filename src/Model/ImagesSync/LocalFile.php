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

namespace Nanobots\DataPatchCreator\Model\ImagesSync;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;

class LocalFile implements ImageSyncInterface
{
    /** @var string  */
    public const XPATH_FILE_SYNC_DESTINATION_DIR = 'nanobots_datapatchcreator/images/local_folder';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var DirectoryList  */
    protected $directoryList;

    /** @var File  */
    protected $fileIo;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param DirectoryList $directoryList
     * @param File $fileIo
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList,
        File $fileIo
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
        $this->fileIo = $fileIo;
    }

    /**
     * Returns destination folder for images storage
     *
     * @return string
     * @throws FileSystemException
     */
    private function _getLocalFolder(): string
    {
        $destinationFolder = $this->scopeConfig->getValue(self::XPATH_FILE_SYNC_DESTINATION_DIR);
        return str_replace(
            ['{{baseDir}}'],
            [$this->directoryList->getPath(DirectoryList::ROOT)],
            $destinationFolder
        );
    }

    /**
     * Get Magento 2 media dir
     *
     * @return string
     * @throws FileSystemException
     */
    private function _getMediaDir(): string
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR;
    }

    /**
     * @inheritDoc
     */
    public function dumpImage(string $imagePath): void
    {
        /**
         * imagePath = everything after media/*
         * attribute/swatch/p/r/product_swatch.jpg
         * wysiwyg/image.jpg
         * random_folder/something/magento.png
         * without trailing slash
         */

        try {
            $sourceFile =  $this->_getMediaDir() . $imagePath;
            $targetFile = $this->_getLocalFolder() . $imagePath;
            $destinationFolder = $this->fileIo->getDestinationFolder($targetFile);

            if ($this->fileIo->checkAndCreateFolder($destinationFolder)) {
                $this->fileIo->cp($sourceFile, $targetFile);
            }
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                __('There was an issue with folder creation or file copy %1', $e->getMessage())
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchImage(string $imagePath): void
    {
        try {
            $sourceFile = $this->_getLocalFolder() . $imagePath;
            $targetFile = $this->_getMediaDir() . $imagePath;
            $destinationFolder = $this->fileIo->getDestinationFolder($targetFile);

            if ($this->fileIo->checkAndCreateFolder($destinationFolder)) {
                $this->fileIo->cp($sourceFile, $targetFile);
            }
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                __('There was an issue with folder creation  or file copy %1', $e->getMessage())
            );
        }
    }
}
