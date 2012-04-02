<?php
/**
 * JSON-RPC sub processor that handles post requests.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcPostSubProcessor.php 2636 2010-08-13 19:23:30Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDate',
                      'net::stubbles::service::jsonrpc::stubJsonRpcWriter',
                      'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractInvokingSubProcessor'
);
/**
 * JSON-RPC sub processor that handles post requests.
 *
 * Supports also (JSON-RPC) Notifictaions. Sends an JSON-RPC response in every case,
 * i.e. also when the request was no valid JSON-RPC request.
 *
 * The qooxdoo Server Implementation has some limitations as the specification is concerned:
 *  - Only client requests via XMLHttpRequest (MIME-Type: 'application/json') are supported
 *    and as consequence there is no MIME-Type checking (necessary).
 *  - Currently is no error object implemented. Errors are just simple strings.
 *    This could change in the future.
 *  - The order of the properties in a JSON-RPC response is not standard compliant, but this should be no problem.
 *  - The test methods which the spec suggests are availible but not activated in the json-rpc config.
 *    They reside in org::stubbles::examples::service::QooxdooSpecTestService.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @link        http://qooxdoo.org/documentation/0.8/rpc                                                         Client API / Basic Infos
 * @link        http://qooxdoo.org/documentation/0.8/rpc_server_writer_guide                                     Server (Impl.) Specification
 * @link        http://qooxdoo-contrib.svn.sourceforge.net/viewvc/qooxdoo-contrib/trunk/qooxdoo-contrib/RpcPhp/  PHP4 Implementation (RI)
 */
class stubJsonRpcPostSubProcessor extends stubJsonRpcAbstractInvokingSubProcessor
{
    /**
     * Constant for decode mode.
     */
    const MODE_DECODE = 1;
    /**
     * Constant for encode mode.
     */
    const MODE_ENCODE = 2;

    /**
     * does the processing of the subtask
     *
     * @param  stubRequest     $request   current request
     * @param  stubSession     $session   current session
     * @param  stubResponse    $response  current response
     * @param  stubInjector    $injector  injector instance
     * @param  stubProperties  $config    json-rpc config
     */
    public function process(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, stubProperties $config)
    {
        $phpJsonObj = null;

        try {
            $phpJsonObj = $request->readBody()->asJson();
        } catch (stubIllegalArgumentException $e) {
            $response->write(stubJsonRpcWriter::writeFault(null, $e->getMessage()));
            return;
        }

        // check json-rpc structure
        if ($this->isValidJsonRpcRequest($phpJsonObj) === false) {
            $errorMessage = "Invalid JSON-RPC request. "
                          . "Should have this form: {['service':...,]'method':...,'params':...,'id':...} "
                          . "(with double instead of single quotation marks)";
            $id = (property_exists($phpJsonObj, 'id') === true) ? $phpJsonObj->id : null;
            $response->write(stubJsonRpcWriter::writeFault($id, $errorMessage));
            return;
        }

        // dateString2stubDate conversion
        $this->walkForDateAndProcess($phpJsonObj->params, self::MODE_DECODE);

        $className = null;

        // discover class name
        if ($request->hasParam('__class') === true) {
            $className = $request->readParam('__class')->unsecure();
        } elseif (property_exists($phpJsonObj, 'service') === true) {
            $className = $phpJsonObj->service;
        }

        try {
            // invoke web method
            $reflect = $this->getClassAndMethod($config->getSection('classmap'), $phpJsonObj->method, $className);
            $result  = $this->invokeServiceMethod($injector, $reflect['class'], $reflect['method'], $phpJsonObj->params);
        } catch (Exception $e) {
            $response->write(stubJsonRpcWriter::writeFault($phpJsonObj->id, $e->getMessage()));
            return;
        }

        // just a json-rpc notification?
        if ($phpJsonObj->id === null) {
            return;
        }

        // stubDate2dateString conversion
        $this->walkForDateAndProcess($result, self::MODE_ENCODE);

        // send result back
        $response->write(stubJsonRpcWriter::writeResponse($phpJsonObj->id, $result));
    }

