<?php
class HTTPAnswer {  
  	private $Divider;
  
	function __construct() {
		if (!(defined("HTTP_ANSWER_STATUS_200"))) {
									
			define("HTTP_ANSWER_STATUS_200",'HTTP/1.0 200 OK');
			define("HTTP_ANSWER_STATUS_201",'HTTP/1.0 201 Created');
			define("HTTP_ANSWER_STATUS_202",'HTTP/1.0 202 Accepted');
			
			define("HTTP_ANSWER_STATUS_204",'HTTP/1.0 204 No Content');
			
			define("HTTP_ANSWER_STATUS_400",'HTTP/1.0 400 Bad Request');
			define("HTTP_ANSWER_STATUS_401",'HTTP/1.0 401 Unauthorized');
			
			define("HTTP_ANSWER_STATUS_403",'HTTP/1.0 403 Forbidden');			
			define("HTTP_ANSWER_STATUS_404",'HTTP/1.0 404 Not Found');
			define("HTTP_ANSWER_STATUS_405",'HTTP/1.0 405 Method Not Allowed');
			
			define("HTTP_ANSWER_STATUS_408",'HTTP/1.0 408 Request Timeout');
			
			define("HTTP_ANSWER_STATUS_413",'HTTP/1.0 413 Request Entity Too Large');
			define("HTTP_ANSWER_STATUS_414",'HTTP/1.0 414 Request-URI Too Long');
			
			define("HTTP_ANSWER_STATUS_500",'HTTP/1.0 500 Internal Server Error');
			define("HTTP_ANSWER_STATUS_501",'HTTP/1.0 501 Not Implemented');
			
			define("HTTP_ANSWER_STATUS_503",'HTTP/1.0 503 Service Unavailable');			
		}	
	}
	
	function HTTPAnswer($StatusCode,$Data,$DieAfterRun = false) {
		if (headers_sent()) {
			// TODO: Logovat chybu!!!
			echo "FATAL ERROR";
			if ($DieAfterRun) die();
			return false;
		}
		header($StatusCode); /*
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers 
		header("Content-Type: Content-Type: text/plain; charset=utf-8");
*/		echo $Data;
		if ($DieAfterRun) die();
	}
	
}
