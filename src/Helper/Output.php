<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Helper;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File as FileIo;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filter\Template as FilterTemplate;
use Magento\Framework\Module\Dir as ModuleDir;

class Output extends AbstractHelper
{
    /** @var string  */
    public const MODULE = 'Nanobots_DataPatchCreator';

    /** @var string  */
    public const BASE_DIR = '{{baseDir}}';

    /** @var ModuleDir  */
    protected $moduleDir;

    /** @var FilterTemplate  */
    protected $templateFilter;

    /** @var File  */
    protected $fileDriver;

    /** @var FileIo  */
    protected $fileIo;

    /** @var DirectoryList  */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     * @param FileIo $fileIo
     * @param File $fileDriver
     * @param ModuleDir $moduleDir
     * @param FilterTemplate $templateFilter
     * @param Context $context
     */
    public function __construct(
        DirectoryList $directoryList,
        FileIo $fileIo,
        File $fileDriver,
        ModuleDir $moduleDir,
        FilterTemplate $templateFilter,
        Context $context
    ) {
        $this->directoryList = $directoryList;
        $this->fileIo = $fileIo;
        $this->fileDriver = $fileDriver;
        $this->moduleDir = $moduleDir;
        $this->templateFilter = $templateFilter;
        parent::__construct($context);
    }

    /**
     * Returns a path to folder template
     *
     * @return string
     */
    private function _getTemplateFolder(): string
    {
        return $this->moduleDir->getDir(self::MODULE) .
            DIRECTORY_SEPARATOR . '_templates' . DIRECTORY_SEPARATOR;
    }

    /**
     * Pass array to template engine
     *
     * @param array $variables
     * @return $this
     */
    public function setTemplateVariables(array $variables): Output
    {
        $this->templateFilter->setVariables($variables);
        return $this;
    }

    /**
     * Just a testing function
     *
     * @param string $templateFile
     * @return string
     * @throws FileSystemException
     * @throws Exception
     */
    public function generateDataPatchContent(string $templateFile): string
    {
        $fileContent = $this->fileDriver->fileGetContents(
            $this->_getTemplateFolder() . $templateFile
        );

        return $this->templateFilter->filter($fileContent);
    }

    /**
     * Return file driver
     *
     * @return File
     */
    public function getFileDriver(): File
    {
        return $this->fileDriver;
    }

    /**
     * Create data patch File
     *
     * @param string $filePath
     * @param string $fileContent
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function writePatchFile(string $filePath, string $fileContent): string
    {
        $filePath = str_replace(self::BASE_DIR, $this->directoryList->getRoot(), $filePath);
        $destinationFolder = $this->fileIo->getDestinationFolder($filePath);

        try {
            if ($this->fileIo->checkAndCreateFolder($destinationFolder)) {
                $this->fileDriver->filePutContents($filePath, $fileContent);
            }
        } catch (LocalizedException|FileSystemException $e) {
            throw new FileSystemException(
                __(
                    'There was a problem with creating a patch file %1, error: %2',
                    $filePath,
                    $e->getMessage()
                )
            );
        }
        return $filePath;
    }
}
