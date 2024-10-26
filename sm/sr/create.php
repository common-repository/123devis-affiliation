<?php
	class sm_sr_create extends sm_toprenderable  {	
	
		function has_errors(){
			return isset($this->data['errors']);
		}
		
		function get_api_errors(){
			return $this->data['errors'];
		}
		
		function get_track_id(){
			return $this->data['_track_id'];
		}
		
		function save_submission_info($data_to_save){
			
		}

	}