<?php
class Bootstrap_Test_Database extends PHPUnit_Framework_TestCase
{
	private $_workflow = null;

	public function __construct()
	{
		$this->_workflow = Zend_Registry::get('cleanSchemaSetup');
	}

	public function setUp()
	{
		$this->_workflow->execute();
	}
}
