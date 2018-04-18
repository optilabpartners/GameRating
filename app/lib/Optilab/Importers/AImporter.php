<?php
namespace Optilab\Importers;
use GuzzleHttp;

abstract class AImporter 
{
	protected $_url;
	protected $_response;

	public function __construct($url, $user = null, $pass = null) {
		$this->setup($url, $user, $pass);
	}

	private function setup($url, $user, $pass) {
		$client = new GuzzleHttp\Client();
		if ($user == null || $pass == null ) {
			$this->_response = $client->request('GET', $url);
		} else {
			$this->_response = $client->request('GET', $url, [
			    'auth' => [$user, $pass]
			]);
		}
		
	}

	
}