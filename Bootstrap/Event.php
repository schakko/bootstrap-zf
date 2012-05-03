<?php 
/**
 * A simple event bus
 */
class Bootstrap_Event
{
	/** @var array */
	private $_listeners = array();

	/** @var array */
	private $_listenersRegex = array();

	private static $_instance = null;

	/**
	 * @return Bootstrap_Event
	 */
	public function getInstance() {
		if (null == self::$_instance) {
			self::$_instance  = new Bootstrap_Event();
		}

		return self::$_instance;
	}

	/**
	 * Fires a new event. First parameter must be a string and is used as an event name.
	 * You can pass as many parameters as you want.
	 *
	 * @throws Exception If no parameters were given
	 * @throws Exception If first parameter is not a string
	 */
	public function fire() {
		$params = func_get_args();

		if (sizeof($params) == 0) {
			throw new Exception("You must provide an event name as first parameter");
		}

		$event = $params[0];

		if (!is_string($event)) {
			throw new Exception("Only a string is valid for an event");
		}

		if (isset($this->_listeners[$event])) {
			$this->_fire($this->_listeners[$event], $params);
		}

		while (list($regex, $listeners) = each($this->_listenersRegex)) {
			if (preg_match($regex, $event)) {
				$this->_fire($listeners, $params);
			}
		}
	}

	/**
	 * Executes all listeners with given parameters
	 * @param array $listeners
	 * @param array $params
	 */
	protected function _fire($listeners, $params) {
		foreach ($listeners as $listener) {
			call_user_func($listener, $params);
		}
	}

	/**
	 * Registers all events with given closure as listener
	 *
	 * @param array $container
	 * @param string|array $events
	 * @param function $closure
	 * @return array
	 */
	protected function _register($container, $events, $closure) {
		if (!is_array($events)) {
			$events = array($events);
		}

		foreach ($events as $event) {
			if (!isset($container[$event])) {
				$container[$event] = array();
			}

			$container[$event][] = $closure;
		}

		return $container;
	}

	/**
	 * Registers one or more events
	 *
	 * @param string|array $events
	 * @param function $closure
	 */
	public function register($events, $closure) {
		$this->_listeners = $this->_register($this->_listeners, $events, $closure);
	}

	/**
	 * Registers one or more events, identified by regular expression
	 *
	 * @param string|array $events
	 * @param function $closure
	 */
	public function registerRegex($eventName, $closure) {
		$this->_listenersRegex = $this->_register($this->_listenersRegex, $events, $closure);
	}
}