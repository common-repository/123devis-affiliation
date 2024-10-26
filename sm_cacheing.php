<?php
	class sm_cacheing {

		private $time_to_cache = 10800;//3hrs in seconds

		public function save($name, $dta){
			set_transient( $name, $dta, $this->time_to_cache );
		}

		public function retrieve($name){
			return get_transient( $name );
		}

	}