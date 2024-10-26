<?php
	class sm_autoloader {
		private static $paths = array();
		
		public static function attach($more_paths = array()){
			$al_obj = new sm_autoloader();
			foreach ($more_paths as $autoload_path){
				sm_autoloader::add_path($autoload_path);
			}
			spl_autoload_register ("sm_autoloader::load");
		}
	
		public static function add_path($path){
			array_unshift(self::$paths, $path . "/");
		}
		
		public static function load($name){
			//exit if not servicemagic specific
			if (strpos($name, "sm_") !== 0) return;
			
			//naming convention uses __ in classnames as . in file paths
			$name = str_replace("__", ".", $name);
			
			//check each override path first to see if we can find the alternate class file
			foreach (self::$paths as $path){
				$override_path = $path . $name . '.php';
                if (file_exists($override_path)){
					require_once $override_path;
					return;
				}
			}
			
			//finally check the plugin for class file
			$lib_name = str_replace("_", "/", substr($name, 2)) . ".php";
			$lib_path = dirname(__FILE__) . $lib_name;
			if (file_exists($lib_path)){
				require_once $lib_path;
				return;
			}
		}
	}
