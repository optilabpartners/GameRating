<?php
namespace Optilab\Importers;

/**
* team
*/
class TeamImporter extends AImporter
{
	private function insertTeam($team) {
		if ( !$team->isNBAFranchise ) {
			return false;
		}
		$nba = term_exists('NBA', 'game_org');
		if (!term_exists($team->fullName, 'team')) {
			// create term
			$new_team = wp_insert_term(
				$team->fullName, // the term 
				'team', // the taxonomy
				array(
					'slug' => sanitize_title_with_dashes($team->fullName, null, 'save'),
				)
			);
			if (is_array($new_team)) {
				update_term_meta( $new_team['term_id'], 'game_org', $nba['term_id'] );
				update_term_meta( $new_team['term_id'], 'short_name', $team->nickname );
				update_term_meta( $new_team['term_id'], 'team_id', $team->teamId );
				var_dump($new_team['term_id'], $team->teamId );
			}
			
		} else {
			global $wpdb;
			$term_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->terms} WHERE name = '{$team->fullName}'");
			if (!get_term_meta($term_id, 'team_id', true)) {
				update_term_meta( $term_id, 'team_id', $team->teamId );
			}
			update_term_meta( $term_id, 'game_org', $nba['term_id'] );
			update_term_meta( $term_id, 'short_name', $team->nickname );
		}
	}

	public function insertTeams() {
		$body = json_decode($this->_response->getBody());
		$teams = $body->league->standard;
		if (count($teams) > 0) {
			foreach ($teams as $team) {
				$this->insertTeam($team);
			}
		}

	}
}