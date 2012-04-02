<?php
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker
 * @version     $Id: stubRequestBroker.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::request::broker::stubRequestBrokerException',
                      'net::stubbles::ipo::request::broker::stubRequestBrokerMethodPropertyMatcher',
                      'net::stubbles::ipo::request::filter::annotation::stubAnnotationBasedFilterFactory',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker
 * @Singleton
 */
class stubRequestBroker extends stubBaseObject
{
    /**
     * the matcher to be used for methods and properties
     *
     * @var  stubRequestBrokerMethodPropertyMatcher
     */
    protected static $methodAndPropertyMatcher;
    /**
     * factory to create filters with
     *
     * @var  stubAnnotationBasedFilterFactory
     */
    protected $annotationBasedFilterFactory;

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$methodAndPropertyMatcher = new stubRequestBrokerMethodPropertyMatcher();
    }

    /**
     * constructor
     *
     * @param  stubAnnotationBasedFilterFactory  $annotationBasedFilterFactory
     * @Inject
     */
    public function __construct(stubAnnotationBasedFilterFactory $annotationBasedFilterFactory)
    {
        $this->annotationBasedFilterFactory = $annotationBasedFilterFactory;
    }

    /**
     * does the real action
     *
     * @param   stubRequest               $request
     * @param   object                    $object           the object instance to fill with values
     * @param   string                    $prefix           optional  prefix for access to request values
     * @param   array<string,stubFilter>  $overruleFilters  optional  list of filters to overrule annotated filters with
     * @throws  stubIllegalArgumentException
     */
    public function process(stubRequest $request, $object, $prefix = '', array $overruleFilters = array())
    {
        if ($object instanceof stubObject) {
            $refClass = $object->getClass();
        } elseif (is_object($object) === true) {
            $refClass = new stubReflectionClass(get_class($object));
        } else {
            throw new stubIllegalArgumentException('Parameter object must a concrete object instance.');
        }
        
        $this->processProperties($request, $refClass, $object, $prefix, $overruleFilters);
        $this->processMethods($request, $refClass, $object, $prefix, $overruleFilters);
    }

    /**
     * fills properties with values from request
     *
     * @param  stubRequest               $request
     * @param  stubBaseReflectionClass   $refClass
     * @param  object                    $object
     * @param  string                    $prefix
     * @param  array<string,stubFilter>  $overruleFilters
     */
    protected function processProperties(stubRequest $request,
                                         stubBaseReflectionClass $refClass,
                                         $object,
                                         $prefix,
                                         array $overruleFilters)
    {
        foreach ($refClass->getPropertiesByMatcher(self::$methodAndPropertyMatcher) as $refProperty) {
            list($fieldName, $value) = $this->readParam($request,
                                                        $refProperty->getAnnotation('Filter'),
                                                        $prefix,
                                                        $overruleFilters
                                       );
            if ($request->paramErrors()->existFor($fieldName) === false) {
                $refProperty->setValue($object, $value);
            }
        }
    }

    /**
     * calls methods with values from request
     *
     * @param  stubRequest               $request
     * @param  stubBaseReflectionClass   $refClass
     * @param  object                    $object
     * @param  string                    $prefix
     * @param  array<string,stubFilter>  $overruleFilters
     */
    protected function processMethods(stubRequest $request,
                                      stubBaseReflectionClass $refClass,
                                      $object,
                                      $prefix,
                                      array $overruleFilters)
    {
        foreach ($refClass->getMethodsByMatcher(self::$methodAndPropertyMatcher) as $refMethod) {
            list($fieldName, $value) = $this->readParam($request,
                                                        $refMethod->getAnnotation('Filter'),
                                                        $prefix,
                                                        $overruleFilters
                                       );
            if ($request->paramErrors()->existFor($fieldName) === false) {
                $refMethod->invoke($object, $value);
            }
        }
    }

    /**
     * reads param and returns its name and value
     *
     * @param   stubRequest               $request
     * @param   stubAnnotation            $filterAnnotation
     * @param   string                    $prefix
     * @param   array<string,stubFilter>  $overruleFilters
     * @return  array<string,mixed>
     */
    protected function readParam(stubRequest $request,
                                 stubAnnotation $filterAnnotation,
                                 $prefix,
                                 array $overruleFilters)
    {
            $fieldName = $prefix . $filterAnnotation->getFieldName();
            $value     = $request->readParam($fieldName)
                                 ->withFilter($this->getFilter($filterAnnotation,
                                                               $fieldName,
                                                               $overruleFilters
                                                     )
                                   );
            return array($fieldName, $value);
    }

    /**
     * returns filter based on overrules and annotation
     *
     * @param   stubAnnotation            $filterAnnotation
     * @param   string                    $fieldName
     * @param   array<string,stubFilter>  $overruleFilters
     * @return  stubFilter
     */
    protected function getFilter(stubAnnotation $filterAnnotation, $fieldName, array $overruleFilters)
    {
        if (isset($overruleFilters[$fieldName]) === true) {
            return $overruleFilters[$fieldName];
        }

        return $this->annotationBasedFilterFactory->createForAnnotation($filterAnnotation);
    }
}
?>