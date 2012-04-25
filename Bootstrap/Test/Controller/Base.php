<?php
abstract class Bootstrap_Test_Controller_Base extends Zend_Test_PHPUnit_ControllerTestCase
{
	public function setUp()
	{
		parent::setUp();

		try {
			Zend_Registry::get('db')->rollBack();
		}
		catch (Exception $e) {
		}

		Zend_Registry::get('db')->beginTransaction();

		try {
			$setUpFixture = Zend_Registry::get('setUpFixture');
			$setUpFixture->execute();
		}
		catch (Zend_Exception $e) {
		}
	}

	public function tearDown()
	{
		parent::tearDown();
		
		try {
			$tearDownFixture = Zend_Registry::get('tearDownFixture');
			$tearDownFixture->execute();
		} 
		catch (Zend_Exception $e) {
		}
	}

	/**
	 * Dispatches the URL and toggles that exceptions will be thrown
	 * @see Zend_Test_PHPUnit_ControllerTestCase::dispatch()
	 */
	public function dispatch($url = null)
	{
		// redirector should not exit
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$redirector->setExit(false);

		// json helper should not exit
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;

		$request = $this->getRequest();
		if (null !== $url) {
			$request->setRequestUri($url);
		}
		$request->setPathInfo(null);

		$this->getFrontController()
		->setRequest($request)
		->setResponse($this->getResponse())
		->throwExceptions(true)
		->returnResponse(false);

		if ($this->bootstrap instanceof Zend_Application) {
			$this->bootstrap->run();
		} else {
			$this->getFrontController()->dispatch();
		}
	}

	/**
	 * Dispatches the URL and expecects that Exception with name of $clazzException is thrown
	 * @param string $url
	 * @param string $clazzException Name of expected exception class name
	 * @param integer $code
	 * @throws Ambigous <Exception, PHPUnit_Framework_AssertionFailedError>
	 */
	public function dispatchWithExpectedException($url = null, $clazzException, $code = null) {
		try {
			$this->dispatch($url);
			$this->fail('Expected exception ' .$clazzException . ' was not thrown!');
		}
		catch (Exception $e) {
			if (!($e instanceof PHPUnit_Framework_AssertionFailedError)) {
				$clazzExceptionThrown = get_class($e);
				
				if ($clazzExceptionThrown !== $clazzException) {
					$this->fail('Exception ' . $clazzException . ' was expected, but ' . $clazzExceptionThrown . ' was thrown (message: "' . $e->getMessage() . '")');
				}
				
				if (($code != null) && ($code != $e->getCode())) {
					$this->fail('Expected code ' . $code . ' does not equal with actual code ' . $e->getCode() . ' (message: "' . $e->getMessage() . '")');
				}

			} else {
				throw $e;
			}
		}
	}

	public function setJsonRequest($data, $method = 'POST') {
		$this->getRequest()->setMethod($method)->setRawBody(Zend_Json::encode($data));
		$this->getRequest()->setHeader('Content-Type', 'application/json');
	}

	/**
	 * Returns the URI from location header
	 * @return string|null
	 */
	public function getLocation()
	{
		foreach ($this->getResponse()->getHeaders() as $k => $header) {
			if ($header['name'] == 'Location') {
				return $header['value'];
			}
		}

		return null;
	}

	/**
	 * Assert that response body is in JSON format
	 * @return object converted object
	 */
	public function assertJson()
	{
		$body = Zend_Json::decode($this->getResponse()->getBody());
		$this->assertNotNull($body);

		return $body;
	}
}
