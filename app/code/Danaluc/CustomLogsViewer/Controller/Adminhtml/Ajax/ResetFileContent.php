<?php

namespace Danaluc\CustomLogsViewer\Controller\Adminhtml\Ajax;

use Magento\Framework\Filesystem\DriverInterface;
use Danaluc\CustomLogsViewer\Helper\File;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class ResetFileContent extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Danaluc_CustomLogsViewer::view_logs_file';

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var \Danaluc\CustomLogsViewer\Helper\File
     */
    private $file;

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
     * @param DriverInterface $driver
     * @param \Danaluc\CustomLogsViewer\Helper\File $file
     * @param Context $context
     * @param Escaper $escaper
     * @param \Danaluc\CustomLogsViewer\Helper\Config $configHelper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        DriverInterface $driver,
        \Danaluc\CustomLogsViewer\Helper\File $file,
        Context $context,
        Escaper $escaper,
        \Danaluc\CustomLogsViewer\Helper\Config $configHelper,
        JsonFactory $resultJsonFactory,
    ) {
        $this->driver = $driver;
        $this->file = $file;
        $this->escaper = $escaper;
        $this->configHelper = $configHelper;
        $this->request = $context->getRequest();
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Ajax clean log file.
     *
     * @return Json
     */
    public function execute()
    {
        if (!$this->request->isAjax()) {
            return;
        }
        $fileToRestart = $this->escaper->escapeHtml($this->request->getParam('fileToRestart'));
        $fileConfigData =  $this->escaper->escapeHtml($this->configHelper->getLogFileLabel($fileToRestart) . " ($fileToRestart)");
        try {
            if ($this->driver->isExists($this->file->getLogFilePath($fileToRestart))) {
                $this->driver->fileOpen($this->file->getLogFilePath($fileToRestart) , 'w');
                $msg = "$fileConfigData has been cleaned.";
                $result = true;
            } else {
                $result = false;
                $msg = "$fileConfigData does not exist.";
            }
        } catch (\Exception $e) {
            $result = false;
            $msg = "Error cleaning $fileConfigData. Error: " . $e->getMessage();
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'result' => $result,
                'message' => $msg
            ]
        );
        return $resultJson;
    }
}