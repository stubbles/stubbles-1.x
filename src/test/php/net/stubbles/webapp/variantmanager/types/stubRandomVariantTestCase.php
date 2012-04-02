<?php
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubRandomVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubRandomVariantTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubRandomVariant',
                      'net::stubbles::webapp::variantmanager::types::stubRootVariant'
);
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubRandomVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubRandomVariantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRandomVariant
     */
    protected $randomVariant;
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
     * set up test environment
     */
    public function setUp()
    {
        $this->randomVariant = new stubRandomVariant();
        $this->mockSession   = $this->getMock('stubSession');
        $this->mockRequest   = $this->getMock('stubRequest');
    }
    
    /**
     * test that weight is correct
     *
     * @test
     */
    public function weight()
    {
        $this->assertEquals(0, $this->randomVariant->getWeight());
        $this->randomVariant->setWeight(1);
        $this->assertEquals(1, $this->randomVariant->getWeight());
    }
    
    /**
     * assure that a random variant is never enforcing
     *
     * @test
     */
    public function enforcing()
    {
        $this->assertFalse($this->randomVariant->isEnforcing($this->mockSession, $this->mockRequest));
    }
    
    /**
     * assure that the conditions for a random variant are always met
     *
     * @test
     */
    public function conditionsMet()
    {
        $this->assertTrue($this->randomVariant->conditionsMet($this->mockSession, $this->mockRequest));
    }
    
    /**
     * assure that a single random variant without a parent is always valid
     *
     * @test
     */
    public function singleRandomVariantWithoutParent()
    {
        $this->assertTrue($this->randomVariant->isValid($this->mockSession, $this->mockRequest));
    }
    
    /**
     * assure that a single random variant is always valid
     *
     * @test
     */
    public function singleRandomVariantWithParent()
    {
        $parent = new stubRootVariant();
        $parent->addChild($this->randomVariant);
        $this->assertTrue($this->randomVariant->isValid($this->mockSession, $this->mockRequest));
    }
    
    /**
     * assure that a bunch of sibling variants generates always the same valid variant
     *
     * @test
     */
    public function randomVariantWithSiblings()
    {
        $this->randomVariant->setName('foo');
        $this->randomVariant->setWeight(1);
        $randomVariant1 = new stubRandomVariant();
        $randomVariant1->setName('bar');
        $randomVariant1->setWeight(1);
        $randomVariant2 = new stubRandomVariant();
        $randomVariant2->setName('baz');
        $randomVariant2->setWeight(1);
        $parent = new stubRootVariant();
        $parent->setName('parent');
        $parent->addChild($this->randomVariant);
        $parent->addChild($randomVariant1);
        $parent->addChild($randomVariant2);

        $result1 = $this->randomVariant->isValid($this->mockSession, $this->mockRequest);
        $result2 = $this->randomVariant->isValid($this->mockSession, $this->mockRequest);
        $this->assertEquals($result1, $result2);
    }
}
?>