<?php
/**
 * Test for net::stubbles::service::rest::format::stubPlainTextFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @version     $Id: stubPlainTextFormatterTestCase.php 2568 2010-05-26 11:04:25Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubPlainTextFormatter');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 */
class StringConversionTestHelper
{
    /**
     * returns string conversion of this class
     *
     * @return  string
     */
    public function __toString()
    {
        return 'converted to string';
    }
}
/**
 * Tests for net::stubbles::service::rest::format::stubPlainTextFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @since       1.1.2
 * @group       service
 * @group       service_rest
 * @group       service_rest_format
 */
class stubPlainTextFormatterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPlainTextFormatter
     */
    protected $plainTextFormatter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->plainTextFormatter = new stubPlainTextFormatter();
    }

    /**
     * @test
     */
    public function correctContentType()
    {
        $this->assertEquals('text/plain',
                            $this->plainTextFormatter->getContentType()
        );
    }

    /**
     * @test
     */
    public function returnsPlainText()
    {
        $this->assertEquals('This is a response',
                            $this->plainTextFormatter->format('This is a response')
        );
    }

    /**
     * @test
     */
    public function returnsPlainTextForNumbers()
    {
        $this->assertEquals('303',
                            $this->plainTextFormatter->format(303)
        );
    }

    /**
     * @test
     */
    public function returnsPlainTextForBoolean()
    {
        $this->assertEquals('true',
                            $this->plainTextFormatter->format(true)
        );
        $this->assertEquals('false',
                            $this->plainTextFormatter->format(false)
        );
    }

    /**
     * @test
     */
    public function usesVarExportForArrays()
    {
        $this->assertEquals("array (\n  303 => 'cool',\n)",
                            $this->plainTextFormatter->format(array(303 => 'cool'))
        );
    }

    /**
     * @test
     */
    public function usesVarExportForObjectWithoutToStringMethod()
    {
        $stdClass = new stdClass();
        $stdClass->foo = 'bar';
        $this->assertEquals("stdClass::__set_state(array(\n   'foo' => 'bar',\n))",
                            $this->plainTextFormatter->format($stdClass)
        );
    }

    /**
     * @test
     */
    public function castsObjectWithToStringMethod()
    {
        $this->assertEquals('converted to string',
                            $this->plainTextFormatter->format(new StringConversionTestHelper())
        );
    }

    /**
     * @test
     */
    public function formatNotFoundError()
    {
        $this->assertEquals('Given resource could not be found.',
                            $this->plainTextFormatter->formatNotFoundError()
        );
    }

    /**
     * @test
     */
    public function formatMethodNotAllowedError()
    {
        $this->assertEquals('The given request method PUT is not valid. Please use GET, POST, DELETE.',
                            $this->plainTextFormatter->formatMethodNotAllowedError('PUT', array('GET', 'POST', 'DELETE'))
        );
    }

    /**
     * @test
     */
    public function formatInternalServerError()
    {
        $this->assertEquals('Internal Server Error: Error message',
                            $this->plainTextFormatter->formatInternalServerError(new Exception('Error message'))
        );
    }
}
?>