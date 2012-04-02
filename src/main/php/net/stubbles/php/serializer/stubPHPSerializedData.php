<?php
/**
 * Helper class to operate on strings containing serialized data.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @version     $Id: stubPHPSerializedData.php 3264 2011-12-05 12:56:16Z mikey $
 */
/**
 * Helper class to operate on strings containing serialized data.
 * 
 * Taken from the XP frameworks's class remote.protocol.SerializedData.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubPHPSerializedData extends stubBaseObject
{
    /**
     * the serialized data itself
     *
     * @var  string
     */
    protected $data;
    /**
     * the offset where we currently are
     *
     * @var  int
     */
    protected $offset = 0;

    /**
     * constructor
     *
     * @param  string  $data  the serialized data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * returns the current offset
     *
     * @return  int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * move the offset for given length
     *
     * @param  int  $length  optional
     */
    public function moveOffset($length = 1)
    {
        $this->offset += $length;
    }

    /**
     * returns the character at the given offset
     *
     * @param   int   $offset
     * @return  char
     */
    public function getCharAt($offset)
    {
        return $this->data{$offset};
    }

    /**
     * returns a subpart of the data
     *
     * @param   int     $start
     * @param   int     $end
     * @return  string
     */
    public function getSubData($start, $end)
    {
        return substr($this->data, $start, ($end - $start));
    }

    /**
     * consume a string ([length]:"[string]")
     * 
     * @return  string
     */
    public function consumeString()
    {
        $length        = substr($this->data, $this->offset, strpos($this->data, ':', $this->offset) - $this->offset);
        $bound         = strlen($length) + 2;       // 1 for ':', 1 for '"'
        $value         = substr($this->data, $this->offset + $bound, $length);
        $this->offset += $bound + $length + 2; // 1 for '"', +1 to set the marker behind
        return $value;
    }

    /**
     * consume everything up to the next ";" and return it
     * 
     * @return  string
     */
    public function consumeWord()
    {
        $value         = substr($this->data, $this->offset, strpos($this->data, ';', $this->offset) - $this->offset);
        $this->offset += strlen($value) + 1;  // +1 to set the marker behind
        return $value;
    }

    /**
     * consume everything up to the next ":" character and return it
     * 
     * @return  string
     */
    public function consumeSize()
    {
        $value         = substr($this->data, $this->offset, @strpos($this->data, ':', $this->offset) - $this->offset);
        $this->offset += strlen($value) + 1;  // +1 to set the marker behind
        return $value;
    }
}
?>