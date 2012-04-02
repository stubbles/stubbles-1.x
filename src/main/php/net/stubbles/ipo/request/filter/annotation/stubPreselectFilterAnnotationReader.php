<?php
/**
 * Class to create mail filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader',
                      'net::stubbles::ipo::request::validator::stubPreSelectValidator'
);
/**
 * Class to create mail filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubPreselectFilterAnnotationReader extends stubAbstractFilterAnnotationReader
{
    /**
     * injector to create class which delivers the list of allowed preselect values
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @Inject
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilterBuilder
     */
    protected function doCreateFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation)
    {
        $sourceDataClass  = $filterAnnotation->getSourceDataClass();
        /* @var  $sourceDataClass  stubBaseReflectionClass */
        $sourceDataMethod = $sourceDataClass->getMethod($this->getSourceDataMethodName($filterAnnotation));
        /* @var  $sourceDataMethod  stubReflectionMethod */
        if ($sourceDataMethod->isStatic() === false) {
            $sourceDataProvider = $this->injector->getInstance($sourceDataClass->getFullQualifiedClassName());
        } else {
            $sourceDataProvider = null;
        }

        return $filterFactory->createForType('string')
                             ->validatedBy(new stubPreSelectValidator($sourceDataMethod->invoke($sourceDataProvider)),
                                           $filterAnnotation->getErrorId()
                               );
    }

    /**
     * returns name of source data method to be called
     *
     * @param   stubAnnotation  $filterAnnotation
     * @return  string
     */
    protected function getSourceDataMethodName(stubAnnotation $filterAnnotation)
    {
        if ($filterAnnotation->hasSourceDataMethod() === true) {
            return $filterAnnotation->getSourceDataMethod();
        }

        return 'getData';
    }
}
?>