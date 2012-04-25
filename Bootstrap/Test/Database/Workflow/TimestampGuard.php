<?php
/**
 * Interrupts the workflow if timestamp  is not reached
 * @author ckl
 *
 */
class Bootstrap_Test_Database_Workflow_TimestampGuard implements Bootstrap_Test_Database_IWorkflow
{

	private $_name = "makes a timestamp";

	private $_filename = "__last_workflow.timestamp";

	private $_noExecutionAfter = 300; /** migrations are five minutes valid */

	public function __construct($filename = null, $noExecutionAfter = null, $name = null)
	{
		if ($filename) {
			$this->_filename = $filename;
		}

		if ($noExecutionAfter) {
			$this->_noExecutionAfter = $noExecutionAfter;

		}
		
		if ($name) {
			$this->_name = $name;
		}
	}

	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL)
	{
		$lastExecution = time();
		$now = $lastExecution;

		if (file_exists($this->_filename)) {
			$lastExecution = file_get_contents($this->_filename);
		} else {
			$lastExecution = 0;
		}

		if ($now < ($timeout = ($lastExecution + $this->_noExecutionAfter))) {
			throw new Bootstrap_Test_Database_Workflow_Exception_Interrupt("Workflow is locked till " . strftime("%T", $timeout)); 
		}
		file_put_contents($this->_filename, $now);
	}

	public function __toString()
	{
		return $this->_name;
	}
}
