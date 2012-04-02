<?php
/**
 * Test for net::stubbles::ipo::request::stubMockFilteringRequestValue.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubMockFilteringRequestValueTestCase.php 2686 2010-08-24 20:15:24Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubMockFilteringRequestValue');
/**
 * Test for net::stubbles::ipo::request::stubMockFilteringRequestValue.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @since       1.3.0
 * @group       ipo
 * @group       ipo_request
 */
class stubMockFilteringRequestValueTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function constructionWorks()
    {
        $mockFilteringRequestValue = new stubMockFilteringRequestValue('foo', 'bar');
        $this->assertEquals('foo', $mockFilteringRequestValue->getName('foo'));
        $this->assertEquals('bar', $mockFilteringRequestValue->unsecure());
    }
}
?>