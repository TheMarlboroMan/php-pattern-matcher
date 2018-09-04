<?php
require("../../lib/autoload.php");

function check(\tools\pattern_matcher\matcher $_pm, array $_strings) {

	foreach($_strings as $v) {
		echo "Checking for '$v'".PHP_EOL;
		$result=$_pm->match($v);
		if(!$result->is_match()) {
			echo "Didn't match".PHP_EOL;
		}
		else {
			echo "Matched!".PHP_EOL.print_r($result, true).PHP_EOL;
		}
	}
}

$strings=["simple-path-lol", 'simple-path', 'let/another_string/say'];

//Manually creating patterns...
$ep=\tools\pattern_matcher\matcher::from_empty();
$patterns=['composite_path' => 'let/[type:int]/thing/[id:int]/and/[val:alpha]/index.html',
	'another_string' => "let/another_string/[val:alpha]",
	'final' => "let/[type:int]/end",
	'simple' => "simple-path"];

foreach($patterns as $k => $v) {
	$ep->add_pattern(new \tools\pattern_matcher\pattern($v, $k));
}
check($ep, $strings);

//From a file...
$fp=\tools\pattern_matcher\matcher::from_file('patterns.json');
check($fp, $strings);
