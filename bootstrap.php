<?php
/**
 * The bootstrap class takes care of providing all necessary data required in the bootstrap process.
 *
 * @author   Frank Kleine <mikey@stubbles.net>
 * @package  stubbles
 * @version  $Id: bootstrap.php 3298 2011-12-27 15:17:10Z mikey $
 */
/**
 * The bootstrap class takes care of providing all necessary data required in the bootstrap process.
 *
 * @package  stubbles
 */
class stubBootstrap
{
    /**
     * path to php source files
     *
     * @var  string
     */
    private static $sourcePath = null;
    /**
     * current project
     *
     * @var  string
     */
    private static $project    = null;

    /**
     * returns path where common files are stored
     *
     * @return  string
     */
    public static function getCommonPath()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'projects' . DIRECTORY_SEPARATOR . 'common';
    }

    /**
     * returns name of current project
     *
     * @return  string
     * @since   1.7.0
     */
    public static function getCurrentProjectPath()
    {
        return self::getRootPath() . '/projects/' . self::$project;
    }

    /**
     * returns root path of the installation
     *
     * @return  string
     */
    public static function getRootPath()
    {
        return dirname(__FILE__);
    }

    /**
     * returns path to php source files

     * @return  string
     */
    public static function getSourcePath()
    {
        if (null == self::$sourcePath) {
            self::$sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'main';
        }

        return self::$sourcePath;
    }

    /**
     * loads stubbles core classes and initializes pathes
     *
     * @param  string  $projectPath  path to project
     * @param  string  $classFile    optional  defaults to stubbles.php
     */
    public static function init($projectPath, $classFile = 'stubbles.php')
    {
        if (null === self::$project) {
            $lastString = strstr($projectPath, 'projects' . DIRECTORY_SEPARATOR);
            if (false === $lastString) {
                $lastString = strstr($projectPath, 'projects/');
            }

            self::$project = substr($lastString, 9);
        }

        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $classFile;
        stubClassLoader::load('net::stubbles::lang::stubPathRegistry');
        stubPathRegistry::setProjectPath($projectPath);
    }

    /**
     * run an application
     *
     * @param  string  $appClass   full qualified class name of the app to run
     * @param  string  $project    path to project files
     * @param  string  $classFile  optional  defaults to stubbles.php
     * @since  1.7.0
     */
    public static function run($appClass, $project, $classFile = 'stubbles.php')
    {
        self::$project = $project;
        self::init(array('project' => self::getCurrentProjectPath()), $classFile);
        stubClassLoader::load('net::stubbles::ioc::stubApp');
        stubApp::createInstance($appClass, self::getCurrentProjectPath())
               ->run();
    }
}
?>