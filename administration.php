<?php
/*
 * @redmine #11003 		: AFFILIATION | Amélioration design API Wordpress SR FORMS
 */

function sm_admin_menu_setup() {
	wp_enqueue_style('sm_admin_css', plugins_url('ui/css/sm_admin.css', __FILE__));

	//WEBSITE-3691 : API Change the publishers' rights
	if(!(!get_option('sm_creds') && !current_user_can('sm_api_manage_options'))){

	// Top level menu
	add_menu_page(__('ServiceMagic','sm_translate'), __('ServiceMagic','sm_translate'), 'sm_api_manage_forms', 'sm_admin_help', 'sm_admin_help_page', plugins_url('ui/img/sm_logo.png', __FILE__));

	add_submenu_page('sm_admin_help', __('Help','sm_translate'), __('First steps, help and Suggestions','sm_translate'), 'sm_api_manage_forms', 'sm_admin_help', 'sm_admin_help_page');

	//duplicate root menu as submenu to rename
	add_submenu_page('sm_admin_help', __('Credentials','sm_translate'), __('Credentials','sm_translate'), 'sm_api_manage_options', 'sm_admin_settings', 'sm_admin_settings_page' );

	if (get_option('sm_creds')){
		// SM Forms

		add_submenu_page('sm_admin_help', __('Embeddable Forms','sm_translate'), __('Embeddable Forms','sm_translate'), 'sm_api_manage_forms', 'sm_admin_forms', 'sm_admin_forms_page');
		add_submenu_page('sm_admin_forms', __('Embeddable Form','sm_translate'), __('Embeddable Form','sm_translate'), 'sm_api_manage_forms', 'sm_admin_forms_form', 'sm_admin_forms_form_page');
		add_submenu_page('sm_admin_forms', __('Embeddable Form','sm_translate'), __('Embeddable Form','sm_translate'), 'sm_api_manage_forms', 'sm_admin_sr_forms_form', 'sm_admin_sr_forms_form_page');
		add_submenu_page('sm_admin_forms', __('Embeddable Form','sm_translate'), __('Embeddable Form','sm_translate'), 'sm_api_manage_forms', 'sm_admin_sp_forms_form', 'sm_admin_sp_forms_form_page');

		// SM Form Defaults
		add_submenu_page('sm_admin_help', __('Form Options','sm_translate'), __('Form Options','sm_translate'), 'sm_api_manage_forms', 'sm_admin_form_defaults', 'sm_admin_form_defaults_page');

		//add_submenu_page('sm_admin_settings', __('Tracking Labels','sm_translate'), __('Tracking Labels','sm_translate'), 'manage_options', 'sm_admin_labels', 'sm_admin_labels_page');
	}
	// Documentation
	add_submenu_page('sm_admin_help', __('Documentation','sm_translate'), __('Documentation','sm_translate'), 'sm_api_manage_forms', 'sm_admin_docs', 'sm_admin_docs_page');
	//add_submenu_page('sm_admin_docs', __('Quick Start','sm_translate'), __('Quick Start','sm_translate'), 'manage_options', 'sm_admin_docs_quickstart', 'sm_admin_docs_quickstart_page');

	add_submenu_page('sm_admin_help', __('History','sm_translate'), __('History','sm_translate'), 'sm_api_manage_options', 'sm_history', 'sm_history_page');
	}
}

