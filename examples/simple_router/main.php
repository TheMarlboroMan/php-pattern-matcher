<?php
require("../../lib/autoload.php");

abstract class entrypoint {

	public abstract function execute(\tools\pattern_matcher\result $_res);
}

class entrypoint_a {
	public function execute(\tools\pattern_matcher\result $_res) {
		return "This is entrypoint a talking";
	}
}

class entrypoint_b {

	public function execute(\tools\pattern_matcher\result $_res) {

		return "This is entrypoint b talking, with id set to ".$_res->get_parameter('id')->get_value();
	}
}

$uri=$_SERVER['REQUEST_URI'];
$fp=\tools\pattern_matcher\matcher::from_file('config/routing.json');
$match=$fp->match($uri);

if(!$match->is_match()) {
	die('This would do a 404');
}
else {

	$match_name=$match->get_name();
	if(!class_exists($match_name)) {
		die('This would be a 404');
	}

	$ep=new $match_name;
	die($ep->execute($match));
}
