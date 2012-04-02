<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubRegexValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubRegexValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubRegexValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubRegexValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubRegexValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRegexValidator
     */
    protected $regexValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        // regex allows only three lowercase characters
        $this->regexValidator = new stubRegexValidator('/^([a-z]{3})$/');
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertTrue($this->regexValidator->validate('foo'));
        $this->assertFalse($this->regexValidator->validate('Bar'));
        $this->assertFalse($this->regexValidator->validate('baz0123'));
        $this->assertFalse($this->regexValidator->validate(null));
    }

    /**
     * assure that validation using modifiers in regular expression works correct
     *
     * @test
     */
    public function validationWithModifier()
    {
        $regexValidator = new stubRegexValidator('/^([a-z]{3})$/i');
        $this->assertTrue($regexValidator->validate('foo'));
        $this->assertTrue($regexValidator->validate('Bar'));
        $this->assertFalse($regexValidator->validate('baz0123'));
        $this->assertFalse($regexValidator->validate(null));
    }

    /**
     * assert that a runtime exception is thrown in case an invalid regular
     * expression is used
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function invalidRegex()
    {
        $regexValidator = new stubRegexValidator('^([a-z]{3})$');
        $regexValidator->validate('foo');
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('regex' => '/^([a-z]{3})$/'), $this->regexValidator->getCriteria());
    }
}
?>