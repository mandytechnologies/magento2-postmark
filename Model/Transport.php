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
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessageInterface;
use Mandytech\Postmark\Helper\Data;
use Mandytech\Postmark\Model\Transport\Postmark;
use Magento\Framework\Mail\TransportInterface;
use Throwable;

class Transport extends \Magento\Framework\Mail\Transport implements TransportInterface
{
    /**
     * @var EmailMessageInterface
     */
    protected $message;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Postmark
     */
    protected $transportPostmark;

    /**
     * @param Data $helper
     * @param Postmark $transportPostmark
     * @param EmailMessageInterface $message
     * @param null $parameters
     */
    public function __construct(
        Data $helper,
        Postmark $transportPostmark,
        EmailMessageInterface $message,
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
     * @throws MailException|Throwable
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
            throw new MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
