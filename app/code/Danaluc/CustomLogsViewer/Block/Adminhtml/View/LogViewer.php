<?php

namespace Danaluc\CustomLogsViewer\Block\Adminhtml\View;

/**
 * Log viewer block
 *
 * @api
 */
class LogViewer extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Danaluc_CustomLogsViewer::view/logviewer.phtml';

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
  
        $this->addChild(
            'log_files',
            \Danaluc\CustomLogsViewer\Block\Adminhtml\View\LogViewer\LogFiles::class
        );

        return parent::_prepareLayout();
    }

    /**
     * Get services html
     *
     * @return string
     */
    public function getLogFilesHtml()
    {
        return $this->getChildHtml('log_files');
    }
}