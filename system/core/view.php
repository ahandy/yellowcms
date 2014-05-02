<?php

/*
*---------------------------------------------------------
* Making sure there was no direct access to the script
*---------------------------------------------------------
*/ 
defined('SYSTEM_PATH') OR exit('No direct access to the script is allowed');


/*
*---------------------------------------------------------
* View class that would output the file
*---------------------------------------------------------
*/ 
class View {
	public function load($view = NULL, $header = NULL, $footer = NULL, $folder = NULL) {
		if($header  === NULL) $header = get_setting('load_header');
		if($footer  === NULL) $footer = get_setting('load_footer');
		
		if($header) {
			$header = (is_bool($header)) ? get_setting('header_path') : $header;

			$path = (is_null($folder)) ? VIEWS_PATH . $header : VIEWS_PATH . $folder . '/' . $header;

			if(!file_exists($path)) {
				if(DEBUG_ALL) throw503("Please make sure the view header file has a correct path in the settings.php (Current: {$path})");
			}

			else require_once $path;
		}

		if(strpos($view, '.php') === FALSE && strpos($view, '.htm') === FALSE && strpos($view, '.html') === FALSE) {
			if(!is_null($folder)) $view = $folder . '/' . $view . '.php';
			else $view = $view . '.php';
		}

		else $view = $folder . '/' . $view;

		if(!file_exists(VIEWS_PATH . $view)) {
			if(DEBUG_ALL) throw503("View file was not found in " . VIEWS_PATH . $view);
			echo "View file {$view} could not be fount.";
		}

		else {
			if(get_setting('csrf_checker') || get_setting('honeypot')) {
				// Adding a CSRF token checker in case of a form 
				// Loading the Simple HTML Parser
				$htmlParser = load_file(HELPERS_PATH . 'simple_html_dom_node.php');
				ob_start();
				include(VIEWS_PATH . $view);
				$html_with_php = ob_get_contents();
				ob_end_clean();
				$html = str_get_html($html_with_php);
				if(empty($html)) return;
				$formCheck = $html -> find('form');
				if(isset($formCheck)) {
					$session = load_class('session', 'helpers');
					$security = load_class('security', 'helpers');

					$csrf = $security -> returnCSRF();
					$session -> set('current_time', $csrf);
					
					$inputToAdd = '';

					if(get_setting('csrf_checker')) $inputToAdd .= "<input type='hidden' name='current_time' value='{$csrf}' />";
					if(get_setting('honeypot')) $inputToAdd .= "<input type='text' name='_birthday' autocomplete='off' style='position: absolute; left: -9999px;' />";
					foreach($formCheck as &$form) {
						$form -> innertext = $inputToAdd . $form -> innertext();
					}
				}
				echo $html;
			}

			else {
				require_once VIEWS_PATH . $view;
			}
		}

		if($footer) {
			$footer = (is_bool($footer)) ? get_setting('footer_path') : $footer;
			$path = (is_null($folder)) ? VIEWS_PATH . $footer : VIEWS_PATH . $folder . '/' . $footer;

			if(!file_exists($path)) {
				if(DEBUG_ALL) throw503("Please make sure the view footer file has a correct path in the settings.php (Current: " . VIEWS_PATH . $path . ")");
			}

			else require_once $path;
		}
	}
}