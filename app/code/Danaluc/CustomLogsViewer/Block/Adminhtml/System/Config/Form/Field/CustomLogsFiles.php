<?php

declare(strict_types=1);

namespace Danaluc\CustomLogsViewer\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Status
 */
class CustomLogsFiles extends AbstractFieldArray
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected $_addAfter = false;

    /**
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * Check if columns are defined, set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'custom_log_file_label',
            [
                'label' => __('File Label')
            ]
        );

        $this->addColumn(
            'custom_log_file_name',
            [
                'label' => __('Custom Log Filename')
            ]
        );

        $this->addColumn(
            'custom_log_clean_every',
            [
                'label' => __('Restart file every x days')
            ]
        );
    }
}
