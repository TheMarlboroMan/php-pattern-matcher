<?php
require("lib/autoload.php");

$pm=new \tools\pattern_matcher;
//print_r($pm->match("let/11/end"));
$strings=["let/another_string/end", "la", "simple-path"];

foreach($strings as $v) {
	echo "CHECK FOR '$v'".PHP_EOL;
	print_r($pm->match($v));
}
