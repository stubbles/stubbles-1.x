<?php
/**
 * Class that serializes arbitrary data into the format used by PHP's (un)serialize function.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @version     $Id: stubPHPSerializer.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::php::serializer::stubFormatException',
                      'net::stubbles::php::serializer::stubPHPSerializedData',
                      'net::stubbles::php::serializer::stubPHPSerializerMapping',
                      'net::stubbles::php::serializer::stubPHPSerializerObjectMapping',
                      'net::stubbles::php::serializer::stubPHPSerializerSPLSerializableMapping',
                      'net::stubbles::php::serializer::stubUnknownObject',
                      'net::stubbles::reflection::stubReflectionObject'
);
/**
 * Class that serializes arbitrary data into the format used by PHP's (un)serialize function.
 * 
 * You should not use this class for serializing in PHP only environments, use
 * serialize() and unserialize() directly in such cases. This class is to be used
 * when the format needs to be extended.
 * 
 * Taken from the XP frameworks's class remote.protocol.Serializer.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubPHPSerializer extends stubBaseObject
{
    /**
     * mapping of data types
     *
     * @var  array<char,stubPHPSerializerMapping>
     */
    protected $mappings             = array();
    /**
     * a cache for class mappings
     *
     * @var  array<string,stubPHPSerializerMapping>
     */
    protected $classMappingCache    = array();
    /**
     * mapping for default objects
     *
     * @var  stubPHPSerializerObjectMapping
     */
    protected $defaultObjectMapping;
    /**
     * mapping of packages
     *
     * @var  array<string,string>
     */
    protected $packageMapping       = array();
    /**
     * mapping of exceptions
     *
     * @var  array<string,string>
     */
    protected $exceptionMapping     = array();

    /**
     * constructor
     */
    public function __construct()
    {
        // default mapper can not be added to mapping because there is no
        // common root class for PHP classes
        $this->defaultObjectMapping = new stubPHPSerializerObjectMapping();
        
        // add mapping for classes implementing the Serializable interface from SPL
        $this->addMapping(new stubPHPSerializerSPLSerializableMapping());
    }

    /**
     * register a mapping
     * 
     * Be careful when adding mappings that may apply to the same class: later
     * added mappings with same distance are favoroured about earlier added
     * mappings.
     *
     * @param  stubPHPSerializerMapping  $mapping  the mapping to add
     * @param  char                      $token    optional  token to use instead of the default token
     */
    public function addMapping(stubPHPSerializerMapping $mapping, $token = null)
    {
        if (null === $token) {
            $token = $mapping->getToken();
        }
        
        $this->mappings[$token]  = $mapping;
        $this->classMappingCache = array();
    }

    /**
     * fetch best fitted mapper for the given object
     *
     * @param   object  $object               the object to retrieve the mapping for
     * @return  stubPHPSerializerMapping
     * @throws  stubIllegalArgumentException
     */
    public function findMappingFor($object)
    {
        if (is_object($object) == false) {
            throw new stubIllegalArgumentException('Can only retrieve mappings for objects.');
        }
        
        $fqClassName = (($object instanceof stubObject) ? ($object->getClassName()) : (get_class($object)));
        // Check the mapping-cache for an entry for this object's class
        if (isset($this->classMappingCache[$fqClassName]) == true) {
            return $this->classMappingCache[$fqClassName];
        }
        
        // Find most suitable mapping by calculating the distance in the inheritance
        // tree of the object's class to the class being handled by the mapping.
        $cinfo = array();
        $class = (($object instanceof stubObject) ? ($object->getClass()) : (new stubReflectionObject($object)));
        foreach ($this->mappings as $mapping) {
            $refClass     = $mapping->getHandledClass();
            $refClassName = $refClass->getName();
            if (($object instanceof $refClassName) === false) {
                continue;
            }
            
            $distance    = 0;
            $objectClass = $class;
            do {
                // check for direct match
                if ($refClass->getName() != $objectClass->getName()) {
                    $distance++;
                }
            } while (0 < $distance && null !== ($objectClass = $objectClass->getParentClass()));
            
            // register distance to object's class in cinfo
            $cinfo[$distance] = $mapping;
    
            if (isset($cinfo[0]) == true) {
                break;
            }
        }
        
        // no handlers found
        if (0 == count($cinfo)) {
            return null;
        }
    
        ksort($cinfo, SORT_NUMERIC);
        // first class is best class
        // remember this, so we can take shortcut next time
        $this->classMappingCache[$fqClassName] = $cinfo[key($cinfo)];
        return $this->classMappingCache[$fqClassName];
    }

    /**
     * add a package mapping
     *
     * @param  string  $localPackage   name of the package locally
     * @param  string  $remotePackage  name of the package on remote server
     */
    public function addPackageMapping($localPackage, $remotePackage)
    {
        $this->packageMapping[$remotePackage] = $localPackage;
    }

    /**
     * returns locale package name for given remote package
     *
     * @param   string  $remotePackage
     * @return  string
     */
    public function translateToLocalePackage($remotePackage)
    {
        return strtr($remotePackage, $this->packageMapping);
    }

    /**
     * add an exception mapping
     *
     * @param  string  $localException   name of the exception locally
     * @param  string  $remoteException  name of the exception on remote server
     */
    public function addExceptionMapping($localException, $remoteException)
    {
        $this->exceptionMapping[$localException] = $remoteException;
    }

    /**
     * returns the local exception for a given remote exception
     * 
     * If none is found net::stubbles::php::serializer::stubExceptionReference
     * will be used instead.
     *
     * @param   string  $remoteException  name of the exception to use instead
     * @return  string
     */
    public function getLocalException($remoteException)
    {
        $localException = array_search($remoteException, $this->exceptionMapping);
        if (false === $localException) {
            return 'net::stubbles::php::serializer::stubExceptionReference';
        }
        
        return $localException;
    }

    /**
     * returns the full qualified classname of the remote exception to use
     * instead of the local exception
     *
     * If none is found the return value is null.
     *
     * @param   string  $localException  name of the exception locally
     * @return  string
     */
    public function getRemoteException($localException)
    {
        if (isset($this->exceptionMapping[$localException]) === true) {
            return $this->exceptionMapping[$localException];
        }
        
        return null;
    }
    /**
     * serialize data into PHP's serialize() format
     *
     * @param   mixed                $data     the data to serialize
     * @param   array<string,mixed>  $context  optional  context data
     * @return  string
     * @throws  stubFormatException if an error is encountered in the format
     */
    public function serialize($data, array $context = array())
    {
        $type = gettype($data);
        switch ($type) {
            case 'NULL':
                return 'N;';
            
            case 'boolean':
                return 'b:' . (true === $data ? 1 : 0) . ';';

            case 'integer':
                return 'i:' . $data . ';';
            
            case 'double':
                return 'd:' . $data . ';';
            
            case 'string':
                return 's:' . strlen($data) . ':"' . $data . '";';
            
            case 'array':
                $s = 'a:' . sizeof($data) . ':{';
                foreach (array_keys($data) as $key) {
                    $s .= serialize($key) . $this->serialize($data[$key], $context);
                }
                
                return $s . '}';
            
            case 'resource':
                return ''; // ignore (resources can't be serialized)

            case 'object':
                $mapping = $this->findMappingFor($data);
                if (null !== $mapping) {
                    return $mapping->serialize($this, $data, $context);
                }

                return $this->defaultObjectMapping->serialize($this, $data, $context);

            default:
                throw new stubFormatException('Cannot serialize unknown type ' . $type);
        }
    }

    /**
     * Retrieve serialized representation of a variable
     *
     * @param   stubPHPSerializedData  $serialized
     * @param   array<string,mixed>    $context     optional  context data
     * @return  mixed
     * @throws  stubFormatException
     */
    public function unserialize(stubPHPSerializedData $serialized, array $context = array())
    {
        $start = $serialized->getOffset();
        $token = $serialized->getCharAt($start);
        switch ($token) {
            case 'N':
                // null
                $serialized->moveOffset(2);
                return null;
            
            case 'b':
                // booleans
                $serialized->moveOffset(2);
                return (bool) $serialized->consumeWord();
            
            case 'i':
                // integers
                $serialized->moveOffset(2);
                return (int) $serialized->consumeWord();
            
            case 'd':
                // decimals
                $serialized->moveOffset(2);
                return (float) $serialized->consumeWord();
            
            case 's':
                // strings
                $serialized->moveOffset(2);
                return $serialized->consumeString();

            case 'a':
                // arrays
                $serialized->moveOffset(2);
                $a    = array();
                $size = $serialized->consumeSize();
                $serialized->moveOffset();   // opening "{"
                for ($i = 0; $i < $size; $i++) {
                    $key     = $this->unserialize($serialized, $context);
                    $a[$key] = $this->unserialize($serialized, $context);
                }
                
                $serialized->moveOffset();  // closing "}"
                return $a;
            
            case 'O':
                // objects
                return $this->defaultObjectMapping->unserialize($this, $serialized, $context);

            default:
                // default, check if we have a mapping
                if (isset($this->mappings[$token]) == false) {
                    throw new stubFormatException('Cannot unserialize unknown type "' . $token . '" (' . $serialized . ')');
                }

                return $this->mappings[$token]->unserialize($this, $serialized, $context);
        }
    }

}
?>