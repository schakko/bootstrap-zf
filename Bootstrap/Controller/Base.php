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

		if ($this->isJsonRequest() || ($this->getRequest()->getParam('format') == 'json')) {
			$this->_helper->layout->disableLayout();
			$this->getRequest()->setParam('format', 'json');
			return;
		}

		$this->view->session = $this->_session;
		$this->view->acl = Zend_Registry::get('Zend_Acl');

		Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->_user->getRoleId());
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
			// Wenn JSON-Request, Fehlermeldung werfen
			if ($this->isJsonRequest()) {
				throw new Bootstrap_Service_Exception_Authorization("You must be logged in to use this feature", 401);
			}

			$this->redirectToLogin();
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

	/**
	 * Liefert zurück, ob der angefragte Content-Type oder Accept-Header "json" erwartet
	 * @return boolean
	 */
	protected function isJsonRequest()
	{
		$r = (($this->_getParam('format') === 'json') 
				|| (stripos($this->getRequest()->getHeader('X-Requested-With'), "XMLHttpRequest") !== FALSE)
				|| (stripos($this->getRequest()->getHeader('Content-Type'), "json") !== FALSE)
				|| (stripos($this->getRequest()->getHeader('Accept'), "json") !== FALSE)
		);

		return $r;
	}

	/**
	 * Liefert die Formulardaten aus dem Request zurück
	 * @return array
	 */
	protected function getFormData() {
		if ($this->isJsonRequest()) {
			return $this->getJsonDataFromRequest();
		}

		return $_POST;
	}

	/**
	 * Validiert das Formular. Sind dabei irgendwelche Fehler aufgetreten, werden diese gemergt und es wird null zurückgeliefert.
	 *
	 * @param Zend_Form $form
	 * @param Zend_View $view Wird keine View angegeben, werden die Fehler in $this->view gemergt
	 * @param integer Status-Code
	 * @param array Formulardaten
	 *
	 * @return array Formulardaten
	 */
	protected function validateForm(Zend_Form $form, Zend_View $view = null, $httpStatusCode = 400, $data = null) {
		if (($data === null) || !is_array($data)) {
			$data = $this->getFormData();
		}

		if (!is_array($data)) {
			$data = array();
		}
		
		$form->isValid($data);

		if (null == $view) {
			$view = $this->view;
		}

		if (sizeof($form->getMessages()) > 0) {
			$this->merge_array_to_object($form->getErrorsAsArray(), $view);
			$this->getResponse()
			->setHttpResponseCode($httpStatusCode);
			return;
		}

		$r = $form->getValues();
		return $r;
	}

	protected function getJsonDataFromRequest() {
		if (!$this->isJsonRequest()) {
			throw new Zend_Http_Exception("Your given content type is not acceptable. Expecting 'application/json'.", 406);
		}

		try {
			if (stripos($this->getRequest()->getHeader('Content-Type'), 'application/x-www-form-urlencoded') !== FALSE) {
				if ($this->getRequest()->isPost()) {
					return $this->getRequest()->getPost();
				}

				$params = array();
				parse_str($this->getRequest()->getRawBody(), $params);
				return $params;
			}
			$decode = Zend_Json::decode($this->getRequest()->getRawBody());
			return $decode;
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
		$this->_helper->contextSwitch()->setAutoJsonSerialization(false);
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$this->getResponse()->clearBody();
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

	/**
	 * Kopiert jedes Element aus $arr nach $obj->$key
	 * @param array $arr
	 * @param object $obj
	 * @return object
	 */
	protected function merge_array_to_object($arr, $obj) {
		if (!is_array($arr) || !is_object($obj)) {
			return;
		}

		while(list($k, $v) = each($arr)) {
			$obj->$k = $v;
		}

		return $obj;
	}
	
	/**
	 * Kopiert jede Eigenschaft aus $source nach $target->$key
	 * @param object $source
	 * @param object $target
	 * @return object
	 */
	protected function merge_objects($source, $target) {
		if (!is_object($source) || !is_object($target)) {
			return;
		}
		
		foreach ($source as $k => $v) {
			$target->$k = $v;
		}
		
		return $target;
	}

	/**
	 * Veröffentlicht die Form, wenn kein JSON-Request vorliegt
	 * @param Zend_Form $form
	 */
	public function publishForm(Zend_Form $form)
	{
		if (!$this->isJsonRequest()) {
			$this->view->form = $form;
		}
	}

	/**
	 * Leitet weiter zur Login-Seite.
	 * Im Session-Namespace 'origin' wird in der Variablen 'url' die ggw. Seite gespeichert
	 */
	public function redirectToLogin() {
		$namespace = new Zend_Session_Namespace('origin');
		$namespace->url = $this->getRequest()->getRequestUri();
		$this->_redirect('/login');
	}

	/**
	 * Überprüft, dass mindestens einer der Parameter gesetzt ist, ansonsten wird eine Bootstrap_Service_Exception_InvalidArgument geworfen
	 * @param array $paramsNeeded
	 * @param string $paramNameDefault
	 * @param string $paramValueDefault
	 */
	public function requireParam($paramsNeeded = array(), $paramNameDefault = '', $paramValueDefault = '')
	{
		foreach ($paramsNeeded as $param) {
			if ($this->_getParam($param)) {
				return true;
			}
		}

		if ($paramNameDefault && !$this->_getParam($paramNameDefault)) {
			$this->_setParam($paramNameDefault, $paramValueDefault);
			return true;
		}

		throw new Bootstrap_Service_Exception_InvalidArgument("You must provide at least one parameter of: " . implode(", ", $paramsNeeded));
	}
}
