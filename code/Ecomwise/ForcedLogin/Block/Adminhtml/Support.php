<?php

namespace Ecomwise\ForcedLogin\Block\Adminhtml;

class Support extends \Magento\Backend\Block\Template implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    
    protected $_template = 'forcedlogin/support.phtml';

    public $module = 'Ecomwise_Forcedlogin';

    public $version = '';

    public $supportUrl = 'http://support.ecomwise.com/support/tickets/new';

    public $email = 'feedback@ecomwise.com';

    public $faq = 'http://support.ecomwise.com/support/solutions/folders/111251';

    public $name = 'B2B Forced Login';

    public $compatibility = 'Magento CE 2.0';

    public $manualUrl = 'http://support.ecomwise.com/solution/articles/3000059527-b2b-forced-login-2-0';

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\FullModuleList $loader
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\FullModuleList $loader,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->version = $this->getModuleVersion($loader);
    }

    /**
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return html
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * Undocumented function
     *
     * @param $loader
     * @return string
     */
    public function getModuleVersion($loader)
    {
        $version = '';
        $module  = $loader->getOne('Ecomwise_ForcedLogin');
        if (is_array($module) && ! empty($module)) {
            $version = $module['setup_version'];
        }

        return $version;
    }
}
