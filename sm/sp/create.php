<?php
	class sm_sp_create extends sm_toprenderable  {	
	
		public function has_errors(){
			return isset($this->data['errors']);
		}
		
		public function get_api_errors(){
			return $this->data['errors'];
		}
		
		public function save_submission_info($data_to_save){
			
		}

	}