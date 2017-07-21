<?php
namespace Optilab\DB;


class DB_Table_Column
{
	private $_column, $_columnName, $_isUnique, $_isPrimary;

	function __construct( $columnName, $type, $default = null, $isNotNull = false, $isUnique = false, $isPrimary = false, $autoIncrement = false ) {
		$this->_columnName = $columnName;
		$this->_column = "$columnName $type";
		if ($default !== null) {
			$this->_column .= " DEFAULT $default";
		}
		if ($isNotNull !== false) {
			$this->_column .= " NOT NULL";
		}
		if ($autoIncrement !== false) {
			$this->_column .= " AUTO_INCREMENT";
		}
		
		$this->_isUnique = $isUnique;
		$this->_isPrimary = $isPrimary;
	}

	public function columnName() {
		return $this->_columnName;
	}

	public function toString() {
		return $this->_column;
	}

	public function isUnique() {
		return $this->_isUnique;
	}

	public function isPrimary() {
		return $this->_isPrimary;
	}

}