<?php
/**
 * Factory to create filters.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubDefaultFilterFactory.php 2988 2011-02-11 18:31:24Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubFilterBuilder',
                      'net::stubbles::ipo::request::filter::stubFilterFactory',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Factory to create filters.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @Singleton
 */
class stubDefaultFilterFactory extends stubBaseObject implements stubFilterFactory
{
    /**
     * list of filters to provide
     *
     * @var  array<string,string>
     */
    protected $typeFilter = array();
    /**
     * the request error value factory to be used by the filter
     *
     * @var  stubRequestErrorValueFactory
     */
    protected $rveFactory;

    /**
     * constructor
     *
     * @param  array<string,string>          $typeFilter  list of filters to provide
     * @param  stubRequestValueErrorFactory  $rveFactory  default rve factory
     * @Inject
     * @Named{typeFilter}('net.stubbles.ipo.request.filter.types')
     */
    public function __construct(array $typeFilter, stubRequestValueErrorFactory $rveFactory)
    {
        $this->typeFilter = $typeFilter;
        $this->rveFactory = $rveFactory;
    }

    /**
     * creates a filter for the given type
     *
     * @param   string             $type  type of filter to create
     * @return  stubFilterBuilder
     * @throws  stubIllegalArgumentException
     */
    public function createForType($type)
    {
        if (isset($this->typeFilter[$type]) === false) {
            throw new stubIllegalArgumentException('No filter known for given type ' . $type);
        }
        
        stubClassLoader::load($this->typeFilter[$type]);
        $classname = stubClassLoader::getNonQualifiedClassName($this->typeFilter[$type]);
        return $this->createBuilder(new $classname($this->rveFactory));
    }

    /**
     * create a builder instance for an existing filter
     *
     * @param   stubFilter         $filter
     * @return  stubFilterBuilder
     */
    public function createBuilder(stubFilter $filter)
    {
        return new stubFilterBuilder($filter, $this->rveFactory);
    }
}
?>