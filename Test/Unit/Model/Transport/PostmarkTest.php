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
namespace Mandytech\Postmark\Test\Unit\Model\Transport;

use Mandytech\Postmark\Helper\Data;
use Mandytech\Postmark\Model\Transport\Postmark;
use Mandytech\Postmark\Model\Transport\Exception as PostmarkTransportException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PostmarkTest extends TestCase
{
    /** @var Data */
    protected $helper;

    /** @var Postmark */
    protected $transport;

    /** @var HttpClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $httpClientMock;

    public function setUp(): void
    {
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getApiKey', 'getMessageStream', 'isDebugMode', 'log'])
            ->getMock();

        $this->helper->method('getApiKey')->willReturn('test-api-key');
        $this->helper->method('getMessageStream')->willReturn('outbound');
        $this->helper->method('isDebugMode')->willReturn(false);

        $this->transport = new Postmark($this->helper);

        // Mock Symfony HttpClient
        $this->httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->getMock();

        $this->transport->setHttpClient($this->httpClientMock);
    }

    public function testSendEmailSuccess()
    {
        $email = (new Email())
            ->from('sender@example.com')
            ->to('recipient@example.com')
            ->subject('Test Subject')
            ->text('Test Text')           // string
            ->html('<p>Test HTML</p>');  // string

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn(['Message' => 'OK']);

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $this->transport->send($email);

        $this->assertTrue(true); // If no exception is thrown, test passes
    }

    public function testSendEmailFailureThrowsException()
    {
        $this->expectException(PostmarkTransportException::class);

        $email = (new Email())
            ->from('sender@example.com')
            ->to('recipient@example.com')
            ->subject('Test Subject')
            ->text('Test Text');

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $responseMock->method('getStatusCode')->willReturn(422);
        $responseMock->method('toArray')->willReturn([
            'ErrorCode' => 123,
            'Message' => 'Invalid request'
        ]);

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $this->transport->send($email);
    }
}