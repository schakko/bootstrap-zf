<?php
/**
 * Some migration helper methods
 * 
 * @author ckl
 *
 */
class Bootstrap_Test_Database_Workflow_MigrationHelper implements Bootstrap_Test_Database_IWorkflow
{

	private $_db = null;

	private $_name = "detect installed schema version or create table if not exist";

	private $_tableSchemaMigration = 'schema_migration';

	private $_createTableMigration = 'CREATE TABLE IF NOT EXISTS %TABLENAME% (id INT NOT NULL AUTO_INCREMENT, migration_on DATETIME, version CHAR(128), comment LONGTEXT, PRIMARY KEY(id))';

	private $_latestMigration = 0;

	public function __construct(Zend_Db_Adapter_Abstract $db, $tableSchemaMigration = 'schema_migration')
	{
		$this->_db = $db;
		
		if ($tableSchemaMigration) {
			$this->_tableSchemaMigration = $tableSchemaMigration;
		}
	}

	/**
	 * Returns tha latest installed migration
	 * @return string|int
	 */
	public function getLatestMigration()
	{
		return $this->_latestMigration;
	}

	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL)
	{
		$stat = $this->_db->query(str_replace('%TABLENAME%', $this->_tableSchemaMigration, $this->_createTableMigration));
		
		$stat = $this->_db->query('SELECT version FROM ' . $this->_tableSchemaMigration . ' ORDER BY version DESC LIMIT 1');
		$stat->setFetchMode(Zend_Db::FETCH_NUM);
		$r = $stat->fetch();
		
		if ($r) {
			$this->_latestMigration = $r[0];
		} else {
			$this->registerMigration($this->_latestMigration, 'initalizing schema migration table');
		}
	}

	/**
	 * Register a new migration in database
	 * @param string|int $version
	 * @param string $comment
	 */
	public function registerMigration($version, $comment)
	{
		$this->_db->query(
				"INSERT INTO " . $this->_tableSchemaMigration . " (migration_on, version, comment) VALUES(NOW(), '" . $version . "', '" . $comment . "')");
	}

	public function __toString()
	{
		return $this->_name;
	}
}
