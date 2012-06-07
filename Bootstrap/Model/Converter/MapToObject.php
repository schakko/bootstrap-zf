<?php
class Bootstrap_Model_Converter_MapToObject extends Bootstrap_Model_Converter_Abstract
{
	public function convert($map = null, $object = null) {
		if (!is_array($map)) {
			throw new Exception("First parameter must be an array");
		}

		if (!is_object($object)) {
			throw new Exception("Second parameter must be an object");
		}

		$clazzName = get_class($object);
		$props = get_class_vars($clazzName);
		
		while (list($key, $v) = each($props))
		{
			$property = new ReflectionAnnotatedProperty($object, $key);

			foreach ($property->getAnnotations() as $annotation) {
				if ($annotation instanceof Map) {
					$useArray = &$map;

					if (isset($annotation->category)) {
						if (!isset($this->_includeCategories[$annotation->category])) {
							continue;
						}

						if (!isset($this->_flatCategories[$annotation->category])) {
							if (!isset($map[$annotation->category])) {
								$map[$annotation->category] = array();
							}

							$useArray = &$map[$annotation->category];
						}
					}
					$useName = $key;
						
					if ($annotation->name) {
						$useName = $annotation->name;
					}

					if (isset($this->_nameMapping[$key])) {
						$useName = $this->_nameMapping[$key];
					}
						
					if (isset($useArray[$useName])) {
						$object->$key = $useArray[$useName];
					}
				}
			}
		}

		return $object;
	}
}

