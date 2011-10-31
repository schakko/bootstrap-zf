<?php
class Bootstrap_Model
{
	/**
	 * ID
	 * @var int
	 */
	public $id;
	
	public function __get($key)
	{
		$inflector = new Zend_Filter_Word_UnderscoreToCamelCase();

		$method = 'get' . $inflector->filter($key);

		if(method_exists($this, $method)) {
			return $this->{$method}();
		}
		
		if (!property_exists($this, $key)) {
			throw new Exception("You tried to access property '$key' of class " . get_class($this) . " but this property does not exists");
		}

		return $this->{$key};
	}

	public function __set($key, $value)
	{
		$inflector = new Zend_Filter_Word_UnderscoreToCamelCase();

		$method = 'set' . $inflector->filter($key);

		if(method_exists($this, $method)) {
			return $this->{$method}($value);
		}

		$this->{$key} = $value;
	}
}