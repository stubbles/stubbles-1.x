<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubMailValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubMailValidatorTestCase.php 2404 2009-12-07 19:05:53Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubMailValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubMailValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubMailValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMailValidator
     */
    protected $mailValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mailValidator = new stubMailValidator();
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertFalse($this->mailValidator->validate(null));
        $this->assertFalse($this->mailValidator->validate(''));
        $this->assertFalse($this->mailValidator->validate('xcdsfad'));
        $this->assertFalse($this->mailValidator->validate('foobar@thishost.willnever.exist'));
        $this->assertTrue($this->mailValidator->validate('example@example.org'));
        $this->assertTrue($this->mailValidator->validate('example.foo.bar@example.org'));
        $this->assertFalse($this->mailValidator->validate('.foo.bar@example.org'));
        $this->assertFalse($this->mailValidator->validate('example@example.org\n'));
        $this->assertFalse($this->mailValidator->validate('example@exa"mple.org'));
        $this->assertFalse($this->mailValidator->validate('example@example.org\nBcc: example@example.com'));
    }

    /**
     * test other failing addresses
     *
     * @test
     */
    public function failingAddresses()
    {
        $this->assertFalse($this->mailValidator->validate('space in@mailadre.ss'));
        $this->assertFalse($this->mailValidator->validate('f��@mailadre.ss'));
        $this->assertFalse($this->mailValidator->validate('foo@bar@mailadre.ss'));
        $this->assertFalse($this->mailValidator->validate('foo&/4@mailadre.ss'));
        $this->assertFalse($this->mailValidator->validate('foo..bar@mailadre.ss'));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array(), $this->mailValidator->getCriteria());
    }

    /**
     * @test
     * @group  bug223
     * @link  http://stubbles.net/ticket/223
     */
    public function validatesIndependendOfLowerOrUpperCase()
    {
        $this->assertTrue($this->mailValidator->validate('Example@example.ORG'));
        $this->assertTrue($this->mailValidator->validate('Example.Foo.Bar@EXAMPLE.org'));
    }
}
?>