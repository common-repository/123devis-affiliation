<?php
	class sm_http {

		private $timeout_for_get = 5;
		private $timeout_for_post = 20;
		private $http_obj;

		public function get_http_obj($http_adapter = null){

			//if not specified, pick the best available
			if (is_null($http_adapter)){
				//if curl exists, use it
				if (function_exists('curl_version')){
					$http_adapter = 'curl';
				} else {
					$http_adapter = 'file_get_contents';
				}
			}

			//validate selected adapter
			list($php_major, $php_minor) = explode(".", PHP_VERSION);

			if ($http_adapter == 'file_get_contents' AND $php_major <= 5 AND $php_minor <= 3){
				throw new sm_exception("the http adapter file_get_contents is only useable with php 5.3 or greater");
			}

			if ($http_adapter == 'curl' AND !function_exists('curl_version')){
				throw new sm_exception("curl is not enabled on this server");
			}

			//validation passed, now make the adapter
			$http_adapter_class_name = "sm_httpadapters_" . str_replace("_", "", $http_adapter);
			$this->http_obj = new $http_adapter_class_name;

			//load some default timeouts
			$this->http_obj->set_timeout($this->timeout_for_get, $this->timeout_for_post);

			return $this->http_obj;
		}

	}