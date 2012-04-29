<?php 
/**
 * Falls während der Aktion innerhalb des Service-Layers einer der Rahmenbedingungen 
 * einen ungültigen Status besitzt.
 * <br />
 * Als Error-Code wird <strong>403</strong> benutzt.
 */
class Bootstrap_Service_Exception_InvalidState extends Bootstrap_Service_Exception
{
	/**
	 * Error-Code ist 403
	 * @param string $msg
	 * @param integer $code
	 */
	public function __construct($msg, $code = 403)
	{
		parent::__construct($msg, $code);
	}
}
