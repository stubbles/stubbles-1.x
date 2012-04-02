<?php
/**
 * Test for net::stubbles::ipo::request::stubValidatingRequestValue.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubValidatingRequestValueTestCase.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubValidatingRequestValue');
/**
 * Test for net::stubbles::ipo::request::stubValidatingRequestValue.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @since       1.3.0
 * @group       ipo
 * @group       ipo_request
 */
class stubValidatingRequestValueTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * helper method to create test instances
     *
     * @param   string                      $value
     * @return  stubValidatingRequestValue
     */
    protected function createValidatingRequestValue($value)
    {
        return new stubValidatingRequestValue('bar', $value);
    }

    /**
     * @test
     */
    public function containsReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('foo')->contains('o'));
    }

    /**
     * @test
     */
    public function containsReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->contains('u'));
    }

    /**
     * @test
     */
    public function isEqualToReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('foo')->isEqualTo('foo'));
    }

    /**
     * @test
     */
    public function isEqualToReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isEqualTo('bar'));
    }

    /**
     * @test
     */
    public function isHttpUrlReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('http://example.net/')->isHttpUrl());
    }

    /**
     * @test
     */
    public function isHttpUrlReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isHttpUrl());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV4Address()
    {
        $this->assertTrue($this->createValidatingRequestValue('127.0.0.1')->isIpAddress());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV6Address()
    {
        $this->assertTrue($this->createValidatingRequestValue('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                               ->isIpAddress()
        );
    }

    /**
     * @test
     */
    public function isIpAddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isIpAddress());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('127.0.0.1')->isIpV4Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isIpV4Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsFalseForIpV6Addresses()
    {
        $this->assertFalse($this->createValidatingRequestValue('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                                ->isIpV4Address()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                               ->isIpV6Address()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isIpV6Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsFalseForIpV4Addresses()
    {
        $this->assertFalse($this->createValidatingRequestValue('127.0.0.1')->isIpV6Address());
    }

    /**
     * @test
     */
    public function isMailAddressReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('mail@example.net')->isMailAddress());
    }

    /**
     * @test
     */
    public function isMailAddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isMailAddress());
    }

    /**
     * @test
     */
    public function isOneOfReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('foo')->isOneOf(array('foo', 'bar', 'baz')));
    }

    /**
     * @test
     */
    public function isOneOfReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->isOneOf(array('bar', 'baz')));
    }

    /**
     * @test
     */
    public function satisfiesRegexReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValidatingRequestValue('foo')->satisfiesRegex('/foo/'));
    }

    /**
     * @test
     */
    public function satisfiesRegexReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValidatingRequestValue('foo')->satisfiesRegex('/bar/'));
    }

    /**
     * @test
     */
    public function withValidatorReturnsValidatorResult()
    {
        $mockValidator = $this->getMock('stubValidator');
        $mockValidator->expects($this->once())
                      ->method('validate')
                      ->with($this->equalTo('foo'))
                      ->will($this->returnValue(true));
        $this->assertTrue($this->createValidatingRequestValue('foo')->withValidator($mockValidator));
    }

    /**
     * @test
     */
    public function getNameReturnsParamName()
    {
        $this->assertEquals('bar', $this->createValidatingRequestValue('foo')->getName());
    }
}
?>