<?php 
	$saveable_data['parameters']['only_required_fields'] = $_REQUEST['only_required_fields'];
	$saveable_data['parameters']['text_above_questions'] = stripslashes_deep($_REQUEST['text_above_questions']);
	$saveable_data['parameters']['submit_string'] = stripslashes_deep($_REQUEST['submit_string']);
	
	if (!empty($_REQUEST['default_activated'])){
		$saveable_defaults = array();
		foreach ($_REQUEST['default_activated'] as $saveable){
			$saveable_defaults[$saveable] = $_REQUEST['default'][$saveable];
		}
		$saveable_data['parameters']['defaults'] = $saveable_defaults;
	}