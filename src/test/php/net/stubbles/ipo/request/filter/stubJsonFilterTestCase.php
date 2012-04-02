<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubJsonFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubJsonFilterTestCase.php 2251 2009-06-23 16:23:37Z richi $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubJsonFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubJsonFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubJsonFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Object under test.
     *
     * @var  stubJsonFilter
     */
    public $objUnderTest;

    public function setUp()
    {
        $this->objUnderTest = new stubJsonFilter();
    }

    /**
     * Assures valid JSON filtering.
     *
     * @test
     */
    public function filterValidWithJson()
    {
        $this->assertEquals(array(1), $this->objUnderTest->execute('[1]'));

        $obj = new stdClass();
        $obj->id = "abc";
        $this->assertEquals($obj, $this->objUnderTest->execute('{"id":"abc"}'));
    }

    /**
     * Assures valid JSON-RPC filtering.
     *
     * @test
     */
    public function filterValidWithJsonRpc()
    {
        $phpJsonObj = new stdClass();
        $phpJsonObj->method = 'add';
        $phpJsonObj->params = array(1, 2);
        $phpJsonObj->id = 1;

        $this->assertEquals($phpJsonObj, $this->objUnderTest->execute('{"method":"add","params":[1,2],"id":1}'));
    }

    /**
     * Assures that invalid input (too big) throws an exception.
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function filterInvalidInputTooBig()
    {
        $this->objUnderTest->execute(str_repeat("a", 20001));
    }

    /**
     * Assures that invalid input (bad syntax) throws an exception.
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function filterInvalidJson()
    {
        $this->objUnderTest->execute('{foo]');
    }

    /**
     * Assures that invalid input (bad syntax) throws an exception.
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function filterInvalidJsonWhichSlipsThroughNaiveJsonSyntaxCheck()
    {
        $this->objUnderTest->execute('{"foo":"bar","foo","bar"}');
    }

    /**
     * Assures that invalid input (bad syntax) throws an exception.
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function filterInvalidJsonAlthoughPhpDecodesIt()
    {
        $this->objUnderTest->execute('"foo"');
    }

    /**
     * Assures that invalid input (bad syntax) throws an exception.
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function filterInvalidJsonNull()
    {
        $this->objUnderTest->execute(null);
    }
}
?>