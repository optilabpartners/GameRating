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
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$this->_response = curl_exec($ch);
			//var_dump($this->_response);die;
			//curl_close($ch);
			//$this->_response = $client->request('GET', $url);
		} else {
			$this->_response = $client->request('GET', $url, [
			    'auth' => [$user, $pass]
			]);
		}
		
	}

	
}