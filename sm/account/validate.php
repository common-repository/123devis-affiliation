<?php
	class sm_account_validate extends sm_toprenderable  {
				
		function did_validate(){
			return isset($this->data['kwids']);
		}
		
		function get_sm_kwids(){
			return $this->data['sm_kwids'];
		}
		
		function get_sm_token(){
			return $this->data['sm_token'];
		}
		
		function get_sm_aff_id(){
			//print_r($this->data);
			return $this->data['sm_aff_id'];
		}
		
		function get_sm_spa_accept(){
			return (isset($this->data['sm_spa_accept']) ? $this->data['sm_spa_accept'] : 0);
		}
		
	}