// credentials
function sm_admin_settings_page() {
	$sm_version = sm_get_plugin_version();
	$nounce = wp_create_nonce( 'settings_form' );
	$sm_api_url = get_option("sm_api_url");
	$current_sm_creds = get_option("sm_creds");
	$sm_api_server = get_option("sm_api_server");
	$messages = array();

	if (!empty($_POST) AND isset($_POST['sm_username']) AND isset($_POST['_nonce'])){
		$nonce = $_POST['_nonce'];
		if (!wp_verify_nonce($nonce, 'settings_form')){
			die("bad nounce");
		}

		$settings_validator = new sm_validation;
		$settings_validator->set_data($_POST);
		$settings_validator->not_empty("sm_username", __("The Username is required", "sm_translate"));
		$settings_validator->not_empty("sm_password", __("The Password is required", "sm_translate"));
		$settings_validator->valid_full_url("sm_api_url", __("The API Url must be a valid url", "sm_translate"));

		$sm_creds = array(
			'sm_username' => $_POST['sm_username'],
			'sm_password' => $_POST['sm_password']
		);

		$sm_api_url = $_POST['sm_api_url'];
		$sm_api_server = $_POST['sm_api_server'];

		if (!$settings_validator->has_errors()){
			//call api to see if creds work
			try {
				$api = sm_api_factory();
				$api->set_api_url($sm_api_url, $sm_api_server);

				$validate_api = $api->account->validateapi->post();

				if (!$validate_api->was_successful()){
					$settings_validator->add_error("sm_api_url", $validate_api->get_api_errors());
				}
			} catch(sm_exception_httperror $e){
				$settings_validator->add_error("sm_api_url", __("There was a problem connecting to the ServiceMagic API.  Please check your API Url settings or contact your Affiliate Representative.", "sm_translate"));
			}
		}

		//only call api to check creds if we know it might be valid
		if (!$settings_validator->has_errors()){
			//here we know we got a valid api url so we update the wp-options which get reloaded in sm_api_factory
			update_option("sm_api_url", $sm_api_url);
			update_option("sm_api_server", $sm_api_server);

			//call api to see if creds work
			$api = sm_api_factory();

			$cred_check_result = $api->account->validate->post($sm_creds);

			__("Login details not found", "sm_translate");

			if ($cred_check_result->has_errors()){
				foreach($cred_check_result->get_errors() as $error_key => $errors){
					foreach ($errors as $error){
						$settings_validator->add_error($error_key, __($error, "sm_translate") );
					}
				}
			}
		}


		if (!$settings_validator->has_errors()){
			//make saveable array used for future items
			$kwids = $cred_check_result->get_sm_kwids();
			$sm_saveable = array(
				"sm_aff_id" => $cred_check_result->get_sm_aff_id(),
				"sm_kwids"=>$kwids,
				"sm_token"=>$cred_check_result->get_sm_token(),
				"sm_username"=>$sm_creds['sm_username'],
			);

			update_option("sm_accept_spa", $cred_check_result->get_sm_spa_accept());

			if (get_option('sm_creds')){
				update_option("sm_creds", $sm_saveable);
			} else {
				add_option("sm_creds", $sm_saveable);
			}

			new sm_wp_log("Settings validated. Affiliate id : ".$cred_check_result->get_sm_aff_id().", KWID : " . $kwids[0]);
			$messages["updated"] = __("Credentials Saved", "sm_translate");

		} else {
			new sm_wp_log(array("type" => "warning", "message" => __("Settings not accepted", "sm_translate") . $settings_validator->get_formatted_errors()));
			$messages["error"] = $settings_validator->get_formatted_errors(__("Please fix these errors", "sm_translate"));
		}

	}

	include 'forms/settings.php';
}

