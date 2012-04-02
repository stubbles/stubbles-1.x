<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubDefaultFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubDefaultFilterFactoryTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubDefaultFilterFactory');
/**
 * Tests for net::stubbles::ipo::request::filter::stubDefaultFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubDefaultFilterFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultFilterFactory
     */
    protected $defaultFilterFactory;

    /**
     * list of filters to provide via filter factory
     *
     * @var  array<string,string>
     */
    protected $typeFilter = array('int'      => 'net::stubbles::ipo::request::filter::stubIntegerFilter',
                                  'integer'  => 'net::stubbles::ipo::request::filter::stubIntegerFilter',
                                  'double'   => 'net::stubbles::ipo::request::filter::stubFloatFilter',
                                  'float'    => 'net::stubbles::ipo::request::filter::stubFloatFilter',
                                  'string'   => 'net::stubbles::ipo::request::filter::stubStringFilter',
                                  'text'     => 'net::stubbles::ipo::request::filter::stubTextFilter',
                                  'json'     => 'net::stubbles::ipo::request::filter::stubJsonFilter',
                                  'password' => 'net::stubbles::ipo::request::filter::stubPasswordFilter',
                                  'http'     => 'net::stubbles::ipo::request::filter::stubHTTPURLFilter',
                                  'date'     => 'net::stubbles::ipo::request::filter::stubDateFilter',
                                  'mail'     => 'net::stubbles::ipo::request::filter::stubMailFilter'
                            );

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultFilterFactory = new stubDefaultFilterFactory($this->typeFilter, $this->getMock('stubRequestValueErrorFactory'));
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $class = $this->defaultFilterFactory->getClass();
        $this->assertTrue($class->hasAnnotation('Singleton'));
        
        $constructor = $class->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        
        $params = $constructor->getParameters();
        $this->assertTrue($params[0]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.ipo.request.filter.types', $params[0]->getAnnotation('Named')->getName());
        
        $class = new stubReflectionClass('stubFilterFactory');
        $this->assertTrue($class->hasAnnotation('ImplementedBy'));
    }

    /**
     * @test
     */
    public function injectInterfaceGivesImplementation()
    {
        $binder = new stubBinder();
        $binder->bindConstant()
               ->named('net.stubbles.ipo.request.filter.types')
               ->to(array());
        $filterFactory = $binder->getInjector()->getInstance('stubFilterFactory');
        $this->assertInstanceOf('stubDefaultFilterFactory', $filterFactory);
    }

    /**
     * test that integer filter is created
     *
     * @test
     */
    public function integerFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('integer');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubIntegerFilter', $filter->getDecoratedFilter());
        $filter = $this->defaultFilterFactory->createforType('int');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubIntegerFilter', $filter->getDecoratedFilter());
    }

    /**
     * test that float filter is created
     *
     * @test
     */
    public function floatFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('double');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubFloatFilter', $filter->getDecoratedFilter());
        $filter = $this->defaultFilterFactory->createforType('float');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubFloatFilter', $filter->getDecoratedFilter());
    }

    /**
     * test that string filter is created
     *
     * @test
     */
    public function stringFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('string');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubStringFilter', $filter->getDecoratedFilter());
    }

    /**
     * test that text filter is created
     *
     * @test
     */
    public function textFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('text');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubTextFilter', $filter->getDecoratedFilter());
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalFilterThrowsIllegalArgumentException()
    {
        $this->defaultFilterFactory->createforType('illegal');
    }

    /**
     * test that hpasswordttp filter is created
     *
     * @test
     */
    public function passwordFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('password')->minDiffChars(3)->nonAllowedValues(array(1, 2, 3));
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $passwordFilter = $filter->getDecoratedFilter();
        $this->assertInstanceOf('stubPasswordFilter', $passwordFilter);
        $this->assertEquals(3, $passwordFilter->getMinDiffChars());
        $this->assertEquals(array(1, 2, 3), $passwordFilter->getNonAllowedValues());
    }

    /**
     * test that http filter is created
     *
     * @test
     */
    public function httpFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('http');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubHTTPURLFilter', $filter->getDecoratedFilter());
    }

    /**
     * test that mail filter is created
     *
     * @test
     */
    public function mailFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('mail');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubMailFilter', $filter->getDecoratedFilter());
    }

    /**
     * test that date filter is created
     *
     * @test
     */
    public function dateFilter()
    {
        $filter = $this->defaultFilterFactory->createforType('date');
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertInstanceOf('stubDateFilter', $filter->getDecoratedFilter());
    }

    /**
     * test that method chaining is possible with forFilter() is created
     *
     * @test
     */
    public function forFilterMethodChaining()
    {
        $mockFilter = $this->getMock('stubFilter');
        $filter     = $this->defaultFilterFactory->createBuilder($mockFilter);
        $this->assertInstanceOf('stubFilterBuilder', $filter);
        $this->assertSame($mockFilter, $filter->getDecoratedFilter());
    }
}
?>