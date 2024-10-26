<?php
	class sm_sr_activity_interview_formbasic extends sm_renderable  {
			
		protected $template_interview_description = "<div class=\"description\">\n[description]\n</div>\n";

		public function render($interview, $form_data, $validator){
			$s = "<div class=\"interview\">\n";
			$title_obj = new sm_sr_activity_interview_title;
			$s .= $title_obj->render($this->data);
			
			$description_obj = new sm_sr_activity_interview_description;
			$s .= $description_obj->render($this->data);
			
			$s .= "<div class=\"form\">\n";
			$s .= "<form method=\"post\" action=\"\" class=\"sm_form\">\n";

			foreach($interview['questions'] as $qid => $qdata){
				if ($qdata['type'] == 'hidden'){
					$form_obj_name = "sm_sr_activity_interview_" . $qdata['type'];
					$form_obj = new $form_obj_name;
					$s .= $form_obj->render($qdata, $form_data);
				} else {
					$s .= "<div class=\"sm_item\">\n";
					$s .= "<label>\n";
					
					$s .= $qdata['label'];
					if (isset($qdata['required'])){
						$s .= "<span class=\"sm_required\">*</span>";
					}
					$s .= "\n</label>\n";

					$form_obj_name = "sm_sr_activity_interview_" . $qdata['type'];
					$form_obj = new $form_obj_name;
					$s .= $form_obj->render($qdata, $form_data);
					
					if ($validator->item_has_error($qdata['name'])){
						$s .= "<div class=\"sm_error_container\">";
						$s .= $validator->get_item_formatted_errors($qdata['name']);
						$s .= "</div>\n";
					}
					
					$s .= "</div>\n";
				}
			}
			$s .= "<input type=\"submit\" class=\"sm_submit\" value=\"Submit\">";
			$s .= "</form>\n";
			$s .= "</div>\n";
			$s .= "</div>\n";
			
			return $s;
		}
		
	}