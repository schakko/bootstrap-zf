<?php
class Bootstrap_Model_Converter_ObjectToMap extends Bootstrap_Model_Converter_Abstract
{
	public function convert($object = null, $to = null) {
		if (!is_object($object)) {
			throw new Exception("First argument must be an object");
		}

		$clazzName = get_class($object);
		$props = get_class_vars($clazzName);
		$r = array();
		
		while (list($key, $v) = each($props))
		{
			$property = new ReflectionAnnotatedProperty($object, $key);

			foreach ($property->getAnnotations() as $annotation) {
				if ($annotation instanceof Map) {
					$useArray = &$r;

					if (isset($annotation->category)) {
						if (!isset($this->_includeCategories[$annotation->category])) {
							continue;
						}

						if (!isset($r[$annotation->category])) {
							$r[$annotation->category] = array();
						}

						$useArray = &$r[$annotation->category];
					}

					$useName = $key;
					if ($annotation->name) {
						$useName = $annotation->name;
					}

					if (isset($this->_nameMapping[$key])) {
						$useName = $this->_nameMapping[$key];
					}

					$useArray[$useName] = $property->getValue($object);
				}
			}
		}

		return $r;
	}
}

