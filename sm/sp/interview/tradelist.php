<?php
	class sm_sp_interview_tradelist extends sm_renderable {

		public function render($data, $form){
			$translation = $this->data->get_parameter("translate");
			$trades_to_show = $this->data->get_parameter("trades", array());
			if (count($trades_to_show)){
				foreach ($data['options'] as $k => $option){
					if (! in_array($option["value"], $trades_to_show)){
						unset($data['options'][$k]);
					}
				}
			}
			$data['options'] = array_values($data['options']);
			$s = "<div class=\"sm_tradelist\">\n";

			foreach ($data['options'] as $k => $option){
				//basics
				$s .= "<div class=\"col3\"><input type=\"radio\" name=\"{$data['name']}\" value=\"{$option['value']}\" ";

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
				$s .= ">{$option['label']}</div>\n";

				if ($k % 3 == 2 AND ($k +1)!= count($data['options'])){
					$s .= "<br class=\"sm_clear\">\n";
				}
			}
			$s .= "<br class=\"sm_clear\">\n";
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