// credentials
function sm_admin_form_defaults_page() {
	$nounce = wp_create_nonce( 'settings_form' );
	$sm_display_defaults_default_options = array(
		'sm_font_size'		=> '',
		'sm_bg_color'		=> '',
		'sm_font_color'		=> '',
		'sm_design'			=> 1,
		);
	$sm_display_defaults = array_merge($sm_display_defaults_default_options, get_option("sm_display_defaults"));
	$sm_default_aff_str = get_option("sm_default_aff_str");
	$sm_default_success_more_text = get_option("sm_default_success_more_text");
	$sm_deactivate_api_during_slow = get_option("sm_deactivate_api_during_slow", 0);
	$sm_set_user_cookie = get_option("sm_set_user_cookie", 1);
	$sm_clear_all_trace_on_deactivation = get_option("sm_clear_all_trace_on_deactivation", 0);
	$sm_api_cache_mechanism = get_option("sm_api_cache_mechanism", "ETAG");
	$sm_sr_ty_message = get_option("sm_sr_ty_message", "");
	$sm_sp_ty_message = get_option("sm_sp_ty_message", "");
	// #100018 Affiliation API WP - add generic pages
	$sm_default_allact_title  = get_option('sm_default_allact_title', '');
	$sm_default_listcat_title = get_option('sm_default_listcat_title', '');
	$sm_default_genform_title = get_option('sm_default_genform_title', '');
	$sm_default_form_gen 	  = get_option('sm_default_form_gen', '');

	if ($sm_accept_spa = get_option("sm_accept_spa", 0)){
		$sm_api_server = get_option("sm_api_server");
		$sm_api_server_country = strtoupper(str_replace(array("dev-", "local-"), "", $sm_api_server));
		$sm_sp_submit_to_country = get_option("sm_sp_submit_to_country", $sm_api_server_country);
	}

	$form_data_list = array(stripslashes_deep($_REQUEST));

	$messages = array();

	if (!empty($_POST) AND isset($_POST['sm_font_size']) AND isset($_POST['_nonce'])){
		$nonce = $_POST['_nonce'];
		if (!wp_verify_nonce($nonce, 'settings_form')){
			die("bad nounce");
		}

		$display_validator = new sm_validation;
		$display_validator->set_data($_POST);

		//$display_validator->not_empty("sm_default_aff_str", __("Affiliate Tracking String must not be empty", "sm_translate"));
		if ($_POST['sm_font_size']) $display_validator->is_int("sm_font_size", __("The Font Size must be an integer", "sm_translate"));
		if ($_POST['sm_font_color']) $display_validator->hex_value("sm_font_color", __("The Font Color must be a valid hex value", "sm_translate"));
		if ($_POST['sm_bg_color']) $display_validator->hex_value("sm_bg_color", __("The Background Color must be a valid hex value", "sm_translate"));
		$display_validator->is_in_list("sm_api_cache_mechanism", __("The Cache mechanism must be selected", "sm_translate"), array("ETAG","Timeout"));

		$sm_display_defaults = array(
			"sm_font_size" => $_POST['sm_font_size'],
			"sm_font_color" => $_POST['sm_font_color'],
			"sm_bg_color" => $_POST['sm_bg_color'],
			"sm_design" => $_POST['sm_design'],
		);

		if ($display_validator->has_errors()){
			$messages["error"] = $display_validator->get_formatted_errors(__("Please fix these errors", 'sm_translate'));
			new sm_wp_log(array("type"=>"warning", "message" => "API Defaults not accepted " . $display_validator->get_formatted_errors()));
		} else {
			//update
			update_option("sm_display_defaults", $sm_display_defaults);
			$sm_deactivate_api_during_slow = empty($_POST['sm_deactivate_api_during_slow']) ? "0" : "1";
			update_option("sm_deactivate_api_during_slow", $sm_deactivate_api_during_slow);
			$sm_clear_all_trace_on_deactivation = empty($_POST['sm_clear_all_trace_on_deactivation']) ? "0" : "1";
			update_option("sm_clear_all_trace_on_deactivation", $sm_clear_all_trace_on_deactivation);
			update_option("sm_default_success_more_text", stripslashes_deep($_POST['sm_default_success_more_text']));
			update_option("sm_api_cache_mechanism", stripslashes_deep($_POST['sm_api_cache_mechanism']));
			$sm_set_user_cookie = empty($_POST['sm_set_user_cookie']) ? "0" : "1";
			update_option("sm_set_user_cookie", $sm_set_user_cookie);
			update_option("sm_default_aff_str", $_POST['sm_default_aff_str']);
			update_option("sm_sr_ty_message", stripslashes_deep($_POST['sm_sr_ty_message']));
			if (get_option("sm_accept_spa", 0)){
				update_option("sm_sp_ty_message", stripslashes_deep($_POST['sm_sp_ty_message']));
				update_option("sm_sp_submit_to_country", $_POST['sm_sp_submit_to_country']);
			}		update_option("sm_default_allact_title", 	$_POST['sm_default_allact_title']);
			// save settings of Generic Pages
			update_option("sm_default_allact_title", 	$_POST['sm_default_allact_title']);
			update_option("sm_default_listcat_title", 	$_POST['sm_default_listcat_title']);
			update_option("sm_default_genform_title", 	$_POST['sm_default_genform_title']);
			update_option("sm_default_form_gen", 		$_POST['sm_default_form_gen']);
			$messages["updated"] = __("Options Saved!", "sm_translate");
			new sm_wp_log("API Defaults updated");
		}
	}
	//colorpicker script
	wp_enqueue_style('colorpicker_css', plugins_url('sm/ui/js/jquery-miniColors/jquery.miniColors.css', __FILE__));
	wp_enqueue_script('colorpicker_js', plugins_url('sm/ui/js/jquery-miniColors/jquery.miniColors.js', __FILE__));

	// get generics forms
	$sm_sr_generic_forms = sm_get_generic_sr_forms();

	include 'forms/form_defaults.php';
}

