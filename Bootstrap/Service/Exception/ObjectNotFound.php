<?php 
/**
 * Einer der benötigten Ressourcen wurde nicht gefunden.
 * <br />
 * Als Error-Code wird <strong>404</strong> benutzt.
 *
 */
class Bootstrap_Service_Exception_ObjectNotFound extends Bootstrap_Service_Exception
{
	/**
	 * Error-Code ist 404
	 * @param string $msg
	 * @param integer $code
	 */
	public function __construct($msg, $code = 404)
	{
		parent::__construct($msg, $code);
	}
}
