<?php
class honeypot {
	public function checkForHoneypot() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(isset($_POST['_birthday'])) {
				if(!empty($_POST['_birthday'])) return true;
				else return false;
			}
			else return true;
		}

		else return true;
	}
}