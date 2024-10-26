<?php 
	$validator->not_empty("only_required_fields", __("Choose whether to show all fields", "sm_translate"));
	if (isset($_REQUEST['default_activated'])){
		foreach($_REQUEST['default_activated'] as $activated){
			if (empty($_REQUEST['default'][$activated])) {
				$validator->add_error($activated, __("All defaulted values must have a value", "sm_translate"));
				break;
			}
		}	
	}