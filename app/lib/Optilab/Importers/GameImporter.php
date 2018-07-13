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

}