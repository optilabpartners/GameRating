<?php
namespace Optilab\Ratings\RequestHandlers;
/**
* Request handler
*/
abstract class RequestHandler
{

	public static function method_identifier() {
		$method = '';

		if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'delete'))  $method = "DELETE";
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'put'))  $method = 'PUT';
		if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'post'))  $method = 'POST';
		if ($_SERVER['REQUEST_METHOD'] === 'GET' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'get'))  $method = 'GET';

		return $method;
	}
}