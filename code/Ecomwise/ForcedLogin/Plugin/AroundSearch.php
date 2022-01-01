<?php
	
namespace Ecomwise\ForcedLogin\Plugin;

class AroundSearch
{
    /**
     *
     * @var \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper
     */
    protected $forcedLoginHelper;

    /**
     * constructor
     *
     * @param \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper
     */
    public function __construct(
    \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper
    ){
        $this->forcedLoginHelper = $forcedLoginHelper;
    }

    /**
     * Undocumented function
     *
     * @param \Amasty\Xsearch\Helper\Data $subject
     * @param \Closure $proceed
     * @param $layout
     * @return result
     */
    public function aroundGetBlocksHtml(
        \Amasty\Xsearch\Helper\Data $subject,
        \Closure $proceed,
        $layout
    ) {
        $isLoggedIn = $this->forcedLoginHelper->isLoggedIn();
        $forced_login_status = $this->forcedLoginHelper->getForcedLoginStatus();

        $result = [];

            if($isLoggedIn || !$forced_login_status){
                return $proceed($layout);
            }else{
                $this->_pushItem(
                    $subject::XML_PATH_TEMPLATE_PRODUCT_POSITION,
                    $layout->createBlock(\Ecomwise\ForcedLogin\Block\Search\Login::class, 'ecw.search.login'),
                    $result
                );
            }

        ksort($result);

        return $result;
    }

    /**
     * push items
     *
     * @param $position
     * @param $block
     * @param $result
     * @return void
     */
    protected function _pushItem($position, $block, &$result)
    {
        $positions = explode('/', $position);
        $type = isset($positions[0]) ? $positions[0] : false;
        $position = $this->forcedLoginHelper->getAmastyModuleConfig($position) * 10;
        while (isset($result[$position])) {
            $position++;
        }
        $currentHtml = $block->toHtml();

        $result[$position] = [
            'type' =>  $type,
            'html' => $currentHtml
        ];
    }
}