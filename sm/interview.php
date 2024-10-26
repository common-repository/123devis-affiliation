<?php
	class sm_interview extends sm_toprenderable  {
		
		protected $validator;
		protected $submission_result;
		
		public function __construct($data, $settings, $api){
			parent::__construct($data, $settings, $api);
			if ($this->has_errors()){
				throw new sm_exception_general($this->get_formatted_errors());
			}
		}
			
		public function get_id(){
			return $this->data['id'];
		}
		
		public function get_title(){
			return $this->data['title'];
		}
		
		public function get_questions(){
			return $this->data['questions'];
		}
		
		public function set_questions($questions){
			$this->data["questions"] = $questions;
		}
			
		public function get_validator(){
			if (is_null($this->validator)) {
				$this->validator = new sm_validation;
			}
			return $this->validator;
		}
			
		public function submit($form_data){
			
			//update with defaults if they exist
			$defaults = $this->get_parameter("defaults", array());
			foreach($defaults as $default_key => $default_val){
				$form_data[$default_key] = $default_val;
			}
			
			//first do local validation
			if (! $this->validate($form_data)){
				return false;
			}
			
			//try to send it
			$form_data["aff_track"] = $this->affiliate['aff_track'];

			$this->submission_result = $this->get_submission_result_obj_by_api($form_data);
			
			//if get back errors from api, save them to the interview errors for consistent input
			if ($this->submission_result->has_errors()) {
				//print_r($submission->get_api_errors());
				foreach ($this->submission_result->get_api_errors() as $fname => $error){
					foreach ((array)$error as $error_text){
						$this->validator->add_error($fname, $error_text);
					}
				}
				return false;
			} 
			
			//save submission info
			$this->submission_data = $form_data;
			$this->submission_result->save_submission_info($form_data);
			
			return true;
		}
		
		public function get_submission_result(){
			return $this->submission_result;			
		}
		
		public function validate($data){
			$validator = $this->get_validator();
			$validator->set_data($data);
			$questions = $this->data['questions'];
			
			//deal with behaviors - currently only one affects here so handle locally
			foreach ($questions as $ki => $ditem){
				if (!empty($ditem['behavior'])){
					foreach($ditem['behavior'] as $behavior){
						if ($behavior['type'] == 'alternate_validation'){
							if (!empty($data[$behavior['observed']]) && in_array($data[$behavior['observed']], $behavior['conditions']['value_in'])){
								$questions[$ki]['validation'] = $behavior['validation'];
							}
						}
						if ($behavior['type'] == 'hidden_unless'){//should only have validation if observed field has value in list
							if (!empty($data[$behavior['observed']]) && !in_array($data[$behavior['observed']], $behavior['conditions']['value_in'])){
								$questions[$ki]['validation'] = array();
							}
						}
					}
				}
			}

			foreach ($questions as $ditem){
				$val = isset($data[$ditem['name']]) ? $data[$ditem['name']] : "";
				$required = isset($ditem['required']) AND $ditem['required'];
				if ($required OR (!$required AND $val != '' AND !empty($ditem['validation']))){
					foreach($ditem['validation'] as $val_check){
						$message = isset($val_check['message_empty']) ? $val_check['message_empty'] : $val_check['error_message'];
						$val_method = $val_check['type'];
						if (isset($val_check[$val_method])){//check for specfic error message for method
							$validator->$val_method($ditem['name'], $message, $val_check[$val_method]);
						} else {//or use default
							$validator->$val_method($ditem['name'], $message);
						}
					}
				}
			}

			return ! $validator->has_errors();  //result should be true if form validates with no errors
		}
		
		public function render_with_submit(){
			//print_r($_POST);
			if (!empty($_POST) AND count($_POST) > 5 AND $this->submit($_POST)){
				$this->set_parameter("view", "thanks");
			}
		
			return parent::render();
		}

	}