<?php
/**
 * Test for net::stubbles::service::rest::format::stubJsonFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @version     $Id: stubJsonFormatterTestCase.php 2398 2009-12-01 13:13:32Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubJsonFormatter');
/**
 * Tests for net::stubbles::service::rest::format::stubJsonFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @since       1.1.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_format
 */
class stubJsonFormatterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubJsonFormatter
     */
    protected $jsonFormatter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->jsonFormatter = new stubJsonFormatter();
    }

    /**
     * @test
     */
    public function correctContentType()
    {
        $this->assertEquals('application/json',
                            $this->jsonFormatter->getContentType()
        );
    }

    /**
     * @test
     */
    public function formatsJson()
    {
        $this->assertEquals(json_encode(array('foo', 'bar' => 313)),
                            $this->jsonFormatter->format(array('foo', 'bar' => 313))
        );
    }

    /**
     * @test
     */
    public function formatNotFoundError()
    {
        $this->assertEquals(json_encode(array('error' => 'Given resource could not be found.')),
                            $this->jsonFormatter->formatNotFoundError()
        );
    }

    /**
     * @test
     */
    public function formatMethodNotAllowedError()
    {
        $this->assertEquals(json_encode(array('error' => 'The given request method PUT is not valid. Please use GET, POST, DELETE.')),
                            $this->jsonFormatter->formatMethodNotAllowedError('PUT', array('GET', 'POST', 'DELETE'))
        );
    }

    /**
     * @test
     */
    public function formatInternalServerError()
    {
        $this->assertEquals(json_encode(array('error' => 'Internal Server Error: Error message')),
                            $this->jsonFormatter->formatInternalServerError(new Exception('Error message'))
        );
    }
}
?>