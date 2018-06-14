<?php
namespace tools\pattern_matcher;

//!A pattern matcher. Must be fed a group of patterns and works by calling
//!match against a string, upon which a result object will be returned.
class matcher {

	private 		$list=null;

	//!parses the JSON file in $_filename (absolute path) and returns a
	//!matcher with all patterns set. The json filename expects to contain
	//!a single node "patterns", with an array of objects composed of
	//!"pattern" and "name"  (something like {"patterns":[{"pattern":"x", "name":"y"}]}
	//!will throw upon any error in the input, including sanity related ones.
	public static function	from_file($_filename) {

		if(!file_exists($_filename) || !is_file($_filename)) {
			throw new pattern_matcher_exception("file '$_filename' does not exist");
		}

		$json=json_decode(file_get_contents($_filename));
		if(null===$json) {
			throw new pattern_matcher_exception("file '$_filename' does not contain valid json");
		}

		if(!property_exists($json, "patterns")) {
			throw new pattern_matcher_exception("file '$_filename' does not contain the json node 'patterns'");
		}

		$list=[];

		foreach($json->patterns as $k => $v) {

			if(!property_exists($v, "pattern") || !property_exists($v, "name")) {
				throw new pattern_matcher_exception("file '$_filename' contains malformed patterns");
			}

			$list[]=new pattern($v->pattern, $v->name);
		}

		$res=new matcher($list);
		$res->check_sanity();
		return $res;
	}

	//!Creates an empty matcher that can be filled with calls to add_pattern.
	public static function	from_empty() {

		$res=new matcher([]);
		return $res;
	}

	//!Manually adds a pattern to the list. Will throw if the insert failed
	//!due to sanity checks. The added pattern will be removed in that case.
	public function add_pattern(pattern $_p) {

		$this->list[]=$_p;

		try {
			$this->check_sanity();
		}
		catch(pattern_matcher_exception $e) {
			array_pop($this->list);
			throw $e;
		}
	}

	//!matches the input string against all patterns. Returns a result
	//!object.
	public function 	match($_input) {

		foreach($this->list as $k => $v) {

			$r=$v->matches($_input);
			if(null!==$r) {
				return $r;
			}
		}

		return new result(false, null, []);
	}

	//!Constructor. Needs an array of patterns.
	private function 	__construct(array $_l) {

		$this->list=$_l;
	}

	//!Does sanity checks to the pattern list. Will throw upon any error.
	private function	check_sanity() {

		if(!count($this->list)) {
			throw new pattern_matcher_exception("matcher cannot be empty");
		}
/*
		//TODO: There's no need, actually, maybe we could just extend.
		$names=[];
		foreach($this->list as $k => $v) {

			$curname=$v->get_name();
			if(false!==array_search($curname, $names)) {
				throw new pattern_matcher_exception("pattern names cannot be repeated in matcher ('$curname'");
			}
			$names[]=$curname;
		}
*/
	}
}
