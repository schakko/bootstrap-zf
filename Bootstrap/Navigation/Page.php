<?php 
require_once "Zend/Navigation/Page/Uri.php";

class Bootstrap_Navigation_Page extends Zend_Navigation_Page_Uri
{
	/**
	 * @var Zend_Controller_Request_Http
	 */
	public $_request = null;

	private $_callback;

	private $_bindedObject = null;

	private $_parentObject = null;

	public function __construct($id, $bindedParameter = null, $callback = null)
	{
		$this->setId($id);
		$this->_callback = $callback;
		$this->set('bindedParameter', $bindedParameter ? $bindedParameter : $id);
	}

	/**
	 * Setzt das Eltern-Objekt dieser Seite
	 * @param object $object
	 */
	public function setParentObject($object)
	{
		$this->_parentObject = $object;
	}

	/**
	 * Liefert das Eltern-Objekt dieser Seite zurück
	 * @return object
	 */
	public function getParentObject()
	{
		return $this->_parentObject;
	}

	/**
	 * @param object $object
	 */
	public function setBindedObject($object) {
		$this->_bindedObject = $object;
	}

	/**
	 * @return object
	 */
	public function getBindedObject()
	{
		return $this->_bindedObject;
	}

	/**
	 * @return Zend_Controller_Request_Http
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * @param Zend_Controller_Request_Http $request
	 */
	public function setRequest(Zend_Controller_Request_Http $request = null)
	{
		$this->_request = $request;
	}

	public function isResponsibleForParameter()
	{
		if ($this->hasChildren()) {
			$children = $this->getPages();
				
			foreach ($children as $child) {
				if ($useChild = $child->isResponsibleForParameter()) {
					return $useChild;
				}
			}
		}

		if ($this->_request) {
			if ($this->_request->getParam($this->get('bindedParameter')) != null) {
				return $this;
			}
		}

		return null;
	}

	/**
	 * Aktualisiert diese Seite mit den Daten aus dem Backend
	 * @param int|object|null $mixedData Entweder ist es eine ID, ein Objekt oder aber null. Bei null wird über den gebindeten Parameter versucht, dass Objekt herauszufinden.
	 */
	public function update($mixedData = null) {
		if ((null == $mixedData) && $this->_request) {
			$mixedData = $this->_request->getParam($this->get('bindedParameter'));
		}

		$this->_bindedObject = $mixedData;

		if ($mixedData == null) {
			return;
		}

		if ($this->_callback) {
			$closure = $this->_callback;
			$closure($this);
		}

		if ($parent = $this->getParent()) {
			if ($parent instanceof Bootstrap_Navigation_Page) {
				$parent->update($this->_parentObject);
			}
		}
	}
}