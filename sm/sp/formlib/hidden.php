<?php 
	class sm_formlib_hidden extends sm_renderable {
	
		function render($data, $form){
			$s = "<input type=\"hidden\" id=\"{$data['name']}_form\" name=\"{$data['name']}\" value=\"";
			
			if (isset($form[$data['name']])){
				$s .= $form[$data['name']];
			} elseif (isset($data['default'])) {
				$s .= $data['default'];
			} else $s .= "";
			
			$s .= "\">\n";
			return $s;
		}
		
	}	