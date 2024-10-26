<?php
	class sm_exception_general extends Exception {
      public function __construct($message, $data=array(), $code = 0, Exception $previous = null) {
         $this->data = $data;
         parent::__construct($message, $code);//, $previous);5.1
      }
      public function getData(){
         return $this->data;
      }
   }