<?php

namespace Danaluc\CustomLogsViewer\Block\Adminhtml\View\LogViewer;

class LogFiles extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Danaluc_CustomLogsViewer::view/logviewer/logfiles.phtml';

    /**
     * @var \Danaluc\CustomLogsViewer\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Danaluc\CustomLogsViewer\Helper\File
     */
    private $file;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Danaluc\CustomLogsViewer\Helper\Config $configHelper
     * @param \Danaluc\CustomLogsViewer\Helper\File $file
     * @param \Magento\Framework\Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Danaluc\CustomLogsViewer\Helper\Config $configHelper,
        \Danaluc\CustomLogsViewer\Helper\File $file,
        \Magento\Framework\Escaper $escaper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->file = $file;
        $this->escaper = $escaper;
        parent::__construct($context, $data);
    }

    /**
     * Create import services form select element
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'log_files',
            $this->getLayout()->createBlock(
                \Magento\Framework\View\Element\Html\Select::class
            )->setOptions(
                $this->configHelper->getCustomLogsFilesNames()
            )->setId(
                'log_files'
            )->setClass(
                'admin__control-select'
            )->setName(
                'log_files'
            )->setTitle(
                __('Log Files')
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Get custom log file content
     *
     * @return string
     */
    public function getLogFileContent(): string
    {
        $fileContent = '';
        $logFiles = $this->configHelper->getCustomLogsFilesNames();
        if (!empty($logFiles)) {
            $fileContent = nl2br($this->escaper->escapeHtml($this->file->getLogFileContent($logFiles[0]['value']))); 
        }
        return $fileContent;
    }

    /**
     * Get custom log file content
     *
     * @return string
     */
    public function getLogFileLabel(): string
    {
        return $this->configHelper->getDefaultLogFileLabel();
    }

    /**
     * @return string
     */
    public function getAjaxLoadContentUrl()
    {
        return $this->getUrl('customlogs/ajax/loadfilecontent');
    }

    /**
     * @return string
     */
    public function getAjaxResetContentUrl()
    {
        return $this->getUrl('customlogs/ajax/resetfilecontent');
    }

    /**
     * @return string
     */
    public function getLogFileDir(): string
    {
        return $this->file->getLogFileDir();
    }
    
}