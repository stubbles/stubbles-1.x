<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubEqualValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubEqualValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubEqualValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubEqualValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubEqualValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that construction works correct
     *
     * @test
     */
    public function construction()
    {
        $equalValidator = new stubEqualValidator(5);
        $equalValidator = new stubEqualValidator('foo');
        $equalValidator = new stubEqualValidator(true);
        $equalValidator = new stubEqualValidator(false);
        $equalValidator = new stubEqualValidator(null);
    }

    /**
     * assure that construction works correct
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructionWithObject()
    {
        $equalValidator = new stubEqualValidator(new stdClass());
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $equalValidator = new stubEqualValidator(5);
        $this->assertFalse($equalValidator->validate('foo'));
        $this->assertFalse($equalValidator->validate(null));
        $this->assertFalse($equalValidator->validate(true));
        $this->assertFalse($equalValidator->validate(false));
        $this->assertFalse($equalValidator->validate(4));
        $this->assertFalse($equalValidator->validate(6));
        $this->assertTrue($equalValidator->validate(5));
        
        $equalValidator = new stubEqualValidator('foo');
        $this->assertFalse($equalValidator->validate('bar'));
        $this->assertFalse($equalValidator->validate('foo5'));
        $this->assertFalse($equalValidator->validate(null));
        $this->assertFalse($equalValidator->validate(true));
        $this->assertFalse($equalValidator->validate(false));
        $this->assertTrue($equalValidator->validate('foo'));
        $this->assertFalse($equalValidator->validate(4));
        
        $equalValidator = new stubEqualValidator(true);
        $this->assertFalse($equalValidator->validate('foo'));
        $this->assertFalse($equalValidator->validate(null));
        $this->assertTrue($equalValidator->validate(true));
        $this->assertFalse($equalValidator->validate(false));
        $this->assertFalse($equalValidator->validate(4));
        
        $equalValidator = new stubEqualValidator(false);
        $this->assertFalse($equalValidator->validate('foo'));
        $this->assertFalse($equalValidator->validate(null));
        $this->assertFalse($equalValidator->validate(true));
        $this->assertTrue($equalValidator->validate(false));
        $this->assertFalse($equalValidator->validate(4));
        
        $equalValidator = new stubEqualValidator(null);
        $this->assertFalse($equalValidator->validate('foo'));
        $this->assertTrue($equalValidator->validate(null));
        $this->assertFalse($equalValidator->validate(true));
        $this->assertFalse($equalValidator->validate(false));
        $this->assertFalse($equalValidator->validate(4));
        
        $this->equalValidator = new stubEqualValidator(5);
        $this->assertFalse($equalValidator->validate(new stdClass()));
    }
    
    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $equalValidator = new stubEqualValidator(5);
        $this->assertEquals(array('expected' => 5), $equalValidator->getCriteria());
        
        $equalValidator = new stubEqualValidator('foo');
        $this->assertEquals(array('expected' => 'foo'), $equalValidator->getCriteria());
        
        $equalValidator = new stubEqualValidator(true);
        $this->assertEquals(array('expected' => true), $equalValidator->getCriteria());
        
        $equalValidator = new stubEqualValidator(false);
        $this->assertEquals(array('expected' => false), $equalValidator->getCriteria());
        
        $equalValidator = new stubEqualValidator(null);
        $this->assertEquals(array('expected' => null), $equalValidator->getCriteria());
    }
}
?>