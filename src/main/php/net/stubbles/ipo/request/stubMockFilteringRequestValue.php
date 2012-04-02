<?php
/**
 * Simplified filtering request value instance.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubMockFilteringRequestValue.php 3118 2011-03-31 13:58:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::ioc::stubFilterTypeProvider',
                      'net::stubbles::ipo::request::stubDefaultRequestValueErrorCollection',
                      'net::stubbles::ipo::request::stubFilteringRequestValue',
                      'net::stubbles::ipo::request::stubRequestValueErrorPropertiesFactory',
                      'net::stubbles::ipo::request::filter::stubDefaultFilterFactory',
                      'net::stubbles::lang::stubResourceLoader'
);
/**
 * Simplified filtering request value instance.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 */
class stubMockFilteringRequestValue extends stubFilteringRequestValue
{
    /**
     * constructor
     *
     * @param  string  $name   name of value
     * @param  string  $value  original value
     */
    public function __construct($name, $value)
    {
        $resourceLoader     = new stubResourceLoader();
        $filterTypeProvider = new stubFilterTypeProvider($resourceLoader);
        parent::__construct(new stubDefaultRequestValueErrorCollection(),
                            new stubDefaultFilterFactory($filterTypeProvider->get(stubFilterTypeProvider::FILTER_TYPES_NAME),
                                                         new stubRequestValueErrorPropertiesFactory($resourceLoader)
                            ),
                            $name,
                            $value
        );
    }
}
?>