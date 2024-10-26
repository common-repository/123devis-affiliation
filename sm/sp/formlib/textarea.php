<?php 
	class sm_formlib_textarea extends sm_renderable {
	
		function render($data, $form){
			$s = "<textarea id=\"{$data['name']}_form\" name=\"{$data['name']}\" ";
			
			if (isset($data['attributes'])){
				foreach($data['attributes'] as $att_key => $att_array){
					$s .= " $att_key=\"" . implode(" ", (array)$att_array) . '" ';
				}
			}
			
			$s .= ">";
				
			if (isset($form[$data['name']])){
				$s .= $form[$data['name']];
			}
			
			$s .= "</textarea>";
			return $s;
		}
		
	}