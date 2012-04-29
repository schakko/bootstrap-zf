<?php
/**
 * Falls an die Methode des Service-Layers ein ungültiges Argument übergeben wurde
 * <br />
 * Als Error-Code wird <strong>412</strong> benutzt.
 */
class Bootstrap_Service_Exception_InvalidArgument extends Bootstrap_Service_Exception
{
	/**
	 * Error-Code ist 412
	 * @param string $msg
	 * @param integer $code
	 */
	public function __construct($msg, $code = 412 /* Precondition failed */)
	{
		parent::__construct($msg, $code);
	}
}
