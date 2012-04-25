<?php
class Bootstrap_Date
{
	public static function nowUtc()
	{
		$date = new Zend_Date();
		$date->setTimezone('UTC');

		return $date->get('YYYY-MM-dd HH:mm:ss');
	}
}
