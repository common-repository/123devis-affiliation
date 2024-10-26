<?php
	class sm_validation  {

		protected $errors = array();
		protected $validateable_data = array();

		public function set_data($data){
			$this->validateable_data = $data;
		}

		public function get_data_val($name){
			if (array_key_exists($name, $this->validateable_data)) return $this->validateable_data[$name];
			else return "";
		}

		public function is_int($name, $message){
			$val = $this->get_data_val($name);
			if (strlen($val) == 0 OR (int)$val != $val){
				$this->add_error($name, $message);
				return false;
			}
			return true;
		}

		public function regex($name, $message, $regex){
			$val = $this->get_data_val($name);
			if (!preg_match($regex, $val)) {
				$this->add_error($name, $message);
				return false;
			}
			return true;
		}

		public function is_in_list($name, $message, $acceptables){
			$val = $this->get_data_val($name);
			if (array_search($val, $acceptables) === FALSE) {
				$this->add_error($name, $message);
				return false;
			}
			return true;
		}

		public function must_match($name, $comparable, $message){
			$val = $this->get_data_val($name);
			if ($val != $comparable) {
				$this->add_error($name, $message);
				return false;
			}
			return true;
		}

		public function re_match_one($name, $message, $regex_list){
			$val = $this->get_data_val($name);
			foreach($regex_list as $regex){
				if (preg_match($regex, $val)) {
					return true;
				}
			}
			$this->add_error($name, $message);
			return false;
		}

		public function not_empty($name, $message){
			$val = $this->get_data_val($name);
			if (
				(is_array($val) AND count($val) == 0)
				OR
				$val == ''
			) {
				$this->add_error($name, $message);
				return false;
			}
			return true;
		}

		public function hex_value($name, $message){
			$val = $this->get_data_val($name);
			if (! preg_match("/^#[a-zA-Z0-9]{6}$/", $val)) {
				$this->add_error($name, $message);
				return false;
			}
			return true;
		}

		public function valid_password($name, $message){
			$val = $this->get_data_val($name);
			if (strlen($val) < 6 OR  strlen($val) > 20){
				$this->add_error($name, $message);
				return false;
			}
			$required_list = array("/[A-Z]/", "/[0-9]/", "/[a-z]/");
			foreach ($required_list as $ritem) {
				if (! preg_match($ritem, $val)){
					$this->add_error($name, $message);
					return false;
				}
			}
			$invalid_list = array("/\s/");
			foreach ($invalid_list as $iitem) {
				if (preg_match($iitem, $val)){
					$this->add_error($name, $message);
					return false;
				}
			}
			return true;
		}

		public function valid_full_url($name, $message){
			$val = $this->get_data_val($name);
			return $this->regex($name, $message, "/^(http|https):\/\/[a-zA-Z0-9]+([\-\.]{1}[a-zA-Z0-9]+)*\.[a-zA-Z]{2,6}(:[0-9]{1,5})?$/");
		}


		public function valid_username($name, $message){
			$val = $this->get_data_val($name);
			if (strlen($val) < 6 OR  strlen($val) > 19){
				$this->add_error($name, $message);
				return false;
			}
			$invalid_list = array("/\s/");
			foreach ($invalid_list as $req_item) {
				if (preg_match($req_item, $val)){
					$this->add_error($name, $message);
					return false;
				}
			}
			return true;
		}

		public function add_error($name, $msg){
			$this->errors[$name][] = $msg;
		}

		public function has_errors(){
			return count($this->errors);
		}

		public function get_errors(){
			return $this->errors;
		}

		public function item_has_error($name){
			return !empty($this->errors[$name]);
		}

		public function get_item_formatted_errors($name){
			if (count($this->errors[$name])){
				return "<ul class=\"sm_errors\">\n<li>" . implode("</li>\n<li>", $this->errors[$name]) . "</li>\n</ul>\n";
			} else return "";
		}

		public function get_item_first_error($name){
			if (count($this->errors[$name])){
				return $this->errors[$name][0];
			} else return "";
		}

		public function get_formatted_errors($msg = null){
			if (count($this->errors)){
				$errors = array();
				foreach($this->errors as $error_name => $error_item){
					$errors[] = "<li id=\"e_$error_name\">\n" . implode("</li>\n<li>", $error_item) . "</li>\n";
				}
				$r = "";
				if ($msg) $r = "<div class=\"sm_error_lbl\">" . $msg . "</div>";
				$r .= "<ul class=\"sm_errors\">\n" . implode("\n", $errors) . "</ul>\n";
				return $r;
			} else return "";
		}
	}