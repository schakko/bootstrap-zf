<?php 
class MapToColumn extends Annotation
{
	public $column = null;
	public $dbExpression = null;
	
	public function handle($result, $propertyName, $value) {
		if ($this->column != null) {
			$propertyName = $this->column;
		}
		
		($this->dbExpression != null) ? ($val = new Zend_Db_Expr($this->dbExpression)) : ($val = $value);
		$result[$propertyName] = $val;
		
		return $result;
	}
}

class ModelAnnotationResultFactory
{
	public static function map($instance, ReflectionAnnotatedProperty $property, $result)
	{
		foreach ($property->getAnnotations() as $annotation)
		{
			$result = $annotation->handle($result, $property->getName(), $property->getValue($instance));
		}
		
		return $result;
	}
}