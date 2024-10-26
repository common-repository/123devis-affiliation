<?php
	class sm_exception_general extends Exception {

		public $data = array();

		function __construct($msg, $data = array(), $code = 0, Exception $previous = null){
			$this->data = $data;
			
			new sm_wp_log(array(
				'type' => "error",
				'message' => $msg,
				'message_more' => json_encode($data)
			));
			
			parent::__construct($msg, $code);//, $previous);5.1
		}
		public function getData(){
			return $this->data;
		}
	}
