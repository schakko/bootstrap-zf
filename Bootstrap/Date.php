<?php
class Bootstrap_Date
{
	/**
	 * Returns current date time in UTC
	 * @return string YYYY-MM-dd HH:mm:ss
	 */
	public static function nowUtc()
	{
		$date = new Zend_Date();
		$date->setTimezone('UTC');

		return self::asDatabaseformat($date);
	}

	/**
	 * Returns given date object as database compatible format
	 * @param Zend_Date $date 
	 * @return string YYYY-MM-dd HH:mm:ss
	 */
	public static function asDatabaseformat(Zend_Date $date) {
		return $date->get('YYYY-MM-dd HH:mm:ss');
	}
}
