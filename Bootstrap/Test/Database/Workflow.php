<?php
class Bootstrap_Test_Database_Workflow implements Bootstrap_Test_Database_IWorkflow
{
	private $_stack = array();
	private $_name = "workflow";

	public function __construct($name = null)
	{
		if ($name) {
			$this->_name = $name;
		}
	}

	public function register(Bootstrap_Test_Database_IWorkflow $workflow) {
		$this->_stack[] = $workflow;
		return $this;
	}
	
	public function execute()
	{
		$idx = 0;

		foreach ($this->_stack as $workflow) {
			$workflow->execute();

		}
	}

	public function __toString()
	{
		return $this->_name;
	}
}
