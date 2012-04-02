<?php
/**
 * Tests for net::stubbles::webapp::variantmanager::stubAbstractVariantFactory.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubAbstractVariantFactoryTestCase.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubAbstractVariantFactory',
                      'net::stubbles::webapp::variantmanager::types::stubLeadVariant'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 */
class TeststubAbstractVariantFactory extends stubAbstractVariantFactory
{
    /**
     * sets the variants map to be used
     *
     * @param  stubVariantsMap  $variantsMap
     */
    public function  __construct(stubVariantsMap $variantsMap)
    {
        $this->variantsMap = $variantsMap;
    }

    /**
     * creates the variants map
     *
     * @return  stubVariantsMap
     */
    protected function createVariantsMap()
    {
        return $this->variantsMap;
    }
}
/**
 * Tests for net::stubbles::webapp::variantmanager::stubAbstractVariantFactory.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubAbstractVariantFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubAbstractVariantFactory
     */
    protected $abstractVariantFactory;
    /**
     * root variant
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockVariantsMap;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockVariantsMap        = $this->getMock('stubVariantsMap');
        $this->abstractVariantFactory = new TeststubAbstractVariantFactory($this->mockVariantsMap);
    }

    /**
     * call should just be redirected to variants map
     *
     * @test
     */
    public function getVariantNames()
    {
        $this->mockVariantsMap->expects($this->once())
                              ->method('getVariantNames')
                              ->will($this->returnValue(array('foo', 'foo:bar', 'foo:baz')));
        $this->assertEquals(array('foo', 'foo:bar', 'foo:baz'), $this->abstractVariantFactory->getVariantNames());
    }

    /**
     * call should just be redirected to variants map
     *
     * @test
     */
    public function getVariantByName()
    {
        $mockVariant = $this->getMock('stubVariant');
        $this->mockVariantsMap->expects($this->once())
                              ->method('getVariantByName')
                              ->with($this->equalTo('foo'))
                              ->will($this->returnValue($mockVariant));
        $this->assertSame($mockVariant, $this->abstractVariantFactory->getVariantByName('foo'));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function shouldUsePersistence()
    {
        $this->mockVariantsMap->expects($this->once())
                              ->method('shouldUsePersistence')
                              ->will($this->returnValue(true));
        $this->assertTrue($this->abstractVariantFactory->shouldUsePersistence());
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function getVariant()
    {
        $mockRequest = $this->getMock('stubRequest');
        $mockSession = $this->getMock('stubSession');
        $mockVariant = $this->getMock('stubVariant');
        $this->mockVariantsMap->expects($this->once())
                              ->method('getVariant')
                              ->with($this->equalTo($mockSession),
                                     $this->equalTo($mockRequest)
                                )
                              ->will($this->returnValue($mockVariant));
        $this->assertSame($mockVariant, $this->abstractVariantFactory->getVariant($mockSession, $mockRequest));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function getEnforcingVariant()
    {
        $mockRequest = $this->getMock('stubRequest');
        $mockSession = $this->getMock('stubSession');
        $mockVariant = $this->getMock('stubVariant');
        $this->mockVariantsMap->expects($this->once())
                              ->method('getEnforcingVariant')
                              ->with($this->equalTo($mockSession),
                                     $this->equalTo($mockRequest)
                                )
                              ->will($this->returnValue($mockVariant));
        $this->assertSame($mockVariant, $this->abstractVariantFactory->getEnforcingVariant($mockSession, $mockRequest));
    }

    /**
     * variant map should be returned as is
     *
     * @test
     */
    public function getVariantsMap()
    {
        $this->assertSame($this->mockVariantsMap, $this->abstractVariantFactory->getVariantsMap());
    }

    /**
     * @test
     * @since  1.5.0
     */
    public function isVariantValidDelegatesToVariantsMap()
    {
        $mockRequest = $this->getMock('stubRequest');
        $mockSession = $this->getMock('stubSession');
        $this->mockVariantsMap->expects($this->once())
                              ->method('isVariantValid')
                              ->with($this->equalTo('foo'),
                                     $this->equalTo($mockSession),
                                     $this->equalTo($mockRequest)
                                )
                              ->will($this->returnValue(true));
        $this->assertTrue($this->abstractVariantFactory->isVariantValid('foo', $mockSession, $mockRequest));
    }

    /**
     * @test
     * @since  1.5.0
     */
    public function getVariantsMapNameReturnsNameOfVariantMap()
    {
        $this->mockVariantsMap->expects($this->once())
                              ->method('getName')
                              ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->abstractVariantFactory->getVariantsMapName());
    }
}
?>