<?php
/**
 * Test for net::stubbles::webapp::stubUriConfiguration.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::stubUriConfiguration');
/**
 * Test for net::stubbles::webapp::stubUriConfiguration.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @group       webapp
 */
class stubUriConfigurationTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubUriConfiguration
     */
    protected $uriConfiguration;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->uriConfiguration = new stubUriConfiguration('xml');
    }

    /**
     * @test
     */
    public function returnsOnlyApplicablePreInterceptors()
    {
        $this->assertEquals(array('my::All',
                                  'my::Other'
                            ),
                            $this->uriConfiguration->addPreInterceptor('my::All')
                                                   ->addPreInterceptor('my::Other', '^/xml/')
                                                   ->addPreInterceptor('my::NotAvailable', '^/rest/')
                                                   ->getPreInterceptors(new stubUriRequest('/xml/Home'))
        );
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function addProcessorNameWithNullUriConditionThrowsIllegalArgumentException()
    {
        $this->uriConfiguration->addProcessorName('example', null);
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function addProcessorNameWithEmptyUriConditionThrowsIllegalArgumentException()
    {
        $this->uriConfiguration->addProcessorName('example', '');
    }

    /**
     * @test
     */
    public function returnsDefaultProcessorNameIfNoProcessorSatisfiesUriRequest()
    {
        $this->assertEquals('xml',
                            $this->uriConfiguration->addProcessorName('example', '^/example/')
                                                   ->getProcessorName(new stubUriRequest('/xml/Home'))
        );
    }

    /**
     * @test
     */
    public function returnsProcessorNameWhichSatisfiesUriRequest()
    {
        $this->assertEquals('example',
                            $this->uriConfiguration->addProcessorName('example', '^/example/')
                                                   ->getProcessorName(new stubUriRequest('/example/Home'))
        );
    }

    /**
     * @test
     */
    public function defaultProcessorIsAlwaysEnabled()
    {
        $this->assertTrue($this->uriConfiguration->isProcessorEnabled('xml'));
    }

    /**
     * @test
     */
    public function addedProcessorIsAlwaysEnabled()
    {
        $this->assertTrue($this->uriConfiguration->addProcessorName('example', '^/example/')
                                                 ->isProcessorEnabled('example')
        );
    }

    /**
     * @test
     */
    public function nonAddedProcessorIsNeverEnabled()
    {
        $this->assertFalse($this->uriConfiguration->isProcessorEnabled('example'));
    }

    /**
     * @test
     */
    public function returnsOnlyApplicablePostInterceptors()
    {
        $this->assertEquals(array('my::All',
                                  'my::Other'
                            ),
                            $this->uriConfiguration->addPostInterceptor('my::All')
                                                   ->addPostInterceptor('my::Other', '^/xml/')
                                                   ->addPostInterceptor('my::NotAvailable', '^/rest/')
                                                   ->getPostInterceptors(new stubUriRequest('/xml/Home'))
        );
    }
}
?>