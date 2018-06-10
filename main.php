<?php

/*
//TODO: This could be an example format...
path/{id: number}/and/{name:string}/finally/{something:any}/and_done

number: 0-9
string: a-zA-z-_
any: .

translated to

path/[0-9]+/and/[a-zA-Z\-_]+/finally/[.+]/and_done
*/

class pattern {

	private	$raw_pattern;
	private	$id;

	public function		__construct($_p, $_i) {

		$this->raw_pattern=$_p;
		//TODO: Turn raw_pattern into regex.
		$this->id=$_i;
	}

	public function		matches($_input) {

		//TODO... This should actually do a regex.
		return $_input==$this->raw_pattern;
	}
}

class pattern_matcher {

	private 		$list=null;

	//TODO: Build from file.
	public function 	__construct() {

		$this->setup_list();
	}

	public function 	match($_input) {

		foreach($this->list as $k => $v) {
			if($v->matches($_input)) {

				//TODO: Return a dedicated result object
				//that may include the parameters.
				return $v;
			}
		}

		return null;
	}

	//TODO: Build from file.
	private function 	setup_list() {

		//TODO: Turn each pattern into a regex.
		$this->list=[
			new pattern("/simple/path", "simple_path"),
			new pattern("another_string", "another_string"), 
		];
	}
}

$pm=new pattern_matcher;
print_r($pm->match("another_string"));
