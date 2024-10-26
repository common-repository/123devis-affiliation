<?php 
	class sm_formlib_text extends sm_renderable {
	
		function render($data, $form){
			$s = "<input id=\"{$data['name']}_form\" type=\"text\" name=\"{$data['name']}\" id=\"{$data['name']}\" ";
			
			if (isset($data['attributes'])){
				foreach($data['attributes'] as $att_key => $att_array){
					$s .= " $att_key=\"" . implode(" ", (array)$att_array) . '" ';
				}
			}
			
			$s .= "value=\"";
			if (isset($form[$data['name']])){
				$s .= $form[$data['name']];
			}
			
			$s .= "\">\n";
			return $s;
		}
		
	}	