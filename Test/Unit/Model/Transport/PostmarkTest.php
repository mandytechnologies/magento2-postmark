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

class PostmarkTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Laminas_Http_Client_Adapter_Interface
     */
    protected $adapter;

    /**
     * @var \Mandytech\Postmark\Model\Transport\Postmark;
     */
    protected $transport;

    /**
     * @var \Mandytech\Postmark\Helper\Data
     */
    protected $helper;

    public function setUp()
    {
        $this->adapter = new \Laminas_Http_Client_Adapter_Test();

        $this->helper = $this->getMockBuilder(\Mandytech\Postmark\Helper\Data::class)
            ->setMethods(['getApiKey'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper->expects($this->once())
            ->method('getApiKey')
            ->will($this->returnValue('test-api-key'));

        $this->transport = new \Mandytech\Postmark\Model\Transport\Postmark($this->helper);
        $this->transport->getHttpClient()->setAdapter($this->adapter);
    }

    public function testSendMail()
    {
        $mail = new \Laminas_Mail;

        $this->adapter->setResponse(
            "HTTP/1.1 200 OK"        . "\r\n" .
            "Content-type: text/json" . "\r\n" .
                                       "\r\n" .
            '{"success": true}'
        );

        $this->transport->setMail($mail);
        $response = $this->transport->_sendMail();
        $this->assertNotEmpty($response);
        $this->assertTrue($response['success']);
    }

    public function testGetHttpClient()
    {
        $this->assertInstanceOf('\Laminas_Http_Client', $this->transport->getHttpClient());
    }

    public function testGetFrom()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getFrom());

        $mail->setFrom('test');
        $this->assertEquals('test', $this->transport->getFrom());
    }

    public function testGetTo()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getTo());

        $mail->addTo('test');
        $this->assertEquals('test', $this->transport->getTo());

        $mail->addTo('test1');
        $this->assertEquals('test,test1', $this->transport->getTo());
    }

    public function testGetCc()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getCc());

        $mail->addCc('test');
        $this->assertEquals('test', $this->transport->getCc());

        $mail->addCc('test1');
        $this->assertEquals('test,test1', $this->transport->getCc());
    }

    public function testGetBcc()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getBcc());

        $mail->addBcc('test');
        $this->assertEquals('test', $this->transport->getBcc());

        $mail->addBcc('test1');
        $this->assertEquals('test,test1', $this->transport->getBcc());
    }

    public function testGetReplyTo()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getReplyTo());

        $mail->setReplyTo('test');
        $this->assertEquals('test', $this->transport->getReplyTo());
    }

    public function testGetSubject()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getSubject());

        $mail->setSubject('test');
        $this->assertEquals('test', $this->transport->getSubject());
    }

    public function testGetBodyHtml()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getBodyHtml());

        $mail->setBodyHtml('test html');
        $this->assertEquals('test html', $this->transport->getBodyHtml());
    }

    public function testGetBodyText()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getBodyText());

        $mail->setBodyText('test text');
        $this->assertEquals('test text', $this->transport->getBodyText());
    }

    public function testGetTags()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getTags());

        $mail->addHeader('postmark-tag', 'test', true);
        $this->assertEquals('test', $this->transport->getTags());

        $mail->addHeader('postmark-tag', 'test1', true);
        $this->assertEquals('test,test1', $this->transport->getTags());
    }

    public function testGetAttachements()
    {
        $mail = new \Laminas_Mail;

        $this->transport->setMail($mail);
        $this->assertEmpty($this->transport->getAttachments());

        $at = $mail->createAttachment('test');
        $at->type        = 'image/gif';
        $at->disposition = \Laminas_Mime::DISPOSITION_INLINE;
        $at->encoding    = \Laminas_Mime::ENCODING_BASE64;
        $at->filename    = 'test.gif';
        $this->transport->setMail($mail);

        $attachements = $this->transport->getAttachments();
        $this->assertNotEmpty($attachements);
        $this->assertEquals('image/gif', $attachements[0]['ContentType']);
        $this->assertEquals('test.gif', $attachements[0]['Name']);
    }
}
