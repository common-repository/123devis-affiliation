<?php
	class sm_toprenderable extends sm_renderable implements IteratorAggregate {
		
		protected $data;
		protected $sm_settings;
		protected $api;
		protected $parameters = array();
		protected $renderable;
		protected $affiliate;
		
		public function __construct($data, $sm_settings, $api){
			$this->data = $data;
			$this->sm_settings = $sm_settings;
			$this->api = $api;
		}
		
		public function set_affiliate_track_string($aff_track){
			$this->affiliate['aff_track'] = $aff_track;
		}

		public function getIterator(){
			return new ArrayIterator($this->data);
		}
		
		public function get_api(){
			return $this->api;
		}
		
		public function get_data($name = null, $default = null){
			if (is_null($name)) return $this->data;
			elseif (isset($this->data[$name])) return $this->data[$name];
			elseif (! is_null($default)) return $default;
			throw new Exception ("No data exists for \"$name\"");
		}
		
		public function has_errors(){
			return isset($this->data['errors']);
		}
		
		public function get_errors($formatted = false){
			if (isset($this->data['errors']) AND count($this->data['errors'])){
				return $this->data['errors'];
			} else {
				return array();
			}
		}
		
		public function get_formatted_errors($format="html"){
			if (!empty($this->data['errors'])){
				$errors = $this->data['errors'];
				foreach($errors as $error_name => $error_item){
					$errors[$error_name] = implode("", $error_item);
				}
				if ($format=="html"){
					foreach($errors as $error_name => $error_item){
						$errors[$error_name] = "<li id=\"e_$error_item\">$error_item</li>";
					}
					return "<ul class=\"sm_errors\"><li \"{}\">" . implode("</li>\n<li>", $errors) . "</li></ul>";
				}
				elseif ($format=="text"){
					foreach($errors as $error_name => $error_item){
						$errors[$error_name] = "$error_name : $error_item; \n";
					}
					return "Errors :\n" . implode("\n", $errors);
				} else {
					throw new Exception("unknown error format : $format");
				}
			} else throw new Exception("Currently no errors, check \"has_errors\" first");
		}
		
		public function set_parameter($name, $data){
			$this->parameters[$name] = $data;
		}
		
		public function get_parameter($name, $default=null){
			if (isset($this->parameters[$name])) return $this->parameters[$name];
			elseif (!is_null($default)) return $default;
			throw new Exception ("No parameter exists for \"$name\"");
		}
		
		public function has_parameter($name){
			return isset($this->parameters[$name]);
		}
		
		
		public function render(){
			//get renderable object
			$renderable = $this->get_renderable();
			//process it
			return $renderable->render($this);
		}
		
		public function get_renderable(){
		
			//if renderable already exists
			if (is_null($this->renderable)) {
				
				//otherwise generate renderable
				
				//make a class that will render the data
				$renderable_class_name = get_class($this) . '__' . $this->get_parameter("view", "basic");
				$this->renderable = new $renderable_class_name($this);
				
				//merge the parameters and save them to this object
				$this->parameters = array_merge($this->renderable->get_parameters(), $this->parameters);
			}
			
			//load translation object
			$this->parameters['translate'] = new sm_translate($this->api->get_country());

			return $this->renderable;
		}
		
	}