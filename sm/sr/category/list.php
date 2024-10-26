<?php
	class sm_sr_category_list extends sm_toprenderable  {
		
		function __construct($data, $sm_settings, $api){
			parent::__construct($data, $sm_settings, $api);
			if (isset($data["errors"])){
				throw new Exception($this->get_formatted_errors("text"));
			}
		}
		
		function get_categories(){
			return $this->data;
		}
	}