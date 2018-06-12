<?php
namespace tools;


/*
//TODO: This could be an example format...
path/{id: number}/and/{name:string}/finally/{something:any}/and_done

number: 0-9
string: a-zA-z-_
any: .

translated to

path/[0-9]+/and/[a-zA-Z\-_]+/finally/[.+]/and_done
*/

//TODO

interface pattern_chunk {
	//_s is the full string, _i is the index where the previous match left,
	//and must be written by each pattern_chunk.
	public function match($_s, &$_i);
}

class pattern_chunk_fixed implements pattern_chunk{

	public $str;
	public $len;

	public function __construct($_s) {

		$this->str=$_s;
		$this->len=strlen($this->str);
	}

	public function match($_v, &$_i) {

//print_r($this);
//echo $_v.' '.$_i.PHP_EOL;

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

	public $type;
	public $name;

	public function __construct($_s) {

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

	public function match($_v, &$_i) {

		//TODO
		//UP TO HOW MUCH DO WE READ???

print_r($this);
echo $_v.' '.$_i.PHP_EOL;
die();
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

		$this->prepare_pattern();
		$this->check_pattern();
	}

	public function		matches($_input) {

		echo 'matching '.$this->raw_pattern.' to '.$_input.PHP_EOL;

		$index=0;
		foreach($this->chunks as $v) {

			if(!$v->match($_input, $index)) {
				return false;
			}
		}

		return true;
	}

	private function	prepare_pattern() {

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
							$this->chunks[]=new pattern_chunk_match($current); break;
					}

					$current='';
				}
				$mode=$new_mode;
			}
		}
	}

	private function check_pattern() {

		//TODO: Throw real shit.
		if(!count($this->chunks)) {
			throw new \Exception("pattern must have at least one part for '$this->raw_pattern'");
		}

		if($this->chunks[0] instanceof pattern_chunk_match) {
			throw new \Exception("the first part of a pattern cannot be a match type for '$this->raw_pattern'");
		}
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
				//TODO: Return a dedicated result object that will the parameter values...
				return $v;
			}
		}

		return null;
	}

	//TODO: Build from file.
	private function 	setup_list() {

		$this->list=[
			new pattern("let/[type:string]/thing/[id:int]/and/[val:string]/index.html", "composite_path"),
			new pattern("let/another_string", "another_string"),
		];
	}
}
