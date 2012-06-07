<?php
class Bootstrap_Date
{
	/**
	 * Creates a date object with UTC timezone.
	 * @param $date YYYY-MM-dd HH:mm:ss
	 */
	public static function createUtcDate($date)
	{
		$r = new Zend_Date();
		$r->set($date);
		$r->setTimezone('UTC');
		
		return $r;
	}
	
	/**
	 * Returns current date time in UTC
	 * @return string YYYY-MM-dd HH:mm:ss
	 */
	public static function nowUtc()
	{
		$date = self::nowUtcDate();

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
	
	/**
	 * Returns current date object in UTC
	 * @return Zend_Date
	 */
	public static function nowUtcDate()
	{
		$date = new Zend_Date();
		$date->setTimezone('UTC');
		
		return $date;
	}
	
	/**
	 * Returns true if the given date is before current timestamp
	 * @param Zend_Date $date
	 * @param integer $minimumSecondsBeforeNow
	 */
	public static function isBeforeNow(Zend_Date $date, $minimumSecondsBeforeNow = 0)
	{
		$now = self::nowUtcDate();
		$r = ($date->getTimestamp() < ($now->getTimestamp() - $minimumSecondsBeforeNow));
		
		return $r;
	}
	
	/**
	 * Returns if the given date is after current date
	 * @param Zend_Date $date
	 * @param integer $minimumSecondsAfterNow 
	 */
	public static function isAfterNow(Zend_Date $date, $minimumSecondsAfterNow = 0) {
		$now = self::nowUtcDate();
		$r = ($date->getTimestamp() > ($now->getTimestamp() + $minimumSecondsAfterNow));
		
		return $r;
	}
}
