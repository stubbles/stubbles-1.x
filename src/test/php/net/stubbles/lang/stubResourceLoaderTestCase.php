<?php
/**
 * Tests for net::stubbles::lang::stubResourceLoader.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubResourceLoaderTestCase.php 3049 2011-02-19 17:51:37Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubResourceLoader');
/**
 * Tests for net::stubbles::lang::stubResourceLoader.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @since       1.6.0
 * @group       lang
 */
class stubResourceLoaderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubResourceLoader
     */
    protected $resourceLoader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->resourceLoader = new stubResourceLoader();
    }

    /**
     * @test
     */
    public function isAnnotatedAsSingleton()
    {
        $this->assertTrue($this->resourceLoader->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * @test
     */
    public function returnsListOfAllResourceUrisForExistingFile()
    {
        $this->assertEquals(array(stubBootstrap::getSourcePath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'ipo/request.ini'),
                            $this->resourceLoader->getResourceUris('ipo/request.ini')
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfAllResourceUrisForNonExistingFile()
    {
        $this->assertEquals(array(),
                            $this->resourceLoader->getResourceUris('doesnot.exist')
        );
    }

    /**
     * As there is no star file in the lib directory the list is empty.
     *
     * @test
     */
    public function returnsListOfAllStarResourceUris()
    {
        $this->assertEquals(array(),
                            $this->resourceLoader->getStarResourceUris('ipo/request.ini')
        );
    }

    /**
     * @test
     */
    public function returnsFullFileNameForFileResourceUri()
    {
        $this->assertEquals(stubBootstrap::getSourcePath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'ipo/request.ini',
                            $this->resourceLoader->getFileResourceUri('ipo/request.ini')
        );
    }
}
?>