// forms
function sm_admin_forms_page() {
	global $wpdb;
	$messages = array();
	$nounce = wp_create_nonce( 'admin_forms' );
	if (!empty($_POST) AND isset($_POST['type'], $_POST['id'], $_POST['action'], $_POST["_archive_nounce"])){
		if (!wp_verify_nonce($_POST["_archive_nounce"], 'admin_forms')){
			die("bad nounce");
		}
		$tbl = $wpdb->prefix . "sm_" . ($_POST['type'] == 'sr' ? "sr" : "sp") . "_forms";
		if ($_POST['action'] == 'delete'){
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $tbl
					WHERE id = %d",
					$_POST['id']
				)
			);
		} else {
			$wpdb->update(
				$tbl,
				array('is_archived' => ($_POST['action'] == 'archive' ? 1 : 0)),
				array('id' => $_POST['id'])
			);
		}
		switch($_POST['action']){
			case 'archive':	$messages['updated'] = __("Item archived", "sm_translate"); break;
			case 'delete' : $messages['updated'] = __("Item permanently deleted", "sm_translate"); break;
			case 'unarchive' : $messages['updated'] = __("Item de-archived", "sm_translate"); break;
		}
	}

	$mysrforms = $wpdb->get_results( "SELECT id, is_archived, activity_id, activity_title, embedable_name, name, parameters, created, altered FROM {$wpdb->prefix}sm_sr_forms" );
	$mysrforms_counts = array("active"=>0, "archived"=>0);
	foreach($mysrforms as $myform) $mysrforms_counts[$myform->is_archived ? "archived" : "active"]++;
	if (get_option( "sm_accept_spa", 0)){
		$myspforms = $wpdb->get_results( "SELECT id, is_archived, embedable_name, name, parameters, created, altered FROM {$wpdb->prefix}sm_sp_forms" );
		$myspforms_counts = array("active"=>0, "archived"=>0);
		foreach($myspforms as $myform) $myspforms_counts[$myform->is_archived ? "archived" : "active"]++;
	}

	include "forms/embeddables_list.php";
}

// forms
function sm_admin_forms_form_page() {
	global $wpdb;

	$sm_accept_spa = get_option( "sm_accept_spa", 0);

	//get directory list of options
	$this_dir = dirname(__FILE__);

	//depending on privileges to submit spa, create patterns for glob that find folders starting with sp, or sr or both
	$patterns = array("sr");

	if ($sm_accept_spa){
		$patterns[] = "sp";
	}

	$pattern = $this_dir . '/forms/embed_{'.implode(",", $patterns).'}*';

	$embeddable_folders = glob( $pattern, GLOB_ONLYDIR|GLOB_BRACE);

	//show list
	include "forms/embeddables_form_list.php";
	return;
}

