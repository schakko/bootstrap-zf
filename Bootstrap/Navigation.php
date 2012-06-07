<?php
class Bootstrap_Navigation extends Zend_Navigation
{
	private $_currentPage = null;

	private $_activeId = null;

	private $_activeObject = null;

	/**
	 * @var Zend_Controller_Request_Http
	 */
	private $_request = null;

	public function __construct($pages = null, Zend_Controller_Request_Http $request = null)
	{
		parent::__construct($pages);
		$this->setRequest($request);
	}

	public function setRequest(Zend_Controller_Request_Http $request = null)
	{
		$this->_request = $request;
	}

	public function getRequest()
	{
		return $this->_request;
	}

	public function init()
	{
	}

	public function setCurrentPage($page) {
		if (is_string($page)) {
			$label = $page;
			$page = new Zend_Navigation_Page_Uri();
			$page->setLabel($label);
			$page->setUri("#");
		}

		$this->_currentPage = $page;
	}

	public function getCurrentPage() {
		return $this->_currentPage;
	}

	public function setActiveObject($object = null) {
		$this->_activeObject = $object;
	}

	public function setActiveId($activeId)
	{
		$this->_activeId = $activeId;
		return $this;
	}

	public function detectHierarchy()
	{
		/**
		 * @var Bootstrap_Navigation_Page
		 */
		$leaf = null;

		if ($this->_activeId != null) {
			$leaf = $this->findById($this->_activeId);
		}
		else {
			if ($this->hasChildren()) {
				$children = $this->getPages();

				foreach ($children as $child) {
					if ($leaf = $child->isResponsibleForParameter()) {
						break;
					}
				}
			}
		}
		
		$useLeaf = &$leaf;
		if (!$leaf) {
			if ($this->hasChildren()) {
				$leaf = new Zend_Navigation_Page_Uri();
				$leaf->setLabel("Unnamed hierarchy element");
			}
		}
		else {
			if ($this->_currentPage) {
				$this->_currentPage->setParent($leaf);
				$leaf->addPage($this->_currentPage);

				$useLeaf = &$this->_currentPage;
			}
		}

		if ($useLeaf) {
			$useLeaf->setActive(true);

			// Falls eine eigene Bootstrap_Navigation_Page übergeben wurde, muss diese geupdatet werden
			if ($useLeaf instanceof Bootstrap_Navigation_Page) {
				$useLeaf->update($this->_activeObject);
			} else if ($leaf instanceof Bootstrap_Navigation_Page) {
				$leaf->update($this->_activeObject);
			}
		}

		return $useLeaf;
	}
}