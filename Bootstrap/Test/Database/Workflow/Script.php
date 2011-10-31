<?php 
class Bootstrap_Test_Database_Workflow_Script implements Bootstrap_Test_Database_IWorkflow
{
	private $_script = null;
	private $_mysqlCommand = null;

	public function __construct($script, $mysqlCommand) {
		if (!file_exists($script)) {
			throw new Exception("MySQL script '" . $script . "' does not exist");
		}

		$this->_script = $script;
		$this->_mysqlCommand = $mysqlCommand;
	}


	public function execute()
	{
		$cmd = str_replace("%FILE%", $this->_script, $this->_mysqlCommand);
		$rvar = 0;
		system($cmd, $rvar);

		return $rvar;
	}
}
