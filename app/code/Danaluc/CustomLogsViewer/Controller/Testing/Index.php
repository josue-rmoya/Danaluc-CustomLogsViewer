<?php
namespace Danaluc\CustomLogsViewer\Controller\Testing;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    private $cronClean;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       \Danaluc\CustomLogsViewer\Cron\Clean $cronClean
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->cronClean = $cronClean;
        return parent::__construct($context);
    }
    
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->cronClean->execute();
    }
}
