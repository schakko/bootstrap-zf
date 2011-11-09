<?php
class Bootstrap_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
	/**
	 * @var Zend_Acl
	 */
	private $_acl = null;

	public function setAcl(Zend_Acl $acl)
	{
		$this->_acl = $acl;
	}

	public function IsAllowed($resource, $privilege)
	{
		$session = Zend_Session::namespaceGet('user');
		$sessionUser = $session['user'];
		return $this->_acl->isAllowed($sessionUser, $resource, $privilege);
	}
}