// forms
function sm_admin_sr_forms_form_page() {
	global $wpdb;

	$view_type="sr";

	$sm_default_success_more_text = get_option("sm_default_success_more_text");
	$messages = array();

	$id = intval($_REQUEST["id"]);
	$form_data_list = array(stripslashes_deep($_REQUEST));

	wp_enqueue_style('sm_admin_css', plugins_url('sm_admin.css', __FILE__));

	if (!empty($_POST) AND isset($_POST['_nonce'])){
		$nonce = $_POST['_nonce'];
		if (!wp_verify_nonce($nonce, 'embeddable_form')){
			$messages["error"] = "bad nounce";
			include 'forms/show_message.php';
			return;
		}

		if (isset($_REQUEST['category']) && $_REQUEST['category']=='999') {
			$_REQUEST['activity_id'] = 999;
			$_REQUEST['activity_title'] = __('All categories', 'sm_translate');;

		}
		$saveable_data = array_intersect_key($_REQUEST, array(
			"name"=>1,
			"activity_id"=>1,
			"tracking_label"=>1,
			"activity_title"=>1,
			"embedable_name" =>1
		));

		$saveable_data = stripslashes_deep($saveable_data);
		$saveable_data['parameters'] = array();

		$validator = new sm_validation;
		$validator->set_data($_POST);

		$validator->not_empty("name", __("Name is required", "sm_translate"));
		$validator->not_empty("activity_id", __("Please select a ServiceMagic form", "sm_translate"));
		//$validator->not_empty("tracking_label", __("Please select a Label", "sm_translate"));

		$more_val = sm_val_in_arrays("view",$form_data_list, "none");
		if (file_exists($more_val)){
			include $more_val;
		}

		if (intval($_REQUEST['id']) == 0 OR isset($saveable_data['embedable_name'])){
			$saveable_data['embedable_name'] = sm_sanitize_for_slug((isset($saveable_data['embedable_name']) ? $saveable_data['embedable_name'] : $saveable_data['name']));
			//$validator->must_match("embedable_name", $saveable_data['embedable_name'], __("This form identifyer is not valid. Only letters, numbers, and underscores are permissible."));
			if ($saveable_data['embedable_name'] == '') {
				$validator->add_error('name', __("This Name is too short or not useable", "sm_translate"));
			}
			$uniquetest = $wpdb->get_row(
				$wpdb->prepare( "SELECT id FROM {$wpdb->prefix}sm_sr_forms WHERE embedable_name = '%s' AND id <> %d UNION SELECT id FROM {$wpdb->prefix}sm_sp_forms WHERE embedable_name = '%s'",
					$saveable_data['embedable_name'],
					$id,
					$saveable_data['embedable_name']
				)
			);
			if (!empty($uniquetest)){
				$validator->add_error('name', __("This Name is not unique", "sm_translate"));
			}
			//unset the embedable_name in $form_data_list (ordered lookup array)
			//so that updated embedable_name is used instead of one from request which is not sanitized
			unset ($form_data_list[0]['embedable_name']);
		}

		//give this display mode a chance to validate
		$view = sm_val_in_arrays("view", $form_data_list, "none");
		$validation_file = dirname(__FILE__) . "/forms/embed_sr_" . $view . "/validation.php";
		if (file_exists($validation_file)){
			include $validation_file;
		}

		if (!$validator->has_errors()){
			//give this display mode a chance to add to saveable info
			$view = sm_val_in_arrays("view",$form_data_list, "none");
			$saveable_file = dirname(__FILE__) . "/forms/embed_sr_" . $view . "/save.php";

			if (file_exists($saveable_file)){
				include $saveable_file;
			}

			$saveable_data['parameters']['view'] = $view;
			$saveable_data['parameters']['success_more_text'] = stripslashes_deep($_REQUEST['success_more_text']);
			$saveable_data['parameters'] = json_encode($saveable_data['parameters']);

			if (!empty($_REQUEST['id'])){
				$where = array("id"=>intval($_REQUEST['id']));
				$saveable_data['altered'] = date( 'Y-m-d H:i:s');
				$wpdb->update( "{$wpdb->prefix}sm_sr_forms", $saveable_data, $where);
				new sm_wp_log("Embeddable SR form \"{$saveable_data['name']}\" updated");

			} else {
				$saveable_data['created'] = date( 'Y-m-d H:i:s');
				$wpdb->insert( "{$wpdb->prefix}sm_sr_forms", $saveable_data );
				$id = $wpdb->insert_id;
				new sm_wp_log("Embeddable SR form \"{$saveable_data['name']}\" created");
			}
			$messages["updated"] = __("Saved", "sm_translate")
				. ". "
				. "<a href=\"admin.php?page=sm_admin_forms\">"
				. __("Return to embedable forms list", "sm_translate")
				. "</a>?";
		} else {
			new sm_wp_log(array("type"=>"warning","message"=>"Embeddable SR form not accepted " . $validator->get_formatted_errors(__("Please fix these errors", "sm_translate"))));
			$messages["error"] = $validator->get_formatted_errors(__("Please fix these errors", "sm_translate"));

		}
	}

	//load this form
	$myform = $wpdb->get_row( $wpdb->prepare("SELECT id, embedable_name, name, activity_id, activity_title, tracking_label, parameters, created, altered FROM {$wpdb->prefix}sm_sr_forms WHERE id=%d", $id) );

	if (!empty($myform)){
		if ($myform->embedable_name != sm_sanitize_for_slug($myform->embedable_name)){
			$messages["error"] = __("This form has invalid charachters in the form identifyer. Only letters, numbers and underscores are permissible.", "sm_translate");
		}
		$form_data_list[] = (array)$myform;

		$myform->parameters = json_decode($myform->parameters, true);
		$form_data_list[] = $myform->parameters;
		$save_action_target = admin_url( "admin.php?page=sm_admin_sr_forms_form&id=$id");
	} else {
		$save_action_target = "";
	}


	//give this display mode a chance do some settings - ususally wp includes for js /css
	$view = sm_val_in_arrays("view", $form_data_list, "none");
	$this_file = dirname(__FILE__) . "/forms/embed_sr_" . $view . "/setup.php";
	if (file_exists($this_file)){
		include $this_file;
	}

	// API WordPress: création auto des pages génériques
	$categories = sm_api_get_categories_list();

	include 'forms/embeddable.php';
}


