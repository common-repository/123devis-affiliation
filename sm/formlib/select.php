<?php 
	class sm_formlib_select extends sm_renderable {
	
		function render($data, $form){
			$translation = $this->data->get_parameter("translate");
			$s = "<select id=\"{$data['name']}_form\" name=\"{$data['name']}\" ";
			
			if (isset($data['other_option_name'])){
				if (!isset($data['attributes'])) $data['attributes'] = array();
				$data['attributes']['class'][] = "sm_select_has_other";
			}
			
			if (isset($data['attributes'])){
				foreach($data['attributes'] as $att_key => $att_array){
					$s .= " $att_key=\"" . implode(" ", (array)$att_array) . '" ';
				}
			}
			
			$s .=">\n";
			
			$s .= "<option value=\"\">" . $translation->trans("Choose") . "</option>\n";

			foreach ($data['options'] as $option){
				$selected = (isset($form[$data['name']]) AND $form[$data['name']] == $option['value']);
				$s .= "<option value=\"{$option['value']}\" ".($selected ? "selected" : "").">{$option['label']}</option>\n";
			}
			
			$s .= "</select>\n";
			
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