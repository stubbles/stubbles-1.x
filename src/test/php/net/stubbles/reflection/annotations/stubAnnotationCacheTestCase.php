<?php
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationCache.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @version     $Id: stubAnnotationCacheTestCase.php 3220 2011-11-14 15:33:46Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotationCache',
                      'net::stubbles::reflection::annotations::stubGenericAnnotation',
                      'net::stubbles::reflection::stubReflectionClass'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationCache.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        stubAnnotationCache::flush();
        stubAnnotationCache::refresh();
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('stubAnnotationFactoryTestCase requires vfsStream, see http://vfs.bovigo.org/.');
        }
        
        vfsStream::setup();
        stubAnnotationCache::setCacheFile(vfsStream::url('root/annotations.cache'));
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        stubAnnotationCache::setCacheFile(stubBootstrap::getCurrentProjectPath() . '/cache/annotations.cache');
    }

    /**
     * @test
     */
    public function noAnnotationAddedDoesNotWriteCacheFile()
    {
        stubAnnotationCache::__shutdown();
        $this->assertFalse(file_exists(vfsStream::url('root/annotations.cache')));
    }

    /**
     * @test
     */
    public function addingAnnotationWritesCacheFile()
    {
        $annotation = new stubGenericAnnotation();
        stubAnnotationCache::put(stubAnnotation::TARGET_CLASS, 'foo', 'bar', $annotation);
        stubAnnotationCache::__shutdown();
        $this->assertTrue(file_exists(vfsStream::url('root/annotations.cache')));
        $data = unserialize(file_get_contents(vfsStream::url('root/annotations.cache')));
        $this->assertTrue(isset($data[stubAnnotation::TARGET_CLASS]));
        $this->assertTrue(isset($data[stubAnnotation::TARGET_CLASS]['foo']));
        $this->assertTrue(isset($data[stubAnnotation::TARGET_CLASS]['foo']['bar']));
        $this->assertEquals($annotation, $data[stubAnnotation::TARGET_CLASS]['foo']['bar']->getUnserialized());
    }
}
?>