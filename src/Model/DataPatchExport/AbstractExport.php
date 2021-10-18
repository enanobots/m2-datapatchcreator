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

namespace Nanobots\DataPatchCreator\Model\DataPatchExport;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Nanobots\DataPatchCreator\Converters\ObjectToString;
use Nanobots\DataPatchCreator\Helper\Output;
use Nanobots\DataPatchCreator\Model\Config\Source\ExportType;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSync;
use Nanobots\DataPatchCreator\Model\ImagesSync\ImageSyncInterface;
use Magento\Framework\Filesystem;
use ReflectionClass;

abstract class AbstractExport implements DataPatchExportInterface
{
    /** @var string  */
    public const XPATH_NAMESPACE = 'nanobots_datapatchcreator/export/namespace';

    /** @var string  */
    public const XPATH_MODULE = 'nanobots_datapatchcreator/export/module';

    /** @var string  */
    public const XPATH_EXPORT_LOCAL_PATH = 'nanobots_datapatchcreator/export/local_path';

    /** @var string  */
    public const XPATH_CONFIG_IMAGE_SYNC_ENABLED = 'nanobots_datapatchcreator/images/sync';

    /** @var string  */
    public const XPATH_CONFIG_IMAGE_SYNC_MODEL = 'nanobots_datapatchcreator/images/sync_model';

    /** @var string  */
    public const XPATH_CONFIG_EXPORT_SWATCHES = 'nanobots_datapatchcreator/images/swatches';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var Output  */
    protected $outputHelper;

    /** @var ObjectToString  */
    protected $objectToString;

    /** @var AdapterInterface  */
    protected $connection;

    /** @var array  */
    public $base64Images = [];

    /** @var ImageSync  */
    protected $imageSync;

    /** @var Filesystem  */
    protected $filesystem;

    /** @var bool */
    private $_massExport = false;

    /**
     * @param Filesystem $filesystem
     * @param ImageSync $imageSync
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectToString $objectToString
     * @param Output $outputHelper
     */
    public function __construct(
        FileSystem $filesystem,
        ImageSync $imageSync,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        ObjectToString $objectToString,
        Output $outputHelper
    ) {
        $this->filesystem = $filesystem;
        $this->imageSync = $imageSync;
        $this->connection = $resourceConnection->getConnection();
        $this->scopeConfig = $scopeConfig;
        $this->objectToString = $objectToString;
        $this->outputHelper = $outputHelper;
    }

    /**
     * Class short name
     *
     * @return string+
     */
    public function getShortClassName(): string
    {
        $classReflection = new ReflectionClass($this);
        return $classReflection->getShortName();
    }

    /**
     * @inheritDoc
     */
    public function getDataPatchClassName(): string
    {
        return $this->getShortClassName() .
            implode('', array_map('ucfirst', explode('_', $this->getIdentifier())));
    }

    /**
     * Get namespace from configuration
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->scopeConfig->getValue(self::XPATH_NAMESPACE);
    }

    /**
     * Get module name from configuration
     *
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->scopeConfig->getValue(self::XPATH_MODULE);
    }

    /**
     * Check if image export to base64 is enabled
     *
     * @return bool
     */
    public function isImageSyncEnabled(): bool
    {
        return (int)$this->scopeConfig->getValue(self::XPATH_CONFIG_IMAGE_SYNC_ENABLED) === 1;
    }

    /**
     * Get sync model object
     *
     * @return ImageSyncInterface
     */
    public function getSyncModel(): ImageSyncInterface
    {
        return $this->imageSync->getSyncModel(
            $this->scopeConfig->getValue(self::XPATH_CONFIG_IMAGE_SYNC_MODEL)
        );
    }

