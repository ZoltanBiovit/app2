<?php

namespace MageArray\OrderAttachments\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Post extends Action
{

    /**
     * Post constructor.
     * @param Context $context
     * @param Data $dataHelper
     */
    public function __construct(
        Context $context,
        \MageArray\OrderAttachments\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
        $post = $this->getRequest()->getPostValue();


        // echo json_encode($post,JSON_PRETTY_PRINT);
        // die;

        
        if ($post) {
            try {
                $this->_dataHelper->saveAttachment('customer', $post);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } catch (\Exception $e) {
                // $this->messageManager->addError($e->getMessage());
                $this->messageManager->addError(__('lskdflskdjf later.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addError(__('Please try again later.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
