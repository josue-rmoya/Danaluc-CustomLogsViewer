<?php

namespace  Danaluc\CustomLogsViewer\Helper;

use DateTime;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Creates the csv files in export folder and move to archive when it's complete.
 * Log info and debug to a custom log file connector.log
 */
class File
{
    private const LOG_SIZE_LIMIT = 500000;

    /**
     * @var string
     */
    private $outputFolder;

    /**
     * @var string
     */
    private $outputArchiveFolder;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var Danaluc\CustomLogsViewer\Helper\Config
     */
    private $configHelper;

    /**
     * File constructor.
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param DriverInterface $driver
     * @param \Danaluc\CustomLogsViewer\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        DriverInterface $driver,
        \Danaluc\CustomLogsViewer\Helper\Config $configHelper,
    ) {
        $this->directoryList        = $directoryList;
        $this->driver               = $driver;
        $this->configHelper         = $configHelper;
        $varPath                    = $directoryList->getPath('var');
        $this->outputFolder         = $varPath . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . 'customlogs';
        $this->outputArchiveFolder  = $this->outputFolder . DIRECTORY_SEPARATOR . 'archive';
    }

    /**
     * @return string
     */
    public function getLogFileDir(): string
    {
        return $this->directoryList->getPath('log') . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getLogFilePath(string $filename): string
    {
        return $this->getLogFileDir() . $filename;
    }

    /**
     * Get log file content
     *
     * @param string $filename
     *
     * @return string
     */
    public function getLogFileContent(string $filename): string
    {
        $pathLogfile = $this-> getLogFilePath($filename);

        try {
            $handle = $this->driver->fileOpen($pathLogfile, 'r');
            if (!$handle) {
                return "Could not open log file at path " . $pathLogfile;
            }

            $logFileSize = $this->driver->stat($pathLogfile)['size'];
            if ($logFileSize === 0) {
                return "This log file is empty.";
            }

            if ($logFileSize > self::LOG_SIZE_LIMIT) {
                $this->driver->fileSeek($handle, -self::LOG_SIZE_LIMIT, SEEK_END);
            }

            $contents = $this->driver->fileReadLine($handle, $logFileSize);
            if (empty($contents)) {
                return "Could not read from file at path " . $pathLogfile;
            }

            $this->driver->fileClose($handle);
            return $contents;
        } catch (\Exception $e) {
            return $e->getMessage() . $pathLogfile;
        }
    }

    /**
     * @param string $filePath
     * @return int|null
     */
    public function getDaysSinceCreation(string $filePath)
    {
        $daysSinceCreation = -1;
        if ($this->driver->isExists($filePath)) {
            $creationDate = new DateTime(date("Y-m-d", filectime($filePath)));
            $currentDate = new DateTime();
            $interval = $creationDate->diff($currentDate);
            $daysSinceCreation = $interval->days;
        }
        return $daysSinceCreation;
    }
}