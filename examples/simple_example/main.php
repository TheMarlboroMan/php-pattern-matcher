<?php
require("../../lib/autoload.php");

ini_set('error_reporting', -1);
ini_set('display_errors', 1);

function check(\tools\pattern_matcher\matcher $_pm, array $_strings, &$_increment_on_success, &$_increment_on_failure, array &$_failed, $_expectation) {

	foreach($_strings as $v) {

		echo "Checking for '$v'".PHP_EOL;
		
		$result=$_pm->match($v);

		$is_match=$result->is_match();
		if($_expectation != $is_match) {
			++$_increment_on_failure;
			$_failed[]=$v;
			echo "Expectation failed!!!".PHP_EOL;
		}
		else {
			++$_increment_on_success;
			if(!$is_match) {
				echo "Didn't match".PHP_EOL;			
			}
			else {
				echo "Matched!".PHP_EOL.print_r($result, true).PHP_EOL;
			}
		}
	}
}

//Manually creating patterns...
$ep=\tools\pattern_matcher\matcher::from_empty();
$patterns=[
	'composite_path' => 'let/[type:int]/thing/[id:int]/and/[val:alpha]/index.html',
	'another_string' => "let/another_string/[val:alpha]",
	'final' => "let/[type:int]/end",
	'simple_two' => "hey/[thing:alnum]/one_match/simple-path",
	'simple' => "simple-path",
	'end-as-alnum' => "edit/[seed:alnum]"
];
foreach($patterns as $k => $v) {
	$ep->add_pattern(new \tools\pattern_matcher\pattern($v, $k));
}

//These are crafted to work.
$success_cases=[
	'let/33/thing/12/and/hello/index.html',
	'let/another_string/hey',
	'let/12/end',
	'hey/friends12/one_match/simple-path',
	'simple-path', 
	'edit/thisthing12', 
];

//These are crafted to fail.
$failure_cases=[
	'let/hey/thing/12/and/hello/index.html',
	'let/another_string/33',
	'let/itall/end',
	'hey/friends12/one_match/simple-pathfail',
	'simple-path-fail', 
	'edit'
];

$total_success_count=0;
$total_error_count=0;
$failed=[];

check($ep, $success_cases, $total_success_count, $total_error_count, $failed, true);
check($ep, $failure_cases, $total_success_count, $total_error_count, $failed, false);
$view_failures=implode(PHP_EOL, $failed);

echo <<<R
Success cases: $total_success_count
Failure cases: $total_error_count

Failures: 
{$view_failures}

R;

/*
//From a file...
$fp=\tools\pattern_matcher\matcher::from_file('patterns.json');
check($fp, $strings);
*/