    /**
     * Checks if the decoded JSON string is a valid JSON-RPC request.
     *
     * @param   string  $phpJsonObj
     * @return  bool
     */
    public function isValidJsonRpcRequest($phpJsonObj)
    {
        if (null === $phpJsonObj) {
            return false;
        }

        if (property_exists($phpJsonObj, 'method') === false) {
            return false;
        }

        if (property_exists($phpJsonObj, 'params') === false) {
            return false;
        }

        if (property_exists($phpJsonObj, 'id') === false) {
            return false;
        }

        return true;
    }

    /**
     * Looks (at every level, i.e.  recursive) for date strings in the given array|object
     * and (en|de)codes them using a stubDate object or a dateString format.
     *
     * The first parameter has to be called by reference cause
     * arrays won't get change without although they are called by reference.
     *
     * The reference Operator in the foreach is needed because:
     * "Unless the array is referenced, foreach operates on a copy
     *  of the specified array and not the array itself."
     *
     * @param   array<mixed>|mixed  &$arrayOrObjectOrScalar  (stdClass when called recursivly (decoding) and mixed when scalar (encoding))
     * @param   string              $mode
     * @return  array<mixed>
     * @see     http://de.php.net/foreach
     */
    public function walkForDateAndProcess(&$arrayOrObjectOrScalar, $mode)
    {
        // nothing to do
        if (is_scalar($arrayOrObjectOrScalar) === true || null === $arrayOrObjectOrScalar) {
            return;
        }

        // stubDate obj
        if ($arrayOrObjectOrScalar instanceof stubDate) {
            $arrayOrObjectOrScalar = $this->encodeDate($arrayOrObjectOrScalar);
            return;
        }

        // nested?
        foreach ($arrayOrObjectOrScalar as $key => &$param) {
            if (is_scalar($param) === false
              && ($param instanceof stubDate) === false) {
                return $this->walkForDateAndProcess($param, $mode);
            }

            $validModes = array(self::MODE_DECODE, self::MODE_ENCODE);
            if (in_array($mode, $validModes) === true) {
                $encOrDecVal = ($mode === self::MODE_DECODE)
                                        ? $this->decodeDate($param)
                                        : $this->encodeDate($param);
            }

            // set value
            if (is_object($arrayOrObjectOrScalar) === true) {
                $arrayOrObjectOrScalar->$key = $encOrDecVal;
            } elseif (is_array($arrayOrObjectOrScalar) === true) {
                $param = $encOrDecVal;
            }
        }
    }

    /**
     * Decodes a datestring (datestring2StubDate).
     *
     * Note: Milliseconds currently get cut off.
     *
     * @param   string          $value
     * @return  stubDate|mixed  (given value without adaptions)
     */
    public function decodeDate($value)
    {
        $success = sscanf($value,
                          'new Date(Date.UTC(%d,%d,%d,%d,%d,%d,%d))',
                           $year, $month, $day, $hour, $minute, $sec, $millisec
        );
        if (7 === $success) {
            $dateTime = $year .'-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $sec;
            return new stubDate($dateTime, null);
        }

        return $value;
    }

    /**
     * Encodes stubDate objects using a date string (stubDate2datestring).
     *
     * JSON defines no date format, so we use the date format the qooxdoo team
     * defined. Qooxdoo dates are represented by a string which looks like this:
     * "new Date(Date.UTC(2006,5,20,22,18,42,223))"
     *
     * TODO: Test if a date in a request is also without a daylight saving time
     * addition and cutting off is also valid in a response.
     *
     * @param   mixed         $value
     * @return  string|mixed  (given value without adaptions)
     */
    public function encodeDate($value)
    {
        if ($value instanceof stubDate) {
            // there is no character for min and sec without leading zeros
            $dateTime = str_replace(',0', ',', $value->format('Y,n,j,G,i,s'));
            return 'new Date(Date.UTC(' . $dateTime . ',000))';
         }

         return $value;
    }
}
?>