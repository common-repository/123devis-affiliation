<?php
	class sm_sp_interview_title extends sm_renderable  {
			
		protected $template_interview_title = "<div class=\"sm_title\">\n[title]\n</div>\n";
		
		public function render($data){
			return $this->use_template('interview_title', $data);
		}
		
	}