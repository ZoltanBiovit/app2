<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_PromotionBar
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\PromotionBar\Block\Adminhtml\Bar\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Content
 * @package Bss\PromotionBar\Block\Adminhtml\Bar\Edit\Tab
 */
class Content extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * Wysiwyg Config
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Bss\PromotionBar\Model\Bar $promotionBar */
        $promotionBar = $this->_coreRegistry->registry('bss_promotionbar_bar');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('bar_');
        $form->setFieldNameSuffix('bar');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Content and Design'),
                'class' => 'fieldset-wide'
            ]
        );

        $config['document_base_url'] = $this->getData('store_media_url');
        $config['add_variables'] = false;
        $config['add_widgets'] = false;
        $config['add_directives'] = true;
        $config['use_container'] = true;
        $config['container_class'] = 'hor-scroll';

        $fieldset->addField(
            'bar_content',
            'editor',
            [
                'name' => 'bar_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'wysiwyg' => true,
                'required' => true,
                'force_load' => false,
                'config' => $this->wysiwygConfig->getConfig($config)
            ]
        );

        $fieldset->addField(
            'bar_background',
            'text',
            [
                'name' => 'bar_background',
                'class' => 'jscolor {hash:true,refine:false}',
                'label' => __('Background Color'),
                'title' => __('Background Color')
            ]
        );

        $fieldset->addField(
            'bar_height',
            'text',
            [
                'name' => 'bar_height',
                'class' => 'validate-zero-or-greater',
                'label' => __('Height (pixel)'),
                'title' => __('Height (pixel)')
            ]
        );

        $barData = $this->_session->getData('bss_promotionbar_bar_data', true);
        if ($barData) {
            $promotionBar->addData($barData);
        } else {
            if (!$promotionBar->getId()) {
                $promotionBar->addData($promotionBar->getDefaultValues());
            }
        }

        $form->addValues($promotionBar->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content and Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
