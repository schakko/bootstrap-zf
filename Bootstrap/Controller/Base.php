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
		$this->view->session = $this->_session;
		$this->view->acl = Zend_Registry::get('Zend_Acl');

		Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->_user->getRoleId());
		$this->view->isOnOwnSite = false;

		if ($this->_user->isLoggedIn() && ($this->getRequest()->getParam('username') == $this->_user->getUser()->username)) {
			$this->view->isOnOwnSite = true;
		}
		
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
		}
	}
	
	public function getSessionUser()
	{
		return $this->_user;
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
}
