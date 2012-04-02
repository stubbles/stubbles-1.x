<?php
/**
 * Class to load resources from arbitrary locations.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubResourceLoader.php 3049 2011-02-19 17:51:37Z mikey $
 */
/**
 * Class to load resources from arbitrary locations.
 *
 * @package     stubbles
 * @subpackage  lang
 * @since       1.6.0
 * @Singleton
 */
class stubResourceLoader extends stubBaseObject
{
    /**
     * uri to resource files
     *
     * @var  string
     */
    protected $resourcePath = null;

    /**
     * constructor
     *
     * If no resource path given it will fall back to src/main/resources of the
     * local application.
     *
     * @param  string  $resourcePath  optional  path to resource files on disc
     */
    public function __construct($resourcePath = null)
    {
        if (null == $resourcePath) {
            $this->resourcePath = stubBootstrap::getSourcePath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
        } else {
            $this->resourcePath = $resourcePath;
        }
    }

    /**
     * return all uris for a resource
     *
     * @param   string         $resourceName  the resource to retrieve the uris for
     * @return  array<string>
     */
    public function getResourceUris($resourceName)
    {
        $uris = $this->getStarResourceUris($resourceName);
        if (file_exists($this->resourcePath . $resourceName) === true) {
            $uris[] = $this->getFileResourceUri($resourceName);
        }

        return $uris;
    }

    /**
     * return all uris for a resource which are located in star files
     *
     * @param   string         $resourceName  the resource to retrieve the uris for
     * @return  array<string>
     */
    public function getStarResourceUris($resourceName)
    {
        if (class_exists('StarClassRegistry', false) === true) {
            return StarClassRegistry::getUrisForResource($resourceName);
        }

        return array();
    }

    /**
     * returns the uri for a resource in the real resource path
     *
     * @param   string  $resourceName  the resource to retrieve the path for
     * @return  string
     */
    public function getFileResourceUri($resourceName)
    {
        return $this->resourcePath . $resourceName;
    }
}
?>