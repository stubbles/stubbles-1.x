<?php
/**
 * Test for net::stubbles::ipo::request::stubRequestValueErrorPropertiesFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubRequestValueErrorPropertiesFactoryTestCase.php 3049 2011-02-19 17:51:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorPropertiesFactory');
/**
 * Test for net::stubbles::ipo::request::stubRequestValueErrorPropertiesFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @since       1.3.0
 * @group       ipo
 * @group       ipo_request
 */
class stubRequestValueErrorPropertiesFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRequestValueErrorPropertiesFactory
     */
    protected $requestValueErrorPropertiesFactory;
    /**
     * mocked resource loader
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResourceLoader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockResourceLoader = $this->getMock('stubResourceLoader');
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array(TEST_SRC_PATH . '/resources/ipo/request1.ini',
                                                                 TEST_SRC_PATH . '/resources/ipo/request2.ini'
                                                           )
                                        )
                                   );
        $this->requestValueErrorPropertiesFactory = new stubRequestValueErrorPropertiesFactory($this->mockResourceLoader);
        
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function createNonExistingRequestValueErrorThrowsInvalidArgumentException()
    {
        $this->requestValueErrorPropertiesFactory->create('doesNotExist');
    }

    /**
     * @test
     */
    public function createFromSectionWithValueKeysProperty()
    {
        $requestValueError = $this->requestValueErrorPropertiesFactory->create('ERROR_WITH_VALUE_KEYS');
        $this->assertInstanceOf('stubRequestValueError', $requestValueError);
        $this->assertEquals('ERROR_WITH_VALUE_KEYS', $requestValueError->getId());
        $this->assertTrue($requestValueError->hasMessage('default'));
        $this->assertTrue($requestValueError->hasMessage('en_*'));
        $this->assertTrue($requestValueError->hasMessage('de_*'));
        $this->assertTrue($requestValueError->hasMessage('fr_*'));
        $this->assertTrue($requestValueError->hasMessage('es_*'));
        $this->assertTrue($requestValueError->hasMessage('ro_*'));
        $this->assertTrue($requestValueError->hasMessage('pl_*'));
        $this->assertFalse($requestValueError->hasMessage('valueKeys'));
        $this->assertEquals(array('key1', 'key2'), $requestValueError->getValueKeys());
    }

    /**
     * @test
     */
    public function createFromSectionWithoutValueKeysProperty()
    {
        $requestValueError = $this->requestValueErrorPropertiesFactory->create('ERROR_WITHOUT_VALUE_KEYS');
        $this->assertInstanceOf('stubRequestValueError', $requestValueError);
        $this->assertEquals('ERROR_WITHOUT_VALUE_KEYS', $requestValueError->getId());
        $this->assertTrue($requestValueError->hasMessage('default'));
        $this->assertTrue($requestValueError->hasMessage('en_*'));
        $this->assertTrue($requestValueError->hasMessage('de_*'));
        $this->assertTrue($requestValueError->hasMessage('fr_*'));
        $this->assertTrue($requestValueError->hasMessage('es_*'));
        $this->assertTrue($requestValueError->hasMessage('ro_*'));
        $this->assertTrue($requestValueError->hasMessage('pl_*'));
        $this->assertFalse($requestValueError->hasMessage('valueKeys'));
        $this->assertEquals(array(), $requestValueError->getValueKeys());
    }

    /**
     * @test
     */
    public function createFromShadowedSection()
    {
        $requestValueError = $this->requestValueErrorPropertiesFactory->create('SHADOWED_ERROR');
        $this->assertInstanceOf('stubRequestValueError', $requestValueError);
        $this->assertEquals('SHADOWED_ERROR', $requestValueError->getId());
        $this->assertTrue($requestValueError->hasMessage('default'));
        $this->assertEquals('Please enter a different value.', $requestValueError->getMessage('default'));
        $this->assertTrue($requestValueError->hasMessage('en_*'));
        $this->assertTrue($requestValueError->hasMessage('de_*'));
        $this->assertTrue($requestValueError->hasMessage('fr_*'));
        $this->assertTrue($requestValueError->hasMessage('es_*'));
        $this->assertTrue($requestValueError->hasMessage('ro_*'));
        $this->assertTrue($requestValueError->hasMessage('pl_*'));
        $this->assertFalse($requestValueError->hasMessage('valueKeys'));
        $this->assertEquals(array(), $requestValueError->getValueKeys());
    }
}
?>