<?php

/*
*---------------------------------------------------------
* Making sure there was no direct access to the script
*---------------------------------------------------------
*/ 
defined('SYSTEM_PATH') OR exit('No direct access to the script is allowed');

global $routes;
$routes = array();

/*
*---------------------------------------------------------
* Here you can define custom routes
*---------------------------------------------------------
*/ 

/*
* Must be in the syntax of:
* $route['route_url'] = array('controller' => 'controller_name',
*							  'class_name' => 'class_name',
*							  (OPTIONAL) 'method' => 'method_name',
*							  (OPTIONAL) 'parameters' => array('parameters'));
*/


foreach($routes as $route) {
	if(empty($route['controller']) || empty($route['class_name'])) {
		throw503("Please make sure your routes are properly set.");
	}
}