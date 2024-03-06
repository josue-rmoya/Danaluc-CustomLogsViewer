<?php

namespace Danaluc\CustomLogsViewer\Controller\Adminhtml\View;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Logs extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Danaluc_CustomLogsViewer::view_logs_file';

    const PAGE_TITLE = 'Custom Logs Files';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
       \Magento\Backend\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
         /** @var \Magento\Framework\View\Result\Page $resultPage */
         $resultPage = $this->_pageFactory->create();
         $resultPage->setActiveMenu('Danaluc_CustomLogsViewer::custom_logs_viewer');
         $resultPage->addBreadcrumb(__(self::PAGE_TITLE), __(self::PAGE_TITLE));
         $resultPage->getConfig()->getTitle()->prepend(__(self::PAGE_TITLE));
         $this->_addContent(
            $this->_view->getLayout()->createBlock(\Danaluc\CustomLogsViewer\Block\Adminhtml\View\LogViewer::class)
        );
         //$this->_view->renderLayout();

         return $resultPage;
    }

    /**
     * Is the user allowed to view the page.
    *
    * @return bool
    */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Danaluc_CustomLogsViewer::view_logs_file');
    }
}
