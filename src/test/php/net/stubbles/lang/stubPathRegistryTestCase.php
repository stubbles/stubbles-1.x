<?php
/**
 * Tests for net::stubbles::lang::stubPathRegistry.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubPathRegistryTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubPathRegistry');
/**
 * Tests for net::stubbles::lang::stubPathRegistry.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubPathRegistryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * list of original pathes to preserve
     *
     * @var  array<string,string>
     */
    protected $originalPathes = array();

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->originalPathes = array('cache'  => stubPathRegistry::getCachePath(),
                                      'config' => stubPathRegistry::getConfigPath(),
                                      'log'    => stubPathRegistry::getLogPath(),
                                      'page'   => stubPathRegistry::getPagePath()
                                );
        stubPathRegistry::resetCachePath();
        stubPathRegistry::resetConfigPath();
        stubPathRegistry::resetLogPath();
        stubPathRegistry::resetPagePath();
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        stubPathRegistry::setCachePath($this->originalPathes['cache']);
        stubPathRegistry::setConfigPath($this->originalPathes['config']);
        stubPathRegistry::setLogPath($this->originalPathes['log']);
        stubPathRegistry::setPagePath($this->originalPathes['page']);
    }

    /**
     * handling of cache path
     *
     * @test
     */
    public function cachePath()
    {
        $this->assertEquals('bar', stubPathRegistry::getCachePath('bar'));
        stubPathRegistry::setCachePath('foo');
        $this->assertEquals('foo', stubPathRegistry::getCachePath());
        $this->assertEquals('foo', stubPathRegistry::getCachePath('bar'));
    }

    /**
     * retrieving cache path fails if no cache path configured
     *
     * @test
     * @expectedException  stubConfigurationException
     */
    public function retrievingCachePathFailsIfNoCachePathConfigured()
    {
        stubPathRegistry::getCachePath();
    }

    /**
     * handling of config path
     *
     * @test
     */
    public function configPath()
    {
        $this->assertEquals('bar', stubPathRegistry::getConfigPath('bar'));
        stubPathRegistry::setConfigPath('foo');
        $this->assertEquals('foo', stubPathRegistry::getConfigPath());
        $this->assertEquals('foo', stubPathRegistry::getConfigPath('bar'));
    }

    /**
     * retrieving config path fails if no config path configured
     *
     * @test
     * @expectedException  stubConfigurationException
     */
    public function retrievingConfigPathFailsIfNoConfigPathConfigured()
    {
        stubPathRegistry::getConfigPath();
    }

    /**
     * handling of log path
     *
     * @test
     */
    public function logPath()
    {
        $this->assertEquals('bar', stubPathRegistry::getLogPath('bar'));
        stubPathRegistry::setLogPath('foo');
        $this->assertEquals('foo', stubPathRegistry::getLogPath());
        $this->assertEquals('foo', stubPathRegistry::getLogPath('bar'));
    }

    /**
     * retrieving log path fails if no config path configured
     *
     * @test
     * @expectedException  stubConfigurationException
     */
    public function retrievingLogPathFailsIfNoLogPathConfigured()
    {
        stubPathRegistry::getLogPath();
    }

    /**
     * handling of page path
     *
     * @test
     */
    public function pagePath()
    {
        $this->assertEquals('bar', stubPathRegistry::getPagePath('bar'));
        stubPathRegistry::setPagePath('foo');
        $this->assertEquals('foo', stubPathRegistry::getPagePath());
        $this->assertEquals('foo', stubPathRegistry::getPagePath('bar'));
    }

    /**
     * retrieving page path fails if no config path configured
     *
     * @test
     * @expectedException  stubConfigurationException
     */
    public function retrievingPagePathFailsIfNoPagePathConfigured()
    {
        stubPathRegistry::getPagePath();
    }

    /**
     * handling of project path
     *
     * @test
     */
    public function settingProjectPathSetsAllOtherPathes()
    {
        stubPathRegistry::setProjectPath('foo');
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath('bar'));
    }

    /**
     * setting pathes allows overwriting of cache path
     *
     * @test
     */
    public function settingPathesAllowsOverwritingCachePath()
    {
        stubPathRegistry::setPathes(array('project' => 'foo', 'cache' => 'baz'));
        $this->assertEquals('baz', stubPathRegistry::getCachePath());
        $this->assertEquals('baz', stubPathRegistry::getCachePath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath('bar'));
    }

    /**
     * setting pathes allows overwriting of config path
     *
     * @test
     */
    public function settingPathesAllowsOverwritingConfigPath()
    {
        stubPathRegistry::setPathes(array('project' => 'foo', 'config' => 'baz'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath('bar'));
        $this->assertEquals('baz', stubPathRegistry::getConfigPath());
        $this->assertEquals('baz', stubPathRegistry::getConfigPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath('bar'));
    }

    /**
     * setting pathes allows overwriting of log path
     *
     * @test
     */
    public function settingPathesAllowsOverwritingLogPath()
    {
        stubPathRegistry::setPathes(array('project' => 'foo', 'log' => 'baz'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath('bar'));
        $this->assertEquals('baz', stubPathRegistry::getLogPath());
        $this->assertEquals('baz', stubPathRegistry::getLogPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'pages', stubPathRegistry::getPagePath('bar'));
    }

    /**
     * setting pathes allows overwriting of page path
     *
     * @test
     */
    public function settingPathesAllowsOverwritingPagePath()
    {
        stubPathRegistry::setPathes(array('project' => 'foo', 'page' => 'baz'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'cache', stubPathRegistry::getCachePath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'config', stubPathRegistry::getConfigPath('bar'));
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath());
        $this->assertEquals('foo' . DIRECTORY_SEPARATOR . 'log', stubPathRegistry::getLogPath('bar'));
        $this->assertEquals('baz', stubPathRegistry::getPagePath());
        $this->assertEquals('baz', stubPathRegistry::getPagePath('bar'));
    }
}
?>