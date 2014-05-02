<?php
class Url {
	public function is_url($url) {
		if(!filter_var($url, FILTER_VALIDATE_URL)) return false;
		else return true;
	}
}