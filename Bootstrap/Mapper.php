<?php
class Bootstrap_Mapper
{
	protected $_modelName;
	protected $_dbTable;

	public function __construct($modelName = null)
	{
		if (null == $modelName) {
			if (preg_match("/(.*)_(\w*)Mapper/i", get_class($this), $r)) {
				$modelName = $r[2];
			}
		}
		
		if (null == $modelName) {
			throw new Exception("Please provide a model name for class " . get_class($this));
		}
		
		$this->_modelName = "Application_Model_" . $modelName;
		$dbTableName = "Application_Model_DbTable_" . $modelName;
		$this->_dbTable = new $dbTableName;
	}

	private function createDefaultStat()
	{
		($this->_dbTable->getViewName() != null) ? ($refTable = $this->_dbTable->getViewName()) : ($refTable = $this->_dbTable->getTableName());
		
		$stat = $this->_dbTable->getAdapter()
		->select()
		->from(array('t' => $refTable));
		return $stat;
	}
	
	public function findAll()
	{
		$stat = $this->createDefaultStat();
		$r = $stat->query()->fetchAll();
		return $this->toObjects($r);
	}
	
	public function findById($id)
	{
		$stat = $this->createDefaultStat();
		$stat->where('id = ?', array($id));
		$r = $stat->query()->fetch();
		
		return $this->toObject($r);
		
	}

	public function toObject($row)
	{
		if (!isset($this->_modelName)) {
			throw new Exception("No _modelName given for " . get_class($this));
		}
		
		if ($row == null) {
			return null;
		}

		$r = new $this->_modelName;

		while (list($property, $v) = each($row)) {
			if (property_exists($r, $property)) {
				$r->{$property} = $v;
			}
		}

		return $r;
	}

	public function toObjects(array $rows)
	{
		$r = array();

		for ($i = 0, $m = sizeof($rows); $i < $m; $i++) {
			$r[] = $this->toObject($rows[$i]);
		}

		return $r;
	}
	
	public function toRow($o)
	{
		require_once APPLICATION_PATH . '/../library/addendum/annotations.php';
		require_once APPLICATION_PATH . '/../library/Nostradamus/Annotations.php';
		
		$clazzName = get_class($o);
		$props = get_class_vars($clazzName);
		$r = array();
		
		while (list($k,$v) = each($props))
		{
			$r = ModelAnnotationResultFactory::map($o, new ReflectionAnnotatedProperty($o, $k), $r);
		}
		
		return $r;
	}
	
	public function save($object)
	{
		if ($object == null) {
			throw new Exception("You tried to save a null object!");
		}
		
		$row = $this->toRow($object);
		$id = 0;
		
		if (!isset($object->id) || $object->id == 0) {
			$id = $this->_dbTable->insert($row);
		} else {
			$where = $this->_dbTable->getAdapter()->quoteInto('id = ?', $object->id);
			$this->_dbTable->update($row, $where);
			$id = $object->id;
		}
		
		return $this->findById($id);
	}

	public function createStatementFindByColumn($column, $value)
	{
		$stmt = $this->_dbTable->query("SELECT * FROM " . $this->_dbTable->getTableName() . " WHERE $column = ?", array($value));
		return $stmt;
	}
}
