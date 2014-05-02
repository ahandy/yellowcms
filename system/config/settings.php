<?php
/*
*---------------------------------------------------------
* Making sure there was no direct access to the script
*---------------------------------------------------------
*/ 
defined('SYSTEM_PATH') OR exit('No direct access to the script is allowed');

/*
*---------------------------------------------------------
* Settings file:
* You may edit the values, however please
* pay close attention to what you are editing
*---------------------------------------------------------
*/ 


// Setting the config array
$_setting = array();

// The default homepage in case nothing was passed through the URL
$_setting['index_page'] = 'index';

// The hashing salt to be used in case needed
$_setting['hash_salt'] = md5('cms123');

// Chars to allow inside the URL
// Do not edit this, preferrably
$_setting['allowed_url_chars'] = 'a-z 0-9~%.:_\-';

// Automatically load the header and footer files
$_setting['load_header'] = TRUE;
$_setting['load_footer'] = TRUE;
$_setting['header_path'] = 'common/header.php';
$_setting['footer_path'] = 'common/footer.php';

// Database information start
// if local
if(IS_LOCAL) {
	$_setting['database_type'] = 'mysql';
	$_setting['database_host'] = '127.0.0.1';
	$_setting['database_name'] = 'xxx';
	$_setting['database_user'] = 'xx';
	$_setting['database_pass'] = 'xxxx';
}
// else if online
else {
	$_setting['database_type'] = 'mysql';
	$_setting['database_host'] = 'xx';
	$_setting['database_name'] = 'xx';
	$_setting['database_user'] = 'xxx';
	$_setting['database_pass'] = 'xxxxx';
}
// Database information end


// Session information start
// The name to be used for the session and for the cookie
$_setting['session_name'] = 'MVCSESSION';
// Session max lifetime
$_setting['session_maxlifetime'] = 5000;
// HTTPS only?
$_setting['session_https'] = false;
// Save path (should be outside public_html)
$_setting['session_save_path'] = ABSOLUTE_ROOT_PATH . 'system/sessions';
// Encrypt session?
$_setting['session_encrypt'] = false;
// Session information end

// Automatic CSRF token checker
$_setting['csrf_checker'] = FALSE;

// Automatic honeypot checker
$_setting['honeypot'] = FALSE;

// Mail options start
$_setting['smtp_host'] = 'xxxxxxxx';
$_setting['smtp_port'] = 'xxxxxxx';
$_setting['smtp_user'] = 'xxxxxxxx';
$_setting['smtp_pass'] = 'xxxxxxxx';
$_setting['smtp_from'] = 'xxxxxxxxx';
$_setting['smtp_from_name'] = 'xxxxxxxxx';
// Mail options end