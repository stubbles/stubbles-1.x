<?php
/**
 * Base implementation for a xml form processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id: stubAbstractXmlFormProcessable.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubAbstractProcessable',
                      'net::stubbles::webapp::xml::route::stubXmlFormProcessable'
);
/**
 * Base implementation for a xml form processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 */
abstract class stubAbstractXmlFormProcessable extends stubAbstractProcessable implements stubXmlFormProcessable
{
    /**
     * switch whether form value serialization to DOM should be disabled
     *
     * @var    bool
     * @since  1.2.0
     */
    private $_serializeFormValue = true;

    /**
     * disables serialization of form values into DOM
     *
     * This can be helpful in case you want to redisplay the form after
     * processing it successfully but not prefilled with values from the request
     * before.
     *
     * @since  1.2.0
     */
    protected function disableFormValueSerialization()
    {
        $this->_serializeFormValue = false;
    }

    /**
     * enables serialization of form values into DOM
     *
     * Redo disabling. There is no need to specifically enable the serialization,
     * it's enabled by default. This method is only in case you need to revert
     * the disabling from before.
     *
     * @since  1.2.0
     */
    protected function enableFormValueSerialization()
    {
        $this->_serializeFormValue = true;
    }

    /**
     * returns a list of form values
     *
     * @return  array<string,string>
     */
    public function getFormValues()
    {
        if (false === $this->_serializeFormValue) {
            return array();
        }
        
        $data      = array();
        foreach ($this->request->getParamNames() as $key) {
            $data[$key] = $this->request->readParam($key)->unsecure();
        }
        
        return $data;
    }
}
?>