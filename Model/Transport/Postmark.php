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
namespace Mandytech\Postmark\Model\Transport;

use Mandytech\Postmark\Helper\Data;
use Mandytech\Postmark\Model\Transport\Exception as PostmarkTransportException;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

class Postmark
{
    const API_URI = 'https://api.postmarkapp.com/email';
    const RECIPIENTS_LIMIT = 20;

    /** @var string */
    protected $apiKey;

    /** @var Data */
    protected $helper;

    /** @var HttpClientInterface */
    protected $client;

    /**
     * Constructor
     *
     * @param Data $helper
     * @throws PostmarkTransportException
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;

        $apiKey = $this->helper->getApiKey();
        if (empty($apiKey)) {
            throw new PostmarkTransportException(self::class . ' requires API key');
        }

        $this->apiKey = $apiKey;

        $this->client = HttpClient::create([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Postmark-Server-Token' => $this->apiKey,
            ],
        ]);
    }

    /**
     * Send message through Postmark
     *
     * @param Email $email
     * @throws Throwable
     */
    public function send(Email $email)
    {
        $data = [
            'From'          => $email->getFrom()[0]->toString(),
            'To'            => implode(',', array_map(fn($a) => $a->getAddress(), $email->getTo())),
            'Cc'            => implode(',', array_map(fn($a) => $a->getAddress(), $email->getCc())),
            'Bcc'           => implode(',', array_map(fn($a) => $a->getAddress(), $email->getBcc())),
            'Subject'       => $email->getSubject(),
            'HtmlBody'      => $email->getHtmlBody(),
            'TextBody'      => $email->getTextBody(),
            'ReplyTo'       => implode(',', array_map(fn($a) => $a->getAddress(), $email->getReplyTo())),
            'MessageStream' => $this->helper->getMessageStream() ?: 'outbound',
            'Attachments'   => $this->getAttachments($email),
        ];

        $data = $this->filterEmpty($data);

        try {
            $response = $this->client->request('POST', self::API_URI, [
                'json' => $data
            ]);

            $status = $response->getStatusCode();
            $result = $response->toArray(false);

            if ($status >= 400) {
                $code = $result['ErrorCode'] ?? 'Unknown';
                $msg  = $result['Message'] ?? 'Unknown';

                throw new PostmarkTransportException("Postmark Error $code: $msg");
            }

        } catch (Throwable $e) {
            if ($this->helper->isDebugMode()) {
                $this->helper->log("Postmark send error: " . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Convert Symfony Mime attachments for Postmark API
     *
     * @param Email $email
     * @return array
     */
    private function getAttachments(Email $email)
    {
        $attachments = [];

        foreach ($email->getAttachments() as $attachment) {
            /** @var DataPart $attachment */
            $attachments[] = [
                'Name'        => $attachment->getFilename(),
                'ContentType' => $attachment->getMediaType() . '/' . $attachment->getMediaSubtype(),
                'Content'     => base64_encode($attachment->getBody()),
            ];
        }

        return $attachments;
    }

    /**
     * Remove empty or null fields
     *
     * @param array $input
     * @return array
     */
    private function filterEmpty(array $input)
    {
        return array_filter($input, fn($v) => $v !== null && $v !== '');
    }
}