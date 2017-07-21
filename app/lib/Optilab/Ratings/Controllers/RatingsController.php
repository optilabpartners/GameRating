<?php
namespace Optilab\Ratings\Controllers;

use Optilab\DB;
use Optilab\Ratings\Models;

/**
* Ratings Controller
*/
class RatingsController
{
	
	function __construct()
	{
		
	}

	public static function bootstrap() {
		global $wpdb;
		DB\DB_Manager::execute(call_user_func(function() use ($wpdb) {
	        $rows = array(
	            new DB\DB_Table_Column('id', 'int', null, false, false, true, true ),
	            new DB\DB_Table_Column('post_id', 'int', null, true ),
	            new DB\DB_Table_Column('value', 'int', null, true ),
	        );
	        $table = new DB\DB_Table('ratings', $rows, $wpdb);
	        return $table->create();
    	}));
	}

	public static function create(Models\RatingModel $rating) {
		global $wpdb;
		$table = new DB\DB_Table('ratings');
		$result = DB\DB_Manager::insert(call_user_func(function() use ($rating, $table) {
			return $table->addRow($rating);
		}));
		if (is_int($result)) {
			$rating->id = (int)$result;
			return $rating;
		} else {
			return false;
		}
	}

	public static function fetchAverageRating($post_id ) {
		$row = DB\DB_Manager::fetchMany("SELECT sum(val)/sum(cnt) as avg FROM (
			SELECT COUNT(value) as cnt, value*COUNT(value) as val FROM wp_ratings WHERE post_id = {$post_id} GROUP BY VALUE
			) a");
		return round($row[0]->avg, 1);
	}
}