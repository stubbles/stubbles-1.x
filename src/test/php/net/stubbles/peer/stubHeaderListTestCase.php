<?php
/**
 * Test for net::stubbles::peer::stubHeaderList.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @version     $Id: stubHeaderListTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubHeaderList');
/**
 * Test for net::stubbles::peer::stubHeaderList.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @group       peer
 */
class stubHeaderListTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubHeaderList
     */
    protected $headerList;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->headerList = new stubHeaderList();
    }

    /**
     * assure that the headerlist is established correct
     *
     * @test
     */
    public function establish()
    {
        $this->assertEquals(0, $this->headerList->size());
    }

    /**
     * assure that the headerlist gets values and returns them correct
     *
     * @test
     */
    public function put()
    {    
        $this->assertSame($this->headerList, $this->headerList->put('Binford', 6100));
        $this->assertEquals(1, $this->headerList->size());
        $this->assertTrue($this->headerList->containsKey('Binford'));
        $this->assertEquals('6100', $this->headerList->get('Binford'));
    }

    /**
     * assure that the headerlist throws an IllegalArgumentException when putting an array as value
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function putArray()
    {
        $this->headerList->put('Binford', array(6100));
    }

    /**
     * assure that the headerlist throws an IllegalArgumentException when putting an object as value
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function putObject()
    {
        $this->headerList->put('Binford', new stdClass());
    }

    /**
     * assure that the headerlist throws an IllegalArgumentException when putting a non-string as key
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function putUnusualKey()
    {
        $this->headerList->put(6100, new stdClass());
    }

    /**
     * assure that the headerlist removes values correct
     *
     * @test
     */
    public function remove()
    {
        $this->assertSame($this->headerList, $this->headerList->put('Binford', '6100'));
        $this->assertSame($this->headerList, $this->headerList->remove('Binford'));
        $this->assertFalse($this->headerList->containsKey('Binford'));
    }

    /**
     * assure that the headerlist puts predefined keys and values correctly
     *
     * @test
     */
    public function putX()
    {
        $this->assertSame($this->headerList, $this->headerList->putUserAgent('Binford 6100'));
        $this->assertSame($this->headerList, $this->headerList->putReferer('Home Improvement'));
        $this->assertSame($this->headerList, $this->headerList->putCookie(array('testcookie1' => 'testvalue1 %&')));
        $this->assertSame($this->headerList, $this->headerList->putAuthorization('user', 'pass'));
        $time = time();
        $this->assertSame($this->headerList, $this->headerList->putDate($time));
        $this->assertSame($this->headerList, $this->headerList->enablePower());
        
        $this->assertTrue($this->headerList->containsKey('User-Agent'));
        $this->assertEquals('Binford 6100', $this->headerList->get('User-Agent'));
        $this->assertTrue($this->headerList->containsKey('Referer'));
        $this->assertEquals('Home Improvement', $this->headerList->get('Referer'));
        $this->assertTrue($this->headerList->containsKey('Cookie'));
        $this->assertEquals('testcookie1=' . urlencode('testvalue1 %&') . ';', $this->headerList->get('Cookie'));
        $this->assertTrue($this->headerList->containsKey('Authorization'));
        $this->assertEquals('BASIC ' . base64_encode('user:pass'), $this->headerList->get('Authorization'));
        $this->assertTrue($this->headerList->containsKey('Date'));
        $this->assertEquals(gmdate('D, d M Y H:i:s', $time) . ' GMT', $this->headerList->get('Date'));
        $this->assertTrue($this->headerList->containsKey('X-Binford'));
        $this->assertEquals('More power!', $this->headerList->get('X-Binford'));
        
        $this->assertEquals(6, $this->headerList->size());
    }

    /**
     * assert default values are returned if no header with given name is set
     *
     * @test
     */
    public function get()
    {
        $this->assertNull($this->headerList->get('foo'));
        $this->assertEquals('bar', $this->headerList->get('foo', 'bar'));
        $this->headerList->put('foo', 'bar');
        $this->assertEquals('bar', $this->headerList->get('foo'));
        $this->assertEquals('bar', $this->headerList->get('foo', 'bar'));
    }

    /**
     * assure that the headerlist clears correctly
     *
     * @test
     */
    public function clear()
    {
        $this->assertSame($this->headerList,
                          $this->headerList->putUserAgent('Binford 6100')
                                            ->putReferer('Home Improvement')
                                            ->putCookie(array('testcookie1' => 'testvalue1 %&'))
                                            ->putAuthorization('user', 'pass')
                                            ->putDate(time())
                                            ->enablePower()
        );
        
        $this->assertEquals(6, $this->headerList->size());
        $this->headerList->clear();
        $this->assertEquals(0, $this->headerList->size());
    }

    /**
     * assure that the headerlist parses a string of headers correctly
     *
     * @test
     */
    public function parseString()
    {
        $headerlist = stubHeaderList::fromString("Binford: 6100\r\nX-Power: More power!");
        $this->assertInstanceOf('stubHeaderList', $headerlist);
        $this->assertTrue($headerlist->containsKey('Binford'));
        $this->assertTrue($headerlist->containsKey('X-Power'));
        $this->assertEquals(2, $headerlist->size());
        $this->assertEquals('6100', $headerlist->get('Binford'));
        $this->assertEquals('More power!', $headerlist->get('X-Power'));
    }

    /**
     * assure that an empty headerlist generates an empty iterator
     *
     * @test
     */
    public function emptyIterator()
    {
        $counter = 0;
        foreach ($this->headerList as $key => $value) {
            $counter++;
        }
        
        $this->assertEquals(0, $counter);
    }

    /**
     * assure that a filled headerlist generates a non-empty iterator
     *
     * @test
     */
    public function nonEmptyIterator()
    {
        $counter = 0;
        $this->headerList->putUserAgent('Binford 6100');
        $this->headerList->put('X-TV', 'Home Improvement');
        foreach ($this->headerList as $key => $value) {
            $counter++;
        }
        
        $this->assertEquals(2, $counter);
    }
}
?>