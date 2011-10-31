<?php 
class Bootstrap_Service_Exception extends Exception
{
	public function __construct($msg, $code = 500)
	{
		parent::__construct($msg, $code);
	}
}