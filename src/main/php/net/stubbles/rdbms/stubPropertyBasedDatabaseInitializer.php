<?php
/**
 * Creates connection data instances based on property files.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubPropertyBasedDatabaseInitializer.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubProperties',
                      'net::stubbles::lang::exceptions::stubConfigurationException',
                      'net::stubbles::rdbms::stubDatabaseConnectionData',
                      'net::stubbles::rdbms::stubDatabaseInitializer'
);
/**
 * Creates connection data instances based on property files.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @since       1.1.0
 */
class stubPropertyBasedDatabaseInitializer extends stubBaseObject implements stubDatabaseInitializer
{
    /**
     * path to config files
     *
     * @var  string
     */
    protected $configPath;
    /**
     * descriptor to be used
     *
     * @var  string
     */
    protected $descriptor   = 'rdbms';
    /**
     * properties for database connections
     *
     * @var  stubProperties
     */
    protected $dbProperties;

    /**
     * constructor
     *
     * @param  string  $configPath
     * @Inject
     * @Named('net.stubbles.config.path')
     */
    public function  __construct($configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * sets the descriptor to be used
     *
     * @param   string                                $descriptor
     * @return  stubPropertyBasedDatabaseInitializer
     * @Inject(optional=true)
     * @Named('net.stubbles.rdbms.descriptor')
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
        return $this;
    }

    /**
     * checks whether connection data for given id exists
     *
     * @param   string  $id
     * @return  bool
     */
    public function hasConnectionData($id)
    {
        if (null === $this->dbProperties) {
            $this->init();
        }

        return $this->dbProperties->hasSection($id);
    }

    /**
     * returns connection data with given id
     *
     * @param   string                      $id
     * @return  stubDatabaseConnectionData
     * @throws  stubConfigurationException
     */
    public function getConnectionData($id)
    {
        if (null === $this->dbProperties) {
            $this->init();
        }

        if ($this->dbProperties->hasSection($id) === false) {
            throw new stubConfigurationException('No connection defined for id ' . $id);
        }

        return stubDatabaseConnectionData::fromArray($this->dbProperties->getSection($id), $id);
    }

    /**
     * initializing method
     *
     * @return  stubPropertyBasedDatabaseInitializer
     */
    protected function init()
    {
        $this->dbProperties = stubProperties::fromFile($this->configPath . '/' . $this->descriptor . '.ini');
        return $this;
    }
}
?>