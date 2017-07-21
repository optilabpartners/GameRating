<?php
namespace Optilab\DB;
/**
* Db Wrapper
*/
class DB_Manager
{
	public static function execute($sql) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		return dbDelta( $sql );
	}

	public static function insert($sql) {
		global $wpdb;
		$wpdb->query($sql);
		return $wpdb->insert_id;
	}

	public static function fetchOneField($sql) {
		global $wpdb;
		$wpdb->get_var($sql);
	}

	public static function fetchOne($sql) {
		global $wpdb;
		$wpdb->get_row($sql);
	}

	public static function fetchMany($sql) {
		global $wpdb;
		return $wpdb->get_results($sql);
	}

	public static function deleteOne($sql) {
		global $wpdb;
		$wpdb->query($sql);
	}

	public static function update($sql) {
		$return = self::execute($sql);
		return $return;
	}
}