<?php
namespace tools\pattern_matcher;

//!Represents a chunk of fixed test. This is the "hello" part in the pattern
//!"hello[id:number].
class chunk_fixed extends chunk{

	private $str;
	private $len;

	//!$_s is a string.
	public function __construct($_s) {

		$this->str=$_s;
		$this->len=strlen($this->str);
	}

	//!See chunk::match.
	public function match($_v, &$_i, array &$_res) {

		//TODO: This might still fail if the remainder string is shorter.
		$part=substr($_v, $_i, $this->len);
		$_i+=$this->len;
		return strlen($part)==$this->len && $part==$this->str;
	}
}
