<?php
/** Zend_Registry */
require_once "Zend/Registry.php";

/** Bootstrap_Acl_Exceptionable */
require_once "Bootstrap/Acl/Exceptionable.php";

/** Bootstrap_Acl_Exception */
require_once "Bootstrap/Acl/Exception.php";

/** Zend_Acl_Assert_Interface */
require_once "Zend/Acl/Assert/Interface.php";

abstract class Bootstrap_Acl_ReasonableAssertion implements Zend_Acl_Assert_Interface
{
	public function assertionFailed($codeOrException, $message = null)
	{
		if ($codeOrException instanceof Exception) {
			$exception = $codeOrException;
		}
		else {
			if (!$message) {
				$message = "Authorization failed with internal code '$codeOrException'";
			}

			$exception = new Bootstrap_Acl_Exception($message, $codeOrException);
		}

		Zend_Registry::set(Bootstrap_Acl_Exceptionable::LAST_EXCEPTION_KEY, $exception);

		return false;
	}
}

