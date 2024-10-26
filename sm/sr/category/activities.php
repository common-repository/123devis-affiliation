<?php
	class sm_sr_category_activities extends sm_toprenderable  {
		
		function __construct($data, $sm_settings, $api){
			parent::__construct($data, $sm_settings, $api);
			if ($this->has_errors()){
				throw new Exception($this->get_formatted_errors("text"));
			}
		}
		
		function get_activities(){
			return $this->data;
		}
	}