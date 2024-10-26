<?php
	class sm_translate {
		
		static $translations;
		private $lang;
		private $translations_dir;
		
		public function __construct($lang){
			$this->lang = $lang;
			$this->translations_dir = dirname(__FILE__) . '/translations/';
			self::$translations = array();
		}
		
		public function set_translations_dir($dir){
			$this->translations_dir = $dir;
		}
		
		private function lang_discovered($lang){
			return is_array(self::$translations) && isset(self::$translations[$lang]);
		}
		
		private function discover($lang){
			$transfile = $this->translations_dir . $this->lang . '.txt';
			
			if (!file_exists($transfile)) {
             	self::$translations[$lang] = array();
				return;
			}

			$strings = file($transfile);
			foreach ($strings as $line) {
				$ipos = 0;
				$trans = array();
				
				while ($ipos = strpos($line, ":", $ipos)){
					if ($line[$ipos-1] == "\\") $ipos++;
					else {
						$trans[0] = trim(substr($line, 0, $ipos));
						$trans[1] = trim(substr($line, $ipos+1));
						break;
					}
				}

				if (count($trans) !== 2) {
					continue;
				}
				
				foreach($trans as $k => $v){
					$trans[$k] = str_replace("\:", ":", $v);
				}

				self::$translations[$lang][$trans[0]] = $trans[1];
			}
		}
		
		public function trans($word, $lang = null){
			$lang = !is_null($lang) ? $lang : $this->lang;

			if (!$this->lang_discovered($lang)){
				$this->discover($lang);
			}
			
			if (array_key_exists($word, self::$translations[$lang])){
				return self::$translations[$lang][$word];
			} else return $word;
		}
		
		public function write($word, $lang = null){
			print $this->trans($word, $lang);
		}
	}