<?php 
class Bootstrap_Service_Exception_InvalidState extends Bootstrap_Service_Exception
{
	public function __construct($msg, $code = 403)
	{
		parent::__construct($msg, $code);
	}
}
