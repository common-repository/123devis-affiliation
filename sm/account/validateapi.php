<?php
	class sm_account_validateapi extends sm_toprenderable  {	
	
		function was_successful(){
			return isset($this->data['success']) AND $this->data['success'];
		}
		
		function get_api_errors(){
			return $this->data['errors'];
		}

	}