<?php
class Bootstrap_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * @var Zend_Acl
	 */
	private $_acl;

	public function __construct(Zend_Acl $acl)
	{
		$this->_acl = $acl;
	}

	public function authorizeUser($resource, $privilege, $throwExceptionWithMessage)
	{
		$isAllowed = $this->isAllowed($resource, $privilege);

		if (!$isAllowed) {
			if (!$throwExceptionWithMessage) {
				$throwExceptionWithMessage = "You are not allowed to do this action.";
			}
			
			throw new Bootstrap_Service_Exception_Authorization($throwExceptionWithMessage);
		}
	}

	public function isAllowed($resource, $privilege)
	{
		$role = $this->getActionController()->getSessionUser();
		$isAllowed = $this->_acl->isAllowed($role, $resource, $privilege);

		return $isAllowed;
	}
}