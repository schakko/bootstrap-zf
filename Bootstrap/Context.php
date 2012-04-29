<?php
class Bootstrap_Context
{
	private $_registry = array();

	public function __construct() 
	{
	}

	public static function getInstance() 
	{
		if (!Zend_Registry::getInstance()->offsetExists('Bootstrap_Context')) {
			$ctx = new Bootstrap_Context();
			Zend_Registry::set('Bootstrap_Context', $ctx);
		}

		return Zend_Registry::get('Bootstrap_Context');
	}
 
	public static function register($name, $instance = null, $closure = null, $options = array()) 
	{
		if (is_object($name)) {
			$instance = $name;
			$name = get_class($name);
		}

		return self::getInstance()->set($name, $instance, $closure);
	}

	/**
	 * Resolves an existing instance of given object.
	 * You can
	 */
	public static function resolve($name, $aPrototype = false) 
	{
		return self::getInstance()->get($name, $aPrototype);
	}

	public function set($name, $instance = null, $factoryClosure = null, $options = array()) 
	{
		$registration['factory'] = $factoryClosure;

		if (null == $instance) {
			if (isset($options['createInstance'])) {
				$instance = $this->createInstance($name, $registration);
			}
		}

		$registration['instance'] = $instance;

		$this->_registry[$name] = $registration;

		return $instance;
	}

	protected function createInstance($clazzName, $options = null) 	
	{
		if (!isset($options['factory'])) {
			return new $clazzName();
		}

		return $options['factory']();
	}


	/**
	 * Returns an existing or new instance of given class name
	 * @param string $clazzName
	 * @param boolean $aPrototype
	 */
	public function get($clazzName, $aPrototype = false) 
	{
		if (!isset($this->_registry[$clazzName])) {
			return $this->set($clazzName, null, null, array('createInstance' => true));
		}

		if ((!$this->_registry[$clazzName]['instance']) || ($aPrototype)) {
			$options = array();

			if (isset($this->_registry[$clazzName]['factory'])) {
				$options['factory'] = $this->_registry[$clazzName]['factory'];
			}

			$instance = $this->createInstance($clazzName, $options);

			if (!$aPrototype) {
				$this->_registry[$clazzName]['instance'] = $instance;
			}
		} else {
			$instance = $this->_registry[$clazzName]['instance'];
		}

		return $instance;
	}
}
