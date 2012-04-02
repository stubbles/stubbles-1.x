<?php
/**
 * Container to allow method chaining for returned arrays.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubArrayAccessor.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalAccessException');
/**
 * Container to allow method chaining for returned arrays.
 *
 * In case a method returns an array it may return an instance of this class as
 * well, constructed with the array intended for return. This will allow other
 * methods for method chaining. This is required in some circumstances as PHP
 * does not allow to write code like
 * <code>
 * echo asArray()[0]
 * </code>
 * which would access the first value of the returned array. A work around is
 * to write
 * <code>
 * $array = asArray();
 * echo $array[0];
 * </code>
 * With this class it is possible to write it as follows:
 * <code>
 * echo asArray()->first()
 * </code>
 *
 * @package     stubbles
 * @subpackage  lang
 */
class stubArrayAccessor extends stubBaseObject implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * the wrapped array
     *
     * @var  array
     */
    protected $data = array();

    /**
     * constructor
     *
     * @param  array  $data  optional
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * returns value of first element
     *
     * Returns null if there is no first element.
     *
     * @return  mixed
     */
    public function first()
    {
        if (count($this->data) === 0) {
            return null;
        }
        
        reset($this->data);
        return current($this->data);
    }

    /**
     * returns value of last element
     *
     * @return  mixed
     */
    public function last()
    {
        if (count($this->data) === 0) {
            return null;
        }
        
        return end($this->data);
    }

    /**
     * returns value stored under given offset
     *
     * @param   string|int  $offset
     * @return  mixed
     */
    public function at($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * replaces current array with another array
     *
     * @param  array  $data
     */
    public function replace(array $data)
    {
        $this->data = $data;
    }

    /**
     * returns raw array
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * checks whether an entry for given offset exists
     *
     * @param   string|int  $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * returns value stored under given offset
     *
     * @param   string|int  $offset
     * @return  mixed
     * @throws  stubIllegalAccessException
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset]) === true) {
            return $this->data[$offset];
        }
        
        throw new stubIllegalAccessException('No element for offset ' . $offset);
    }

    /**
     * sets value at given offset
     *
     * @param  string|int  $offset
     * @param  mixed       $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * removes given offset
     *
     * @param  string|int  $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->data[$offset]) === true) {
            unset($this->data[$offset]);
        }
    }

    /**
     * returns amount of elements
     *
     * @return  int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * returns an iterator to be used in foreach
     *
     * @return  Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * returns a list of all keys of the wrapped array
     *
     * @return  array<int,scalar>
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }
}
?>