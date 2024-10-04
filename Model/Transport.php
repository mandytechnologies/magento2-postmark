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
namespace Mandytech\Postmark\Model;

use Laminas\Mail\Message as LaminasMessage;
use Laminas\Mail\Headers as LaminasHeaders;

class Transport extends \Magento\Framework\Mail\Transport implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MailMessageInterface
     */
    protected $message;

    /**
     * @var \Mandytech\Postmark\Helper\Data
     */
    protected $helper;

    /**
     * @var \Mandytech\Postmark\Model\Transport\Postmark
     */
    protected $transportPostmark;

    /**
     * @param \Mandytech\Postmark\Helper\Data $helper
     * @param \Mandytech\Postmark\Model\Transport\Postmark $transportPostmark
     * @param \Magento\Framework\Mail\MailMessageInterface $message
     * @param null $parameters
     */
    public function __construct(
        \Mandytech\Postmark\Helper\Data $helper,
        \Mandytech\Postmark\Model\Transport\Postmark $transportPostmark,
        \Magento\Framework\Mail\MailMessageInterface $message,
        $parameters = null
    ) {
        $this->helper  = $helper;
        $this->transportPostmark = $transportPostmark;

        if ($this->helper->canUse()) {
            $this->message = $message;
        } else {
            parent::__construct($message, $parameters);
        }
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        if (! $this->helper->canUse()) {
            parent::sendMessage();
            return;
        }

        try {
            // Create a Laminas\Mail\Message object to pass to Postmark
            $headers = new LaminasHeaders();
            $headers->addHeaders($this->message->getHeaders());

            $message = new LaminasMessage();
            $message->setHeaders($headers);
            $message->setBody($this->message->getBody());

            $this->transportPostmark->send($message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
