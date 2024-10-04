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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to opensource@ripen.com so we can send you a copy immediately.
 *
 * @category    Ripen
 * @package     Ripen_Postmark
 * @copyright   Copyright (c) SUMO Heavy Industries, LLC
 * @copyright   Copyright (c) Ripen, LLC
 * @copyright   Copyright (c) Mandy Technologies Pvt Ltd
 * @notice      The Postmark logo and name are trademarks of Wildbit, LLC
 * @license     http://www.opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Ripen\Postmark\Model\Plugin;

class TransportInterfaceFactory
{
    /**
     * Transport Factory
     *
     * @var \Ripen\Postmark\Model\TransportFactory
     */
    protected $moduleTransportFactory;

    /**
     * Helper class
     *
     * @var \Ripen\Postmark\Helper\Data
     */
    protected $moduleHelper;

    /**
     * TransportBuilder constructor.
     * @param \Ripen\Postmark\Helper\Data $moduleHelper
     * @param \Ripen\Postmark\Model\TransportFactory $moduleTransportFactory
     */
    public function __construct(
        \Ripen\Postmark\Helper\Data $moduleHelper,
        \Ripen\Postmark\Model\TransportFactory $moduleTransportFactory
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->moduleTransportFactory = $moduleTransportFactory;
    }

    /**
     * Replace mail transport with Postmark if needed
     *
     * @param \Magento\Framework\Mail\TransportInterfaceFactory $subject
     * @param \Closure $proceed
     * @param array $data
     *
     * @return \Magento\Framework\Mail\TransportInterface
     */
    public function aroundCreate(
        \Magento\Framework\Mail\TransportInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        if ($this->isPostmarkEnabled()) {
            return $this->moduleTransportFactory->create($data);
        }

        /** @var \Magento\Framework\Mail\TransportInterface $transport */
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
