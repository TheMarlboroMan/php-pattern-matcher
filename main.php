<?php
require("lib/autoload.php");

$pm=new \tools\pattern_matcher;
print_r($pm->match("let/another_string"));
