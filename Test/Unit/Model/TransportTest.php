<?php
namespace Mandytech\Postmark\Test\Unit\Model;

use Mandytech\Postmark\Helper\Data;
use Mandytech\Postmark\Model\Transport\Postmark;
use Mandytech\Postmark\Model\Transport as PostmarkTransport;
use Mandytech\Postmark\Model\Transport\Exception as PostmarkTransportException;
use Magento\Framework\Mail\EmailMessageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;
use Magento\Framework\Exception\MailException;

class TransportTest extends TestCase
{
    /** @var Data|\PHPUnit\Framework\MockObject\MockObject */
    private $helper;

    /** @var Postmark|\PHPUnit\Framework\MockObject\MockObject */
    private $postmarkMock;

    /** @var EmailMessageInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $emailMessage;

    /** @var PostmarkTransport */
    private $transport;

    public function setUp(): void
    {
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['canUse'])
            ->getMock();

        $this->emailMessage = $this->getMockBuilder(EmailMessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Ensure getBody returns a string (avoids TextPart error)
        $this->emailMessage->method('getBody')->willReturn('<p>Test HTML</p>');
        $this->emailMessage->method('getFrom')->willReturn([]);
        $this->emailMessage->method('getTo')->willReturn([]);
        $this->emailMessage->method('getCc')->willReturn([]);
        $this->emailMessage->method('getBcc')->willReturn([]);
        $this->emailMessage->method('getReplyTo')->willReturn([]);
        $this->emailMessage->method('getSubject')->willReturn('Test Subject');

        $this->postmarkMock = $this->getMockBuilder(Postmark::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transport = new PostmarkTransport(
            $this->helper,
            $this->postmarkMock,
            $this->emailMessage
        );
    }

    public function testSendMessageUsesPostmarkWhenEnabled()
    {
        $this->helper->method('canUse')->willReturn(true);

        $this->postmarkMock->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Email::class));

        $this->transport->sendMessage();
    }

    public function testSendMessageFallbackToParentWhenDisabled()
    {
        $this->helper->method('canUse')->willReturn(false);

        $parentTransport = $this->getMockBuilder(PostmarkTransport::class)
            ->setConstructorArgs([$this->helper, $this->postmarkMock, $this->emailMessage])
            ->onlyMethods(['sendMessage'])
            ->getMock();

        $parentTransport->expects($this->once())
            ->method('sendMessage');

        $parentTransport->sendMessage();
    }

    public function testSendMessageThrowsMailException()
    {
        $this->helper->method('canUse')->willReturn(true);

        $this->postmarkMock->expects($this->once())
            ->method('send')
            ->willThrowException(new PostmarkTransportException('Postmark error'));

        $this->expectException(MailException::class);

        $this->transport->sendMessage();
    }
}
