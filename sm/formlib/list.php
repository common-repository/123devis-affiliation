<?php 
	class sm_formlib_list extends sm_renderable {

		function render($data, $form){
			$translation = $this->data->get_parameter("translate");
			$s = "<div class=\"sm_list_group\">\n";
			$option_count = count($data['options']);
			foreach ($data['options'] as $option){
				//basics
				$s .= "<div class=\"sm_list_group_item\">";
				$s .= "<input type=\"{$this->form_type}\" name=\"{$data['name']}";
				$s .= ($this->form_type == 'checkbox' && $option_count > 1 ? '[]' : "");
				$s .= "\" id=\"form_{$option['value']}\" value=\"{$option['value']}\" ";
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
				$s .= "><label class=\"sm_input_label\" for=\"form_{$option['value']}\">{$option['label']}</label>\n";
				$s .= "</div>\n";
			}
			$s .= "</div>\n";
			
			if (isset($data['other_option_name'])){
				$s .= "<div class=\"sm_other_wrap";
				if (!isset($form[$data['other_option_name']])){
					$s .= " start_hide";
				}
				$s .= "\">\n";
				
				$s .= $translation->trans("Other, please state");
				$s .= " <input type=\"text\" name=\"{$data['other_option_name']}\" ";
				if (isset($form[$data['other_option_name']])){
					$s .= " value=\"{$form[$data['other_option_name']]}\" ";
				}
				$s .= "class=\"sm_txt_other\">\n";
				$s .= "</div>\n";
			}
			return $s;
		}
		
	}