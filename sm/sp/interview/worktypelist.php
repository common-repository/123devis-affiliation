<?php 
	class sm_sp_interview_worktypelist extends sm_renderable {

		public function render($data, $form){
			
			$s = "<div class=\"sm_worktypelist\">\n";
			
			foreach ($data['options'] as $k => $option){
				//basics
				$s .= "<div class=\"col3\"><label for=\"". $data['name']. "_form_" . $k . "\"><input id=\"{$data['name']}_form_{$k}\" type=\"radio\" name=\"{$data['name']}\" value=\"{$option['value']}\" ";
				
				if (isset($form[$data['name']]) AND $form[$data['name']] == $option['value']){
					$s .= 'checked';
				}
				
				if (isset($data['other_option_name'])){
					if (!isset($data['attributes'])) $data['attributes'] = array();
					$data['attributes']['class'][] = "sm_select_has_other";
				}
				
				//add attributes
				if (isset($data['attributes'])){
					foreach($data['attributes'] as $att_key => $att_array){
						$s .= " $att_key=\"" . implode(" ", (array)$att_array) . '" ';
					}
				}
				
				//end with label and closing tag
				$s .= ">{$option['label']}</label></div>\n";
				
				if ($k % 3 == 2 AND ($k +1)!= count($data['options'])){
					$s .= "<br class=\"sm_clear\">\n";
				}
			}
			$s .= "<br class=\"sm_clear\">\n";
			$s .= "</div>\n";
		
			return $s;
		}
		
	}