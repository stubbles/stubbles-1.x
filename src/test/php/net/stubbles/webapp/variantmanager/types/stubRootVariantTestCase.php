<?php
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubRootVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubRootVariantTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubRootVariant');
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubRootVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubRootVariantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRootVariant
     */
    protected $rootVariant;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rootVariant = new stubRootVariant();
    }
    
    /**
     * assure that the name of the root variant can not be changed
     *
     * @test
     */
    public function name()
    {
        $this->assertEquals('root', $this->rootVariant->getName());
        $this->rootVariant->setName('foo');
        $this->assertEquals('root', $this->rootVariant->getName());
    }
    
    /**
     * test that a root variant is always valid and always enforcing
     *
     * @test
     */
    public function valid()
    {
        $session = $this->getMock('stubSession');
        $request = $this->getMock('stubRequest');
        $this->assertTrue($this->rootVariant->isEnforcing($session, $request));
        $this->assertTrue($this->rootVariant->isValid($session, $request));
    }
}
?>