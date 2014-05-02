<?php 
if(isset($this -> success)) echo "<div class='success-add'>Successfully added.</div>";

$form = load_class("form", "helpers");
// if(isset($this -> formErrors)) echo $form -> parseFormError($this -> formErrors);
$form -> parseForm($this -> form);