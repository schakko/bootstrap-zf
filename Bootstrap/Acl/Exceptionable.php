<?php
require_once "Zend/Registry.php";

require_once "Bootstrap/Service/Exception/Authorization.php";

require_once "Zend/Acl.php";

require_once "Zend/Acl/Role/Interface.php";

require_once "Zend/Acl/Resource/Interface.php";

class Bootstrap_Acl_Exceptionable
{
	/**
	 * @var Zend_Acl
	 */
	private $_acl;

	const LAST_EXCEPTION_KEY = 'Bootstrap_Acl_ReasonableAssertion_Last_Exception';

	public function __construct(Zend_Acl $acl)
	{
		$this->_acl = $acl;
	}

	public function tryAuthorization(Zend_Acl_Role_Interface $user = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null)
	{
		$registry = Zend_Registry::getInstance();

		if (isset($registry[self::LAST_EXCEPTION_KEY])) {
			unset($registry[self::LAST_EXCEPTION_KEY]);
		}

		$r = $this->_acl->isAllowed($user, $resource, $privilege);

		if (!$r) {
			if (isset($registry[self::LAST_EXCEPTION_KEY])) {
				$exp = $registry[self::LAST_EXCEPTION_KEY];

				throw $exp;
			}

			throw new Bootstrap_Service_Exception_Authorization("Could not authorize user. Is user authenticated and has role set?");
		}

		return true;
	}
}

