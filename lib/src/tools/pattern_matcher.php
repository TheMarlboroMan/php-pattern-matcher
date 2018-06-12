<?php
namespace tools;

//TODO: Document this.

//TODO: CHOOSE BETTER NAMES!

//TODO: Perhaps we have different classes for different types.
class pattern_match {

	private $name;
	private $value;

	public function __construct($_n, $_v) {
		$this->name=$_n;
		$this->value=$_v;
	}

	public function	get_name() {
		return $this->name;
	}

	public function get_value() {
		return $this->value;
	}
}

class pattern_result {

	private	$success;
	private $name;
	private $params=[];

	public function __construct($_s, $_n, array $_m) {
		$this->success=$_s;
		$this->name=$_n;
		$this->params=$_m;
	}

	public function is_match() {
		return $this->success;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_parameters() {
		return $this->params;
	}
}

interface pattern_chunk {
	//!_s is the full string, _i is the index where the previous match left,
	//!and must be written by each pattern_chunk. $_res is where the params are
	//!written.
	public function match($_s, &$_i, array &$_res);
}

class pattern_chunk_fixed implements pattern_chunk{

	private $str;
	private $len;

	public function __construct($_s) {

		$this->str=$_s;
		$this->len=strlen($this->str);
	}

	public function match($_v, &$_i, array &$_res) {

		$part=substr($_v, $_i, $this->len);
		$_i+=$this->len;
		return $part==$this->str;
	}
}

class pattern_chunk_match implements pattern_chunk {

	const chr_separate=':';
	const type_integer=0;
	const type_string=1;
	const literal_integer='int';
	const literal_string='string';

	private $type;
	public $name;
	private $last_character;

	public function __construct($_s, $_l) {

		$this->last_character=$_l;

		if(false===strpos($_s, self::chr_separate)) {
			//TODO: THrow real exception.
			throw new \Exception("could not find separator in match '".$_s."'");
		}

		$data=explode(self::chr_separate, $_s);
		if(2!=count($data)) {
			//TODO: THrow real exception.
			throw new \Exception("match must have exactly two parts in '".$_s."'");
		}

		try {
			$this->prepare($data[0], $data[1]);
		}
		catch(\Exception $e) {
			//TODO: THrow real exception.
			throw new \Exception($e->getMessage()." in '".$_s."'");
		}
	}

	private function prepare($_n, $_t) {

		$this->name=$_n;

		switch($_t) {
			case self::literal_integer: $this->type=self::type_integer; break;
			case self::literal_string: $this->type=self::type_string; break;
			default:
				//TODO: THrow real exception.
				throw new \Exception("unknown match type"); break;
		}
	}

	public function match($_v, &$_i, array &$_res) {

		$last_index=strlen($_v)-1;
		$val='';

		for($_i; ; $_i++) {

			//We might have arrived at the end of the test string. If we didn't break we are fine.
			if($_i==$last_index && null===$this->last_character) {
				break;
			}

			$char=$_v[$_i];
			//Or perhaps we are a the end of the current match...
			if($char==$this->last_character) {
				break;
			}

			switch($this->type) {
				//TODO: What about negative numbers?
				case self::type_integer: if(!ctype_digit($char)) return false; break;
				case self::type_string: if(!ctype_alpha($char)) return false; break;
			}
		}

		$_res[]=new pattern_match($this->name, $val);
		return true;
	}

}

class pattern {

	private	$raw_pattern;
	private	$id;
	private $chunks=[];

	const chr_open='[';
	const chr_close=']';
	const mode_literal=0;
	const mode_pattern=1;
	const mode_end=-1;

	public function		__construct($_p, $_i) {

		$this->raw_pattern=$_p;
		$this->id=$_i;

		$this->prepare();
		$this->check_integrity();
	}

	public function		matches($_input) {

//echo 'matching '.$this->raw_pattern.' to '.$_input.PHP_EOL;

		$results=[];
		$index=0;
		$max_index=strlen($this->raw_pattern);
		foreach($this->chunks as $v) {

			if(false===$v->match($_input, $index, $results)) {
				return null;
			}

			//The input might have no variables and might be shorter than the pattern...
			//If we have gotten to far, we have approved all matches, so everything
			//is good.
			if($index >= $max_index) {
				break;
			}
		}

		return new pattern_result(true, $this->id, $results);
	}

	private function	prepare() {

		$current='';
		$mode=self::mode_literal;
		$last_index=strlen($this->raw_pattern)-1;

		for($index=0; $index < strlen($this->raw_pattern); $index++) {

			$new_mode=$mode;
			$char=$this->raw_pattern[$index];

			if($char==self::chr_open) {
				$new_mode=self::mode_pattern;
			}
			else if($char==self::chr_close) {
				$new_mode=self::mode_literal;
			}
			else if($index==$last_index) {
				$new_mode=self::mode_end;
				$current.=$char; //Stupid hack to add the last character.
			}

			if($new_mode==$mode) {
					$current.=$char;
			}
			else {
				if(strlen($current)) {
					switch($mode) {
						case self::mode_literal:
							$this->chunks[]=new pattern_chunk_fixed($current); break;
						case self::mode_pattern:
							$next_character=$index==$last_index ? null : $this->raw_pattern[$index+1];
							$this->chunks[]=new pattern_chunk_match($current, $next_character); break;
					}

					$current='';
				}
				$mode=$new_mode;
			}
		}
	}

	private function check_integrity() {

		//TODO: Throw real shit.
		if(!count($this->chunks)) {
			throw new \Exception("pattern must have at least one part for '$this->raw_pattern'");
		}

		if($this->chunks[0] instanceof pattern_chunk_match) {
			throw new \Exception("the first part of a pattern cannot be a match type for '$this->raw_pattern'");
		}

		//TODO: Check for duplicate names!!!!
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

			$r=$v->matches($_input);
			if(null!==$r) {
				return $r;
			}
		}

		return new pattern_result(false, null, []);
	}

	//TODO: Build from file.
	private function 	setup_list() {

		$this->list=[
			new pattern("let/[type:int]/thing/[id:int]/and/[val:string]/index.html", "composite_path"),
			new pattern("let/another_string/[val:string]", "another_string"),
			new pattern("let/[type:int]/end", "final"),
			new pattern("simple-path", "simple"),
		];
	}
}
