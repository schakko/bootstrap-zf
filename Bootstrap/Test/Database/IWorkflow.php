<?php
/**
 * Simple chaining interface for executing a task
 * @author ckl
 *
 */
interface Bootstrap_Test_Database_IWorkflow
{

	/**
	 * Executes a workflow definition
	 *
	 * @param $stack Bootstrap_Test_Database_Workflow
	 *       	 parent workflow wich initiated this workflow
	 */
	public function execute(Bootstrap_Test_Database_Workflow $stack = NULL);
}
