<?php
namespace Optilab\DB;


class DB_Table
{
	private $_columns, $_rows, $_tableName, $_wpdb;
	function __construct($tableName, $columns = array()) {
		global $wpdb;
		$this->_wpdb = $wpdb;
		if ( count($columns) && $columns[0] instanceof DB_Table_Column  ) {
			$this->_columns = $columns;
		}
		$this->_tableName = $tableName;
	}

	public function create() {
		$uniqueColumns = array();
		$primaryColumn = null;

		$sql = "CREATE TABLE IF NOT EXISTS {$this->_wpdb->prefix}{$this->_tableName} (\n";
		foreach ($this->_columns as $column) {
			$sql .= $column->toString() . ",\n";
			if ($column->isUnique()) {
				$uniqueColumns[] = $column->columnName();
			} else {
				$sql = rtrim($sql, ',');
			}
			if ($column->isPrimary()) {
				$primaryColumn = $column->columnName();
			} else {
				$sql = rtrim($sql, ',');
			}
		}
		foreach ($uniqueColumns as $uniqueColumn) {
			$sql .= "UNIQUE $uniqueColumn ( $uniqueColumn ),";
		}
		if ($primaryColumn) {
			$sql .= "PRIMARY KEY ($primaryColumn)";
		}
		$sql .= ") {$this->_wpdb->get_charset_collate()}";
		return $sql;
	}

	public function getRow(DB_Table_Row $row) {
		if (empty($row->id)) return;
		$properties = $row->getPublicProperties([]);
		$sql = "SELECT ";
		$sql .= rtrim(implode(", ", array_keys($properties)), ', ');
		$sql .= " FROM  {$this->_wpdb->prefix}{$this->_tableName}";
		$sql .= " WHERE ";
		foreach ($properties as $key => $value) {
			$sql .= "{$key} = '{$value}' AND ";
		}
		$sql = rtrim($sql, " AND " );
		return $sql;
	}

	public function getRows($criteria, $isCondition_AND = true, $group_by = false, $offset = false, $limit = false ) {
		$condition = 'OR';
		if ($isCondition_AND == true) {
			$condition = 'AND';
		}

		$sql = "SELECT * ";
		// $sql .= rtrim(implode(", ", array_keys($row)), ', ');
		$sql .= "FROM {$this->_wpdb->prefix}{$this->_tableName} ";

		if ($criteria != null) {
			if ( is_numeric($criteria) && $criteria > 0 ) {
				$sql .= " WHERE id = {$criteria}";
			} else {
				$sql .= " WHERE ";
				foreach ($criteria as $key => $value) {
					$sql .= "{$key} = '{$value}' {$condition} ";
				}
				$sql = rtrim($sql, " {$condition} " );
			}
		}
		$sql .= " ORDER BY id asc ";
		if ($group_by != false) {
			$sql .= "GROUP BY {$group_by}";
		}

		if ($limit != false || $offset != false ) {
			$sql .= "LIMIT {$offset},{$limit} ";
		}
		
		// if ($offset != false) {
		// 	$sql .= "OFFSET {$offset}";
		// }
		// var_dump($sql);
		return $sql;
	}

	public function addRow(DB_Table_Row $row) {
		$this->_rows[] = $row;
		$sql = "INSERT INTO {$this->_wpdb->prefix}{$this->_tableName} (";
		$sql .= rtrim(implode(", ", array_keys($row->getPublicProperties())), ', ');
		$sql .= ") VALUES ( '";
		$sql .= rtrim(implode("', '", array_values($row->getPublicProperties())), ', ');
		$sql .= "' )";
		return $sql;
	}

	// public function updateRow(DB_Table_Row $row, $selector = false) {
	// 	if (empty($row->id)) return;
	// 	$properties = $row->getPublicProperties();

	// 	$sql = "UPDATE {$this->_wpdb->prefix}{$this->_tableName} SET ";
	// 	foreach ($properties as $key => $value) {
	// 		if ($value != null) {
	// 			$sql .= "{$key} = '{$value}', ";
	// 		}
	// 	}
	// 	$sql = rtrim($sql, ', ');
	// 	$sql .= " WHERE id = {$row->id}";
	// 	return $sql;
	// }

	public function updateRow(DB_Table_Row $row, $selector = false) {

		$where = null;
		if (!$selector) {
			if (empty($row->id)) return;
			$where .= " WHERE id = {$row->id}";
		} else {
			$where .= " WHERE ";
			foreach ($selector as $item) {
				$where .= "{$item} = '" . $row->{$item} . "' AND";
			}
			$where = rtrim($where, ' AND');
		}
		$properties = $row->getPublicProperties();

		$sql = "UPDATE {$this->_wpdb->prefix}{$this->_tableName} SET ";
		foreach ($properties as $key => $value) {
			if ($value != null) {
				$sql .= "{$key} = '{$value}', ";
			}
		}
		$sql = rtrim($sql, ', ');
		$sql .= $where;

		return $sql;
	}

	public function deleteRow(DB_Table_Row $row) {
		if (empty($row->id)) return;
		$properties = $row->getPublicProperties([]);

		$sql = "DELETE FROM {$this->_wpdb->prefix}{$this->_tableName} WHERE ";
		$sql .= "id = {$row->id}";
		return $sql;
	}



	public function updateRows(array $updatedData, $criteria, $isCondition_AND = true ) {
		$condition = 'OR';
		if ($isCondition_AND == true) {
			$condition = 'AND';
		}
		$sql = "UPDATE {$this->_wpdb->prefix}{$this->_tableName} \n SET ";
		foreach ($update as $key => $value) {
			$sql .= "{$key} = {$value} ";
		}
		if ( is_numeric($criteria) && $criteria > 0 ) {
			$sql .= "WHERE id = {$criteria}";
		} else {
			$sql .= "WHERE";
			foreach ($updatedData as $key => $value) {
				$sql .= "{$key} = {$value} {$condition} ";
			}
			$sql = rtrim($sql, " {$condition} " );
		}
		return $sql;
	}

	public function deleteRows($criteria, $isCondition_AND = true ) {
		$condition = 'OR';
		if ($isCondition_AND == true) {
			$condition = 'AND';
		}
		$sql = "DELETE FROM $this->_wpdb->prefix}{$this->_tableName} WHERE ";
		if ( is_numeric($criteria) && $criteria > 0 ) {
			$sql .= "id = {$criteria}";
		} else {
			foreach ($updatedData as $key => $value) {
				$sql .= "{$key} = {$value} {$condition} ";
			}
			$sql = rtrim($sql, " {$condition} " );
		}
		return $sql;
	}
}