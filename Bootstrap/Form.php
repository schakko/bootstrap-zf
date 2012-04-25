<?php
class Bootstrap_Form extends Zend_Form
{
	public function getErrorsAsArray()
	{
		return array('id' => $this->getAttrib('id'), 'name' => $this->getName(), 'errors' => $this->getMessages());
	}
}
