<?php
	class sm_httpadapters_curl {

		private $config = array();
		private $headers = array();
		private $response_data = array();
		private $timeout_for_get;
		private $timeout_for_post;


		public function __construct(){
			$this->config[CURLOPT_HEADER] = true;
			$this->config[CURLOPT_NOBODY] = false;
			$this->config[CURLOPT_RETURNTRANSFER] = true;
			//$this->config[CURLOPT_USERAGENT] = "mmm";
			$this->config[CURLOPT_SSL_VERIFYPEER] = false;
			//use for debugging low level curl requests : $this->config[CURLOPT_VERBOSE] = true;
			// Suivre les redirections...
			$this->config[CURLOPT_FOLLOWLOCATION] = true;
		}

		public function add_http_header($value)	{
			$this->headers[] = $value;
		}

		public function get_response_field($field){
			if (isset($this->response_data[$field])) {
				return $this->response_data[$field];
			}
			return false;
		}

		public function set_timeout($a1, $a2 = null){
			//pass two integers to specify get and post timeouts concurrently
			if ($a2 === null && is_integer($a1)){
				$this->timeout_for_get = $a2;
				$this->timeout_for_post = $a2;
			} else {//or args = ["get", integer] to specify one at a time
				$function_name = "timeout_for_" . $a1;
				$this->$function_name = $a2;
			}
		}

		public function set_http_method($value){
			$this->config[CURLOPT_POST] = (strtolower($value) == "post" ? true : false);
		}


		public function get($url, $data = array()){
			$this->set_http_method('GET');

			$datastr = "";
			foreach ($data as $di => $dv){
				$datastr .= "$di=" .rawurlencode($dv) . '&';
			}

			$this->config[CURLOPT_TIMEOUT] = $this->timeout_for_get;

			return $this->send($url . '?' . $datastr);
		}

		public function post($url, $data)	{
			$this->set_http_method('POST');

			$this->add_http_header('Content-type: application/x-www-form-urlencoded');

			//$data = http_build_query($data, '', '&');
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

			$this->config[CURLOPT_POSTFIELDS] = $data;
			$this->config[CURLOPT_TIMEOUT] = $this->timeout_for_post;

			return $this->send($url);
		}

		private function send($url)	{

			$curl_obj = curl_init();

			$this->config[CURLOPT_URL] = $url;

			foreach($this->config as $config_key => $config_item){
				curl_setopt($curl_obj, $config_key, $config_item);
			}

			curl_setopt($curl_obj, CURLOPT_HTTPHEADER, $this->headers);

			$http_result_str = curl_exec($curl_obj);

			if ($http_result_str === false) {
				throw new sm_exception_httperror("Failed calling url : " . $url . " " . curl_error($curl_obj));
			}

			//deal with 100 codes which can be ignored
			$http_result_str = trim(preg_replace("#HTTP/1.1 1[0-9]{2} [^\n]+#", "", $http_result_str));

			//parse returned string...
			$http_result = explode("\r\n\r\n", $http_result_str, 2);

			$http_response_header_str = $http_result[0];

			$this->response_data['body'] = count($http_result) > 1 ? $http_result[1] : "";

			$http_response_header = explode("\r\n", $http_response_header_str);
			$curl_data = curl_getinfo ($curl_obj);

			$status_str = array_shift($http_response_header);
			$status = explode(" ", $status_str);

			$this->response_data['success'] = $status[2];
			$this->response_data['status_code'] = $curl_data['http_code'];

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