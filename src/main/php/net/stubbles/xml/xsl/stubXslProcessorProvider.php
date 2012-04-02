<?php
/* 
 * Injection provider for XSL processor instances.
 *
 * @package     stubbles
 * @subpackage  xml_xsl
 * @version     $Id: stubXslProcessorProvider.php 2867 2011-01-10 17:02:33Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::ioc::stubInjector',
                      'net::stubbles::xml::xsl::stubXSLProcessor'
);
/**
 * Injection provider for XSL processor instances.
 *
 * @package     stubbles
 * @subpackage  xml_xsl
 * @since       1.5.0
 */
class stubXslProcessorProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * injector instance to create instances of other classes
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * path to config files
     *
     * @var  string
     */
    protected $configPath;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @param  string        $configPath
     * @Inject
     * @Named{configPath}('net.stubbles.config.path')
     */
    public function  __construct(stubInjector $injector, $configPath)
    {
        $this->injector   = $injector;
        $this->configPath = $configPath;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        $xslProcessor = new stubXSLProcessor();
        if ($this->shouldHaveCallbacks($name) === true && $this->callbackConfigurationExists() === true) {
            $callbackList = @parse_ini_file($this->configPath . '/xsl-callbacks.ini');
            if (false === $callbackList) {
                throw new stubConfigurationException('XSL callback in ' . $this->configPath . '/xsl-callbacks.ini contains errors and can not be parsed.');
            }

            foreach ($callbackList as $callbackName => $callbackClass) {
                $xslProcessor->usingCallback($callbackName, $this->injector->getInstance($callbackClass));
            }
        }

        return $xslProcessor;
    }

    /**
     * checks whether the xsl processor instance to create should have callbacks
     *
     * @param   string  $name
     * @return  bool
     */
    protected function shouldHaveCallbacks($name)
    {
        return ('net.stubbles.xml.xsl.callbacks.disabled' !== $name);
    }

    /**
     * checks whether callback configuration file exists
     *
     * @return  bool
     */
    protected function callbackConfigurationExists()
    {
        return file_exists($this->configPath . '/xsl-callbacks.ini');
    }
}
?>