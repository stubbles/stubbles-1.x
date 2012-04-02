<?php
/**
 * Class to chain exceptions, e.g. to wrap php-builtin exceptions.
 * 
 * @package     stubbles
 * @subpackage  lang_exceptions
 * @version     $Id: stubChainedException.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Class to chain exceptions, e.g. to wrap php-builtin exceptions.
 * 
 * @package     stubbles
 * @subpackage  lang_exceptions
 */
abstract class stubChainedException extends stubException
{
    /**
     * cause of the exception
     *
     * @var  Exception
     */
    protected $cause;

    /**
     * constructor
     *
     * @param  string     $message  the message of the exception
     * @param  Exception  $cause    the exception that caused this exception
     */
    public function __construct($message, Exception $cause = null)
    {
        parent::__construct($message);
        $this->cause = $cause;
    }

    /**
     * checks whether a cause exists
     *
     * @return  bool
     */
    public function hasCause()
    {
        return null !== $this->cause;
    }

    /**
     * returns the cause for this exception
     *
     * @return  Exception
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * returns final message
     *
     * @return  string
     */
    public function getFinalMessage()
    {
        if (null === $this->cause) {
            return $this->getMessage();
        }
        
        if (($this->cause instanceof stubChainedException) === false) {
            return $this->cause->getMessage();
        }
        
        return $this->cause->getFinalMessage();
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * example::MyException {
     *     message(string): This is an exception.
     *     file(string): foo.php
     *     line(integer): 4
     *     code(integer): 3
     *     stacktrace(string): __STACKTRACE__
     * } caused by AnotherExeption {
     *     message(string): This is another exception.
     *     file(string): bar.php
     *     line(integer): 55
     *     code(integer): 4
     *     stacktrace(string): __STACKTRACE__
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        $parentString = parent::__toString();
        if (null === $this->cause) {
            return $parentString;
        }
        
        $string  = substr($parentString, 0, strlen($parentString) - 1);
        $string .= ' caused by ';
        if ($this->cause instanceof stubChainedException) {
            $string .= $this->cause->__toString();
        } else {
            if ($this->cause instanceof stubThrowable) {
                $string .= $this->cause->getClassName();
            } else {
                $string .= get_class($this->cause);
            }
            
            $string .= " {\n    message(string): " . $this->cause->getMessage() . "\n";
            $string .= '    file(string): ' . $this->cause->getFile() . "\n";
            $string .= '    line(integer): ' . $this->cause->getLine() . "\n";
            $string .= '    code(integer): ' . $this->cause->getCode() . "\n";
            $string .= '    stacktrace(string): ' . $this->cause->getTraceAsString() . "\n";
            $string .= "}\n";
        }
        
        return $string;
    }
}
?>