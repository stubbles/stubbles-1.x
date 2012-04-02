<?php
/**
 * Module to read properties from a file and bind them.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 * @version     $Id: stubPropertiesBindingModule.php 2493 2010-01-26 20:38:06Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule');
/**
 * Module to read properties from a file and bind them.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 */
class stubPropertiesBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * project path related data
     */
    const PROJECT     = 'project';
    /**
     * common path related data
     */
    const COMMON      = 'common';
    /**
     * list of pathes
     *
     * @var  array<string,array<string,string>>
     */
    protected $pathes = array(self::PROJECT => array(),
                              self::COMMON  => array()
                        );

    /**
     * constructor
     *
     * @param  string  $projectPath        path to project
     * @param  string  $commonProjectPath  optional  path to common project
     */
    public function __construct($projectPath, $commonProjectPath = null)
    {
        $this->setProjectPath($projectPath);
        if (null == $commonProjectPath) {
            $commonProjectPath = realpath($projectPath . '/../common');
            if (false === $commonProjectPath) {
                return;
            }
        }

        $this->setProjectPath($commonProjectPath, self::COMMON);
    }

    /**
     * static constructor
     *
     * @param   string                       $projectPath        path to project
     * @param   string                       $commonProjectPath  optional  path to common project
     * @return  stubPropertiesBindingModule
     */
    public static function create($projectPath, $commonProjectPath = null)
    {
        return new self($projectPath, $commonProjectPath);
    }

    /**
     * sets project path
     *
     * @param   string                       $projectPath
     * @param   string                       $project
     * @return  stubPropertiesBindingModule
     * @since   1.1.0
     */
    public function setProjectPath($projectPath, $project = self::PROJECT)
    {
        if (substr($projectPath, -1) !== DIRECTORY_SEPARATOR) {
            $projectPath .= DIRECTORY_SEPARATOR;
        }

        return $this->setCachePath($projectPath . 'cache', $project)
                    ->setConfigPath($projectPath . 'config', $project)
                    ->setDataPath($projectPath . 'data', $project)
                    ->setDocrootPath($projectPath . 'docroot', $project)
                    ->setLogPath($projectPath . 'log', $project)
                    ->setPagePath($projectPath . 'pages', $project);
    }

    /**
     * sets cache path
     *
     * @param   string                       $cachePath
     * @param   string                       $project    type of project: project or common
     * @return  stubPropertiesBindingModule
     */
    public function setCachePath($cachePath, $project = self::PROJECT)
    {
        $this->pathes[$project]['cache'] = $cachePath;
        return $this;
    }

    /**
     * sets config path
     *
     * @param   string                       $configPath
     * @return  stubPropertiesBindingModule
     */
    public function setConfigPath($configPath, $project = self::PROJECT)
    {
        $this->pathes[$project]['config'] = $configPath;
        return $this;
    }

    /**
     * sets data path
     *
     * @param   string                       $dataPath
     * @return  stubPropertiesBindingModule
     * @since   1.1.0
     */
    public function setDataPath($dataPath, $project = self::PROJECT)
    {
        $this->pathes[$project]['data'] = $dataPath;
        return $this;
    }

    /**
     * sets docroot path
     *
     * @param   string                       $docrootPath
     * @return  stubPropertiesBindingModule
     * @since   1.1.0
     */
    public function setDocrootPath($docrootPath, $project = self::PROJECT)
    {
        $this->pathes[$project]['docroot'] = $docrootPath;
        return $this;
    }

    /**
     * sets log path
     *
     * @param   string                       $logPath
     * @return  stubPropertiesBindingModule
     */
    public function setLogPath($logPath, $project = self::PROJECT)
    {
        $this->pathes[$project]['log'] = $logPath;
        return $this;
    }

    /**
     * sets page path
     *
     * @param   string                       $pagePath
     * @return  stubPropertiesBindingModule
     */
    public function setPagePath($pagePath, $project = self::PROJECT)
    {
        $this->pathes[$project]['page'] = $pagePath;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $this->bindPathes($binder, self::PROJECT)
             ->bindPathes($binder, self::COMMON);
        foreach ($this->getProperties() as $key => $value) {
            $binder->bindConstant()
                   ->named($key)
                   ->to($value);
        }
    }

    /**
     * helper method for binding the pathes
     *
     * @param   stubBinder                   $binder
     * @param   string                       $type
     * @return  stubPropertiesBindingModule
     * @since   1.1.0
     */
    protected function bindPathes(stubBinder $binder, $type)
    {
        foreach ($this->pathes[$type] as $key => $value) {
            $name = 'net.stubbles.' . $key . '.path';
            if (self::COMMON === $type) {
                $name .= '.common';
            }

            $binder->bindConstant()
                   ->named($name)
                   ->to($value);
        }

        return $this;
    }

    /**
     * returns list of properties
     *
     * @return  array<string,scalar>
     */
    protected function getProperties()
    {
        if (file_exists($this->pathes[self::PROJECT]['config'] . DIRECTORY_SEPARATOR . 'config.ini') === false) {
            return array();
        }
        
        return parse_ini_file($this->pathes[self::PROJECT]['config'] . DIRECTORY_SEPARATOR . 'config.ini');
    }
}
?>