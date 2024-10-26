<?php
	class sm_sr_activity_interview__thanks extends sm_renderable  {
		public function render($interview){
			$translation = $this->data->get_parameter("translate");
			
			$s = '<div class="sm_interview sm_thanks">';
			$s .= '<h2>' . $translation->trans("Your service request has been successfully received") . '</h2>';
			$s .= '<p>' . $translation->trans("We are currently looking for available service professionals from your area able to provide you with quotes.(up to 4 max)") . '</p>';
			$s .= '<p>' . $translation->trans("We will send you their contact details by email every time we find you a match.") . '</p>';
			$s .= '</div>';
					
			if (!$this->data->get_parameter("ajax", "0")){
				$s .= "<script>\n";
				$s .= "jQuery(function(){\n";
				$s .= 	"setTimeout(function() {\n";
				$s .= 		"jQuery(\"body\").trigger(\"sr_submit.sm_eu\", ['";
				$trackid = $interview->get_submission_result()->get_track_id();
				$s .= 			$trackid;
				$s .=		"']);";
				$s .= 	"}, 40);\n";
				$s .= "});\n";
				$s .= "</script>";
			}
			$s .= $this->data->get_parameter("success_more_text_ty", "");
			
			return $s;
		}
	}