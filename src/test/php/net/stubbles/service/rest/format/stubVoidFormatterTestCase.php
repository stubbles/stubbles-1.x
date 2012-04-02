<?php
/**
 * Test for net::stubbles::service::rest::format::stubVoidFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @version     $Id: stubVoidFormatterTestCase.php 2398 2009-12-01 13:13:32Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubVoidFormatter');
/**
 * Tests for net::stubbles::service::rest::format::stubVoidFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @since       1.1.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_format
 */
class stubVoidFormatterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubVoidFormatter
     */
    protected $voidFormatter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->voidFormatter = new stubVoidFormatter();
    }

    /**
     * @test
     */
    public function correctContentType()
    {
        $this->assertEquals('text/plain',
                            $this->voidFormatter->getContentType()
        );
    }

    /**
     * @test
     */
    public function formatReturnsEmptyString()
    {
        $this->assertEquals('', $this->voidFormatter->format(array('foo', 'bar' => 313)));
    }

    /**
     * @test
     */
    public function formatNotFoundErrorReturnsEmptyString()
    {
        $this->assertEquals('', $this->voidFormatter->formatNotFoundError());
    }

    /**
     * @test
     */
    public function formatMethodNotAllowedErrorReturnsEmptyString()
    {
        $this->assertEquals('', $this->voidFormatter->formatMethodNotAllowedError('PUT', array('GET', 'POST', 'DELETE')));
    }

    /**
     * @test
     */
    public function formatInternalServerErrorReturnsEmptyString()
    {
        $this->assertEquals('', $this->voidFormatter->formatInternalServerError(new Exception('Error  message')));
    }
}
?>