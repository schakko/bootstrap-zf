<?php
require_once "Bootstrap/Model/SessionUser.php";

class Bootstrap_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
	/**
	 * @var Zend_Acl
	 */
	private $_acl = null;

	/**
	 * @var Bootstrap_Model_SessionUser
	 */
	private $_sessionUser = null;

	public function setAcl(Zend_Acl $acl)
	{
		$this->_acl = $acl;
	}

	public function setSessionUser(Bootstrap_Model_SessionUser $sessionUser)
	{
		$this->_sessionUser = $sessionUser;
	}

	public function IsAllowed($resource, $privilege)
	{
		return $this->_acl->isAllowed($this->_sessionUser, $resource, $privilege);
	}
}