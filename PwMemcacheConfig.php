<?php namespace ProcessWire;

class PwMemcacheConfig extends ModuleConfig {

	function __construct() {
		$this->add([[
			"name"			=>	"serverdata",
			"label"			=>	$this->_("Servers"),
			"description"	=>	$this->_("Enter servers with their port appended by colon, one server per line, e.g. *127.0.0.1:11211*"),
			"type"			=>	"textarea",
			"value"			=>	"127.0.0.1:11211"
		], [
			"name"			=>	"cacheactive",
			"label"			=>	$this->_("Active"),
			"description"	=>	$this->_("Caching is only active if this checkbox is checked, to avoid errors and delays if memache connection hasn't been configured yet."),
			"type"			=>	"checkbox",
			"value"			=>	""
		]]);
	}
	
}
