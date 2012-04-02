<?php
/**
 * Test for net::stubbles::service::rest::index::stubRestLink.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::service::rest::index::stubRestLink');
/**
 * Test for net::stubbles::service::rest::index::stubRestLink.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.8.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_index
 */
class stubRestLinkTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRestLink
     */
    private $restLink;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->restLink = new stubRestLink('self', 'http://example.net/foo');
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->restLink->getClass()->hasAnnotation('XMLTag'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetRelMethod()
    {
        $this->assertTrue($this->restLink->getClass()
                                         ->getMethod('getRel')
                                         ->hasAnnotation('XMLAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetUriMethod()
    {
        $this->assertTrue($this->restLink->getClass()
                                         ->getMethod('getUri')
                                         ->hasAnnotation('XMLAttribute')
        );
    }

    /**
     * @test
     */
    public function returnsGivenRelation()
    {
        $this->assertEquals('self', $this->restLink->getRel());
    }

    /**
     * @test
     */
    public function returnsGivenUri()
    {
        $this->assertEquals('http://example.net/foo', $this->restLink->getUri());
    }
}
?>