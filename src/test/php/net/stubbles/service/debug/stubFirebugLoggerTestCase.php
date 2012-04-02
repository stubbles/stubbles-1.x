<?php
/**
 * Tests for net::stubbles::debug::stubFirebugLogger.
 *
 * @package     stubbles
 * @subpackage  service_debug_test
 * @version     $Id: stubFirebugLoggerTestCase.php 3273 2011-12-09 15:07:44Z mikey $
 */
stubClassLoader::load('net::stubbles::service::debug::stubFirebugLogger',
                      'net::stubbles::ipo::response::stubBaseResponse');

/**
 * Fake implemententation to surpress a PHPUnit error because of
 * the header() function interferes with premature output by PHPUnit.
 */
class stubDummyResponse extends stubBaseResponse{
    public function send() {}
}

/**
 * Fake implemententation to reset the static msg counter.
 */
class stubDummyFirebugLogger extends stubFirebugLogger{
    /* initialise with 2 because of header frame */
    public function resetStaticCounter() {
        self::$msgCount = 2;
    }
}

/**
 * Tests for net::stubbles::debug::stubFirebugLogger.
 *
 * @package     stubbles
 * @subpackage  service_debug_test
 * @group       service_debug
 */
class stubFirebugLoggerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Object under test.
     *
     * @var  stubDummyFirebugLogger
     */
    protected $stubFbLogger;
    /**
     * Log header frame (specification).
     *
     * @var  string
     */
    protected $headersSpecLog;
    /**
     * Log dump frame (specification).
     *
     * @var  string
     */
    protected $headersSpecDump;

    /**
     * Sets up test environment.
     *
     * Protocol excerpts:
     *
     * X-FirePHP-Data-100000000001  {
     * X-FirePHP-Data-999999999999  "__SKIP__":"__SKIP__"}
     * X-FirePHP-Data-200000000001  "FirePHP.Dump":{
     * X-FirePHP-Data-299999999999  "__SKIP__":"__SKIP__"},
     * X-FirePHP-Data-300000000001  "FirePHP.Firebug.Console":[
     * X-FirePHP-Data-399999999999  ["__SKIP__"]],
     *
     * dump():
     * X-FirePHP-Data-253836582500  "TestKey":"TestValue",
     *
     * log():
     * X-FirePHP-Data-321094074900  ["LOG","Just logging this."],
     * X-FirePHP-Data-321094074900  ["LOG",["aLabel","Message with a label."]],
     * X-FirePHP-Data-321094074900  ["INFO","Just logging this."],
     * X-FirePHP-Data-321094074900  ["WARN","Just logging this."],
     * X-FirePHP-Data-321094074900  ["ERROR","Just logging this."],
     *
     * @link    http://www.firephp.org/Wiki/Reference/Protocol
     */
    public function setUp()
    {
        $this->stubFbLogger = new stubDummyFirebugLogger(new stubDummyResponse());
        $this->stubFbLogger->resetStaticCounter();
        // protocol frames for producing a valid josn string
        $this->headersSpecLog  = array('100000000001' => '{',
                                       '300000000001' => '"FirePHP.Firebug.Console":[',
                                       '399999999999' => '["__SKIP__"]],',
                                       '999999999999' => '"__SKIP__":"__SKIP__"}'
        );

        $this->headersSpecDump = array('100000000001' => '{',
                                       '200000000001' => '"FirePHP.Dump":{',
                                       '299999999999' => '"__SKIP__":"__SKIP__"},',
                                       '999999999999' => '"__SKIP__":"__SKIP__"}'
        );
    }

    /**
     * Logs a string with and without label.
     * Checks for valid header index order *and* the JSON.
     *
     * @test
     */
    public function logString()
    {
        $this->stubFbLogger->log('Message without a label.');
        $this->stubFbLogger->log('Message with a label.', 'aLabel');

        $this->headersSpecLog['300000000002'] = '["LOG","Message without a label."],';
        $this->headersSpecLog['300000000003'] = '["LOG",["aLabel","Message with a label."]],';
        ksort($this->headersSpecLog);

        $this->assertEquals($this->headersSpecLog, $this->stubFbLogger->getHeaders());
        $this->assertEquals(join($this->headersSpecLog), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Logs an integer.
     *
     * @test
     */
    public function logInteger()
    {
        $this->stubFbLogger->log(42);

        $this->headersSpecLog['300000000002'] = '["LOG",42],';
        ksort($this->headersSpecLog);

        $this->assertEquals(join($this->headersSpecLog), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Logs a boolean.
     *
     * @test
     */
    public function logBoolean()
    {
        $this->stubFbLogger->log(true);

        $this->headersSpecLog['300000000002'] = '["LOG",true],';
        ksort($this->headersSpecLog);

        $this->assertEquals(join($this->headersSpecLog), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Logs a double.
     *
     * @test
     */
    public function logDouble()
    {
        $this->stubFbLogger->log(12.3);

        $this->headersSpecLog['300000000002'] = '["LOG",12.3],';
        ksort($this->headersSpecLog);

        $this->assertEquals(join($this->headersSpecLog), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Logs an array.
     *
     * @test
     */
    public function logArray()
    {
        $this->stubFbLogger->log(array('k1'=>'v1', 'k2'=>'v2'));

        $this->headersSpecLog['300000000002'] = '["LOG",{"k1":"v1","k2":"v2"}],';
        ksort($this->headersSpecLog);

        $this->assertEquals(join($this->headersSpecLog), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Logs an object.
     *
     * @test
     */
    public function logObject()
    {
        $this->stubFbLogger->log(new ArrayObject(array('a'=>1, 'b'=>2)));

        $this->headersSpecLog['300000000002'] = '["LOG",{"__className":"ArrayObject","a":1,"b":2}],';
        ksort($this->headersSpecLog);

        $this->assertEquals(join($this->headersSpecLog), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Dumps a string with and without label.
     * Checks for valid header index order *and* the JSON.
     *
     * @test
     */
    public function dumpString()
    {
        $this->stubFbLogger->dump('Message without a label.');
        $this->stubFbLogger->dump('Message with a label.', 'aLabel');

        $this->headersSpecDump['200000000002'] = '"unknown":"Message without a label.",';
        $this->headersSpecDump['200000000003'] = '"aLabel":"Message with a label.",';
        ksort($this->headersSpecDump);

        $this->assertEquals($this->headersSpecDump, $this->stubFbLogger->getHeaders());
        $this->assertEquals(join($this->headersSpecDump), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Dumps an array with and without label.
     *
     * @test
     */
    public function dumpArray()
    {
        $this->stubFbLogger->dump(array('k1'=>'v1', 'k2'=>'v2'));
        $this->stubFbLogger->dump(array('k1'=>'v1', 'k2'=>'v2'), 'aLabel');

        $this->headersSpecDump['200000000002'] = '"unknown":{"k1":"v1","k2":"v2"},';
        $this->headersSpecDump['200000000003'] = '"aLabel":{"k1":"v1","k2":"v2"},';
        ksort($this->headersSpecDump);

        $this->assertEquals(join($this->headersSpecDump), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Dumps a object.
     *
     * @test
     */
    public function dumpObject()
    {
        $this->stubFbLogger->dump(new ArrayObject(array('a'=>1, 'b'=>2)));
        $this->stubFbLogger->dump(new ArrayObject(array('a'=>1, 'b'=>2)), 'aLabel');

        $this->headersSpecDump['200000000002'] = '"unknown":{"__className":"ArrayObject","a":1,"b":2},';
        $this->headersSpecDump['200000000003'] = '"aLabel":{"__className":"ArrayObject","a":1,"b":2},';
        ksort($this->headersSpecDump);

        $this->assertEquals(join($this->headersSpecDump), $this->stubFbLogger->getHeaders(true));
    }

    /**
     * Tests the index calculating which pads the numberPrefix for dump and log
     * messages with zeros and auto increments.
     *
     * @test
     */
    public function indexCalculator()
    {
        $this->stubFbLogger->log('First');
        $this->stubFbLogger->log('Second');
        $this->stubFbLogger->log('Third');
        $this->stubFbLogger->log('Fourth');
        $this->stubFbLogger->log('Five');
        $this->stubFbLogger->log('Six');
        $this->stubFbLogger->log('Seven');
        $this->stubFbLogger->log('Eight');
        $this->stubFbLogger->log('Nine');

        $this->headersSpecLog['300000000002'] = '["LOG","First"],';
        $this->headersSpecLog['300000000003'] = '["LOG","Second"],';
        $this->headersSpecLog['300000000004'] = '["LOG","Third"],';
        $this->headersSpecLog['300000000005'] = '["LOG","Fourth"],';
        $this->headersSpecLog['300000000006'] = '["LOG","Five"],';
        $this->headersSpecLog['300000000007'] = '["LOG","Six"],';
        $this->headersSpecLog['300000000008'] = '["LOG","Seven"],';
        $this->headersSpecLog['300000000009'] = '["LOG","Eight"],';
        $this->headersSpecLog['300000000010'] = '["LOG","Nine"],';
        ksort($this->headersSpecLog);

        $this->assertEquals($this->headersSpecLog, $this->stubFbLogger->getHeaders());
    }

    /**
     * Logs an exception.
     *
     * @test
     */
    public function logException()
    {
        $this->stubFbLogger->log(new Exception('My Exception'));

        $this->headersSpecLog['300000000002'] = '["EXCEPTION",{"Class":"Exception","Message":"My Exception","File":"{PATH}","Line":"{NUMBER}","Type":"throw","Trace":"not implemented"}],';
        ksort($this->headersSpecLog);

        $header = $this->stubFbLogger->getHeaders();
        $header['300000000002'] = preg_replace('/(?<=File":)[^,]+/', '"{PATH}"'  , $header['300000000002']);
        $header['300000000002'] = preg_replace('/(?<=Line":)[^,]+/', '"{NUMBER}"', $header['300000000002']);
        $this->assertEquals($this->headersSpecLog, $header);
    }
}
?>