<?php
namespace Optilab\Games\Models;
use Optilab\DB;
/**
* TeamModel Class
*/
class GameModel extends DB\DB_Table_Row
{
	public $id;
	public $game_id;
	public $game_url_code;
	public $game_date;
	public $season;
	public $hteam;
	public $vteam;
	public $buzzer_beater;
	public $overtime;
	public $imported;
	function __construct($data = null)
	{
		if (is_numeric($data)) {
			$this->id = $data;
		} else {
			parent::__construct($data);
		}

	}

	function getID() {
		return $this->id;
	}

	function getTeamID() {
		return $this->game_id;
	}

	function setTeamID($game_id) {
		$this->game_id = $game_id;
	}

}