<?php
class CSRF {
	public function checkForCSRF() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$session = load_class('session', 'helpers');
			$session_csrf = $session -> get('current_time');

			if($session_csrf != $_POST['current_time'] || !isset($_POST['current_time'])) {
				if(DEBUG_ALL) {
					refresh();
					die();
				}
				else $_POST = array();
			}
		} 
	}
}