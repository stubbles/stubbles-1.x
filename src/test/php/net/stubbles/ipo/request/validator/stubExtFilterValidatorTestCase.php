<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubExtFilterValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubExtFilterValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubExtFilterValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubExtFilterValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubExtFilterValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Assure, that a validator without options and flags works
     *
     * @test
     */
    public function simple()
    {
        $validator = new stubExtFilterValidator(FILTER_VALIDATE_INT);
        $this->assertTrue($validator->validate(34));
        $this->assertFalse($validator->validate('no int'));
    }

    /**
     * Assure, that a validator with options works
     *
     * @test
     */
    public function options()
    {
        $options = array(
                     'min_range' => 10,
                     'max_range' => 20
                    );
        $validator = new stubExtFilterValidator(FILTER_VALIDATE_INT, $options);
        $this->assertTrue($validator->validate(15));
        $this->assertFalse($validator->validate('no int'));
        $this->assertFalse($validator->validate(25));
    }

    /**
     * Assure, that a validator with flags works
     *
     * @test
     */
    public function flags()
    {
        $validator = new stubExtFilterValidator(FILTER_VALIDATE_INT, array(), FILTER_FLAG_ALLOW_HEX);
        $this->assertTrue($validator->validate(15));
        $this->assertTrue($validator->validate('0xff'));
        $this->assertFalse($validator->validate('no int'));
    }

    /**
     * ext validator has no specific criteria
     *
     * @test
     */
    public function criteria()
    {
        $validator = new stubExtFilterValidator(FILTER_VALIDATE_INT, array(), FILTER_FLAG_ALLOW_HEX);
        $this->assertEquals(array(), $validator->getCriteria());
    }
}
?>