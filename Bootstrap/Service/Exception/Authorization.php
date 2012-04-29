<?php 
/**
 * Falls die Autorisierung innerhalb des Service-Layers nicht funktioniert hat.
 * <br />
 * Als Error-Code wird <strong>403</strong> benutzt.
 * @author ckl
 *
 */
class Bootstrap_Service_Exception_Authorization extends Bootstrap_Service_Exception
{
	/**
	 * Error-Code ist standardmäßig 403
	 * @param string $msg
	 * @param integer $code
	 */
	public function __construct($msg, $code = 403)
	{
		parent::__construct($msg, $code);
	}
}