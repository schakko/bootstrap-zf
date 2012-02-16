<?php
/**
 * A generic class for registering other workflows which can be executed
 * @author ckl
 *
 */
class Bootstrap_Test_Database_Workflow implements Bootstrap_Test_Database_IWorkflow
{

	/**
	 * stack with workflow items
	 *
	 * @var array
	 */
	private $_stack = array();

	private $_name = "workflow";

	/**
	 *
	 * @param $name string       	
	 */
	public function __construct($name = null)
	{
		if ($name) {
			$this->_name = $name;
		}
	}

	/**
	 * register a a new workflow
	 *
	 * @param $workflow Bootstrap_Test_Database_IWorkflow       	
	 */
	public function register(Bootstrap_Test_Database_IWorkflow $workflow)
	{
		$this->_stack[] = $workflow;
		return $this;
	}

	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL)
	{
		foreach ($this->_stack as $workflow) {
			$workflow->execute($this);
		}
	}

	/**
	 * Returns all workflows
	 * 
	 * @return array
	 */
	public function getWorkflows()
	{
		return $this->_stack;
	}

	public function __toString()
	{
		return $this->_name;
	}
}
