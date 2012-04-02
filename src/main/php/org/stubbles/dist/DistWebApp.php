<?php
/**
 * Example web app for the dist project.
 *
 * @package     stubbles
 * @subpackage  dist
 * @version     $Id: DistWebApp.php 3268 2011-12-05 15:07:35Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::stubWebApp');
/**
 * Example web app for the dist project.
 *
 * @package     stubbles
 * @subpackage  dist
 * @since       1.7.0
 */
class DistWebApp extends stubWebApp
{
    /**
     * returns list of bindings required for this web app
     *
     * @param   string                           $projectPath
     * @return  array<string|stubBindingModule>
     */
    public static function __bindings($projectPath)
    {
        return array(self::createModeBindingModule(),
                     self::createPropertiesBindingModule($projectPath),
                     self::createIpoBindingModule(),
                     self::createLogBindingModule(),
                     self::createWebAppBindingModule(self::createXmlUriConfigurator()
                                                         ->provideJsonRpc()
                                                         ->provideRss()
                                                         ->addShowLastXmlPreInterceptor()
                                                         ->addEtagPostInterceptor()
                           )

        );
    }
}
?>
