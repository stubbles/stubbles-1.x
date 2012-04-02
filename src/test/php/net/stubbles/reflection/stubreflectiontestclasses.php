<?php
interface stubInterface {}
class stubTestWithOutMethodsAndProperties {}
class stubTestWithMethodsAndProperties extends stubTestWithOutMethodsAndProperties implements stubInterface
{
    public $property1;
    protected $property2;
    private $property3;
    
    public function __construct()
    {
        // nothing to to here
    }
    
    public function methodA()
    {
        // nothing to to here
    }
    
    protected function methodB()
    {
        // nothing to to here
    }
    
    private function methodC()
    {
        // nothing to to here
    }
}
?>