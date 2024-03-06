<?php

declare(strict_types=1);

namespace Danaluc\CustomLogsViewer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends AbstractHelper
{
    public const XML_FIELD_CUSTOM_LOGS_FILES = 'custom_logs_viewer/general_settings/custom_logs_files';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Json $json
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->json = $json;
        parent::__construct($context);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    protected function getConfigValue(string $field, $storeId = null)
    {
        $storeId = $storeId ?? $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getCustomLogsFilesConfig(int $storeId = null): array
    {
        $logsConfig = [];
        $customLogFilesConfig = $this->getConfigValue(self::XML_FIELD_CUSTOM_LOGS_FILES, $storeId);
        if (!empty($customLogFilesConfig)) {
            $customFilesConfigArray = $this->json->unserialize($customLogFilesConfig);
            foreach ($customFilesConfigArray as $customFileConfigArray) {
                $logsConfig[] = [
                    'label' => isset($customFileConfigArray['custom_log_file_label']) 
                        ? $customFileConfigArray['custom_log_file_label']
                        : '',
                    'file' => $customFileConfigArray['custom_log_file_name'],
                    'days' => $customFileConfigArray['custom_log_clean_every'] ?? 0
                ];
            }
        }
        return $logsConfig;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getCustomLogsFilesNames(int $storeId = null): array
    {
        $customLogsFilesNames = [];
        $customFilesConfigArray = $this->getCustomLogsFilesConfig();
        if (!empty($customFilesConfigArray)) {
            //$customFilesConfigArray = $this->json->unserialize($customLogFilesConfig);
            foreach ($customFilesConfigArray as $customFileConfigArray) {
                $customLogsFilesNames[] = [
                    'label' => isset($customFileConfigArray['label']) 
                        ? $customFileConfigArray['label']
                        : '',
                    'value' => $customFileConfigArray['file']
                ];
            }
        }
        return $customLogsFilesNames;
    }

     /**
     * Get custom log file content
     *
     * @return string
     */
    public function getDefaultLogFileLabel(): string
    {
        $fileLabel = 'Custom log file';
        $logFiles = $this->getCustomLogsFilesNames();
        if (!empty($logFiles) && isset($logFiles[0]['label'])) {
            $fileLabel = $logFiles[0]['label'] . ' (' . $logFiles[0]['value'] . ')'; 
        }
        return $fileLabel;
    }

    /**
     * Get log file label
     *
     * @param string $filename
     * @return string
     */
    public function getLogFileLabel(string $filename): string
    {
        $fileLabel = 'Custom log file';
        $logFiles = $this->getCustomLogsFilesNames();
        if (!empty($logFiles)) {
            $files = array_column($logFiles, 'value');
            $position = array_search($filename,  $files);
            if (false !== $position) {
                $fileLabel = $logFiles[$position]['label'] ?? '';
            }
        }
        return $fileLabel;
    }

}
