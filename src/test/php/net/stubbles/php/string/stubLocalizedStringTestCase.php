<?php
/**
 * Tests for net::stubbles::php::string::stubLocalizedString.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubLocalizedStringTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubLocalizedString');
/**
 * Tests for net::stubbles::php::string::stubLocalizedString.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubLocalizedStringTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLocalizedString
     */
    protected $localizedString;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->localizedString = new stubLocalizedString('en_EN', 'This is a localized string.');
    }

    /**
     * make sure annotations are present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $class = $this->localizedString->getClass();
        $this->assertTrue($class->hasAnnotation('XMLTag'));
        $this->assertTrue($class->getMethod('getLocale')->hasAnnotation('XMLAttribute'));
        $this->assertTrue($class->getMethod('getMessage')->hasAnnotation('XMLTag'));
    }

    /**
     * locale should be returned
     *
     * @test
     */
    public function localeAttribute()
    {
        $this->assertEquals('en_EN', $this->localizedString->getLocale());
    }

    /**
     * content should be returned
     *
     * @test
     */
    public function contentOfString()
    {
        $this->assertEquals('This is a localized string.', $this->localizedString->getMessage());
    }
}
?>