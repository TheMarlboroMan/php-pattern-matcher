<?php
require("../../lib/autoload.php");

ini_set('error_reporting', -1);
ini_set('display_errors', 1);

//!Test case: path is the path to be tested, pattern_name is the name of the
//!pattern it must match. If left null, it is expected not to match anything.
class test_case {

	public				$path;
	public				$pattern_name;	

	public function		__construct($_path, $_pattern_name=null) {

		$this->path=$_path;
		$this->pattern_name=$_pattern_name;
	}
};

function check(\tools\pattern_matcher\matcher $_pm, array $_cases, &$_increment_on_success, &$_increment_on_failure, array &$_failed) {

	foreach($_cases as $v) {

		echo "Checking '$v->path'... ";
		$result=$_pm->match($v->path);
		$is_match=$result->is_match();

		if(!$is_match) {

			if(null!==$v->pattern_name) {
				echo "FAIL: Expectation failed, should have matched $v->pattern_name!!!".PHP_EOL;
				++$_increment_on_failure;
				$_failed[]=$v->path;
			}
			else {
				echo "OK, did not match".PHP_EOL;
				++$_increment_on_success;
			}
		}
		else {

			if(null===$v->pattern_name) {
				echo "FAIL: Expectation failed, should have not matched anything, matched $result->get_name()!!!".PHP_EOL;
				++$_increment_on_failure;
				$_failed[]=$v->path;
			}
			else  {
				if($v->pattern_name!==$result->get_name()) {
					echo "FAIL: Expectation failed, should have $v->pattern_name, matched $result->get_name()!!!".PHP_EOL;
					++$_increment_on_failure;
					$_failed[]=$v->path;
				}
				else
				{
					echo "OK, Matched $v->pattern_name with ".$result->get_name()."!".PHP_EOL;
					++$_increment_on_success;
				}
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
	'simple_two' => "hey/[value:alnum]/one_match/simple-path",
	'simple' => "simple-path",
	'url_like_two' => "something/[value:urllike]",
	'url_like_one' => "something/[value:urllike]/theother",
	'end-as-alnum' => "edit/[value:alnum]"
];
foreach($patterns as $k => $v) {
	$ep->add_pattern(new \tools\pattern_matcher\pattern($v, $k));
}

$cases=[
	new test_case('let/33/thing/12/and/hello/index.html', 'composite_path'),
	new test_case('let/another_string/hey', 'another_string'),
	new test_case('let/12/end', 'final'),
	new test_case('hey/friends12/one_match/simple-path', 'simple_two'),
	new test_case('simple-path', 'simple'),
	new test_case('something/and/theother', 'url_like_one'),
	new test_case('something/else', 'url_like_two'),
	new test_case('edit/thisthing12', 'end-as-alnum'),

	new test_case('let/hey/thing/12/and/hello/index.html'),
	new test_case('let/another_string/33'),
	new test_case('let/itall/end'),
	new test_case('hey/friends12/one_match/simple-pathfail'),
	new test_case('simple-path-fail'), 
	new test_case('something/and/something-else'),
	new test_case('edit')
];

$total_success_count=0;
$total_error_count=0;
$failed=[];

check($ep, $cases, $total_success_count, $total_error_count, $failed);
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