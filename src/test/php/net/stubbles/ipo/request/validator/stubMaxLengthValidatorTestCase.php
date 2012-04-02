<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubMaxLengthValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubMaxLengthValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubMaxLengthValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubMaxLengthValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubMaxLengthValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMaxLengthValidator
     */
    protected $maxLengthValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->maxLengthValidator = new stubMaxLengthValidator(5);
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertTrue($this->maxLengthValidator->validate('123'));
        $this->assertTrue($this->maxLengthValidator->validate('1234'));
        $this->assertTrue($this->maxLengthValidator->validate('12345'));
        $this->assertTrue($this->maxLengthValidator->validate('hällo'));
        $this->assertTrue($this->maxLengthValidator->validate('hällö'));
        $this->assertTrue($this->maxLengthValidator->validate('äöüßµ'));
        $this->assertFalse($this->maxLengthValidator->validate('äöüßµa'));
        $this->assertFalse($this->maxLengthValidator->validate('123456'));
        $this->assertFalse($this->maxLengthValidator->validate('1234567890'));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('maxLength' => 5), $this->maxLengthValidator->getCriteria());
    }
}
?>