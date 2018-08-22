<?php
namespace Optilab\Games\Controllers;

use Optilab\DB;
use Optilab\Games\Models;

/**
* Games Controller
*/
class GamesController
{

	public static function bootstrap() {
		global $wpdb;
		DB\DB_Manager::execute(call_user_func(function() use ($wpdb) {
	        $rows = array(
	            new DB\DB_Table_Column('id', 'int', null, false, false, true, true ),
	            new DB\DB_Table_Column('game_id', 'varchar(20)', null, false, true ),
	            new DB\DB_Table_Column('game_url_code', 'varchar(20)', null, true ),
	            new DB\DB_Table_Column('game_date', 'datetime', null, true ),
	            new DB\DB_Table_Column('season', 'int', null, true ),
	            new DB\DB_Table_Column('hteam', 'int', null, true ),
	            new DB\DB_Table_Column('vteam', 'int', null, true ),
	            new DB\DB_Table_Column('buzzer_beater', 'int', 0 ),
	            new DB\DB_Table_Column('overtime', 'int', 0 ),
	            new DB\DB_Table_Column('imported', 'int', 0 ),
	            new DB\DB_Table_Column('post_id', 'int', null, false, true ),
	        );
	        $table = new DB\DB_Table('games', $rows, $wpdb);
	        return $table->create();
    	}));
	}

	public static function create(Models\GameModel $rating) {
		global $wpdb;
		$table = new DB\DB_Table('games');
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

	public static function fetchMany($criteria = null, $condition_AND = true, $group_by = false, $offset = false, $limit = false ) {
		$table = new DB\DB_Table('games');
		$rows = DB\DB_Manager::fetchMany(call_user_func(function() use ($table, $criteria, $group_by, $condition_AND, $offset, $limit) {
			return $table->getRows($criteria, $condition_AND, $group_by, $offset, $limit);
		}));

		$games = array();
		foreach ($rows as $row) {
			$games[] =  new Models\GameModel((array) $row);
		}
		return $games;
	}

	public static function count() {
		global $wpdb;
		$val = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'games');
		return $val;
	}

	public static function countNotImported() {
		global $wpdb;
		$val = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'games' . ' WHERE imported = 0');
		return $val;
	}

	public static function updateOne(Models\GameModel $game, $selector = false) {
		global $wpdb;
		$table = new DB\DB_Table('games');
		$row = DB\DB_Manager::update(call_user_func(function() use ($table, $game, $selector) {
			return $table->updateRow($game, $selector);
		}));
	}

	public static function deleteOne(Models\GameModel $game) {
		global $wpdb;
		$table = new DB\DB_Table('games');
		$row = DB\DB_Manager::deleteOne(call_user_func(function() use ($table, $game) {
			return $table->deleteRow($game);
		}));
	}
}