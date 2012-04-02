<?php
/**
 * Class for creating request value error codes from an ini configuration file.
 * 
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubRequestValueErrorPropertiesFactory.php 3049 2011-02-19 17:51:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::lang::stubProperties',
                      'net::stubbles::lang::stubResourceLoader',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Class for creating request value error codes from an ini configuration file.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 * @Singleton
 */
class stubRequestValueErrorPropertiesFactory extends stubBaseObject implements stubRequestValueErrorFactory
{
    /**
     * loader for master.xsl resource file
     *
     * @var  stubResourceLoader
     */
    protected $resourceLoader;
    /**
     * parsed properties
     *
     * @var  stubProperties
     */
    private $properties;

    /**
     * constructor
     *
     * @param  stubResourceLoader  $resourceLoader
     * @Inject
     */
    public function __construct(stubResourceLoader $resourceLoader)
    {
        $this->resourceLoader = $resourceLoader;
    }

    /**
     * creates the RequestValueError with the id from the given source
     *
     * @param   string                 $id      id of RequestValueError to create
     * @return  stubRequestValueError
     * @throws  stubIllegalArgumentException
     */
    public function create($id)
    {
        $properties = $this->parseProperties();
        if ($properties->hasSection($id) === false) {
            throw new stubIllegalArgumentException('RequestValueError with id ' . $id . ' does not exist.');
        }

        $messages = $properties->getSection($id);
        if (isset($messages['valueKeys']) === true) {
            unset($messages['valueKeys']);
        }

        return new stubRequestValueError($id, $messages, $properties->parseArray($id, 'valueKeys', array()));
    }

    /**
     * parses properties from property files
     *
     * @return  stubProperties
     */
    protected function parseProperties()
    {
        if (null === $this->properties) {
            $this->properties = new stubProperties();
            foreach ($this->resourceLoader->getResourceUris('ipo/request.ini') as $resourceUri) {
                $this->properties = $this->properties->merge(stubProperties::fromFile($resourceUri));
            }
        }

        return $this->properties;
    }
}
?>