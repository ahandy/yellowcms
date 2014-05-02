<?php

/*
*---------------------------------------------------------
* Making sure there was no direct access to the script
*---------------------------------------------------------
*/ 
defined('SYSTEM_PATH') OR exit('No direct access to the script is allowed');


/*
*---------------------------------------------------------
* Beginning of system initialization jetpack file
*---------------------------------------------------------
*/

// Magic Quotes fix from PHP.net
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}


// Loading the core functions
if(!file_exists(CORE_PATH . 'core_functions.php')) {
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	echo "The core_functions.php file was not found in " . CORE_PATH;
	exit();
}
require_once CORE_PATH . 'core_functions.php';

// Loading the security class
$security = load_class('security', 'helpers');

// Loading the encryption class
$encryption = load_class('encryption', 'helpers');

// Loading the routes
$routes = load_class('routes', 'core');

// Loading the view
$view = load_class('view', 'core');

// Loading the db model
$db = load_class('db', 'core');

// Loading the database helper that avoids
// multiple connections
$db_helper = load_class('database', 'helpers');

// Loading the sessionmanager
$session = load_class('session', 'helpers');

// Loading the controller
if(!file_exists(CORE_PATH . 'controller.php'))
	throw503("Please make sure the controller.php file exists inside " . CORE_PATH);
require_once(CORE_PATH . 'controller.php');

if(!function_exists('getController')) {
	function getController() {
		return Controller::returnController();
	}
}

// Loading the correct controller
require_once(CONTROLLERS_PATH . $routes -> class_file_name);

// If the class does not exist, either throw an error redirect to homepage
if(!class_exists($routes -> class_name)) {
	if(DEBUG_ALL) throw503("Invalid class name {$routes -> class_name} for route");
	redirect(get_setting('index_page'));
}

// If a method was set, check if it exists if it doesn't throw an error.
if(isset($routes -> method_name) && !empty($routes -> method_name)) {
	if(!method_exists($routes -> class_name, $routes -> method_name)) {
		if(DEBUG_ALL) throw503("Invalid method name {$routes -> method_name} for class {$routes -> class_name} in route");
		$dontCallMethod = true;
	}
}

$controller_class = new $routes -> class_name;
// Launch the method in case all the parameters are met
if(isset($routes -> method_name) && !empty($routes -> method_name) && !isset($dontCallMethod)) {
	// checking if the number of given parameters is more than the required ones
	$reflecMethod = new ReflectionMethod($controller_class, $routes -> method_name);
	$number_of_arguments = $reflecMethod -> getNumberOfRequiredParameters();

	if(count($routes -> method_parameters) < $number_of_arguments) {
		if(DEBUG_ALL) throw503("Invalid number or arguments supplied for method {$routes -> method_name}");
	}
	else {
		call_user_func_array(array($controller_class, $routes -> method_name), $routes -> method_parameters);
	}
}

// loading default function
else if(!isset($routes -> method_name) || empty($routes -> method_name)) {
	if(isset($controller_class -> callFunction)) {
		$params = (isset($controller_class -> callParams)) ? $controller_class -> callParams : array();
		call_user_func_array(array($controller_class, $controller_class -> callFunction), $params);
	}
}
