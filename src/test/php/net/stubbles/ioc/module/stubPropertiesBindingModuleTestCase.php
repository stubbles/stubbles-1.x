<?php
/**
 * Test for net::stubbles::ioc::module::stubPropertiesBindingModule.
 *
 * @package     stubbles
 * @subpackage  ioc_module_test
 * @version     $Id: stubPropertiesBindingModuleTestCase.php 3220 2011-11-14 15:33:46Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubPropertiesBindingModule');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::ioc::module::stubPropertiesBindingModule.
 *
 * @package     stubbles
 * @subpackage  ioc_module_test
 * @group       ioc
 * @group       ioc_module
 */
class stubPropertiesBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPropertiesBindingModule
     */
    protected $propertiesBindingModule;
    /**
     * project path used throughout the test
     *
     * @var  string
     */
    protected $projectPath;
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }

        $root = vfsStream::setup();
        vfsStream::newFile('config/config.ini')
                 ->withContent("net.stubbles.locale=\"de_DE\"
net.stubbles.number.decimals=4
net.stubbles.webapp.xml.serializeMode=true")
                 ->at($root);
        $this->projectPath             = vfsStream::url('root');
        $this->propertiesBindingModule = stubPropertiesBindingModule::create($this->projectPath);
        $this->injector                = new stubInjector();
    }

    /**
     * returns complete path
     *
     * @param   string  $part
     * @return  string
     */
    protected function getProjectPath($part)
    {
        return $this->projectPath . DIRECTORY_SEPARATOR . $part;
    }

    /**
     * returns constant names and values
     *
     * @return  array<string,array<string>>
     */
    public function getConstants()
    {
        return array('cache'   => array('cache', 'net.stubbles.cache.path', ),
                     'config'  => array('config', 'net.stubbles.config.path'),
                     'data'    => array('data', 'net.stubbles.data.path'),
                     'docroot' => array('docroot', 'net.stubbles.docroot.path'),
                     'log'     => array('log', 'net.stubbles.log.path'),
                     'pages'   => array('pages', 'net.stubbles.page.path')
        );
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function pathesShouldBeBoundAsConstantIfNotChanged($pathPart, $constantName)
    {
        $this->propertiesBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $this->injector->getConstant($constantName)
        );
    }

    /**
     * returns constant names and values
     *
     * @return  array<string,array<string>>
     */
    public function getChangedConstants()
    {
        return array('cache'   => array('cacheChanged', 'net.stubbles.cache.path', ),
                     'config'  => array('configChanged', 'net.stubbles.config.path'),
                     'data'    => array('data', 'net.stubbles.data.path'),
                     'docroot' => array('docroot', 'net.stubbles.docroot.path'),
                     'log'     => array('logChanged', 'net.stubbles.log.path'),
                     'pages'   => array('pagesChanged', 'net.stubbles.page.path')
        );
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getChangedConstants
     */
    public function pathesShouldBeBoundIfChanged($pathPart, $constantName)
    {
        $this->propertiesBindingModule->setCachePath($this->getProjectPath('cacheChanged'))
                                      ->setConfigPath($this->getProjectPath('configChanged'))
                                      ->setLogPath($this->getProjectPath('logChanged'))
                                      ->setPagePath($this->getProjectPath('pagesChanged'));
        $this->propertiesBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $this->injector->getConstant($constantName)
        );
    }

    /**
     * @test
     */
    public function propertiesShouldBeAvailableAsInjections()
    {
        $this->propertiesBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.locale'));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.number.decimals'));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.webapp.xml.serializeMode'));
        $this->assertEquals('de_DE', $this->injector->getConstant('net.stubbles.locale'));
        $this->assertEquals(4, $this->injector->getConstant('net.stubbles.number.decimals'));
        $this->assertEquals(true, (bool) $this->injector->getConstant('net.stubbles.webapp.xml.serializeMode'));
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function withCommonPathes($pathPart, $constantName)
    {
        $this->propertiesBindingModule = stubPropertiesBindingModule::create($this->projectPath . DIRECTORY_SEPARATOR, $this->projectPath);
        $this->propertiesBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertTrue($this->injector->hasConstant($constantName . '.common'));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $this->injector->getConstant($constantName)
        );
        $this->assertEquals($this->getProjectPath($pathPart),
                            $this->injector->getConstant($constantName . '.common')
        );
    }
}
?>