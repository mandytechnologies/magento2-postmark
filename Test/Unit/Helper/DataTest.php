<?php
/**
 * Postmark integration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Mandytech
 * @package     Mandytech_Postmark
 * @copyright   Copyright (c) SUMO Heavy Industries, LLC
 * @copyright   Copyright (c) Ripen, LLC
 * @copyright   Copyright (c) Mandy Technologies Pvt Ltd
 * @notice      The Postmark logo and name are trademarks of Wildbit, LLC
 * @license     http://www.opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Mandytech\Postmark\Test\Unit\Helper;

use Mandytech\Postmark\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $_helper;

    /**
     * @var MockObject
     */
    protected $_scopeConfig;

    /**
     * @var MockObject
     */
    protected $_logger;

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $className = 'Mandytech\Postmark\Helper\Data';
        $arguments = $objectManagerHelper->getConstructArguments($className);

        $context = $arguments['context'];
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_logger = $context->getLogger();

        $this->_helper = $objectManagerHelper->getObject($className, $arguments);
    }

    public function testIsEnabled()
    {
        $store = null;
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(true));
        $this->assertTrue($this->_helper->isEnabled($store));
    }

    public function testIsNotEnabled()
    {
        $store = null;
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(false));
        $this->assertFalse($this->_helper->isEnabled($store));
    }

    public function testGetApiKey()
    {
        $store = null;
        $this->_scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('test-api-key'));
        $this->assertEquals('test-api-key', $this->_helper->getApiKey($store));
    }

    public function testCanUse()
    {
        $store = null;
        $this->_scopeConfig->expects($this->at(0))
            ->method('getValue')
            ->with(Data::XML_PATH_ENABLED)
            ->will($this->returnValue(true));

        $this->_scopeConfig->expects($this->at(1))
            ->method('getValue')
            ->with(Data::XML_PATH_APIKEY)
            ->will($this->returnValue('test-api-key'));

        $this->assertTrue($this->_helper->canUse($store));
    }

    public function testCanUseNoApiKey()
    {
        $store = null;
        $this->_scopeConfig->expects($this->at(0))
            ->method('getValue')
            ->with(Data::XML_PATH_ENABLED)
            ->will($this->returnValue(true));

        $this->_scopeConfig->expects($this->at(1))
            ->method('getValue')
            ->with(Data::XML_PATH_APIKEY)
            ->will($this->returnValue(null));

        $this->assertFalse($this->_helper->canUse($store));
    }

    public function testCanUseNotEnabled()
    {
        $store = null;
        $this->_scopeConfig->expects($this->at(0))
            ->method('getValue')
            ->with(Data::XML_PATH_ENABLED)
            ->will($this->returnValue(false));

        $this->assertFalse($this->_helper->canUse($store));
    }

    public function testLog()
    {
        $this->_logger->expects($this->once())
            ->method('info');

        $this->_helper->log('Test msg');
    }
}
