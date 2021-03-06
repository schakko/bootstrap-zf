<?php
/**
 * Removes <strong>any</strong> content of any table in given namespace
 * @author ckl
 *
 */
class Bootstrap_Test_Database_Workflow_RemoveFixture implements Bootstrap_Test_Database_IWorkflow
{

	private $_db = null;

	private $_name = "remove fixture";

	public function __construct(Zend_Db_Adapter_Abstract $db, $name = null)
	{
		$this->_db = $db;
		
		if ($name) {
			$this->_name = $name;
		}
	}

	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL)
	{
		$stat = $this->_db->query("SHOW FULL TABLES");
		$stat->setFetchMode(Zend_Db::FETCH_NUM);
		$r = $stat->fetchAll();
		
		foreach ($r as $row) {
			if ($row[1] != 'BASE TABLE') {
				continue;
			}
			
			$this->_db->query("DELETE FROM " . $row[0]);
		}
	}

	public function __toString()
	{
		return $this->_name;
	}
}
