<?php
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubAbstractVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types_test
 * @version     $Id: stubAbstractVariantTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubAbstractVariant',
                      'net::stubbles::webapp::variantmanager::types::stubDummyVariant',
                      'net::stubbles::webapp::variantmanager::types::stubRootVariant'
);
class stubTestVariant extends stubAbstractVariant
{
    protected $enforcing = false;
    protected $valid     = false;
    
    public function setEnforcing($enforcing)
    {
        $this->enforcing = $enforcing;
    }
    
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        return $this->enforcing;
    }
    
    public function setValid($valid)
    {
        $this->valid = $valid;
    }
    public function isValid(stubSession $session, stubRequest $request)
    {
        return $this->valid;
    }
}
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubAbstractVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types_test
 * @group       webapp
 * @group       webapp_variantmanager
 * @group       webapp_variantmanager_types
 */
class stubAbstractVariantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the instance to test
     *
     * @var  stubTestVariant
     */
    protected $abstractVariant;
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
        $this->abstractVariant = new stubTestVariant();
        $this->mockSession     = $this->getMock('stubSession');
        $this->mockRequest     = $this->getMock('stubRequest');
    }

    /**
     * @test
     */
    public function nameIsEmptyByDefault()
    {
        $this->assertEquals('', $this->abstractVariant->getName());
        $this->assertEquals('', $this->abstractVariant->getFullQualifiedName());
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $this->assertEquals('foo', $this->abstractVariant->setName('foo')->getName());
        $this->assertEquals('foo', $this->abstractVariant->getFullQualifiedName());
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function tooLongNameThrowsVariantConfigurationException()
    {
        $this->abstractVariant->setName('foobarbazfoobarbaz');
    }

    /**
     * @test
     */
    public function titleIsEmptyByDefault()
    {
        $this->assertEquals('', $this->abstractVariant->getTitle());
    }

    /**
     * @test
     */
    public function titleCanBeSet()
    {
        $this->assertEquals('foo', $this->abstractVariant->setTitle('foo')->getTitle());
    }

    /**
     * @test
     */
    public function aliasIsEmptyByDefault()
    {
        $this->assertEquals('', $this->abstractVariant->getAlias());
    }

    /**
     * @test
     */
    public function aliasCanBeSet()
    {
        $this->assertEquals('foo', $this->abstractVariant->setAlias('foo')->getAlias());
    }

    /**
     * test a non valid and non enforcing variant
     *
     * @test
     */
    public function nonValidNonEnforcing()
    {
        $this->abstractVariant->setEnforcing(false);
        $this->abstractVariant->setValid(false);
        $this->assertNull($this->abstractVariant->getEnforcingVariant($this->mockSession, $this->mockRequest));
        $this->assertNull($this->abstractVariant->getVariant($this->mockSession, $this->mockRequest));
    }

    /**
     * test a variant that has no childs
     *
     * @test
     */
    public function withoutChilds()
    {
        $this->abstractVariant->setEnforcing(true);
        $this->abstractVariant->setValid(true);
        $this->assertEquals(array(), $this->abstractVariant->getChildren());
        $variant = $this->abstractVariant->getEnforcingVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($this->abstractVariant, $variant);
        $variant = $this->abstractVariant->getVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($this->abstractVariant, $variant);
    }

    /**
     * test a variant that has non-valid childs
     *
     * @test
     */
    public function withNonValidChilds()
    {
        $this->abstractVariant->setEnforcing(true);
        $this->abstractVariant->setValid(true);
        $child1 = $this->getMock('stubConfigurableVariant');
        $child1->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $child1->expects($this->once())->method('getEnforcingVariant')->will($this->returnValue(null));
        $child1->expects($this->once())->method('getVariant', null);
        $this->abstractVariant->addChild($child1);
        $child2 = $this->getMock('stubConfigurableVariant');
        $child2->expects($this->any())->method('getName')->will($this->returnValue('bar'));
        $child2->expects($this->once())->method('getEnforcingVariant')->will($this->returnValue(null));
        $child2->expects($this->once())->method('getVariant', null);
        $this->abstractVariant->addChild($child2);
        $this->assertEquals(array('foo' => $child1, 'bar' => $child2), $this->abstractVariant->getChildren());
        $variant = $this->abstractVariant->getEnforcingVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($this->abstractVariant, $variant);
        $variant = $this->abstractVariant->getVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($this->abstractVariant, $variant);
    }

    /**
     * test with a valid child
     *
     * @test
     */
    public function withValidChilds()
    {
        $this->abstractVariant->setEnforcing(true);
        $this->abstractVariant->setValid(true);
        $child1 = $this->getMock('stubConfigurableVariant');
        $child1->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $child1->expects($this->once())->method('getEnforcingVariant')->will($this->returnValue($child1));
        $child1->expects($this->once())->method('getVariant')->will($this->returnValue($child1));
        $this->abstractVariant->addChild($child1);
        $this->assertEquals(array('foo' => $child1), $this->abstractVariant->getChildren());
        $variant = $this->abstractVariant->getEnforcingVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($child1, $variant);
        $variant = $this->abstractVariant->getVariant($this->mockSession, $this->mockRequest);
        $this->assertSame($child1, $variant);
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function addVariantToItselfAsChildThrowsVariantConfigurationException()
    {
        $this->abstractVariant->addChild($this->abstractVariant);
    }

    /**
     * @test
     */
    public function hasNoParentByDefault()
    {
        $this->assertFalse($this->abstractVariant->hasParent());
        $this->assertNull($this->abstractVariant->getParent());
    }

    /**
     * @test
     */
    public function canNotBeAssignedAsChoosenIfItHasNoParent()
    {
        $this->assertFalse($this->abstractVariant->assign($this->mockSession, $this->mockRequest));
    }

    /**
     * @test
     */
    public function hasParentWhenAddedAsChildToAnotherVariant()
    {
        $parent = new stubTestVariant();
        $parent->setName('foo')
               ->addChild($this->abstractVariant);
        $this->assertTrue($this->abstractVariant->hasParent());
        $this->assertSame($parent, $this->abstractVariant->getParent());
    }

    /**
     * @test
     */
    public function canBeAssignedAsChoosenIfItHasParent()
    {
        $parent = new stubTestVariant();
        $parent->setName('foo')
               ->addChild($this->abstractVariant);
        $this->assertTrue($this->abstractVariant->assign($this->mockSession, $this->mockRequest));
    }

    /**
     * @test
     */
    public function fullQualifiedVariantNameContainsParentName()
    {
        $parent = new stubTestVariant();
        $parent->setName('foo')
               ->addChild($this->abstractVariant);
        $this->assertEquals('foo:bar', $this->abstractVariant->setName('bar')->getFullQualifiedName());
    }

    /**
     * @test
     */
    public function fullQualifiedVariantNameDoesNotContainParentNameIfParentIsRoot()
    {
        $rootVariant = new stubRootVariant();
        $rootVariant->setName('foo')
                    ->addChild($this->abstractVariant);
        $this->assertEquals('bar', $this->abstractVariant->setName('bar')->getFullQualifiedName());
    }

    /**
     * test __sleep() and __wakeup()
     *
     * @test
     */
    public function sleepWakeup()
    {
        $this->abstractVariant->setName('foo');
        $dummy1 = new stubDummyVariant();
        $dummy1->setName('bar');
        $this->abstractVariant->addChild($dummy1);
        $dummy2 = new stubDummyVariant();
        $dummy2->setName('baz');
        $dummy1->addChild($dummy2);
        
        $serialized = serialize($this->abstractVariant);
        $abstractVariant = unserialize($serialized);
        $this->assertFalse($abstractVariant->hasParent());
        $children1 = $abstractVariant->getChildren();
        $this->assertEquals('bar', $children1['bar']->getName());
        $this->assertEquals('foo', $children1['bar']->getParent()->getName());
        $children2 = $children1['bar']->getChildren();
        $this->assertEquals('baz', $children2['baz']->getName());
        $this->assertEquals('bar', $children2['baz']->getParent()->getName());
    }
}
?>