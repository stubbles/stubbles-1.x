<?php
/**
 * Integration test for request value errors defined in ini file.
 *
 * @package     stubbles
 * @subpackage  test_integration
 * @version     $Id: stubRequestValueErrorPropertiesFactoryIntegrationTestCase.php 3049 2011-02-19 17:51:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorPropertiesFactory');
/**
 * Integration test for request value errors defined in ini file.
 *
 * @package     stubbles
 * @subpackage  test_integration
 * @group       integration
 */
class stubRequestValueErrorPropertiesFactoryIntegrationTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * run all value error tests one after another
     *
     * @test
     */
    public function all()
    {
        $rveFactory = new stubRequestValueErrorPropertiesFactory(new stubResourceLoader());
        $this->FIELD_EMPTY($rveFactory);
        $this->FIELD_NO_SELECT($rveFactory);
        $this->FIELD_WRONG_VALUE($rveFactory);
        $this->VALUE_TOO_SMALL($rveFactory);
        $this->VALUE_TOO_GREAT($rveFactory);
        $this->PASSWORDS_NOT_EQUAL($rveFactory);
        $this->PASSWORD_INVALID($rveFactory);
        $this->PASSWORD_TOO_LESS_DIFF_CHARS($rveFactory);
        $this->STRING_TOO_SHORT($rveFactory);
        $this->STRING_TOO_LONG($rveFactory);
        $this->DATE_INVALID($rveFactory);
        $this->DATE_TOO_EARLY($rveFactory);
        $this->DATE_TOO_LATE($rveFactory);
        $this->MAILADDRESS_CANNOT_CONTAIN_SPACES($rveFactory);
        $this->MAILADDRESS_CANNOT_CONTAIN_UMLAUTS($rveFactory);
        $this->MAILADDRESS_MUST_CONTAIN_ONE_AT($rveFactory);
        $this->MAILADDRESS_CONTAINS_ILLEGAL_CHARS($rveFactory);
        $this->MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS($rveFactory);
        $this->MAILADDRESS_INCORRECT($rveFactory);
        $this->URL_INCORRECT($rveFactory);
        $this->URL_NOT_AVAILABLE($rveFactory);
        $this->invalidErrorId($rveFactory);
    }

    /**
     * test that the FIELD_EMPTY error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function FIELD_EMPTY($rveFactory)
    {
        $requestError = $rveFactory->create('FIELD_EMPTY');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('FIELD_EMPTY', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('FIELD_EMPTY');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the FIELD_NO_SELECT error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function FIELD_NO_SELECT($rveFactory)
    {
        $requestError = $rveFactory->create('FIELD_NO_SELECT');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('FIELD_NO_SELECT', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('FIELD_NO_SELECT');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the FIELD_WRONG_VALUE error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function FIELD_WRONG_VALUE($rveFactory)
    {
        $requestError = $rveFactory->create('FIELD_WRONG_VALUE');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('FIELD_WRONG_VALUE', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('FIELD_WRONG_VALUE');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the VALUE_TOO_SMALL error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function VALUE_TOO_SMALL($rveFactory)
    {
        $requestError = $rveFactory->create('VALUE_TOO_SMALL');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('VALUE_TOO_SMALL', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('VALUE_TOO_SMALL');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the VALUE_TOO_GREAT error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function VALUE_TOO_GREAT($rveFactory)
    {
        $requestError = $rveFactory->create('VALUE_TOO_GREAT');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('VALUE_TOO_GREAT', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('VALUE_TOO_GREAT');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the PASSWORDS_NOT_EQUAL error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function PASSWORDS_NOT_EQUAL($rveFactory)
    {
        $requestError = $rveFactory->create('PASSWORDS_NOT_EQUAL');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('PASSWORDS_NOT_EQUAL', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('PASSWORDS_NOT_EQUAL');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the PASSWORD_INVALID error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function PASSWORD_INVALID($rveFactory)
    {
        $requestError = $rveFactory->create('PASSWORD_INVALID');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('PASSWORD_INVALID', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('PASSWORD_INVALID');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the PASSWORD_TOO_LESS_DIFF_CHARS error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function PASSWORD_TOO_LESS_DIFF_CHARS($rveFactory)
    {
        $requestError = $rveFactory->create('PASSWORD_TOO_LESS_DIFF_CHARS');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('PASSWORD_TOO_LESS_DIFF_CHARS', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('PASSWORD_TOO_LESS_DIFF_CHARS');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the STRING_TOO_SHORT error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function STRING_TOO_SHORT($rveFactory)
    {
        $requestError = $rveFactory->create('STRING_TOO_SHORT');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('STRING_TOO_SHORT', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('STRING_TOO_SHORT');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the STRING_TOO_LONG error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function STRING_TOO_LONG($rveFactory)
    {
        $requestError = $rveFactory->create('STRING_TOO_LONG');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('STRING_TOO_LONG', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('STRING_TOO_LONG');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the DATE_INVALID error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function DATE_INVALID($rveFactory)
    {
        $requestError = $rveFactory->create('DATE_INVALID');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('DATE_INVALID', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('DATE_INVALID');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the DATE_TOO_EARLY error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function DATE_TOO_EARLY($rveFactory)
    {
        $requestError = $rveFactory->create('DATE_TOO_EARLY');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('DATE_TOO_EARLY', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('DATE_TOO_EARLY');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the DATE_TOO_LATE error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function DATE_TOO_LATE($rveFactory)
    {
        $requestError = $rveFactory->create('DATE_TOO_LATE');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('DATE_TOO_LATE', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('DATE_TOO_LATE');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the MAILADDRESS_CANNOT_CONTAIN_SPACES error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function MAILADDRESS_CANNOT_CONTAIN_SPACES($rveFactory)
    {
        $requestError = $rveFactory->create('MAILADDRESS_CANNOT_CONTAIN_SPACES');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('MAILADDRESS_CANNOT_CONTAIN_SPACES', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('MAILADDRESS_CANNOT_CONTAIN_SPACES');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the MAILADDRESS_CANNOT_CONTAIN_UMLAUTS error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function MAILADDRESS_CANNOT_CONTAIN_UMLAUTS($rveFactory)
    {
        $requestError = $rveFactory->create('MAILADDRESS_CANNOT_CONTAIN_UMLAUTS');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('MAILADDRESS_CANNOT_CONTAIN_UMLAUTS', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('MAILADDRESS_CANNOT_CONTAIN_UMLAUTS');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the MAILADDRESS_MUST_CONTAIN_ONE_AT error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function MAILADDRESS_MUST_CONTAIN_ONE_AT($rveFactory)
    {
        $requestError = $rveFactory->create('MAILADDRESS_MUST_CONTAIN_ONE_AT');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('MAILADDRESS_MUST_CONTAIN_ONE_AT', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('MAILADDRESS_MUST_CONTAIN_ONE_AT');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the MAILADDRESS_CONTAINS_ILLEGAL_CHARS error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function MAILADDRESS_CONTAINS_ILLEGAL_CHARS($rveFactory)
    {
        $requestError = $rveFactory->create('MAILADDRESS_CONTAINS_ILLEGAL_CHARS');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('MAILADDRESS_CONTAINS_ILLEGAL_CHARS', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('MAILADDRESS_CONTAINS_ILLEGAL_CHARS');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS($rveFactory)
    {
        $requestError = $rveFactory->create('MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the MAILADDRESS_INCORRECT error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function MAILADDRESS_INCORRECT($rveFactory)
    {
        $requestError = $rveFactory->create('MAILADDRESS_INCORRECT');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('MAILADDRESS_INCORRECT', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $this->assertTrue($requestError->hasMessage('fr_*'));
        $this->assertTrue($requestError->hasMessage('es_*'));
        $requestError2 = $rveFactory->create('MAILADDRESS_INCORRECT');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the URL_INCORRECT error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function URL_INCORRECT($rveFactory)
    {
        $requestError = $rveFactory->create('URL_INCORRECT');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('URL_INCORRECT', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('URL_INCORRECT');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that the URL_NOT_AVAILABLE error is created
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function URL_NOT_AVAILABLE($rveFactory)
    {
        $requestError = $rveFactory->create('URL_NOT_AVAILABLE');
        $this->assertInstanceOf('stubRequestValueError', $requestError);
        $this->assertEquals('URL_NOT_AVAILABLE', $requestError->getId());
        $this->assertTrue($requestError->hasMessage('default'));
        $this->assertTrue($requestError->hasMessage('en_*'));
        $this->assertTrue($requestError->hasMessage('de_*'));
        $requestError2 = $rveFactory->create('URL_NOT_AVAILABLE');
        $this->assertNotSame($requestError, $requestError2);
    }

    /**
     * test that trying to retrieve a non-existing error throws an exception
     *
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function invalidErrorId($rveFactory)
    {
        try {
            $rveFactory->create('invalid');
        } catch (stubIllegalArgumentException $iae) {
            return;
        }
        
        $this->fail('Expected stubIllegalArgumentException, got none or another.');
    }
}
?>