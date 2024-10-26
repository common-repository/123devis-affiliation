<?php 
	$saveable_data['parameters']['text_above_questions'] = stripslashes_deep($_REQUEST['text_above_questions']);
	$saveable_data['parameters']['worktypes'] = (isset($_REQUEST['worktypes']) ? $_REQUEST['worktypes'] : array());
	$saveable_data['parameters']['submit_string'] = stripslashes_deep($_REQUEST['submit_string']);
	$saveable_data['parameters']['multiple_display_form_type'] = $_REQUEST['multiple_display_form_type'];