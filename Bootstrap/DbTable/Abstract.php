<?php
class Bootstrap_DbTable_Abstract extends Zend_Db_Table_Abstract
{
	protected $_view = null;
	
	public function getTableName()
	{
		return $this->_name;
	}
	
	public function getViewName()
	{
		return $this->_view;
	}
	
    public function query($sql, $bind = array())
    {
    	return $this->_db->query($sql, $bind);
    }
}