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
namespace Mandytech\Postmark\Model\Plugin;

use Closure;
use Magento\Framework\Mail\TransportInterface;
use Mandytech\Postmark\Helper\Data;
use Mandytech\Postmark\Model\TransportFactory;

class TransportInterfaceFactory
{
    /**
     * Transport Factory
     *
     * @var TransportFactory
     */
    protected $moduleTransportFactory;

    /**
     * Helper class
     *
     * @var Data
     */
    protected $moduleHelper;

    /**
     * TransportBuilder constructor.
     * @param Data $moduleHelper
     * @param TransportFactory $moduleTransportFactory
     */
    public function __construct(
        Data $moduleHelper,
        TransportFactory $moduleTransportFactory
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->moduleTransportFactory = $moduleTransportFactory;
    }

    /**
     * Replace mail transport with Postmark if needed
     *
     * @param \Magento\Framework\Mail\TransportInterfaceFactory $subject
     * @param Closure $proceed
     * @param array $data
     *
     * @return TransportInterface
     */
    public function aroundCreate(
        \Magento\Framework\Mail\TransportInterfaceFactory $subject,
        Closure $proceed,
        array $data = []
    ) {
        if ($this->isPostmarkEnabled()) {
            return $this->moduleTransportFactory->create($data);
        }

        /** @var TransportInterface $transport */
        return $proceed($data);
    }

    /**
     * Get status of Postamrk
     *
     * @return bool
     */
    private function isPostmarkEnabled()
    {
        return $this->moduleHelper->canUse();
    }
}
