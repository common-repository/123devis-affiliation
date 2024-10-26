<?php
	class sm_cacheing {
	
		private $time_to_cache = 86400;//1 day mins in seconds
		
		public function save($name, $dta){
			$dta = json_encode($dta);
			
			$path = $this->get_path($name);
			
			file_put_contents($path, $dta);
		}
		
		public function retrieve($name){			
			$path = $this->get_path($name);
			
			if (!file_exists($path)){
				return false;
			}
			
			if ((filemtime($path) + $this->time_to_cache) < time()){
				return false;
			}

			$dta = file_get_contents($path);
			
			
			$dta = json_decode($dta, 1);
			
			return $dta;
		}
		
		private function get_path($name){
			return dirname(__FILE__) . "/cache/" . $name . ".json";
		}
		
	}