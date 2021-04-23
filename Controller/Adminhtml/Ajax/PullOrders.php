<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 */

namespace Facebook\BusinessExtension\Controller\Adminhtml\Ajax;

use Exception;
use Facebook\BusinessExtension\Helper\CommerceHelper;
use Facebook\BusinessExtension\Helper\FBEHelper;
use Facebook\BusinessExtension\Model\System\Config as SystemConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class PullOrders extends AbstractAjax
{
    /**
     * @var SystemConfig
     */
    protected $systemConfig;

    /**
     * @var CommerceHelper
     */
    protected $commerceHelper;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        SystemConfig $systemConfig,
        FBEHelper $fbeHelper,
        CommerceHelper $commerceHelper
    ) {
        parent::__construct($context, $resultJsonFactory, $fbeHelper);
        $this->systemConfig = $systemConfig;
        $this->commerceHelper = $commerceHelper;
    }

    public function executeForJson()
    {
        // get default store info
        $storeId = $this->_fbeHelper->getStore()->getId();

        // override store if user switched config scope to non-default
        $storeParam = $this->getRequest()->getParam('store');
        if ($storeParam) {
            $storeId = $storeParam;
        }

        if (!$this->systemConfig->isActiveOrderSync($storeId)) {
            $response['success'] = false;
            $response['error_message'] = __('Enable order sync before pulling orders.');
            return $response;
        }

        $this->commerceHelper->setStoreId($storeId);

        try {
            return ['success' => true, 'response' => $this->commerceHelper->pullPendingOrders()];
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $this->_fbeHelper->logException($e);
            return ['success' => false, 'error_message' => $e->getMessage()];
        }
    }
}