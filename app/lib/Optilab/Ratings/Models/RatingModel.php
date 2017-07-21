<?php
namespace Optilab\Ratings\Models;
use Optilab\DB;
/**
* ToplistModel Class
*/
class RatingModel extends DB\DB_Table_Row
{
	public $id;
	public $post_id;
	public $value;
	function __construct($data = null)
	{
		if (is_numeric($data)) {
			$this->id = $data;
		} else {
			parent::__construct($data);
		}

	}

	function getID() {
		return $this->id;
	}

	function getPostID() {
		return $this->post_id;
	}

	function setPostID($post_id) {
		$this->post_id = $post_id;
	}

	function getValue() {
		return $this->value;
	}

	function setValue($value) {
		$this->value = $value;
	}

}