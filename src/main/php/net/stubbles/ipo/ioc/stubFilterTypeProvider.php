<?php
/**
 * Provider for filter types.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc
 * @version     $Id: stubFilterTypeProvider.php 3115 2011-03-30 16:28:24Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::lang::stubProperties',
                      'net::stubbles::lang::stubResourceLoader'
);
/**
 * Provider for filter types.
 *
 * Automatically recognizes filter types and filter annotation readers from
 * star files if those contain a file ipo/filter.ini with the following format:
 * <code>
 * [filter]
 * type=my::example::ExampleFilter
 *
 * [annotationreader]
 * ExampleFilter=my::example::ExampleFilterAnnotationReader
 * </code>
 *
 * If there are are different filters for the same type or different annotation
 * readers for the same annotation, usage order is as follows:
 * 1. Filter/Annotation reader added with addFilter*() methods
 * 2. Filter/Annotation reader from star files in lib directory. No particular
 *    order of single star files is guaranteed.
 * 3. Default filter/annotation reader provided by the framework.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc
 * @since       1.6.0
 */
class stubFilterTypeProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * name of filter type constant
     */
    const FILTER_TYPES_NAME     = 'net.stubbles.ipo.request.filter.types';
    /**
     * name of annotation reader constant
     */
    const ANNOTATIONREADER_NAME = 'net.stubbles.ipo.request.filter.annotationreader';
    /**
     * list of filters to provide via filter factory
     *
     * @var  array<string,string>
     */
    protected $typeFilter       = array('bool'     => 'net::stubbles::ipo::request::filter::stubBoolFilter',
                                        'int'      => 'net::stubbles::ipo::request::filter::stubIntegerFilter',
                                        'integer'  => 'net::stubbles::ipo::request::filter::stubIntegerFilter',
                                        'double'   => 'net::stubbles::ipo::request::filter::stubFloatFilter',
                                        'float'    => 'net::stubbles::ipo::request::filter::stubFloatFilter',
                                        'string'   => 'net::stubbles::ipo::request::filter::stubStringFilter',
                                        'text'     => 'net::stubbles::ipo::request::filter::stubTextFilter',
                                        'json'     => 'net::stubbles::ipo::request::filter::stubJsonFilter',
                                        'password' => 'net::stubbles::ipo::request::filter::stubPasswordFilter',
                                        'http'     => 'net::stubbles::ipo::request::filter::stubHTTPURLFilter',
                                        'date'     => 'net::stubbles::ipo::request::filter::stubDateFilter',
                                        'mail'     => 'net::stubbles::ipo::request::filter::stubMailFilter'
                                  );
    /**
     * list of filter annotation readers
     *
     * @var  array<string,string>
     */
    protected $annotationReader = array('BoolFilter'      => 'net::stubbles::ipo::request::filter::annotation::stubBoolFilterAnnotationReader',
                                        'DateFilter'      => 'net::stubbles::ipo::request::filter::annotation::stubDateFilterAnnotationReader',
                                        'FloatFilter'     => 'net::stubbles::ipo::request::filter::annotation::stubFloatFilterAnnotationReader',
                                        'HTTPURLFilter'   => 'net::stubbles::ipo::request::filter::annotation::stubHTTPURLFilterAnnotationReader',
                                        'IntegerFilter'   => 'net::stubbles::ipo::request::filter::annotation::stubIntegerFilterAnnotationReader',
                                        'MailFilter'      => 'net::stubbles::ipo::request::filter::annotation::stubMailFilterAnnotationReader',
                                        'PasswordFilter'  => 'net::stubbles::ipo::request::filter::annotation::stubPasswordFilterAnnotationReader',
                                        'PreselectFilter' => 'net::stubbles::ipo::request::filter::annotation::stubPreselectFilterAnnotationReader',
                                        'StringFilter'    => 'net::stubbles::ipo::request::filter::annotation::stubStringFilterAnnotationReader',
                                        'TextFilter'      => 'net::stubbles::ipo::request::filter::annotation::stubTextFilterAnnotationReader',
                                  );

    /**
     * constructor
     *
     * @param  stubResourceLoader  $resourceLoader  optional
     */
    public function __construct(stubResourceLoader $resourceLoader = null)
    {
        if (null === $resourceLoader) {
            $resourceLoader = new stubResourceLoader();
        }

        foreach ($resourceLoader->getStarResourceUris('ipo/filter.ini') as $filterPropertiesFile) {
            $properties = stubProperties::fromFile($filterPropertiesFile);
            foreach ($properties->getSection('filter') as $type => $className) {
                $this->addFilterForType($className, $type);
            }

            foreach ($properties->getSection('annotationReader') as $annotationName => $className) {
                $this->addFilterAnnotationReader($className, $annotationName);
            }
        }
    }

    /**
     * adds a filter class for a given type
     *
     * @param   string                  $className  full qualified class name of filter class
     * @param   string                  $type       name of type the filter is added for
     * @return  stubFilterTypeProvider
     */
    public function addFilterForType($className, $type)
    {
        $this->typeFilter[$type] = $className;
        return $this;
    }

    /**
     * adds a filter annotation reader class for given filter annotation
     *
     * @param   string                  $className       full qualified class name of filter annotation reader class
     * @param   string                  $annotationName  name of annotation the reader is added for
     * @return  stubFilterTypeProvider
     */
    public function addFilterAnnotationReader($className, $annotationName)
    {
        $this->annotationReader[$annotationName] = $className;
        return $this;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     * @throws  stubBindingException
     */
    public function get($name = null)
    {
        if (self::FILTER_TYPES_NAME === $name) {
            return $this->typeFilter;
        }

        if (self::ANNOTATIONREADER_NAME === $name) {
            return $this->annotationReader;
        }

        throw new stubBindingException('Invalid binding for name ' . $name . ', don\'t know how to provide.');
    }
}
?>