<?php
require_once ADDENDUM_PATH;
require_once "Bootstrap/Annotations.php";

abstract class Bootstrap_Model_Converter_Abstract
{
	protected $_nameMapping = array();
	protected $_includeCategories = array();
	protected $_flatCategories = array();

	public function includeCategory($category, $isFlatCategory = false)
	{
		if (is_array($category)) {
			foreach ($category as $c) {
				$this->includeCategory($c);
			}
		} else {
			$this->_includeCategories[$category] = $category;
			if ($isFlatCategory) {
				$this->_flatCategories[$category] = $category;
			}
		}
	}

	public function removeCategory($category) 
	{
		if (is_array($category)) {
			foreach ($category as $c) {
				$this->removeCategory($c);
			}
		} else {
			if (isset($this->_includeCategories[$category])) {
				unset($this->_includeCategories[$category]);
			}
		}
	}

	public function setMapping($name, $value = null) 
	{
		if (is_array($name)) {
			while (list($key, $value) = each($name)) {
				$this->setMapping($key, $value);
			}
		} else {
			if (!$value) {
				throw new Exception("Expected value for mapping $name is not given");
			}
			
			$this->_nameMapping[$name] = $value;
		}
	}

	public function unsetMapping($mapping) 
	{
		if (is_array($mapping)) {
			foreach ($mapping as $m) {
				$this->unsetMapping($m);
			}
		} else {
			if (isset($this->_nameMapping[$mapping])) {
				unset($this->_nameMapping[$mapping]);
			}
		}
	}

	abstract public function convert($from = null, $to = null);
}

