<?php
	class sm_sr_activity_interview extends sm_interview  {
		
		public function get_submission_result_obj_by_api($form_data){
			return $this->api->sr->create->post($form_data);
		}
	
	}