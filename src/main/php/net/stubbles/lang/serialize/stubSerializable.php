<?php
/**
 * Interface for objects that can be serialized.
 * 
 * @package     stubbles
 * @subpackage  lang_serialize
 * @version     $Id: stubSerializable.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for objects that can be serialized.
 * 
 * @package     stubbles
 * @subpackage  lang_serialize
 */
interface stubSerializable extends stubObject
{
    /**
     * returns a serialized representation of the class
     * 
     * @return  stubSerializedObject
     */
    public function getSerialized();
}
?>