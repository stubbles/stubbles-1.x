<?php
/**
 * Test for net::stubbles::ipo::response::stubResponseSerializer.
 *
 * @package     stubbles
 * @subpackage  ipo_response_test
 * @version     $Id: stubResponseSerializerTestCase.php 3304 2012-01-04 10:04:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubResponseSerializer');
/**
 * Test for net::stubbles::ipo::response::stubResponseSerializer.
 *
 * @package     stubbles
 * @subpackage  ipo_response_test
 * @since       1.7.0
 * @group       ipo
 * @group       ipo_response
 * @group       bug262
 */
class stubResponseSerializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubResponseSerializer
     */
    protected $responseSerializer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->responseSerializer = new stubResponseSerializer();
    }

    /**
     * @test
     */
    public function serializeTurnsResponseIntoStringRepresentation()
    {
        $this->assertInternalType('string', $this->responseSerializer->serialize(new stubBaseResponse()));
    }

    /**
     * @test
     */
    public function serializeWithoutCookieDoesNotContainCookiesInStringRepresentation()
    {
        $response = new stubBaseResponse();
        $response->addCookie(stubCookie::create('foo', 'bar'));
        $unserializedResponse = unserialize($this->responseSerializer->serializeWithoutCookies($response));
        $this->assertFalse($unserializedResponse->hasCookie('foo'));
    }

    /**
     * @test
     * @since  1.7.1
     */
    public function serializedResponseWithoutCookiesContainsBody()
    {
        $response = new stubBaseResponse();
        $response->write('foo bar baz');
        $unserializedResponse = unserialize($this->responseSerializer->serializeWithoutCookies($response));
        $this->assertEquals('foo bar baz', $unserializedResponse->getBody());
    }

    /**
     * @test
     * @since  1.7.1
     */
    public function serializedResponseWithoutCookiesContainsHeaders()
    {
        $response = new stubBaseResponse();
        $response->addHeader('foo', 'bar')
                 ->addHeader('other', 'baz');
        $unserializedResponse = unserialize($this->responseSerializer->serializeWithoutCookies($response));
        $this->assertTrue($unserializedResponse->hasHeader('foo'));
        $this->assertEquals('bar', $unserializedResponse->getHeader('foo'));
        $this->assertTrue($unserializedResponse->hasHeader('other'));
        $this->assertEquals('baz', $unserializedResponse->getHeader('other'));
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function unserializingInvalidSerializedResponseThrowsIllegalArgumentException()
    {
        $this->responseSerializer->unserialize('invalid');
    }

    /**
     * @test
     */
    public function unserializeReturnsResponseInstance()
    {
        $this->assertInstanceOf('stubResponse',
                                $this->responseSerializer->unserialize(serialize(new stubBaseResponse()))
        );
    }
}
?>