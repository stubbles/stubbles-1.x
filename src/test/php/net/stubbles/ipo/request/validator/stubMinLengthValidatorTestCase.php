<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubMinLengthValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubMinLengthValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubMinLengthValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubMinLengthValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubMinLengthValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMinLengthValidator
     */
    protected $minLengthValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->minLengthValidator = new stubMinLengthValidator(5);
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertFalse($this->minLengthValidator->validate('123'));
        $this->assertFalse($this->minLengthValidator->validate('1234'));
        $this->assertFalse($this->minLengthValidator->validate('äöüß'));
        $this->assertTrue($this->minLengthValidator->validate('hällo'));
        $this->assertTrue($this->minLengthValidator->validate('hällö'));
        $this->assertTrue($this->minLengthValidator->validate('äöüßµ'));
        $this->assertTrue($this->minLengthValidator->validate('12345'));
        $this->assertTrue($this->minLengthValidator->validate('123456'));
        $this->assertTrue($this->minLengthValidator->validate('1234567890'));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('minLength' => 5), $this->minLengthValidator->getCriteria());
    }
}
?>