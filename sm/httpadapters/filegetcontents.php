<?php
	class sm_httpadapters_filegetcontents {

		private $context_data = array();
		private $response_data = array();
		private $timeout_for_get;
		private $timeout_for_post;

		public function add_http_header($value)	{
			$this->add_context("http", "header", $value);
		}
		
		public function set_timeout($a1, $a2 = null){
			//pass two integers to specify get and post timeouts concurrently
			if (is_null($a2) AND is_integer($a1)){
				$this->timeout_for_get = $a2;
				$this->timeout_for_post = $a2;
			} else {//or args = ["get", integer] to specify one at a time
				$function_name = "timeout_for_" . $a1;
				$this->$function_name = $a2;
			}
		}
		
		public function set_http_method($value){
			$this->add_context("http", "method", $value);
		}

		public function get_response_field($field){
			if (isset($this->response_data[$field])) {
				return $this->response_data[$field];
			}
			return false;
		}
		
		public function get($url, $data = array()){
			$this->set_http_method('GET');
			
			$datastr = "";
			foreach ($data as $di => $dv){
				$datastr .= "$di=" .rawurlencode($dv) . '&';
			}
			$this->set_context("http", "timeout", $this->timeout_for_get);
			
			return $this->send($url . '?' . $datastr);
		}

		public function post($url, $data)	{
			$this->set_http_method('POST');
			
			$this->add_http_header('Content-type: application/x-www-form-urlencoded');
			
			if (is_array($data)){
				$datastr = "";
				foreach ($data as $di => $dv){
					if (is_array($dv)){
						foreach($dv as $itm){
							$datastr .= "$di" . '[]=' .rawurlencode($itm) . '&';
						}
					} else {
						$datastr .= "$di=" . rawurlencode($dv) . '&';
					}
				}

				$data = $datastr;
			}
			
			$this->add_context("http", "content", $data);
			$this->set_context("http", "timeout", $this->timeout_for_post);
			
			return $this->send($url);
		}
		
		private function add_context($group, $field, $value){
			if (empty($this->context_data[$group])){
				$this->context_data[$group] = array();
			}
			$this->context_data[$group][$field][] = $value;
		}
		
		private function set_context($group, $field, $value){
			if (empty($this->context_data[$group])){
				$this->context_data[$group] = array();
			}
			$this->context_data[$group][$field] = (array)$value;
		}
		
		private function send($url)	{
			//reprocess context data to flat(er) array
			foreach ($this->context_data as $data_group_key => $data_group){
				foreach ($data_group as $data_key => $data_item){
					$new_data_item = implode("\n", $data_item);
					$this->context_data[$data_group_key][$data_key] = $new_data_item;
				}
			}

			$context  = stream_context_create($this->context_data);
			
			//file_get_contents throws exceptions based on various issues, also thrwos warning on timeout so alter error trapping to not show warnings to customer
			$dflt_error_reporting = error_reporting();
			if (!isset($_REQUEST["show_warnings"])){
				error_reporting(E_ERROR);
			}

			$http_result = file_get_contents($url, false, $context);
			
			if ($http_result === FALSE) {
				throw new sm_exception_httperror("Failed calling url : " . $url);	
			}
			$this->response_data['body'] = $http_result;
			
			error_reporting($dflt_error_reporting);
			
			$status = array_shift($http_response_header);
			$status = explode(" ", $status);
			
			//process the response status
			$this->response_data['success'] = $status[2];
			$this->response_data['status_code'] = $status[1];
			
			//the rest should be name: value, map to array for exposure via get_response_field
			foreach($http_response_header as $r){
				$matches = array();
				if (preg_match('/^([a-zA-Z\-\_]+)\:(.*)$/', $r, $matches)){
					$lbl = $matches[1];
					$lbl = trim($lbl);
					$lbl = strtolower($lbl);
					$lbl = preg_replace("/[^a-z\_]/", "", $lbl);
					$val = trim($matches[2]);
					$this->response_data[$lbl] = $val;
				} 
			}

			return true;
		}
	}