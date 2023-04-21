<?php
namespace Optilab\Importers;

/**
* team
*/
class TeamImporter extends AImporter
{
	private function insertTeam($team) {
		if (!term_exists($team->full_name, 'team')) {
			// create term
			$new_team = wp_insert_term(
				$team->full_name, // the term 
				'team', // the taxonomy
				array(
					'slug' => sanitize_title_with_dashes($team->full_name, null, 'save'),
				)
			);
			if (is_array($new_team)) {
				$nba = term_exists('NBA', 'game_org');
				update_term_meta( $new_team['term_id'], 'game_org', $nba['term_id'] );
				update_term_meta( $new_team['term_id'], 'short_name', $team->abbreviation );
				update_term_meta( $new_team['term_id'], 'team_id', $team->id );
			}
			
		}
	}

	public function insertTeams() {
		$body = json_decode($this->_response);
		$teams = $body->data;
			if (count($teams) > 0) {
			foreach ($teams as $team) {
				$this->insertTeam($team);
			}
		}

	}
}