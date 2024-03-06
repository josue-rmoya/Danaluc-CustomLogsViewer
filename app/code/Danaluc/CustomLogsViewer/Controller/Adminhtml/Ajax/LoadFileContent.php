<?php

namespace Danaluc\CustomLogsViewer\Controller\Adminhtml\Ajax;

use Danaluc\CustomLogsViewer\Helper\File;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class LoadFileContent extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Danaluc_CustomLogsViewer::view_logs_file';

    /**
     * @var File
     */
    private $file;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var Danaluc\CustomLogsViewer\Helper\Config
     */
    private $configHelper;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * LoadFileContent constructor.
     *
     * @param File $file
     * @param Context $context
     * @param Escaper $escaper
     * @param \Danaluc\CustomLogsViewer\Helper\Config $configHelper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        File $file,
        DriverInterface $driver,
        Context $context,
        Escaper $escaper,
        \Danaluc\CustomLogsViewer\Helper\Config $configHelper,
        JsonFactory $resultJsonFactory,
    ) {
        $this->file = $file;
        $this->driver = $driver;
        $this->escaper = $escaper;
        $this->configHelper = $configHelper;
        $this->request = $context->getRequest();
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Ajax get log file content.
     *
     * @return Json
     */
    public function execute()
    {
        if (!$this->request->isAjax()) {
            return;
        }
        $logFile = $this->escaper->escapeHtml($this->request->getParam('log'));
        if ($this->driver->isExists($this->file->getLogFilePath($logFile))) {
            $content = nl2br($this->escaper->escapeHtml($this->file->getLogFileContent($logFile)));
        } else {
            $content = "$logFile does not exist.";
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'content' => $content,
                'header' => $this->configHelper->getLogFileLabel($logFile) . " ($logFile)"
            ]
        );
        return $resultJson;
    }
}