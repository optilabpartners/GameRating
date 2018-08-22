<?php
namespace Optilab\Importers;
use Optilab\Games;
/**
* team
*/
class GameImporter extends AImporter
{
	private function insertGame($game, $season) {
		global $wpdb;

		$count = $wpdb->get_var($wpdb->prepare( 
			"SELECT COUNT(*) FROM $wpdb->termmeta WHERE meta_key = 'team_id' AND ( meta_value = '%s' OR meta_value = '%s')", $game->hTeam->teamId, $game->vTeam->teamId));

		if (!$count || $count < 2)
			return false;

		if ( (int)$game->seasonStageId < 2 ) {
			return false;
		}

		$post_id = $wpdb->get_var($wpdb->prepare( 
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'game_id' AND meta_value = '%s'", $game->gameId));

		if ($post_id) {
			return false;
		}
		
		$overtime = 0;
		if (strpos( $game->nugget->text, 'overtime' )) {
			$overtime = 1;
		}
		$game = Games\Controllers\GamesController::create(
			new Games\Models\GameModel([ 
				'game_id' => $game->gameId,
				'game_url_code' => $game->gameUrlCode,
				'game_date' => $game->startDateEastern,
				'season' => $season,
				'hteam' => $game->hTeam->teamId,
				'vteam' => $game->vTeam->teamId,
				'buzzer_beater' => ($game->isBuzzerBeater)?1:0,
				'overtime' => $overtime
			]
		));
	}

	public function insertGames($season) {
		$body = json_decode($this->_response->getBody());
		$games = $body->league->standard;
		if (count($games) > 0) {
			foreach ($games as $game) {
				$this->insertGame($game, $season);
			}
		}
	}

	private function updateGame($game, $season) {
		global $wpdb;

		$count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}games WHERE game_id = '%s'", $game->gameId));

		if (!$count) {
			return false;
		}

		$overtime = 0;
		if (strpos( $game->nugget->text, 'overtime' )) {
			$overtime = 1;
		}
		
		// var_dump($game->gameId, $game->nugget->text);

		$game = Games\Controllers\GamesController::updateOne(
			new Games\Models\GameModel([ 
				'game_id' => $game->gameId,
				'game_url_code' => $game->gameUrlCode,
				'game_date' => $game->startDateEastern,
				'season' => $season,
				'hteam' => $game->hTeam->teamId,
				'vteam' => $game->vTeam->teamId,
				'buzzer_beater' => ($game->isBuzzerBeater)?1:0,
				'overtime' => $overtime
			]
		), ['game_id']);

		$post_id = $wpdb->get_var("SELECT post_id FROM {$wpdb->prefix}games WHERE game_id = '{$game->gameId}' LIMIT 1");

		if ( (bool)$post_id ) {

			$game_tag = array();

			if ($game->isBuzzerBeater) {
				$term = get_term_by('slug', 'buzzer-beater', 'game_tag');
				$game_tag[] = $term->name;
			}

			if ($overtime) {
				$term = get_term_by('slug', 'overtime', 'game_tag');
				$game_tag[] = $term->name;
			}

			wp_set_object_terms( $post_id, $game_tag, 'game_tag' );
		}
		

	}

	/**
	 * Update Games
	 **/
	public function updateGames($season) {
		$body = json_decode($this->_response->getBody());
		$games = $body->league->standard;

		if (count($games) > 0) {
			foreach ($games as $game) {
				$this->updateGame($game, $season);
			}
		}

		exit();
	}

}