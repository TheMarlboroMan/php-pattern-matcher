<?php
require("../../lib/autoload.php");


class entrypoint_a {
	public function execute() {
		return "This is entrypoint a talking";
	}
}

class entrypoint_b {
	public function do_stuff($_id, $_compulsory, $_optional=null) {
		return "This is entrypoint b talking, with id set to ".$_id.", compulsory to ".$_compulsory." and optional to ".$_optional;
	}
}

$fp=\tools\pattern_matcher\matcher::from_file('config/routing.json');
$uri=$_SERVER['REQUEST_URI'];
$match=$fp->match($uri);

if(!$match->is_match()) {
	die('This would do a 404');
}
else {
	$match_name=$match->get_name();
	if(!class_exists($match_name)) {
		die('This would be a 404');
	}

	//Get all metadata information...
	$method=$match->get_metadata()->method; //This will be the method name to call on the class.
	$params=[]; //These will be the parameters to send.

	foreach($match->get_metadata()->params as $k => $param) {
		switch($param->source) {
			case 'post':
				if(!$param->optional && !isset($_POST[$param->name])) {
					die('ERROR: POST '.$param->name.' is NOT optional');
				}
				$params[]=isset($_POST[$param->name]) ? $_POST[$param->name] : $param->default;
			break;
			case 'param':
				if(!$param->optional && !$match->has_parameter($param->name)) {
					die('ERROR: Parameter '.$param->name.' is NOT OPTIONAL');
				}
				$params[]=$match->get_parameter($param->name)->get_value();
			break;
			default:
				die('Unknown parameter source '.$param->source);
			break;
		}
	}

	$ep=new $match_name;
	//TODO: We should check the function exists too!.
	die(call_user_func_array([$ep, $method], $params));
}
