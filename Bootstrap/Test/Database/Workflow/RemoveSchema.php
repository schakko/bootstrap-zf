<?php 
class Bootstrap_Test_Database_Workflow_RemoveSchema implements Bootstrap_Test_Database_IWorkflow
{
	private $_db = null;
	private $_name = "remove schema";
	
	public function __construct(Zend_Db_Adapter_Abstract $db, $name = null)
	{
		$this->_db = $db;

		if ($name) {
			$this->_name = $name;
		}
	}
	
	public function execute()
	{
		$stat = $this->_db->query("SHOW FULL TABLES");
		$stat->setFetchMode(Zend_Db::FETCH_NUM);
		$r = $stat->fetchAll();

		foreach ($r as $row) {
			$type = 'TABLE';

			if ($row[1] == 'VIEW') {
				$type = 'VIEW';
			}

			$this->_db->query("DROP " . $type . " " . $row[0]);
		}
	}

	public function __toString()
	{
		return $this->_name;
	}
}
