<?php
class Text {
	// Credits to Justin Kelly for the following truncate function
	function truncate($string, $limit, $break=".", $pad="...") {
	  // return with no change if string is shorter than $limit
	  if(strlen($string) <= $limit) return $string;

	  // is $break present between $limit and the end of the string?
	  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
	    if($breakpoint < strlen($string) - 1) {
	      $string = substr($string, 0, $breakpoint) . $pad;
	    }
	  }

	  return $string;
	}

	function alphaNumeric($string) {
		 return preg_replace("/[^a-zA-Z0-9]+/", "", $string);
	}

	public function returnDisplay($value) {
		$ar['clean'] = $value;

		$valueAr = explode("_", $value);
		$valueAr = array_map('ucfirst', $valueAr);
		$ar['display'] = implode(" ", $valueAr);
		return $ar;
	}
}

?>