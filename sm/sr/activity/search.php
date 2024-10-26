<?php
	class sm_sr_activity_search extends sm_toprenderable  {
		
		public function found_results(){
			return !empty($this->data["search_results"]);
		}
		
		public function get_results(){
			return $this->data["search_results"];
		}
		
	}