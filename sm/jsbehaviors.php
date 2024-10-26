<?php
    class sm_jsbehaviors {
        private $s = '';
        private $root_jq_selector;
        public function render($data, $root_jq_selector){
            $this->root_jq_selector = $root_jq_selector;
            $s = "";
            foreach($data as $dataitem){
                if (isset($dataitem['behavior'])){
                    foreach ($dataitem['behavior'] as $behavior){
                        $clsname = $behavior['type']. "_bhvr";
                        $s .= $this->$clsname($dataitem, $behavior);
                    }
                }
            }
            
            return $s;
        }
        
        protected function hidden_unless_bhvr($calledby, $behavior){
            $observed = $behavior['observed'];
            $target = $calledby['name'];
            $conditions = $behavior['conditions']['value_in'];
            $js_in_array = "[\"" . implode("\",\"", $conditions) . "\"]";
            
            $s = "$(\"#{$target}_wrap\", $(\"{$this->root_jq_selector}\")).hide();";
            $s .= "$(\"#{$observed}_form\", $(\"{$this->root_jq_selector}\")).change(function(){\n";
            $s .= " var v = $(this).val();\n";
            $s .= " if (jQuery.inArray(v, $js_in_array) == -1){\n";
            $s .= " $(\"#{$target}_wrap\", $(\"{$this->root_jq_selector}\")).hide();";
            $s .= "} else {";
            $s .= " $(\"#{$target}_wrap\", $(\"{$this->root_jq_selector}\")).show('slow');";
            $s .= "}\n";
            $s .= "});\n";
           
            return $s;
        }
		
		protected function alternate_validation_bhvr($calledby, $behavior){
            $observed = $behavior['observed'];
            $target = $calledby['name'];
            $conditions = $behavior['conditions']['value_in'];
            $js_in_array = "[\"" . implode("\",\"", $conditions) . "\"]";
			
			//determine the errors that will be alternated
			$err_obj = new sm_baseinterview;
	
			$default_errs = $err_obj->setup_jquery_validate_messages(array($calledby));
			$behavior['name'] = $calledby['name']; //needed to get s_j_v_m VV to work
			$update_errs = $err_obj->setup_jquery_validate_messages(array($behavior));			
			
			if (empty($default_errs['rules'])){
				$default_e = array();
			} else {
				$default_e = $default_errs['rules'][$calledby['name']];
				$default_e['messages'] = $default_errs['messages'][$calledby['name']];
			}
			
			if (empty($update_errs['rules'])){
				$update_e = array();
			} else {
				$update_e = $update_errs['rules'][$calledby['name']];	
				$update_e['messages'] = $update_errs['messages'][$calledby['name']];
			}

			$s = "";
			$s .= "$(\"#{$observed}_form\", $(\"{$this->root_jq_selector}\")).change(function(){\n";
            $s .= " var v = $(this).val();\n";
            $s .= " if (jQuery.inArray(v, $js_in_array) == -1){\n";
			//$s .= "		console.log($(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules());";
            $s .= " 	$(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules(\"remove\");\n";
			$s .= " 	$(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules(\"add\", " . json_encode($default_e) . ")\n";
			//$s .= "		console.log($(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules());";
            $s .= "} else {";
			//$s .= "		console.log($(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules());";
            $s .= " 	$(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules(\"remove\");\n";
			$s .= " 	$(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules(\"add\", " . json_encode($update_e) . ")\n";
			//$s .= "		console.log($(\"#{$target}_form\", $(\"{$this->root_jq_selector}\")).rules());";
			
            $s .= "}\n";
            $s .= "});\n";
            $s .= "$(\"#{$observed}_form\", $(\"{$this->root_jq_selector}\")).change();\n";
            return $s;
        }
        
    }
     
    /*   
"behavior": [
{
"observed": "work_type_monovalue",
"conditions": {
"value_in": [
"work_type_monovalue__repair",
"work_type_monovalue__maintenance"
]
},
"type": "hidden_unless"
}
],
*/