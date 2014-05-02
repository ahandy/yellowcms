<?php
class Logout extends Controller {
	public function __construct() {
		parent::__construct();
		$sessionObj = load_class('session', 'helpers');
		$sessionObj -> clearSession();
		redirect(HTTP . login);
	}
}