    /**
     * Just dump images using class implementing ImageSyncInterface
     *
     * @param array $imageArray
     */
    public function dumpImages(array $imageArray): void
    {
        if ($this->isImageSyncEnabled()) {
            $syncModel = $this->getSyncModel();
            if ($syncModel instanceof ImageSyncInterface) {
                foreach ($imageArray as $image) {
                    $syncModel->dumpImage($image);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function prepareDataPatch(): array
    {
        $objectData = $this->prepareObjectArray();
        $this->outputHelper->setTemplateVariables(
            array_merge(
                [
                    'namespace' => $this->getNamespace(),
                    'module' => $this->getModuleName(),
                    'class' => $this->getDataPatchClassName(),
                    'imageSync' => $this->isImageSyncEnabled(),
                    'syncModel' => $this->scopeConfig->getValue(self::XPATH_CONFIG_IMAGE_SYNC_MODEL),
                ],
                $objectData
            )
        );

        $this->dumpImages($objectData['images']);

        try {
            $fileData = [
                'fileName' => sprintf('%s.php', $this->getDataPatchClassName()),
                'content' => $this->outputHelper->generateDataPatchContent(
                    sprintf("%s.txt", $this->getShortClassName())
                )
            ];

            if ($this->scopeConfig->getValue(ExportType::XPATH_EXPORT_TYPE) == ExportType::EXPORT_TYPE_LOCAL_FILE) {
                $destinationFile = $this->scopeConfig->getValue(self::XPATH_EXPORT_LOCAL_PATH) . $fileData['fileName'];
                $fileData['fullPath'] = $this->outputHelper->writePatchFile($destinationFile, $fileData['content']);
            }

            return $fileData;
        } catch (FileSystemException | \Exception $e) {
            throw new LocalizedException(__("Error creating data patch data: " . $e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function prepareMassPatches(array $patchIds): array
    {
        $dataPatchFiles = [];
        foreach ($patchIds as $patchId) {
            $this->setDataIdentifiers($patchId);
            $dataPatchFiles[] = $this->prepareDataPatch();
        }

        if ($this->scopeConfig->getValue(ExportType::XPATH_EXPORT_TYPE) == ExportType::EXPORT_TYPE_DOWNLOAD) {
            $tmpFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
            $zip = new \ZipArchive();
            $zipFileName = sprintf('%ssPackage%s.zip', $this->getShortClassName(), time());
            $zip->open($tmpFolder . $zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            foreach ($dataPatchFiles as $dataPatchFile) {
                $tmpFile = $tmpFolder . $dataPatchFile['fileName'];
                $this->outputHelper->writePatchFile(
                    $tmpFile,
                    $dataPatchFile['content']
                );

                $zip->addFile($tmpFile, $dataPatchFile['fileName']);
            }

            $zip->close();
            return [
                'fileName' => $zipFileName,
                'content' => $this->outputHelper->getFileDriver()->fileGetContents($tmpFolder . $zipFileName)
            ];
        } else {
            return [
                'patchCount' => count($dataPatchFiles),
                'patchLocation' => $this->scopeConfig->getValue(self::XPATH_EXPORT_LOCAL_PATH),
            ];
        }
    }

    /**
     * Get images between {{ }} page/block content
     *
     * @param string $pageContent
     * @return array
     */
    public function getImagesFromContent(string $pageContent): array
    {
        $pattern = '/&quot;(.*?)&quot;/';
        $matchArray = [];
        preg_match_all($pattern, $pageContent, $matchArray);
        return $matchArray[1];
    }

    /**
     * Encode image data to base64
     *
     * @param string $imagePath
     * @return string
     * @throws FileSystemException
     */
    public function imageToBase64(string $imagePath): string
    {
        try {
            $fileContent = $this->outputHelper->getFileDriver()->fileGetContents($imagePath);
            return base64_encode($fileContent);
        } catch (FileSystemException $e) {
            throw new FileSystemException(__('Error reading file: ' . $imagePath));
        }
    }

    /**
     * @inheritDoc
     */
    public function setMassExport(bool $massExport): DataPatchExportInterface
    {
        $this->_massExport = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isMassExport(): bool
    {
        return $this->_massExport;
    }
}
