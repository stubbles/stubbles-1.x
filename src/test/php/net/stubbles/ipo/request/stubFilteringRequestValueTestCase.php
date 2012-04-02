<?php
/**
 * Test for net::stubbles::ipo::request::stubFilteringRequestValue.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubFilteringRequestValueTestCase.php 3327 2012-01-09 14:07:46Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubFilteringRequestValue',
                      'net::stubbles::ipo::request::filter::mock::stubMockFilterFactory'
);
/**
 * Test for net::stubbles::ipo::request::stubFilteringRequestValue.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @since       1.3.0
 * @group       ipo
 * @group       ipo_request
 */
class stubFilteringRequestValueTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequestErrorCollection;
    /**
     * mocked filter factory
     *
     * @var  stubMockFilterFactory
     */
    protected $mockFilterFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequestErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockFilterFactory          = $this->getMock('stubMockFilterFactory', array('createForType'));
    }

    /**
     * helper function to create request value instance
     *
     * @param   string                     $value
     * @return  stubFilteringRequestValue
     */
    protected function createFilteringRequestValue($value)
    {
        return new stubFilteringRequestValue($this->mockRequestErrorCollection,
                                             $this->mockFilterFactory,
                                             'bar',
                                             $value
               );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug266
     */
    public function asBool()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('1'))
                   ->will($this->returnValue(true));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertTrue($this->createFilteringRequestValue('1')->asBool());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug266
     */
    public function asBoolWithUnsetValueReturnsFalse()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo(null))
                   ->will($this->returnValue(false));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertFalse($this->createFilteringRequestValue(null)->asBool());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug266
     */
    public function asBoolWithUnsetValueButDefaultValueReturnsDefaultValue()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->never())
                   ->method('execute');
        $this->mockFilterFactory->expects($this->never())
                                ->method('createForType');
        $this->assertTrue($this->createFilteringRequestValue(null)->asBool(true));
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug266
     */
    public function asBoolWithFalseValueReturnsFalse()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('false'))
                   ->will($this->returnValue(false));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertFalse($this->createFilteringRequestValue('false')->asBool());
    }

    /**
     * @test
     */
    public function asInt()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('313'))
                   ->will($this->returnValue(313));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals(313, $this->createFilteringRequestValue('313')->asInt());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredInt()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('313'))
                   ->will($this->returnValue(313));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals(313, $this->createFilteringRequestValue('313')->asInt(1, 6, 3, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asFloat()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('3.13'))
                   ->will($this->returnValue(3.13));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals(3.13, $this->createFilteringRequestValue('3.13')->asFloat());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredFloat()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('3.13'))
                   ->will($this->returnValue(3.13));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals(3.13, $this->createFilteringRequestValue('3.13')->asFloat(0.1, 0.9, 0.5, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asString()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('foo'))
                   ->will($this->returnValue('foo'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('foo', $this->createFilteringRequestValue('foo')->asString());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredString()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('foo'))
                   ->will($this->returnValue('foo'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('foo', $this->createFilteringRequestValue('foo')->asString(1, 5, null, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asText()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('foo'))
                   ->will($this->returnValue('foo'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('foo', $this->createFilteringRequestValue('foo')->asText());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredText()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('foo'))
                   ->will($this->returnValue('foo'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('foo', $this->createFilteringRequestValue('foo')->asText(1, 5, null, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asJson()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('313'))
                   ->will($this->returnValue('"313"'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('"313"', $this->createFilteringRequestValue('313')->asJson());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredJson()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('313'))
                   ->will($this->returnValue('"313"'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('"313"', $this->createFilteringRequestValue('313')->asJson(null, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asPassword()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('bar'))
                   ->will($this->returnValue('bar'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('bar', $this->createFilteringRequestValue('bar')->asPassword());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredPassword()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('bar'))
                   ->will($this->returnValue('bar'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('bar', $this->createFilteringRequestValue('bar')->asPassword(5, array(), true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asHttpUrl()
    {
        $httpUrl    = stubHTTPURL::fromString('http://example.net/');
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('http://example.net/'))
                   ->will($this->returnValue($httpUrl));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertSame($httpUrl, $this->createFilteringRequestValue('http://example.net/')->asHttpUrl());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredHttpUrl()
    {
        $httpUrl    = stubHTTPURL::fromString('http://example.net/');
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('http://example.net/'))
                   ->will($this->returnValue($httpUrl));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertSame($httpUrl, $this->createFilteringRequestValue('http://example.net/')->asHttpUrl(false, null, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asMailAddress()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('bar@baz.com'))
                   ->will($this->returnValue('bar@baz.com'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('bar@baz.com', $this->createFilteringRequestValue('bar@baz.com')->asMailAddress());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredMailAddress()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('bar@baz.com'))
                   ->will($this->returnValue('bar@baz.com'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('bar@baz.com', $this->createFilteringRequestValue('bar@baz.com')->asMailAddress(true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asDate()
    {
        $date       = new stubDate('2010-08-10 00:00:00');
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('2010-08-10 00:00:00'))
                   ->will($this->returnValue($date));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertSame($date, $this->createFilteringRequestValue('2010-08-10 00:00:00')->asDate());
        $this->assertFalse($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asRequiredDate()
    {
        $date       = new stubDate('2010-08-10 00:00:00');
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('2010-08-10 00:00:00'))
                   ->will($this->returnValue($date));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->will($this->returnValue($mockFilter));
        $this->assertSame($date, $this->createFilteringRequestValue('2010-08-10 00:00:00')->asDate(null, null, null, true));
        $this->assertTrue($mockFilter->wasMethodCalled('asRequired'));
    }

    /**
     * @test
     */
    public function asType()
    {
        $mockFilter = $this->getMock('stubMockFilter', array('execute'));
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('foo'))
                   ->will($this->returnValue('bar'));
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->with($this->equalTo('exampleType'))
                                ->will($this->returnValue($mockFilter));
        $this->assertEquals('bar', $this->createFilteringRequestValue('foo')->asType('exampleType'));
    }

    /**
     * @test
     */
    public function failingFilterAddsErrorValueToRequestValueErrorCollection()
    {
        $valueError = new stubRequestValueError('dummy', array());
        $mockFilter = $this->getMock('stubFilter');
        $mockFilter->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo('bar'))
                   ->will($this->throwException(new stubFilterException($valueError)));
        $this->mockRequestErrorCollection->expects($this->once())
                                         ->method('add')
                                         ->with($this->equalTo($valueError),
                                                $this->equalTo('bar')
                                           );
        $this->assertNull($this->createFilteringRequestValue('bar')->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function ifContainsReturnsValidatedValue()
    {
        $this->assertEquals('303313', $this->createFilteringRequestValue('303313')->ifContains('303'));
    }

    /**
     * @test
     */
    public function ifContainsReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('303313')->ifContains('323'));
    }

    /**
     * @test
     */
    public function ifContainsReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createFilteringRequestValue('303313')->ifContains('323', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsValidatedValue()
    {
        $this->assertEquals('303313', $this->createFilteringRequestValue('303313')->ifIsEqualTo('303313'));
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('303313')->ifIsEqualTo('323313'));
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createFilteringRequestValue('303313')->ifIsEqualTo('323313', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUrlReturnsValidatedValue()
    {
        $this->assertEquals('http://example.net/',
                            $this->createFilteringRequestValue('http://example.net/')->ifIsHttpUrl()
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUrlReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('invalid')->ifIsHttpUrl());
    }

    /**
     * @test
     */
    public function ifIsHttpUrlReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('http://example.org/',
                            $this->createFilteringRequestValue('invalid')->ifIsHttpUrl(false,
                                                                                       'http://example.org/'
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsValidatedValue()
    {
        $this->assertEquals('127.0.0.1', $this->createFilteringRequestValue('127.0.0.1')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('invalid')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('127.0.0.1', 
                            $this->createFilteringRequestValue('invalid')->ifIsIpAddress('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsValidatedValue()
    {
        $this->assertEquals('example@example.net', $this->createFilteringRequestValue('example@example.net')->ifIsMailAddress());
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('invalid')->ifIsMailAddress());
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('example@example.org',
                            $this->createFilteringRequestValue('invalid')->ifIsMailAddress('example@example.org')
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsValidatedValue()
    {
        $this->assertEquals('as value',
                            $this->createFilteringRequestValue('as value')->ifIsOneOf(array('as value',
                                                                                            'anothervalue'
                                                                                      )
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('invalid')->ifIsOneOf(array('as value',
                                                                                            'anothervalue'
                                                                                      )
                                                                         )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createFilteringRequestValue('invalid')->ifIsOneOf(array('as value',
                                                                                           'anothervalue'
                                                                                     ),
                                                                                     'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsValidatedValue()
    {
        $this->assertEquals('a value',
                            $this->createFilteringRequestValue('a value')->ifSatisfiesRegex('/^([a-z ])+$/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createFilteringRequestValue('303')->ifSatisfiesRegex('/^([a-z ])+$/'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createFilteringRequestValue('303')->ifSatisfiesRegex('/^([a-z ])+$/',
                                                                                        'default'
                            )
        );
    }

    /**
     * @test
     */
    public function withReturnsValidatedValue()
    {
        $mockValidator = $this->getMock('stubValidator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(true));
        $this->assertEquals('a value',
                            $this->createFilteringRequestValue('a value')->withValidator($mockValidator)
        );
    }

    /**
     * @test
     */
    public function withReturnsNullIfValidatorCanNotValidateValue()
    {
        $mockValidator = $this->getMock('stubValidator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(false));
        $this->assertNull($this->createFilteringRequestValue('a value')->withValidator($mockValidator));
    }

    /**
     * @test
     */
    public function withReturnsDefaultValueIfValidationFails()
    {
        $mockValidator = $this->getMock('stubValidator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(false));
        $this->assertEquals('default',
                            $this->createFilteringRequestValue('a value')->withValidator($mockValidator,
                                                                                         'default'
                            )
        );
    }

    /**
     * @test
     */
    public function unsecure()
    {
        $this->assertEquals('a value', $this->createFilteringRequestValue('a value')->unsecure());
    }

    /**
     * @test
     */
    public function nameShouldBeEqualToGivenName()
    {
        $this->assertEquals('bar', $this->createFilteringRequestValue('a value')->getName());
    }
}
?>