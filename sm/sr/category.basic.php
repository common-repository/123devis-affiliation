<?php
	class sm_sr_category__basic extends sm_renderable  {

		public $default_parameters = array(
			"header" => "<h2>[label]</h2>"
		);

		public function render(){
			return $this->use_template('header', $this->data->get_data());
		}

	}