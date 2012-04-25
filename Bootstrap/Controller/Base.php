<?php
class Bootstrap_Controller_Base extends Zend_Controller_Action
{
	/**
	 * @var Bootstrap_Model_SessionUser
	 */
	protected $_user = null;

	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session;

	public function init()
	{
		$this->_session = Zend_Session::namespaceGet('user');

		// Lokale Variablen dem Controller und der Session zuweisen
		$this->_user = $this->_session['user'];

		if ($this->getRequest()->isXmlHttpRequest() || ($this->getRequest()->getParam('format') == 'json')) {
			$this->_helper->layout->disableLayout();
			return;
		}

		$this->view->session = $this->_session;
		$this->view->acl = Zend_Registry::get('Zend_Acl');

		Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->_user->getRoleId());
		$this->view->isOnOwnSite = false;

		if ($this->_user->isLoggedIn() && ($this->getRequest()->getParam('username') == $this->_user->getUser()->username)) {
			$this->view->isOnOwnSite = true;
		}
		
	}
	
	public function getSessionUser()
	{
		return $this->_user;
	}

	/**
	 * Erwartet, dass der Benutzer angemeldet ist und liefert dessen Benutzerobjekt zurück
	 */
	public function requireUser()
	{
		if (!$user = $this->_user->getUser()) {
			// TODO Redirect auf Login-Seite oder ähnliches
			throw new Exception("You must be logged in to use this feature", 401);
		}

		return $user;
	}

	protected function forwardIfLoggedIn($action, $controller = "", $params = array())
	{
		$merged = array_merge($params, $this->getRequest()->getParams());
		if (!$this->_user->isLoggedIn()) {
			$this->_forward('index', 'index');
		} else {
			$merged['username'] = $this->_user->getUser()->username;
			$this->_forward($action, $controller, "", $merged);
		}
	}

	protected function isJsonRequest()
	{
		$r = ((stripos($this->getRequest()->getHeader('Content-Type'), "json") !== FALSE)
		|| (stripos($this->getRequest()->getHeader('Accept'), "json") !== FALSE)
		);

		return $r;
	}

	protected function getFormData() {
		if ($this->isJsonRequest()) {
			return $this->getJsonDataFromRequest();
		}
		
		return $_POST;
	}

	protected function getJsonDataFromRequest() {
		if (!$this->isJsonRequest()) {
			throw new Zend_Http_Exception("Your given content type is not acceptable. Expecting 'application/json'.", 406);
		}

		try {
			return Zend_Json::decode($this->getRequest()->getRawBody());
		}
		catch (Exception $e) {
			throw new Zend_Http_Exception("Your given JSON content was not valid.", 406);
		}
	}

	protected function sendJsonFormError(Zend_Form $form, $httpStatusCode = 400)
	{
		$r = array('id' => $form->getAttrib('id'), 'name' => $form->getName(), 'errors' => $form->getMessages());
		$this->sendJsonResponse($r, $httpStatusCode);
	}

	protected function sendJsonResponse($unserializedObject, $httpStatusCode = 200)
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$this->getResponse()
		->setHttpResponseCode($httpStatusCode)
		->setHeader('Content-Type', 'application/json')
		->setBody(Zend_Json::encode($unserializedObject));
	}

	protected function forwardToMethod($map) {
		$method = strtoupper($this->getRequest()->getMethod());

		if (isset($map[$method])) {
			$function = $map[$method];

			if (!method_exists($this, $function)) {
				throw new Exception("Forwarded REST action " . $this->getActionName() . "/" . $method . " -> " . $function . "() does not exist");
			}

			$this->$function();
		}
		else {
			// TODO: HTTP Code 
			throw new Zend_Http_Exception("This resource does not allow method " . $method . ". Only one of them is allowed: " . implode(array_keys($map), ", "), 405);
		}
	}

	protected function merge_array_to_object($arr, $obj) {
		if (!is_array($arr) || !is_object($obj)) {
			return;
		}

		while(list($k, $v) = each($arr)) {
			$obj->$k = $v;
		}
	}
}
