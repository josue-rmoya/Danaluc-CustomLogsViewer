<?php

declare(strict_types=1);

namespace Danaluc\CustomLogsViewer\Cron;

use Magento\Framework\Filesystem\DriverInterface;
use Danaluc\CustomLogsViewer\Helper\File;
use Psr\Log\LoggerInterface;

class Clean
{
    /**
     * @var \Danaluc\CustomLogsViewer\Helper\Config
     */
    private $configHelper;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var File
     */
    private $file;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;


    /**
     * @param \Danaluc\CustomLogsViewer\Helper\Config $configHelper
     * @param File $file
     * @param DriverInterface $driver
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Danaluc\CustomLogsViewer\Helper\Config $configHelper,
        File $file,
        DriverInterface $driver,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->file = $file;
        $this->driver = $driver;
    }

    public function execute() 
    {
        $this->logger->info('Cleaning logs files (Launching Cron Job).');
        $filesToRestart = $this->getFilesToRestart();
        $filesRestarted = $this->restartFiles($filesToRestart);
    }

    /**
     * Get the list of log file to clean
     *
     * @return array
     */
    private function getFilesToRestart(): array
    {
        $filesToRestart = [];
        $customLogsFilesConfig = $this->configHelper->getCustomLogsFilesConfig();
        if (!empty($customLogsFilesConfig)) {
            foreach ($customLogsFilesConfig as $customLogFilesConfig) {
                $filePath = $this->file->getLogfilePath($customLogFilesConfig['file']);
                if ($this->driver->isFile($filePath)) {
                    $days = isset($customLogFilesConfig['days']) ? (int)$customLogFilesConfig['days'] :  null;
                    if (!is_null($days) && $days !== 0) {
                        $daysSinceCreation = $this->file->getDaysSinceCreation($filePath);
                        if ($daysSinceCreation >= $days) {
                            $filesToRestart[] = $filePath;
                        }
                    }
                }
            }
        }
        return $filesToRestart;
    }

    /**
     * @param array $filesToRestart
     * @return void
     */
    private function restartFiles(array $filesToRestart): void
    {
        foreach ($filesToRestart as $fileToRestart) {
            try {
                $this->driver->fileOpen($fileToRestart, 'w');
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
