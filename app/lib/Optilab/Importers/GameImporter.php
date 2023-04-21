<?php
namespace Optilab\Importers;

/**
* Game
*/
class GameImporter extends AImporter
{
	private function insertGame($game) {
		//if (!post_exists($game->id)) {
			// create game
			$longTitle = $game->home_team->full_name . ' vs ' . $game->visitor_team->full_name;
			$orgTaxonomy = 'game_org';
			$post = array(
				'post_title' => $longTitle,
				'post_type' => 'game',
				'tax_input' => array('game_org' , array('nba'))
			);
			
			$new_game = wp_insert_post($post);
			//wp_set_object_terms( $new_game, array( $term_id1, $term_id2 ), 'tax_slug' );
			var_dump($new_game);die;
			
			/*if (is_array($new_team)) {
				$nba = term_exists('NBA', 'game_org');
				update_term_meta( $new_team['term_id'], 'game_org', $nba['term_id'] );
				update_term_meta( $new_team['term_id'], 'short_name', $game->abbreviation );
				update_term_meta( $new_team['term_id'], 'team_id', $game->id );
				var_dump($new_team['term_id'], $game->id );
			}*/
		//}
	}

	public function insertGames() {
		$body = json_decode($this->_response);
		//var_dump($body);die;
		$games = $body->data;
			if (count($games) > 0) {
			foreach ($games as $game) {
				$this->insertGame($game);
			}
		}

	}
}