<?php
/**
 * Executes a program, command line, whatever
 * @author ckl
 *
 */
class Bootstrap_Test_Database_Workflow_Execute implements Bootstrap_Test_Database_IWorkflow
{

	private $_name = "execute program";

	private $_exec = null;

	public function __construct($exec, $name = null)
	{
		if ($name) {
			$this->_name = $name;
		}
	
		$this->_exec = $exec;	
	}

	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL)
	{
		system($this->_exec);
	}

	public function __toString()
	{
		return $this->_name;
	}
}
