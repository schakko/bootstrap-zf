<?php 
class Bootstrap_Test_Database_Workflow_RemoveFixture implements Bootstrap_Test_Database_IWorkflow
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
			if ($row[1] != 'TABLE') {
				continue;
			}

			$this->_db->query("DELETE FROM " . $row[0]);
		}
	}
}
