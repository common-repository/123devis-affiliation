<?php
	class sm_sr_activity_interview_baseinterview extends sm_renderable  {
			
		function setup_jquery_validate_messages($questions){
			//add validation calls
			$rules = array();
			$messages = array();
			foreach ($questions as $question){
				if (isset($question['validation'])){
					foreach ($question['validation'] as $validation_rule){
						if (!isset($rules[$question['name']])){
							$rules[$question['name']] = array();
						}
						switch ($validation_rule['type']){
							case 'not_empty' : 
								$rules[$question['name']]['required'] = true;
								$messages[$question['name']]['required'] = $validation_rule['error_message'];
							break;
							case 'regex' : 
								$rules[$question['name']]['pattern'] = $validation_rule['regex'];
								$messages[$question['name']]['pattern'] = $validation_rule['error_message'];
							break;
							case 're_match_one' : 
								$rules[$question['name']]['re_match_one'] = $validation_rule['re_match_one'];
								$messages[$question['name']]['re_match_one'] = $validation_rule['error_message'];
							break;
							default :
								//var_dump($validation_rule);
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