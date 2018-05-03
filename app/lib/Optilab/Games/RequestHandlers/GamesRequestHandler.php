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
				'recordsFiltered' => Games\Controllers\GamesController::count(),
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
			foreach ($games as $gameId) {
				global $wpdb;
				$gameDetail = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}games WHERE id = $gameId");
				
				$home_team_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = 'team_id' AND meta_value = '{$gameDetail->hteam}'");
				$away_team_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = 'team_id' AND meta_value = '{$gameDetail->vteam}'");
				$home_team = get_term($home_team_id);
				$away_team = get_term($away_team_id);


				$my_post = array(
					'post_title'    => wp_strip_all_tags($home_team->name . " vs " . $away_team->name),
					'post_content'  => " ",
					'post_status'   => 'publish',
					'post_author'   => 1,
					'tax_input'    => array(
						'non_hierarchical_tax'  => array($home_team_id, $away_team_id),
					),
					'meta_input'   => array(
						'game_id' => $game->game_id,
						'game_date' => date('Y-m-d', strtotime($game->game_date)),
					),
				);
 
				// Insert the post into the database
				$new_game = wp_insert_post( $my_post );
				$game = Games\Controllers\GamesController::updateOne(
					new Games\Models\GameModel([ 
						'imported' => 1,
						'id' => $gameId,
					]
				));

			}
			
			if ($game instanceof Games\Models\gameModel) {
				echo ['result' => 1];
			}
			wp_die();
			break;
		}
	}
}