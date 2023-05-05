<?php
namespace Optilab\Importers;

/**
* Game
*/
class GameImporter extends AImporter
{
	private function insertGame($game) {
		// create game
		//echo($game->id);
		$args = array(
			'post_type'   => 'game',
			'meta_query'  => array(
				array(
				'key' => 'api_id',
				'value' => $game->id
				)
			)
		);
		$my_query = new \WP_Query( $args );
		//var_dump(empty($my_query->have_posts()));die;
		if( empty($my_query->have_posts()) ) {
			$longTitle = $game->home_team->full_name . ' vs ' . $game->visitor_team->full_name;
		
			//Convert game date
			$gameDate = date("Y-m-d", strtotime($game->date));
			$post = array(
				'post_title' => $longTitle,
				'post_type' => 'game',
				'post_status' => 'publish',
				'meta_input' => array(
					'game_date' => $gameDate,
					'api_id' => $game->id
					)
			);
			
			$new_game = wp_insert_post($post);

			wp_set_object_terms( $new_game, 'NBA', 'game_org', true );
			//Get the latest season
			wp_set_object_terms( $new_game, 'NBA 2022-23', 'game_season', true );
			//Set the teams to the game
			wp_set_object_terms( $new_game, $game->home_team->full_name, 'team', true );
			wp_set_object_terms( $new_game, $game->visitor_team->full_name, 'team', true );
			
			//Check which week the game is played
			//The 233 needs to be changed so it gets the season that matches the games that are imported.
			$game_weeks = get_terms(
				array(
					'taxonomy'   => 'game_season',
					'parent'     => 233,
					'hide_empty' => false,
					)
				);
				
			if ( ! empty( $game_weeks ) && is_array( $game_weeks ) ) {
				foreach ( $game_weeks as $game_week ) {
					$startsAt = strpos($game_week->name, "(") + strlen("(");
					$endsAt = strpos($game_week->name, ")", $startsAt);
					$result = substr($game_week->name, $startsAt, $endsAt - $startsAt);
					$game_week_format = explode(" - ", $result);

					if (($gameDate >= $game_week_format[0]) && ($gameDate <= $game_week_format[1])) {
						wp_set_object_terms( $new_game, $game_week->name, 'game_season', true );
					}
				}
			}
		}
	}

	public function insertGames() {
		$body = json_decode($this->_response);
		$games = $body->data;
			if (count($games) > 0) {
			foreach ($games as $game) {
				$this->insertGame($game);
			}
		}
	}

	public function returnNumberOfPages() {
		$body = json_decode($this->_response);
		$pages = $body->meta->total_pages;

		return $pages;
	}
}