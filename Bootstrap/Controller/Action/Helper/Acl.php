<?php
class Bootstrap_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
	const LAST_ACL_EXCEPTION_KEY = "lastAclException";

	/**
	 * @var Bootstrap_Acl_Exceptionable
	 */
	private $_exceptionableAcl;

	public function __construct(Bootstrap_Acl_Exceptionable $exceptionableAcl)
	{
		$this->_exceptionableAcl = $exceptionableAcl;
	}

	public function authorizeUser($resource, $privilege, $throwExceptionWithMessage = null)
	{
		$registry = Zend_Registry::getInstance();

		if (isset($registry[self::LAST_ACL_EXCEPTION_KEY])) {
			unset($registry[self::LAST_ACL_EXCEPTION_KEY]);
		}

		try {
			$isAllowed = $this->_exceptionableAcl->tryAuthorization($resource, $privilege);
			
			return true;
		}
		catch (Exception $e) {
			Zend_Registry::set(self::LAST_ACL_EXCEPTION_KEY, $e);
		}

		return false;
	}
}
