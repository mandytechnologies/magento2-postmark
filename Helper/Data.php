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
namespace Mandytech\Postmark\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'postmark/settings/enabled';
    const XML_PATH_DEBUG_MODE = 'postmark/settings/debug_mode';
    const XML_PATH_APIKEY = 'postmark/settings/apikey';

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ObjectManagerInterface $objectManager
    ) {
        $this->_logger = $logger;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getApiKey($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_APIKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isDebugMode($store = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_DEBUG_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return boolean
     */
    public function canUse($store = null)
    {
        return $this->isEnabled($store) && $this->getApiKey($store);
    }

    /**
     * @param $msg
     * @param string $level
     */
    public function log($msg, string $level = \Psr\Log\LogLevel::INFO)
    {
        $this->_logger->log($level, $msg);
    }
}
