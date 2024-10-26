<?php
	class sm_baseinterview extends sm_renderable  {

		public function setup_jquery_validate_messages($questions){
			//add validation calls
			$rules = array();
			$messages = array();
			foreach ($questions as $question){

				if (isset($question['validation'])){
					foreach ($question['validation'] as $validation_rule){
						$name = $question['name'] . ($question['type'] == "checkbox" ? '[]' : "");
						if (!isset($rules[$name])){
							$rules[$name] = array();
						}
						if (!isset($messages[$name])){
							$messages[$name] = array();
						}
						switch ($validation_rule['type']){
							case 'not_empty' :
								$rules[$name]['required'] = true;
								$messages[$name]['required'] = $validation_rule['error_message'];
							break;
							case 'regex' :
								$rules[$name]['pattern'] = $validation_rule['regex'];
								$messages[$name]['pattern'] = $validation_rule['error_message'];
							break;
							case 're_match_one' :
								$rules[$name]['re_match_one'] = $validation_rule['re_match_one'];
								$messages[$name]['re_match_one'] = $validation_rule['error_message'];
							break;
							default :
								throw new Exception("Invalid type : " . $validation_rule['type']);
							break;
						}
					}
				}
			}
			//var_dump($rules);
			//var_dump($messages);die();
			return array("rules"=>$rules, "messages"=>$messages);
		}

	}