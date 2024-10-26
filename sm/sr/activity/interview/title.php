<?php
	class sm_sr_activity_interview_title extends sm_renderable  {
			
		protected $template_interview_title = "<div class=\"sm_title\">\n[title]\n</div>\n";
		
		function render($data){
			return $this->use_template('interview_title', $data);
		}
		
	}