<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubContainsValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubContainsValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubContainsValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubContainsValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubContainsValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that construction works correct
     *
     * @test
     */
    public function construction()
    {
        $containsValidator = new stubContainsValidator(5);
        $containsValidator = new stubContainsValidator('foo');
        $containsValidator = new stubContainsValidator(true);
        $containsValidator = new stubContainsValidator(false);
    }

    /**
     * assure that construction works correct
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructionWithObject()
    {
        $containsValidator = new stubContainsValidator(new stdClass());
    }

    /**
     * assure that construction works correct
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructionWithNull()
    {
        $containsValidator = new stubContainsValidator(null);
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $containsValidator = new stubContainsValidator(5);
        $this->assertFalse($containsValidator->validate('foo'));
        $this->assertFalse($containsValidator->validate(null));
        $this->assertFalse($containsValidator->validate(true));
        $this->assertFalse($containsValidator->validate(false));
        $this->assertFalse($containsValidator->validate(4));
        $this->assertFalse($containsValidator->validate(6));
        $this->assertTrue($containsValidator->validate(5));
        $this->assertTrue($containsValidator->validate('foo5'));
        $this->assertTrue($containsValidator->validate('foo5foo'));
        $this->assertTrue($containsValidator->validate('5foo'));
        
        $containsValidator = new stubContainsValidator('foo');
        $this->assertFalse($containsValidator->validate('bar'));
        $this->assertTrue($containsValidator->validate('foo5'));
        $this->assertFalse($containsValidator->validate(null));
        $this->assertFalse($containsValidator->validate(true));
        $this->assertFalse($containsValidator->validate(false));
        $this->assertTrue($containsValidator->validate('foo'));
        $this->assertFalse($containsValidator->validate(4));
        
        $containsValidator = new stubContainsValidator(true);
        $this->assertFalse($containsValidator->validate('foo'));
        $this->assertFalse($containsValidator->validate(null));
        $this->assertTrue($containsValidator->validate(true));
        $this->assertFalse($containsValidator->validate(false));
        $this->assertFalse($containsValidator->validate(4));
        
        $containsValidator = new stubContainsValidator(false);
        $this->assertFalse($containsValidator->validate('foo'));
        $this->assertFalse($containsValidator->validate(null));
        $this->assertFalse($containsValidator->validate(true));
        $this->assertTrue($containsValidator->validate(false));
        $this->assertFalse($containsValidator->validate(4));
        
        $this->equalValidator = new stubContainsValidator(5);
        $this->assertFalse($containsValidator->validate(new stdClass()));
    }
    
    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $containsValidator = new stubContainsValidator(5);
        $this->assertEquals(array('contained' => 5), $containsValidator->getCriteria());
        
        $containsValidator = new stubContainsValidator('foo');
        $this->assertEquals(array('contained' => 'foo'), $containsValidator->getCriteria());
        
        $containsValidator = new stubContainsValidator(true);
        $this->assertEquals(array('contained' => true), $containsValidator->getCriteria());
        
        $containsValidator = new stubContainsValidator(false);
        $this->assertEquals(array('contained' => false), $containsValidator->getCriteria());
    }
}
?>