// forms
function sm_admin_sp_forms_form_page() {
	global $wpdb;
	$view_type="sp";
	$sm_default_success_more_text = get_option("sm_default_success_more_text");
	$messages = array();

	$id = intval($_REQUEST["id"]);

	$form_data_list = array(stripslashes_deep($_REQUEST));

	wp_enqueue_style('sm_admin_css', plugins_url('sm_admin.css', __FILE__));

	if (!empty($_POST) AND isset($_POST['_nonce'])){

		$saveable_data = array_intersect_key($_REQUEST, array(
			"name"=>1,
			"tracking_label"=>1,
			"embedable_name"=>1
		));

		$saveable_data = stripslashes_deep($saveable_data);

		$saveable_data['parameters'] = array();

		$nonce = $_POST['_nonce'];
		if (!wp_verify_nonce($nonce, 'embeddable_form')){
			$messages["error"] = __("There was a problem connecting to the ServiceMagic API.  Please try again soon or contact your Affiliate Representative.", "sm_translate");
			include 'forms/show_message.php';
			return;
		}

		//start validation
		$validator = new sm_validation;
		$validator->set_data($_POST);
		$validator->not_empty("name", __("Name is required", "sm_translate"));

		//give form a chance to validate
		$more_val = sm_val_in_arrays("view",$form_data_list, "none");
		if (file_exists($more_val)){
			include $more_val;
		}

		if (intval($_REQUEST['id']) == 0 OR isset($saveable_data['embedable_name'])){
			$saveable_data['embedable_name'] = sm_sanitize_for_slug((isset($saveable_data['embedable_name']) ? $saveable_data['embedable_name'] : $saveable_data['name']));
			//$validator->must_match("embedable_name", $saveable_data['embedable_name'], __("This form identifyer is not valid. Only letters, numbers, dashes, and underscores are permissible."));

			if ($saveable_data['embedable_name'] == '') {
				$validator->add_error('name', __("This Name is too short or not useable", "sm_translate"));
			}

			//confirm embedable_name is unique
			$uniquetest = $wpdb->get_row(
				$wpdb->prepare( "SELECT id FROM {$wpdb->prefix}sm_sp_forms WHERE embedable_name = '%s' AND id <> %d UNION SELECT id FROM {$wpdb->prefix}sm_sr_forms WHERE embedable_name = '%s'",
					$saveable_data['embedable_name'],
					$id,
					$saveable_data['embedable_name']
				)
			);
			if (!empty($uniquetest)){
				$validator->add_error('name', __("This Name is not unique", "sm_translate"));
			}
			//unset the embedable_name in $form_data_list (ordered lookup array)
			//so that updated embedable_name is used instead of one from request which is not sanitized
			unset ($form_data_list[0]['embedable_name']);
		}

		//give this display mode a chance to validate
		$view = sm_val_in_arrays("view", $form_data_list, "none");
		$this_file = dirname(__FILE__) . "/forms/embed_sp_" . $view . "/validation.php";
		if (file_exists($this_file)){
			include $this_file;
		}

		if (!$validator->has_errors()){

			//give this display mode a chance to validate
			$view = sm_val_in_arrays("view",$form_data_list, "none");
			$this_file = dirname(__FILE__) . "/forms/embed_sp_" . $view . "/save.php";
			if (file_exists($this_file)){
				include $this_file;
			}

			$saveable_data['parameters']['view'] = $view;
			$saveable_data['parameters']['success_more_text'] = stripslashes_deep($_REQUEST['success_more_text']);
			$saveable_data['parameters'] = json_encode($saveable_data['parameters']);

			if (intval($_REQUEST['id'])){
				$where = array("id"=>$_REQUEST['id']);
				$saveable_data['altered'] = date( 'Y-m-d H:i:s');
				$wpdb->update( "{$wpdb->prefix}sm_sp_forms", $saveable_data, $where);
				new sm_wp_log("Embeddable SP form \"{$saveable_data['name']}\" updated");
			} else {
				$saveable_data['created'] = date( 'Y-m-d H:i:s');
				$wpdb->insert( "{$wpdb->prefix}sm_sp_forms", $saveable_data );
				$id = $wpdb->insert_id;
				new sm_wp_log("Embeddable SP form \"{$saveable_data['name']}\" created");
			}
			$messages["updated"] = __("Saved", "sm_translate")
				. ". "
				. "<a href=\"admin.php?page=sm_admin_forms\">"
				. __("Return to embedable forms list", "sm_translate")
				. "</a>?";
		} else {
			$messages["error"] = $validator->get_formatted_errors("Please fix these errors", "sm_translate");
			new sm_wp_log(array("type"=>"warning","message"=>"Embeddable SP form not accepted " . $validator->get_formatted_errors()));
		}
	}

	$myform = $wpdb->get_row( $wpdb->prepare("SELECT id, embedable_name, name, tracking_label, parameters, created, altered FROM {$wpdb->prefix}sm_sp_forms WHERE id=%d", $id) );

	if (!empty($myform)){
		if ($myform->embedable_name != sm_sanitize_for_slug($myform->embedable_name)){
			$messages["error"] = __("This form has invalid charachters in the form identifyer. Only letters, underscores and dashes are acceptable.", "sm_translate");
		}

		$form_data_list[] = (array)$myform;

		$myform->parameters = json_decode($myform->parameters, true);
		if (isset($myform->parameters['worktype'])) {
			$myform->worktype = $myform->parameters['worktype'];
		}
		$form_data_list[] = $myform->parameters;
		$save_action_target = admin_url( "admin.php?page=sm_admin_sp_forms_form&id=$id");
	} else {
		$save_action_target = "";
	}

	$api = sm_api_factory();

	try {
		$interview_obj = $api->sp->interview->get();
	} catch (sm_exception_httperror $e){
		$messages["error"] = __("There was a problem connecting to the ServiceMagic API.  Please try again soon or contact your Affiliate Representative.", "sm_translate");
		include 'forms/show_message.php';
		return;
	}

	include 'forms/embeddable.php';
}

