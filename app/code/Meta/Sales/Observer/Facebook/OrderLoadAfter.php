<?php

declare(strict_types=1);

/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Meta\Sales\Observer\Facebook;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Meta\Sales\Helper\OrderHelper;
use Psr\Log\LoggerInterface;

class OrderLoadAfter implements ObserverInterface
{
    /**
     * @var OrderHelper
     */
    private OrderHelper $orderHelper;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param OrderHelper     $orderHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderHelper $orderHelper,
        LoggerInterface $logger
    ) {
        $this->orderHelper = $orderHelper;
        $this->logger = $logger;
    }

    /**
     * Load order's extension attributes
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
 * @var Order $order
*/
        $order = $observer->getEvent()->getOrder();

        if (!($order && $order->getId())) {
            return;
        }

        try {
            $this->orderHelper->setFacebookOrderExtensionAttributes($order);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
