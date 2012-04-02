<?php
/**
 * Registry for the different pathes used throughout an application.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubPathRegistry.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubConfigurationException');
/**
 * Registry for the different pathes used throughout an application.
 *
 * @package     stubbles
 * @subpackage  lang
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubPathRegistry extends stubBaseObject
{
    /**
     * list of pathes
     *
     * @var  array<string,string>
     */
    protected static $pathes = array();

    /**
     * sets all pathes
     *
     * @param  array<string,string>  $pathes
     */
    public static function setPathes(array $pathes)
    {
        // set general project path if available
        if (isset($pathes['project']) === true) {
            self::setProjectPath($pathes['project']);
        }
        
        // set other pathes (overwrite default of project path if necessary)
        if (isset($pathes['cache']) === true) {
            self::setCachePath($pathes['cache']);
        }
        
        if (isset($pathes['config']) === true) {
            self::setConfigPath($pathes['config']);
        }
        
        if (isset($pathes['log']) === true) {
            self::setLogPath($pathes['log']);
        }
        
        if (isset($pathes['page']) === true) {
            self::setPagePath($pathes['page']);
        }
    }

    /**
     * sets the project path
     *
     * Setting the project path will set all other pathes to their default
     * location within a project.
     *
     * @param  string  $projectPath
     */
    public static function setProjectPath($projectPath)
    {
        self::setCachePath($projectPath . DIRECTORY_SEPARATOR . 'cache');
        self::setConfigPath($projectPath . DIRECTORY_SEPARATOR . 'config');
        self::setLogPath($projectPath . DIRECTORY_SEPARATOR . 'log');
        self::setPagePath($projectPath . DIRECTORY_SEPARATOR . 'pages');
    }

    /**
     * sets path where cache files should be stored
     *
     * @param  string  $cachePath
     */
    public static function setCachePath($cachePath)
    {
        self::$pathes['cache'] = $cachePath;
    }

    /**
     * resets cache path
     */
    public static function resetCachePath()
    {
        unset(self::$pathes['cache']);
    }

    /**
     * returns path where cache files should be stored
     *
     * @param   string  $default  optional  return value if path not set
     * @return  string
     */
    public static function getCachePath($default = null)
    {
        return self::retrievePath('cache', $default);
    }

    /**
     * sets path where config files are stored
     *
     * @param  string  $configPath
     */
    public static function setConfigPath($configPath)
    {
        self::$pathes['config'] = $configPath;
    }

    /**
     * resets config path
     */
    public static function resetConfigPath()
    {
        unset(self::$pathes['config']);
    }

    /**
     * returns path where config files are stored
     *
     * @param   string  $default  optional  return value if path not set
     * @return  string
     */
    public static function getConfigPath($default = null)
    {
        return self::retrievePath('config', $default);
    }

    /**
     * sets path where log files should be stored
     *
     * @param  string  $logPath
     */
    public static function setLogPath($logPath)
    {
        self::$pathes['log'] = $logPath;
    }

    /**
     * resets log path
     */
    public static function resetLogPath()
    {
        unset(self::$pathes['log']);
    }

    /**
     * returns path where log files should be stored
     *
     * @param   string  $default  optional  return value if path not set
     * @return  string
     */
    public static function getLogPath($default = null)
    {
        return self::retrievePath('log', $default);
    }

    /**
     * sets path where page data is stored
     *
     * @param  string  $pagePath
     */
    public static function setPagePath($pagePath)
    {
        self::$pathes['page'] = $pagePath;
    }

    /**
     * resets page path
     */
    public static function resetPagePath()
    {
        unset(self::$pathes['page']);
    }

    /**
     * returns path where page data is stored
     *
     * @param   string  $default  optional  return value if path not set
     * @return  string
     */
    public static function getPagePath($default = null)
    {
        return self::retrievePath('page', $default);
    }

    /**
     * helper method to retrieve the path with given key
     *
     * @param   string  $key
     * @param   string  $default  return value if path not set
     * @return  string
     * @throws  stubConfigurationException  if path not set and no default value given
     */
    protected static function retrievePath($key, $default)
    {
        if (isset(self::$pathes[$key]) === false) {
            if (null == $default) {
                throw new stubConfigurationException($key . ' path not set.');
            }
            
            return $default;
        }
        
        return self::$pathes[$key];
    }
}
?>