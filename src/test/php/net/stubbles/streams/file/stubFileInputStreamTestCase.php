<?php
/**
 * Test for net::stubbles::streams::file::stubFileInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubFileInputStreamTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::file::stubFileInputStream',
                      'net::stubbles::streams::memory::stubMemoryStreamWrapper'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::streams::file::stubFileInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubFileInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(vfsStream::newDirectory('home'));
        vfsStream::newFile('test.txt')->at(vfsStreamWrapper::getRoot())->withContent('foo');
    }

    /**
     * construct with string as argument
     *
     * @test
     */
    public function constructWithString()
    {
        $fileInputStream = new stubFileInputStream(vfsStream::url('home/test.txt'));
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * construct with string as argument fails throws io exception
     *
     * @test
     * @expectedException  stubIOException
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        $fileInputStream = new stubFileInputStream('memory://doesNotExist', 'r');
    }

    /**
     * construct with resource as argument
     *
     * @test
     */
    public function constructWithResource()
    {
        $fileInputStream = new stubFileInputStream(fopen(vfsStream::url('home/test.txt'), 'rb'));
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * construct with an illegal resource as argument
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructWithIllegalResource()
    {
        if (extension_loaded('gd') === false) {
            $this->markTestSkipped('No known extension with other resource type available.');
        }
        
        $fileInputStream = new stubFileInputStream(imagecreate(2, 2));
    }

    /**
     * construct with an illegal argument
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructWithIllegalArgument()
    {
        $fileInputStream = new stubFileInputStream(0);
    }

    /**
     * seek in file stream
     *
     * @test
     */
    public function seek_SET()
    {
        $fileInputStream = new stubFileInputStream(vfsStream::url('home/test.txt'));
        $this->assertEquals(0, $fileInputStream->tell());
        $fileInputStream->seek(2);
        $this->assertEquals(2, $fileInputStream->tell());
        $this->assertEquals('o', $fileInputStream->readLine());
        $fileInputStream->seek(0, stubSeekable::SET);
        $this->assertEquals(0, $fileInputStream->tell());
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * seek in file stream
     *
     * @test
     */
    public function seek_CURRENT()
    {
        $fileInputStream = new stubFileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(1, stubSeekable::CURRENT);
        $this->assertEquals(1, $fileInputStream->tell());
        $this->assertEquals('oo', $fileInputStream->readLine());
    }

    /**
     * seek in file stream
     *
     * @test
     */
    public function seek_END()
    {
        $fileInputStream = new stubFileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(-2, stubSeekable::END);
        $this->assertEquals(1, $fileInputStream->tell());
        $this->assertEquals('oo', $fileInputStream->readLine());
    }

    /**
     * seek in file stream
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function seek_onClosedStreamFails()
    {
        $fileInputStream = new stubFileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        $fileInputStream->seek(3);
    }

    /**
     * seek in file stream
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function tell_onClosedStreamFails()
    {
        $fileInputStream = new stubFileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        $fileInputStream->tell();
    }
}
?>