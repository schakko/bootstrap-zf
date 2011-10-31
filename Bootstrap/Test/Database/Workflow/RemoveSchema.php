<?php 
class Bootstrap_Test_Database_Workflow_RemoveSchema implements Bootstrap_Test_Database_IWorkflow
{
	private $_db = null;
	
	public function __construct(Zend_Db_Adapter_Abstract $db)
	{
		$this->_db = $db;
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
}
