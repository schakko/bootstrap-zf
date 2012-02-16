<?php
/**
 * Executes a set of migration files. The migrations will be executed ordered, so that the newest migration will be executed at last.
 * You must execute Bootstrap_Test_Database_Workflow_MigrationHelper before this instance is used.
 * @author ckl
 *
 */
class Bootstrap_Test_Database_Workflow_Migrations implements Bootstrap_Test_Database_IWorkflow
{

	private $_name = "run migrations inside a directory against an already defined version";

	private $_migrationDirectory = '';

	private $_mysqlCommand = 'mysql';

	private $_db = null;

	/**
	 * must return an array of array('version' => int, 'comment' => string)
	 *
	 * @var function
	 */
	private $_getMigrationMetainfoProvider = NULL;

	public function __construct($migrationDirectory, $mysqlCommand)
	{
		$this->_migrationDirectory = $migrationDirectory;
		$this->_mysqlCommand = $mysqlCommand;
		
		// register default closure provider
		$this->_getMigrationMetainfoProvider = function ($file)
		{
			$r = array('version' => 0, 'comment' => $file . ' has no valid comment');
			
			if (preg_match("/^(\d*)\-(\d*)\_(.*)\.sql/", $file, $arrRet)) {
				$r['version'] = $arrRet[1] . $arrRet[2];
				$r['comment'] = str_replace('_', ' ', $arrRet[3]);
			}
			
			return $r;
		};
	}

	/**
	 *
	 * @param $stack Bootstrap_Test_Database_Workflow       	
	 * @return Bootstrap_Test_Database_Workflow_MigrationHelper
	 */
	private function findMigrationHelper(Bootstrap_Test_Database_Workflow $stack)
	{
		foreach ($stack->getWorkflows() as $workflow) {
			if ($workflow instanceof Bootstrap_Test_Database_Workflow_MigrationHelper) {
				return $workflow;
			}
		}
		
		return null;
	}

	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL)
	{
		if (! is_dir($this->_migrationDirectory)) {
			throw new Exception("Could not find migration directory '" . $this->_migrationDirectory . "'");
		}
		
		if (NULL == ($migrationHelper = $this->findMigrationHelper($stack))) {
			throw new Exception("You must run an instance of MigrationHelper *before* you run migrations!");
		}
		
		$latestMigration = $migrationHelper->getLatestMigration();
		
		$iterator = new DirectoryIterator($this->_migrationDirectory);
		// $this->_getMigrationMetainfoProvider() does not work...
		$func = $this->_getMigrationMetainfoProvider;
		
		$migrations = array();
		
		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$info = $func($file->getBasename());
				
				if (0 == ((int) $info['version'])) {
					continue;
				}
				
				if ($latestMigration > (int) $info['version']) {
					continue;
				}
				
				$migrations[$info['version']] = array('filename' => $file->getFilename(), 'pathname' => $file->getPathname(), 'info' => $info);
			}
		}
		
		ksort($migrations);
		
		while (list ($version, $migration) = each($migrations)) {
			$info = $migration['info'];
			
			$command = new Bootstrap_Test_Database_Workflow_Script($migration['pathname'], $this->_mysqlCommand, $migration['filename']);
			$command->execute($stack);
			$migrationHelper->registerMigration($info['version'], $info['comment']);
		}
	
	}

	/**
	 * Register a migration info provider.
	 * 
	 * @param $f function
	 *       	 closure which must return an array of array('version' => int,
	 *        	'comment' => string).
	 */
	public function setMetaMigrationInfoProvider($f)
	{
		$this->_getMigrationMetainfoProvider = $f;
	}

	public function __toString()
	{
		return $this->_name;
	}
}
