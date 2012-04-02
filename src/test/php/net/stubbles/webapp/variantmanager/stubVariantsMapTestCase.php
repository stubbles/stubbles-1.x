<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantsMap.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubVariantsMapTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantsMap',
                      'net::stubbles::webapp::variantmanager::types::stubLeadVariant',
                      'net::stubbles::webapp::variantmanager::types::stubRequestParamVariant'
);
/**
 * Enforcing variant to be used in the tests.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 */
class EnforcingVariant extends stubAbstractVariant
{
    /**
     * check whether the variant is an enforcing variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        return true;
    }

    /**
     * check whether the variant is valid
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isValid(stubSession $session, stubRequest $request)
    {
        return true;
    }
}
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantsMap.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubVariantsMapTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the instance to test
     *
     * @var  stubVariantsMap
     */
    protected $variantMap;
    /**
     * the mocked session
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * the mocked request
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * a variant
     *
     * @var  stubLeadVariant
     */
    protected $v1;
    /**
     * a variant
     *
     * @var  stubLeadVariant
     */
    protected $v2;
    /**
     * a variant
     *
     * @var  stubLeadVariant
     */
    protected $v3;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->variantMap  = new stubVariantsMap();
        $this->mockSession = $this->getMock('stubSession');
        $this->mockRequest = $this->getMock('stubRequest');
        $this->v1          = new stubLeadVariant();
        $this->v1->setName('v1');
        $this->v2          = new stubLeadVariant();
        $this->v2->setName('v2');
        $this->v3          = new stubLeadVariant();
        $this->v3->setName('v3');
    }

    /**
     * assure that name is handled correct
     *
     * @test
     */
    public function name()
    {
        $this->assertNull($this->variantMap->getName());
        $this->variantMap->setName('foo');
        $this->assertEquals('foo', $this->variantMap->getName());
    }

    /**
     * assure that persistence is handled correct
     *
     * @test
     */
    public function usePersistence()
    {
        $this->assertTrue($this->variantMap->shouldUsePersistence());
        $this->variantMap->setUsePersistence(false);
        $this->assertFalse($this->variantMap->shouldUsePersistence());
    }

    /**
     * test handling with no variants configured
     *
     * @test
     */
    public function noVariant()
    {
        $this->assertEquals(array(), $this->variantMap->getVariants());
        $this->assertNull($this->variantMap->getVariantByName('foo'));
        $this->assertEquals(array(), $this->variantMap->getVariantNames());
        $this->assertFalse($this->variantMap->variantExists('foo'));
        $this->assertFalse($this->variantMap->isVariantValid('foo', $this->mockSession, $this->mockRequest));
        $this->assertNull($this->variantMap->getEnforcingVariant($this->mockSession, $this->mockRequest));
    }

    /**
     * test handling with no variants configured
     *
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function noVariantThrowsExceptionIfVariantIsRequested()
    {
        $this->variantMap->getVariant($this->mockSession, $this->mockRequest);
    }

    /**
     * test handling with one variant
     *
     * @test
     */
    public function oneVariant()
    {
        $this->variantMap->addChild($this->v1);
        // can not test this: recursion
        // $this->assertEqual($this->variantMap->getVariants(), array('v1' => $this->v1));
        $variantTest1 = $this->variantMap->getVariantByName('v1');
        $this->assertSame($this->v1, $variantTest1);
        $this->assertEquals(array('v1'), $this->variantMap->getVariantNames());
        $this->assertTrue($this->variantMap->variantExists('v1'));
        $this->assertTrue($this->variantMap->isVariantValid('v1', $this->mockSession, $this->mockRequest));
        $this->assertNull($this->variantMap->getEnforcingVariant($this->mockSession, $this->mockRequest));
        $variantTest2 = $this->variantMap->getVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($this->v1, $variantTest2);
    }

    /**
     * Tests that all nested variants are found
     *
     * @test
     */
    public function nestedVariant()
    {
        $this->v2->addChild($this->v3);
        $this->v1->addChild($this->v2);
        $this->variantMap->addChild($this->v1);
        
        $this->assertTrue($this->variantMap->variantExists('v2'));
        $this->assertTrue($this->variantMap->variantExists('v3'));
    }

    /**
     * Test that variant is not valid
     *
     * @test
     */
    public function invalidVariant()
    {
        $var = new stubRequestParamVariant();
        $var->setName('foo');
        $var->setParamName('foo');
        $this->mockRequest->expects($this->any())->method('hasValue')->will($this->onConsecutiveCalls(false, true, false));
        $var2 = clone $var;
        $var2->setName('bar');
        $var->addChild($var2);
        $this->variantMap->addChild($var);
        $this->variantMap->addChild($this->v1);
        
        $this->assertFalse($this->variantMap->isVariantValid('foo', $this->mockSession, $this->mockRequest));
        $this->assertFalse($this->variantMap->isVariantValid('bar', $this->mockSession, $this->mockRequest));
    }

    /**
     * Test that no enforcing variant is returned when there is no enforcing variant set
     *
     * @test
     */
    public function enforcingVariantIsNullIfRoot()
    {
        $this->variantMap->addChild($this->v3);
        $this->variantMap->addChild($this->v2);
        $this->variantMap->addChild($this->v1);
        
        $this->assertNull($this->variantMap->getEnforcingVariant($this->mockSession, $this->mockRequest));
    }

    /**
     * Test that no enforcing variant is returned when there is no enforcing variant set
     *
     * @test
     */
    public function enforcingVariantIsReturned()
    {
        $enforcingVariant = new EnforcingVariant();
        $enforcingVariant->setName('enforcing');
        $this->variantMap->addChild($this->v3);
        $this->variantMap->addChild($enforcingVariant);
        $this->variantMap->addChild($this->v1);
        
        $this->assertSame($enforcingVariant, $this->variantMap->getEnforcingVariant($this->mockSession, $this->mockRequest));
    }

    /**
     * Tests that the variant are handled as references and returned by their name
     *
     * @test
     */
    public function getVariantByName()
    {
        $this->variantMap->addChild($this->v3);
        $this->variantMap->addChild($this->v2);
        $this->variantMap->addChild($this->v1);
        
        $this->assertSame($this->v1, $this->variantMap->getVariantByName('v1'));
        $this->assertSame($this->v2, $this->variantMap->getVariantByName('v2'));
        $this->assertSame($this->v3, $this->variantMap->getVariantByName('v3'));
    }

    /**
     * Test that all variant names will be returned
     *
     * @test
     */
    public function getVariantNames()
    {
        $this->v1->addChild($this->v2);
        $this->v2->addChild($this->v3);
        $this->variantMap->addChild($this->v1);
        
        $this->assertEquals(array('v1', 'v2', 'v1:v2', 'v3', 'v1:v2:v3'), $this->variantMap->getVariantNames());
    }

    /**
     * test creating the variants map with a root variant as argument
     *
     * @test
     */
    public function withRootArgument()
    {
        $rootVariant = new stubRootVariant();
        $this->v2->addChild($this->v3);
        $this->v1->addChild($this->v2);
        $rootVariant->addChild($this->v1);
        $variantMap = new stubVariantsMap($rootVariant);
        
        $this->assertEquals(array('v1', 'v2', 'v1:v2', 'v3', 'v1:v2:v3'), $variantMap->getVariantNames());
        $this->assertSame($this->v1, $variantMap->getVariantByName('v1'));
        $this->assertSame($this->v2, $variantMap->getVariantByName('v2'));
        $this->assertSame($this->v3, $variantMap->getVariantByName('v3'));
    }
}
?>