<?php 
require_once 'Zend/Controller/Router/Rewrite.php';

class Bootstrap_Controller_Router_Rewrite extends Zend_Controller_Router_Rewrite
{
	/**
	 * @var Bootstrap_Model_SessionUser
	 */
	private $_sessionUser = null;
	
	/**
	 * Setzt die aktuelle Benutzersession
	 * @param Bootstrap_Model_SessionUser $sessionUser
	 */
	public function setSessionUser(Bootstrap_Model_SessionUser $sessionUser) 
	{
		$this->_sessionUser = $sessionUser;
	}
	
	/**
	 * Liefert die aktuelle Benutzersession zurück
	 * @return Bootstrap_Model_SessionUser
	 */
	public function getSessionUser() 
	{
		if (!$this->_sessionUser) {
			$this->_sessionUser = new Bootstrap_Model_SessionUser();
		}
		
		return $this->_sessionUser;
	}
}