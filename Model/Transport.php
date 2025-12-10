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

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\TransportInterface;

use Mandytech\Postmark\Helper\Data;
use Mandytech\Postmark\Model\Transport\Postmark;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\TextPart;

use Throwable;

class Transport extends \Magento\Framework\Mail\Transport implements TransportInterface
{
    /** @var EmailMessageInterface */
    protected $message;

    /** @var Data */
    protected $helper;

    /** @var Postmark */
    protected $transportPostmark;

    public function __construct(
        Data $helper,
        Postmark $transportPostmark,
        EmailMessageInterface $message,
        $parameters = null
    ) {
        $this->helper = $helper;
        $this->transportPostmark = $transportPostmark;

        if ($helper->canUse()) {
            $this->message = $message;
        } else {
            parent::__construct($message, $parameters);
        }
    }

    public function sendMessage(): void
    {
        if (!$this->helper->canUse()) {
            parent::sendMessage();
            return;
        }

        try {
            $email = $this->convertMessageToSymfony($this->message);
            $this->transportPostmark->send($email);
        } catch (Throwable $e) {
            throw new MailException(__($e->getMessage()));
        }
    }

    private function convertMessageToSymfony(EmailMessageInterface $message): Email
    {
        $email = new Email();

        foreach ($message->getFrom() as $from) {
            $email->from($from->getEmail());
        }

        foreach ($message->getTo() as $to) {
            $email->to($to->getEmail());
        }

        foreach ($message->getCc() ?: [] as $cc) {
            $email->cc($cc->getEmail());
        }

        foreach ($message->getBcc() ?: [] as $bcc) {
            $email->bcc($bcc->getEmail());
        }

        foreach ($message->getReplyTo() ?: [] as $rt) {
            $email->replyTo($rt->getEmail());
        }

        $email->subject($message->getSubject());

        $body = $message->getBody();

        if ($body instanceof \Symfony\Component\Mime\Part\TextPart) {
            $body = $body->getBody();
        } elseif (is_object($body) && method_exists($body, 'bodyToString')) {
            $body = $body->bodyToString();
        }

        $email->html($body);

        return $email;
    }
}