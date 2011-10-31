<?php
class Bootstrap_Test_Database_Workflow implements Bootstrap_Test_Database_IWorkflow
{
	private $_stack = array();
	
	public function register(Bootstrap_Test_Database_IWorkflow $workflow) {
		$this->_stack[] = $workflow;
		return $this;
	}
	
	public function execute()
	{
		foreach ($this->_stack as $workflow) {
			$workflow->execute();
		}
	}
}