<?php
class Bootstrap_Model_SessionUser implements Zend_Acl_Role_Interface
{
	private $_role = 'guest';

	private $_user = null;

	public function getRoleId()
	{
		return $this->_role;
	}

	public function setRoleId($role)
	{
		$this->_role = $role;
	}

	/**
	 * @return Application_Model_User
	 */
	public function getUser()
	{
		return $this->_user;
	}

	/**
	 * Set current logged in user
	 * @param Application_Model_User $user
	 */
	public function setUser(Application_Model_User $user = null)
	{
		$this->_user = $user;
	}

	public function getId()
	{
		if ($this->_user == null) {
			return -1;
		}
		
		return $this->_user->id;
	}
	
	public function isLoggedIn()
	{
		return ($this->_user !== null);
	}
}