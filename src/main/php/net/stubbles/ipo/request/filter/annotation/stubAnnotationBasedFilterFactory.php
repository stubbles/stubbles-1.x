<?php
/**
 * Base class to create filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::request::filter::stubFilterFactory',
                      'net::stubbles::ipo::request::filter::annotation::stubFilterAnnotationReader',
                      'net::stubbles::lang::exceptions::stubConfigurationException'
);
/**
 * Base class to create filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubAnnotationBasedFilterFactory extends stubBaseObject
{
    /**
     * factory to create filters with
     *
     * @var  stubFilterFactory
     */
    protected $filterFactory;
    /**
     * injector to create filter annotation reader instances with
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * map of annotation names to filter annotation reader type names
     *
     * @var  array<string,string>
     */
    protected $filterAnnotationReader;

    /**
     * constructor
     *
     * @param  stubFilterFactory     $filterFactory            factory to create filter instances with
     * @param  stubInjector          $injector                 injector to create filter annotation reader instances with
     * @param  array<string,string>  $filterAnnotationReader   map of annotation names to filter annotation reader type names
     * @Inject
     * @Named{filterAnnotationReader}('net.stubbles.ipo.request.filter.annotationreader')
     */
    public function __construct(stubFilterFactory $filterFactory, stubInjector $injector, array $filterAnnotationReader)
    {
        $this->filterFactory          = $filterFactory;
        $this->injector               = $injector;
        $this->filterAnnotationReader = $filterAnnotationReader;
    }

    /**
     * creates filter based on given annotation
     *
     * @param   stubAnnotation $annotation
     * @return  stubFilter
     * @throws  stubConfigurationException
     */
    public function createForAnnotation(stubAnnotation $annotation)
    {
        $type = $annotation->getAnnotationName();
        if (isset($this->filterAnnotationReader[$type]) === false) {
            throw new stubConfigurationException('No filter annotation reader known for given annotation type ' . $type);
        }

        $filterAnnotationReader = $this->injector->getInstance($this->filterAnnotationReader[$type]);
        if (($filterAnnotationReader instanceof stubFilterAnnotationReader) === false) {
            throw new stubConfigurationException('Configured annotation reader for annotation type ' . $type . ' is not an instance of net::stubbles::ipo::request::filter::annotation::stubFilterAnnotationReader');
        }

        return $filterAnnotationReader->createFilter($this->filterFactory, $annotation);
    }
}
?>