// Documentation
function sm_admin_docs_page() {
	new sm_wp_log("User on documentation page");
	$locale = get_locale();

	$locale = str_replace("_", "-", $locale);

	$path = plugin_dir_path(__FILE__) . 'forms/docs_quickstart-' . $locale . '.php';

	if (file_exists($path)){
		include $path;
	} else {
		include "forms/docs_quickstart.php";
	}
}

function sm_admin_help_page() {
	new sm_wp_log("User on help page");
	$locale = get_locale();
	$locale = str_replace("_", "-", $locale);

	$path = plugin_dir_path(__FILE__) . 'forms/help-' . $locale . '.php';

	if (file_exists($path)){
		include $path;
	} else {
		include "forms/help.php";
	}
}

function sm_history_page() {
	new sm_wp_log("Reviewing History");

	wp_enqueue_script('datatablesjs', "//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js");
	wp_enqueue_style('datatablescss', "//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css");
	wp_enqueue_style('datatablestrcss', "//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css");

	//sm_locale used to load js language files for datatables
	$sm_locale = (get_locale() AND file_exists(plugin_dir_path(__FILE__) . 'ui/js/datatables.1.9.4.' . get_locale() . '.txt')) ? get_locale() : "";

	//tzs used to prep date presentation in datatables grid per wp locale setting
	$orig_tzs = date_default_timezone_get();
	$tzs = get_option('timezone_string', '');

	if (empty($tzs)){
		if ($orig_tzs == 'UTC' OR empty($orig_tzs)){
			$tzs = "Europe/Paris";
		} else {
			$tzs = $orig_tzs;
		}
	}

	include 'forms/history.php';
}

