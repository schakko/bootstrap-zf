<?php 
interface AnnotationHandler {
	public function handle($result, $propertyName, $value);
}

class Column extends Annotation implements AnnotationHandler
{
	public $name = null;
	public $dbExpression = null;
	public $executeExpression = 'always';
	
	public function handle($result, $propertyName, $value) {
		if ($this->name != null) {
			$propertyName = $this->name;
		}
		
		$val = $value;

		if (null != $this->dbExpression) {
			$expr = strtolower($this->executeExpression);

			if ($expr == 'always' || ($expr == 'isnull' && !$value)) {
				$val = new Zend_Db_Expr($this->dbExpression);
			}
		}

		$result[$propertyName] = $val;
		
		return $result;
	}
}

class Map extends Annotation
{
	public $name = null;
	public $category = null;
}

class ModelAnnotationResultFactory
{
	public static function map($instance, ReflectionAnnotatedProperty $property, $result)
	{
		foreach ($property->getAnnotations() as $annotation)
		{
			if ($annotation instanceof AnnotationHandler) {
				$result = $annotation->handle($result, $property->getName(), $property->getValue($instance));
			}
		}
		
		return $result;
	}
}
