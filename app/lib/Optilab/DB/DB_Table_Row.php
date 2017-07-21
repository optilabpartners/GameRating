<?php
namespace Optilab\DB;


class DB_Table_Row {

	// private $_data;
	public function __construct($data) {
		// $this->_data = $data;
		if (is_array($data) && count($data) > 0) {
			foreach($data as $property => $value) {
	      		$this->{$property} = $value;
	  		}
		}
	}

	public function getPublicProperties($excludes = ['id']) {
		$reflection_properties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
		$public_properties = array();
		foreach ($reflection_properties as &$reflection_property) {
			if (in_array($reflection_property->name, $excludes)) continue;
			$public_properties[$reflection_property->name] = $this->{$reflection_property->name};
		}
		return $public_properties;

	}
	// public function __get($field) {
	// 	return $this->data[$field];
	// }
	// public function __set($field, $value) {
	// 	if (!isset($this->_data[$field]))
	// 		throw new Exception('Field not found', USER_ERROR);
	// 	else $this->_data[$field] = $value;
	// }
}