<?php
/**
 * Binding module to configure the binder with arguments.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule');
/**
 * Binding module to configure the binder with arguments.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 */
class stubArgumentsBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * list of arguments
     *
     * @var  array<string>
     */
    protected $argv;

    /**
     * constructor
     *
     * @param  array<string>  $argv
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        foreach ($this->argv as $position => $value) {
            $binder->bindConstant()
                   ->named('argv.' . $position)
                   ->to($value);
        }
    }
}
?>