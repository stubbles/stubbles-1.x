<?php
/**
 * Class containing error messages for request values.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubRequestValueError.php 2978 2011-02-07 18:56:57Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubClonable',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::php::string::stubLocalizedString'
);
/**
 * Class containing error messages for request values.
 *
 * This is mainly an internal class for the request package, a container for
 * error messages. The messages itself can contain value keys. These value
 * keys are thought to be replaced with concrete values to customize the error
 * message.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @XMLTag(tagName='error')
 */
class stubRequestValueError extends stubSerializableObject implements stubClonable
{
    /**
     * id of the current stubRequestValueError
     *
     * @var  string
     */
    protected $id;
    /**
     * list of messages for current stubRequestValueError
     *
     * @var  array<string,string>
     */
    protected $messages = array();
    /**
     * required values for filling the message
     *
     * @var  array<string>
     */
    protected $values   = array();

    /**
     * constructor
     *
     * @param  string  $id         id of the current RequestValueError
     * @param  array   $messages   list of messages for current RequestValueError
     * @param  array   $valueKeys  optional  list of required values
     */
    public function __construct($id, array $messages, array $valueKeys = array())
    {
        $this->id       = $id;
        $this->messages = $messages;
        foreach ($valueKeys as $valueKey) {
            $this->values[$valueKey] = '';
        }
    }

    /**
     * returns the id of the current stubRequestValueError
     *
     * @return  string
     * @XMLAttribute(attributeName='id')
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * check whether a message for the given locale exists
     *
     * @param   string  $locale
     * @return  bool
     * @XMLIgnore
     */
    public function hasMessage($locale)
    {
        return isset($this->messages[$locale]);
    }

    /**
     * returns the message for the given locale
     *
     * @param   string  $locale
     * @return  string
     * @XMLIgnore
     */
    public function getMessage($locale)
    {
        if (isset($this->messages[$locale]) === true) {
            $message = $this->messages[$locale];
            foreach ($this->values as $key => $value) {
                $message = str_replace('{' . $key . '}', $this->flattenValue($value), $message);
            }

            return $message;
        }

        return null;
    }

    /**
     * returns all messages
     *
     * @return  array<stubLocalizedString>
     * @XMLTag(tagName='messages')
     */
    public function getMessages()
    {
        $messages = array();
        foreach ($this->messages as $locale => $message) {
            foreach ($this->values as $key => $value) {
                $message = str_replace('{' . $key . '}', $this->flattenValue($value), $message);
            }

            $messages[] = new stubLocalizedString($locale, $message);
        }

        return $messages;
    }

    /**
     * flattens the given value to be used within the message
     *
     * @param   mixed   $value
     * @return  string
     */
    protected function flattenValue($value)
    {
        if (is_array($value) === true) {
            $value = join(', ', $value);
        } elseif (is_object($value) === true) {
            if (method_exists($value, '__toString') == false) {
                $value = get_class($value);
            }
        }

        return (string) $value;
    }

    /**
     * Sets the values that should replace the value keys within the messages.
     *
     * This method could be used in conjunction with the getCriteria() method
     * of a validator: the return values of these mostly fit well to the
     * required value keys. Returns itself for easy use in conjunction with
     * the factory.
     *
     * @param   array<string,mixed>  $values
     * @return  stubRequestValueError
     * @throws  stubIllegalArgumentException
     * @XMLIgnore
     */
    public function setValues(array $values)
    {
        foreach (array_keys($this->values) as $key) {
            if (isset($values[$key]) === false) {
                throw new stubIllegalArgumentException('Value for key ' . $key . ' is missing.');
            }

            $this->values[$key] = $values[$key];
        }

        return $this;
    }

    /**
     * returns a list of all value keys
     *
     * @return  array<string>
     * @XMLIgnore
     */
    public function getValueKeys()
    {
        return array_keys($this->values);
    }
}
?>