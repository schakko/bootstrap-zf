<?php 
class Bootstrap_Test_Database_Workflow_Script implements Bootstrap_Test_Database_IWorkflow
{
	private $_name = "execute script";
	private $_script = null;
	private $_mysqlCommand = null;

	public function __construct($script, $mysqlCommand, $name = null) {
		if (!file_exists($script)) {
			throw new Exception("MySQL script '" . $script . "' does not exist");
		}

		if ($name) {
			$this->_name = $name;
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

	public function __toString()
	{
		return $this->_name;
	}
}
