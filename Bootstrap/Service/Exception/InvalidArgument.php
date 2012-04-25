<?php 
class Bootstrap_Service_Exception_InvalidArgument extends Bootstrap_Service_Exception
{
	public function __construct($msg, $code = 412 /* Precondition failed */)
	{
		parent::__construct($msg, $code);
	}
}
