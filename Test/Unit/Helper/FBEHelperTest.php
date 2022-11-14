<?php
/**
 * Copyright (c) Meta Platforms, Inc. and affiliates. All Rights Reserved
 */

namespace Facebook\BusinessExtension\Test\Unit\Helper;

use Facebook\BusinessExtension\Helper\FBEHelper;
use Facebook\BusinessExtension\Helper\Product\Identifier as ProductIdentifier;
use Facebook\BusinessExtension\Logger\Logger;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class FBEHelperTest extends \PHPUnit\Framework\TestCase
{
    protected $fbeHelper;

    protected $systemConfig;

    protected $context;

    protected $objectManagerInterface;

    protected $logger;

    protected $directorylist;

    protected $storeManager;

    protected $curl;

    protected $resourceConnection;

    protected $moduleList;

    protected $productIdentifier;

    /**
     * Used to reset or change values after running a test
     *
     * @return void
     */
    public function tearDown(): void
    {
    }

    /**
     * Used to set the values before running a test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->objectManagerInterface = $this->createMock(ObjectManagerInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->directorylist = $this->createMock(DirectoryList::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->curl = $this->createMock(Curl::class);
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->moduleList = $this->createMock(ModuleListInterface::class);
        $this->productIdentifier = $this->createMock(ProductIdentifier::class);
        $this->systemConfig = $this->createMock(\Facebook\BusinessExtension\Model\System\Config::class);

        $this->fbeHelper = new FBEHelper(
            $this->context,
            $this->objectManagerInterface,
            $this->logger,
            $this->directorylist,
            $this->storeManager,
            $this->curl,
            $this->resourceConnection,
            $this->moduleList,
            $this->productIdentifier,
            $this->systemConfig
        );
    }

    /**
     * Test partner agent is correct
     *
     * @return void
     */
    public function testCorrectPartnerAgent()
    {
        $magentoVersion = '2.3.5';
        $pluginVersion = '1.0.0';
        $this->moduleList->method('getOne')->willReturn(
            [
                'setup_version' => $pluginVersion
            ]
        );
        $source = $this->fbeHelper->getSource();
        $productMetadata = $this->getMockBuilder(ProductMetadataInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getVersion', 'getEdition', 'getName'])
            ->getMock();
        $productMetadata->method('getVersion')->willReturn($magentoVersion);
        $productMetadata->method('getVersion')->willReturn($magentoVersion);
        $this->objectManagerInterface->method('get')->willReturn($productMetadata);
        $this->assertEquals(
            sprintf('%s-%s-%s', $source, $magentoVersion, $pluginVersion),
            $this->fbeHelper->getPartnerAgent(true)
        );
    }
}
