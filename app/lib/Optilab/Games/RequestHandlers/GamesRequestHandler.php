<?php
namespace Optilab\Games\RequestHandlers;
use Optilab\Ratings\RequestHandlers;
use Optilab\Games;
/**
* GamesRequest handler
*/
class GamesRequestHandler extends RequestHandlers\RequestHandler
{
	public static function games()
  	{
  		$info = json_decode( file_get_contents( "php://input" ) );
		$method = static::method_identifier();

		switch ($method) {
			case 'GET':
			$query = $_SERVER['QUERY_STRING'];
			parse_str($query, $query);
			$games = Games\Controllers\GamesController::fetchMany(['imported' => 0], true, false, $query['start'], $query['length'] );
			// var_dump(json_encode($games)); exit;
			echo json_encode(array(
				'draw'=> $query['draw'],
				'recordsTotal' => Games\Controllers\GamesController::count(),
				'recordsFiltered' => Games\Controllers\GamesController::countNotImported(),
				'data' => $games
			));
			wp_die();
			break;
		}
		
  	}

	public static function game() {
		$games = json_decode( file_get_contents( "php://input" ) );
		$method = static::method_identifier();
		// var_dump($games); exit;
		switch ($method) {
			case 'PUT':
			$message = null;
			foreach ($games as $gameId) {
				global $wpdb;
				$gameDetail = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}games WHERE id = $gameId");
				
				$home_team_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = 'team_id' AND meta_value = '{$gameDetail->hteam}'");
				$away_team_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = 'team_id' AND meta_value = '{$gameDetail->vteam}'");
				$home_team = get_term($home_team_id);
				$away_team = get_term($away_team_id);


				if ($home_team->errors || $away_team->errors) {
					echo json_encode(['result' => 0, 'message' => 'One or more teams doesn\'t exist']);
					wp_die();
				}
				$game_tag = array();

				if ($gameDetail->buzzer_beater != 0) {
					$term = get_term_by('slug', 'buzzer-beater', 'game_tag');
					$game_tag[] = $term->name;
				}

				if ($gameDetail->overtime != 0) {
					$term = get_term_by('slug', 'overtime', 'game_tag');
					$game_tag[] = $term->name;
				}
				// var_dump($gameDetail, $game_tag, date('Y-m-d', strtotime($gameDetail->game_date))); exit;

				$my_post = array(
					'post_title'    => wp_strip_all_tags($home_team->name . " vs " . $away_team->name),
					'post_content'  => " ",
					'post_status'   => 'publish',
					'post_type'		=> 'game',
					'post_author'   => 1,
				);

				$termid = $wpdb->get_var("SELECT {$wpdb->termmeta}.term_id FROM {$wpdb->termmeta} LEFT JOIN {$wpdb->term_taxonomy} ON {$wpdb->termmeta}.term_id = {$wpdb->term_taxonomy}.`term_id` WHERE {$wpdb->term_taxonomy}.parent != 0 AND meta_value = (SELECT MAX(meta_value) FROM {$wpdb->termmeta} WHERE date(meta_value) <= date('" . date('Y-m-d', strtotime($gameDetail->game_date)) . "') AND meta_key = 'start_date') AND meta_key = 'start_date' LIMIT 1;");

				$termid1 = $wpdb->get_var("SELECT {$wpdb->termmeta}.term_id FROM {$wpdb->termmeta} LEFT JOIN {$wpdb->term_taxonomy} ON {$wpdb->termmeta}.term_id = {$wpdb->term_taxonomy}.`term_id` WHERE {$wpdb->term_taxonomy}.parent != 0 AND meta_value = (SELECT MIN(meta_value) FROM {$wpdb->termmeta} WHERE date(meta_value) >= date('" . date('Y-m-d', strtotime($gameDetail->game_date)) ."') AND meta_key = 'end_date') AND meta_key = 'end_date' LIMIT 1;");

				// Insert the post into the database
				$new_game = wp_insert_post( $my_post );
				$game = Games\Controllers\GamesController::updateOne(
					new Games\Models\GameModel([ 
						'imported' => 1,
						'id' => $gameId,
					]
				));

				if ($termid == $termid1) {
					$term = get_term($termid);
					wp_set_object_terms( $new_game, [$term->name], 'game_season' );
				} else {
					$message .= "Unable to set game week for Game: " . $new_game . "\n";
				}
	
				wp_set_object_terms( $new_game, array($home_team->name, $away_team->name), 'team' );
				wp_set_object_terms( $new_game, $game_tag, 'game_tag' );
				wp_set_object_terms( $new_game, array('NBA'), 'game_org' );
				update_post_meta($new_game, 'game_id', $gameDetail->game_id);
				update_post_meta($new_game, 'game_date', date('Y-m-d', strtotime($gameDetail->game_date)));


			}
			echo json_encode(['result' => 1, 'message' => $message]);
			wp_die();
			break;
		}
	}
}