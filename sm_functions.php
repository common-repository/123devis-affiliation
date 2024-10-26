<?php
	/*
	[QA][MEP] MKTSM-899 Transfer the "iv" parameter through the API - sm_functions_133624_1.php
	[QA][MEP] WEBSITE-5359 : Ninja forms rich text editor broken - sm_functions_133624_2.php
	*/
	$GLOBALS['glapi'] = null;
	$GLOBALS['interview'] = null;

	function sm_api_factory(){
		global $glapi;

		if ($glapi)	return $glapi;

		$sm_creds = get_option("sm_creds");

		if (!empty($sm_creds['sm_aff_id']) && !empty($sm_creds['sm_token'])){
			$api = new sm_api($sm_creds['sm_aff_id'], $sm_creds['sm_token']);
		} else {
			$api = new sm_api();
		}

		$api->add_header("X-SMF-FORWARDED-KWID", sm_get_kwid());

		if (defined('SM_ENVTYPE')){
			foreach (array('gclid', 'iv_') as $fld){
				if (!empty($_SESSION['track_sem_field_' . $fld])){
					$api->add_header("X-SMF-FORWARDED-" . strtoupper(str_replace('_', '', $fld)), $_SESSION['track_sem_field_' . $fld]);
				}
			}
		}

		//set the creds and cache mechanism
		$api->set_api_url(get_option("sm_api_url"), get_option("sm_api_server"));
		$api->set_cache_mechanism(get_option("sm_api_cache_mechanism", "ETAG"));

		$session_id = session_id();

		if (!empty($session_id)){
			$api->add_header("X-SMF-FORWARDED-SESSION-ID", $session_id);
		} elseif (isset($_COOKIE['sm_uid'])){
			$api->add_header("X-SMF-FORWARDED-SESSION-ID", $_COOKIE['sm_uid']);
		}

		$glapi = $api;

		return $api;
	}

	function sm_val_in_arrays($key, $arrays, $default){
		foreach ($arrays as $array){
			if (!is_array($array)) continue;

			if (is_array($array) && array_key_exists($key, $array)){
				return $array[$key];
			}
		}
		return $default;
	}

	function sm_head_embedeableforms_js_json(){
		global $wpdb;

		//only need this if editing a page
		if (!sm_is_a_post_editor()) return;

		$r = array("sr"=>array());
		if (get_option( "sm_accept_spa", 0)){
			$r["sp"] = array();
		}


		foreach ($r as $type => $nouse){
			if ($type=='sr') 	$filtre_activity = ' and activity_id<>999 ';
			else 	$filtre_activity = '';

			$myforms = $wpdb->get_results( "SELECT embedable_name FROM {$wpdb->prefix}sm_{$type}_forms WHERE is_archived <> 1 $filtre_activity ORDER BY embedable_name" );
			foreach ($myforms as $myform){
				$r[$type][] = $myform->embedable_name;
			}
		}

		// API WordPress: création auto des pages génériques - get caterogies list via api
		$categories = sm_api_get_categories_list();

		// API WordPress: création auto des pages génériques - get interviews for each category via api
		$interviews = array();
		foreach ($categories as $category) {
			$interviews[$category['id']] = sm_api_get_interviews($category['id']);
		}

		// API WP - get generic pages
		$shortcodes = array();
		$generic_pages = sm_get_generic_pages();
		foreach($generic_pages as $generic_page) {
			$shortcodes[$generic_page['post_name']] = $generic_page['post_content'];
		}

		echo '<script> var sm_embedeable_names = ' . json_encode($r) . '</script>';
		echo '<script> var sm_categories = ' . json_encode($categories) . '</script>';
		echo '<script> var sm_interviews = ' . json_encode($interviews) . '</script>';
		echo '<script> var sm_shortcodes = ' . json_encode($shortcodes) . '</script>';
		echo '<script> var sm_translate  = ' . json_encode(array(
						'ListCategories'	=> __( 'List of categories' , 'sm_translate'),
						'ListActivities'	=> __( 'List of activities', 'sm_translate'),
						'Item'				=> __( 'Item','sm_translate'),
						'SavedsrForms'		=> __( 'Saved sr Forms','sm_translate'),
						'SavedspForms'		=> __( 'Saved sp Forms','sm_translate'),
				)) . '</script>';


		return;
	}

	function sm_init(){
		if (get_option( 'sm_set_user_cookie', 1) && empty($_COOKIE['sm_uid'])){
			$user_id_val = substr(md5(str_shuffle (microtime(1))), 0, 25);
			$user_id_expire = time() + get_option("user_id_timeout", 3600 * 24 * 10);
			setcookie ( "sm_uid", $user_id_val,  $user_id_expire, "/");
		}

		//remove_action("send_headers", array()
		wp_enqueue_script("jquery");
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;

		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", "sm_tinymce_plugin");
			add_filter('mce_buttons', 'sm_tinymce_button');
			add_action('admin_footer','sm_head_embedeableforms_js_json');
		}

		add_filter('document_title_parts', 'sm_override_post_title', 10);
	}

	function sm_override_post_title($title){
		global $post;

		if ( is_page('interview') || is_page('categories') ) {

			$shortcode = trim(str_replace(array('[', ']'), array('',''), $post->post_content));
			$atab = explode(' ',$shortcode);
			$atts = array();
			foreach($atab as $att) {
				$value = explode('=',$att);
				$atts[$value[0]] = str_replace('"','',$value[1]);
			}
			if (isset($atts['header']))  {
				$atts['header'] = trim(str_replace(array('{{', '}}'), array('[',']'), $atts['header']));

			}

			if ( is_page( 'interview' ) ) {
				if (strpos($post->post_content,'default_sr_id')) {

					if (!empty($_REQUEST['sm_int_id']) && is_numeric($_REQUEST['sm_int_id'])){
						$sr_id = $_REQUEST['sm_int_id'];
					} else {
						$sr_id =  $atts['default_sr_id'];
					}

					$interview_params = array(
							'embedable_name' => $atts['form_name'],
							'_type' => 'sr',
							'_override_interview_id' => $sr_id
					);
					$interview_params = apply_filters('smwp_pre_make_interview', $interview_params);

					$interview = sm_make_interview_from_embeddable($interview_params);

					$title['title'] = $interview->get_title();
	// 				$title['page'] = '2'; // optional
	// 				$title['tagline'] = 'Home Of Genesis Themes'; // optional
	// 				$title['site'] = 'DevelopersQ'; //optional
					$post->post_title = $title['title'];
				}
			}

			if ( is_page( 'categories' ) ) {
				if (strpos($post->post_content,'header') && strpos($post->post_content,'default_cat_id')) {

					if (!empty($_REQUEST['sm_cat_id']) && is_numeric($_REQUEST['sm_cat_id'])){
						$cat_id = $_REQUEST['sm_cat_id'];
					} else {
						$cat_id =  $atts['default_cat_id'];
					}

					$api = sm_api_factory();
					$category_header = $api->sr->category->get(array('category' => $cat_id));
					$category_header->set_parameter('header', html_entity_decode('[label]'));
					$title['title'] = $category_header->render();
				}
				$post->post_title = $title['title'];
			}
		}


		return $title;
	}

	function sm_the_title_filter($title) {
		$api = sm_api_factory();

		$post = $GLOBALS['post'];

		if ( is_page( 'interview' ) || is_page( 'categories' ) ) {
			$shortcode = trim(str_replace(array('[', ']'), array('',''), $post->post_content));
			$atab = explode(' ',$shortcode);
			$atts = array();
			foreach($atab as $att) {
				$value = explode('=',$att);
				$atts[$value[0]] = str_replace('"','',$value[1]);
			}
			if (isset($atts['header']))  $atts['header'] = trim(str_replace(array('{{', '}}'), array('[',']'), $atts['header']));
		}

		if ( is_page( 'interview' ) and $post->post_name=='interview'  ) {

			if (strpos($post->post_content,'default_sr_id')) {

				if (!empty($_REQUEST['sm_int_id']) && is_numeric($_REQUEST['sm_int_id'])){
					$sr_id = $_REQUEST['sm_int_id'];
				} else {
					$sr_id =  $atts['default_sr_id'];
				}

				$interview_params = array(
						'embedable_name' => $atts['form_name'],
						'_type' => 'sr',
						'_override_interview_id' => $sr_id
				);
				$interview_params = apply_filters('smwp_pre_make_interview', $interview_params);

				$interview = sm_make_interview_from_embeddable($interview_params);

				$title = $interview->get_title();
			}
		}
		if ( is_page( 'categories' ) and $post->post_name=='categories' ) {
			if (strpos($post->post_content,'header') && strpos($post->post_content,'default_cat_id')) {

				if (!empty($_REQUEST['sm_cat_id']) && is_numeric($_REQUEST['sm_cat_id'])){
					$cat_id = $_REQUEST['sm_cat_id'];
				} else {
					$cat_id =  $atts['default_cat_id'];
				}

				$category_header = $api->sr->category->get(array('category' => $cat_id));
				$category_header->set_parameter('header', html_entity_decode('[label]'));
				$title = $category_header->render();
			}
			$post->post_title.=$title;
		}
		// otherwise returns the database content
		return $title;
	}

	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function sm_tinymce_plugin($plugin_array) {
		$version = get_bloginfo('version');
		$filejz = '';

		if (sm_is_a_post_editor()){
			if ($version >= "3.9.1") {
				$filejs = 'tinymce_plugin.v4.js';
			} else {
				$filejs = 'tinymce_plugin.js';
			}
			$plugin_array['smtinymceplugin'] = plugins_url('ui/js/'.$filejs, __FILE__);
		}


		return $plugin_array;
	}

	function sm_tinymce_button($buttons) {
	array_push($buttons, "separator", "smtinymceplugin");
	return $buttons;
	}

	function sm_make_interview_from_embeddable($data){
		global $wpdb;
		global $interview;

		if ($interview and isset($data['_override_interview_id']))
			if($interview->get_id()==$data['_override_interview_id']) return $interview;

		$api = sm_api_factory();

		if (isset($data['_path'])){
			$api->add_header("X-SMF-FORWARDED-PATH", $data['_path']);
		}

		$type = $data['_type'];

		switch($type){
			case "sr":
				$sql = "SELECT id, embedable_name, activity_id, parameters, created, altered, tracking_label FROM {$wpdb->prefix}sm_sr_forms WHERE ";
				$ajax_submit_path = admin_url() . "admin-ajax.php?action=sm_ajax_sr_submit";
			break;
			case "sp":
				$sql = "SELECT id, embedable_name, parameters, created, altered, tracking_label FROM {$wpdb->prefix}sm_sp_forms WHERE ";
				$ajax_submit_path = admin_url() . "admin-ajax.php?action=sm_ajax_sp_submit";
			break;
		}

		// Transforme une URL en chemin. HTTPS déjà géré...
		$ajax_submit_path = preg_replace("~^http[s]?://[^/]+/~", "/", $ajax_submit_path);

		//prep sql and conditions
		$conditions_keys = array();
		$conditions_vals = array();
		foreach ($data as $cond_key => $cond_val){
			if ($cond_key[0] == "_") continue;
			$key = $cond_key;
			if (preg_match("/^0-9+$/", $cond_val)) $key .= "= %d ";
			elseif(is_numeric($cond_val)) $key .= "= %F ";
			else $key .= "= %s ";
			$condition_keys[] = $key;
			$condition_vals[] = $cond_val;
		}

		foreach($condition_keys as $ckey){
			$sql .= $ckey;
		}

		$sql = $wpdb->prepare($sql, $condition_vals);

		$myform = $wpdb->get_row($sql);

		if (empty($myform)){
			return "";
		}

		//found embeddable. load interview via api, process it with wp specific values and return it.
		$myform->parameters = json_decode($myform->parameters, 1);

		try{
			switch ($type){
				case 'sr':
					$sr_search = array("activity"=>$myform->activity_id);

					//interview_id can be overridden by url vars shortcode
					if (isset($data["_override_interview_id"])){
						$sr_search["activity"] = $data["_override_interview_id"];
					}

					$interview = $api->sr->activity->interview->get($sr_search);
				break;
				case 'sp':
					$interview = $api->sp->interview->get(array());
					sm_manage_sp_submission_country($interview);
				break;
			}
		} catch(Exception $e){
			return "";
		}

		//if this records tracking string is empty, pull from default as set in form options
		if (empty($myform->tracking_label)){
			$myform->tracking_label = get_option("sm_default_aff_str", "default");
		}

		$interview->set_affiliate_track_string($myform->tracking_label);

		//set the site wide defaults as set on the defaults page
		$sm_display_defaults = get_option("sm_display_defaults");
		$interview->set_parameter("sm_display_defaults", $sm_display_defaults);

		//determine text to add to thank you from defaults and form forms.
		$more_text_ty = get_option("sm_default_success_more_text");
		if (!empty($myform->parameters['success_more_text'])){
			$interview->set_parameter("success_more_text_ty", $myform->parameters['success_more_text']);
			unset($myform->parameters['success_more_text']);
		} elseif (!empty($more_text_ty)){
			$interview->set_parameter("success_more_text_ty", $more_text_ty);
		}

		//save parameters to interview obj
		foreach ($myform->parameters as $pkey=>$pval) {
			$interview->set_parameter($pkey, $pval);
		}

		//give the ajax path for submission
		$interview->set_parameter("ajax_submit_path", $ajax_submit_path);

		//give the embedable id so ajax submissions can find the right one
		$interview->set_parameter("sm_embeddable_id", $myform->id);

		//show internal useage option if this form is for for sm internal use
		if (array_key_exists('SM_IS_OUR_IP', $_SERVER)){
			$internal_useage = array(
				"name" => "form_data__checkbox_test",
				"label" => __("INTERNAL USAGE (Only visible internally) :", "sm_translate"),
				"type" => "checkbox",
				"group" => "user",
				"required" => 1,//required to show on forms with show only required set as yes
				"validation" => array(),//required if required == 1
				"options" => array(
					array("label" => __("Check this box if you want this request to be filtered", "sm_translate"), "value"=>"test"),
				)
			);
			$questions = $interview->get_questions();
			array_unshift($questions, $internal_useage);
			$interview->set_questions($questions);
		}

		return $interview;
	}

	function sm_no_api_calls_now(){
		if (! get_option("sm_deactivate_api_during_slow", 0)){
			return 0;
		}

		if (!empty($_POST)) return 0;

		$times = get_option("sm_api_timeout_spans", array());

		$localtz = date_default_timezone_get();

		date_default_timezone_set('Europe/Luxembourg');

		$lx_time_now = date("Hi");

		date_default_timezone_set($localtz);

		foreach ($times as $time){
			if ($lx_time_now > $time[0] && $lx_time_now <= $time[1]) {
				return 1;
			}
		}

		return 0;
	}

	/*
	 *	javascript files to enqueue.
	@	alternate array. define each as name => array($path, $dependencies)
	@	requireds array. define as list of names
	 *	To pass a subset, call as  sm_enqueue_required_js_for_forms(array(), array("jquery","jquery-ui-core"))
	 *	To pass an alternate call as  sm_enqueue_required_js_for_forms(array("jquery-ui-core"=> array("//alternateurl", array()))
	 *"jquery-form"
	 */
	function sm_enqueue_required_js_for_forms($alternate= array(), $reqd = array("jquery","jquery-ui-core","jquery-ui-widget","jquery.form.wizard","jquery.validate","jquery.sm_forms")){
		$available = array(
			"jquery" => array(),
			"jquery-ui-core" => array(),
			"jquery-ui-widget" => array(),
			//"jquery-form" => array(),
			"jquery.form.wizard" => array(
				plugins_url('sm/ui/js/jquery.formwizard-3.0.5/js/jquery.form.wizard.js', __FILE__),
				array("jquery", "jquery-ui-core", "jquery-ui-widget", "jquery-form")
			),
			"jquery.validate" => array(
				"url" => "//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js",
				"dependencies" => array("jquery.form.wizard")
			),
			"jquery.bbq" => array(
				"url" => plugins_url('sm/ui/js/jquery.ba-bbq.min.js', __FILE__),
				"dependencies" => array("jquery", "jquery.form.wizard")
			),
			"jquery.sm_forms" => array(
				"url" => plugins_url('sm/ui/js/jquery.forms.js', __FILE__),
				"dependencies" => array("jquery")
			),
			"multiplude_back_fix" => array(
				"url" => plugins_url('sm/ui/js/multiplude_back_fix.js', __FILE__),
				"dependencies" => array("jquery.bbq")
			)
		);

		foreach($reqd as $req){
			if (array_key_exists($req, $alternate))
				call_user_func_array("wp_enqueue_script", array_merge(array($req), $alternate[$req]));
			elseif (array_key_exists($req, $available))
				call_user_func_array("wp_enqueue_script", array_merge(array($req), $available[$req]));
			else throw new sm_exception_general("missing $req for sm_enqueue_required_js_for_forms");
		}
	}

	function sm_show_messages($msgs){
		foreach(array("error", "updated") as $mtype){
			if (isset($msgs[$mtype])){
				echo "<div class=\"$mtype\"><p>" . $msgs[$mtype] . "</p></div>";
			}
		}
	}

	function sm_get_plugin_version(){
		$path = plugin_dir_path(__FILE__) . basename(dirname(__FILE__)).'.php';
		$plugin_data = get_plugin_data($path);
		return $plugin_data['Version'];
	}

	function sm_sanitize_for_slug($str){
		$str = trim($str);
		//first clear accents
		$str = remove_accents($str);

		//next turn anything else into a _.
		$str = preg_replace("/[^a-zA-Z0-9\_]/", "_", $str);

		//this might give us sequential underscores so turn those into just 1
		$str = preg_replace("/[\_]+/", "_", $str);

		//lower case so its easier to write and matches with the shortcode string
		$str = strtolower($str);

		return $str;
	}

	function sm_clear_api_cache(){
		$pattern = plugin_dir_path(__FILE__) . "sm/cache/*.json";
		$files = glob($pattern);
		$deleted_count = 0;
		foreach($files as $file){
			if (unlink($file)){
				$deleted_count ++;
			}
		}
		return $deleted_count;
	}

	function sm_get_kwid(){
		//check to see if its in cookie
		if (!empty($_COOKIE['KWID_COOKIE'])){
			return $_COOKIE['KWID_COOKIE'];
		}
		//otherwise use the one stored in options
		$sm_creds = get_option("sm_creds");
		return $sm_creds['sm_kwids'][0];
	}

	function sm_manage_sp_submission_country ($interview){
		$sm_api_server = get_option("sm_api_server");
		$sm_api_server_country = strtoupper(str_replace(array("dev-", "local-"), "", $sm_api_server));
		$sm_sp_submit_to_country = get_option("sm_sp_submit_to_country", $sm_api_server_country);
		$questions = $interview->get_questions();
		foreach($questions as $k => $question){
			if ($question['name'] == "sp_country"){
				$questions[$k]['type'] = 'hidden';
				unset($questions[$k]['options']);
				$questions[$k]['default'] = str_replace(array("FR", "UK"), array("France", "United Kingdom"), $sm_sp_submit_to_country);
			}
		}

		$interview->set_questions($questions);
	}
	/*
	 * Check if we are on post editor
	 *
	 * @return bool
	 */
	function sm_is_a_post_editor(){
		if(preg_match("/post(\-new)?\.php$/", $_SERVER['SCRIPT_NAME'])){
			return true;
		}else{
			return false;
		}
	}

	/*
	 * API WordPress: création auto des pages génériques - get caterogies list via api
	 *
	 * get categories list via api
	 *
	 * @return array
	 */
	function sm_api_get_categories_list() {
		$categories = null;

		//get categories for display
		$api = sm_api_factory();
		try {
			$categories_obj = $api->sr->category->list->get();
			if (!empty($myform->activity_id)){
				$interview_obj = $api->sr->activity->interview->get(array("activity"=>$myform->activity_id));
			}
		} catch (sm_exception_general $e){
			if (stripos($e->getMessage(), "invalid id") !== false){//ignore invalid id, user will have to re-save
				$messages["error"] = __("The saved interview identifier is invalid, Please reselect the interview and save the form.", "sm_translate");
			} else {
				$messages["error"] = __("There was a problem connecting to the ServiceMagic API.  Please try again soon or contact your Affiliate Representative.", "sm_translate");
			}
			include 'forms/show_message.php';
			return null;
		}

		$categories = $categories_obj->get_categories();

		if (isset($categories['etag'])) unset($categories['etag']);

		return $categories;
	}

	/*
	 * API WordPress: création auto des pages génériques
	 *
	 * get interviews by category via api
	 *
	 * @return array
	 */
	function sm_api_get_interviews($category_id) {

		$api = sm_api_factory();

		$interviews_obj = $api->sr->category->activities->get(array('category'=>$category_id));
		$interviews = $interviews_obj->get_activities();

		$r = array();
		foreach ($interviews as $key => $interview){
			if (is_array($interview)) {
				$r[] = array(
						'id' => $interview['id'],
						'label' => $interview['label']
				);
			}
		}

// 		echo '<pre>';
// 		echo_r($r);
// 		echo '</pre>';
		return $r;
	}

	/*
	 * get generic sr forms
	 *
	 * return array
	 */
	function sm_get_generic_sr_forms() {
		global $wpdb;
		$sm_sr_generic_forms = array();
		$generic_forms = $wpdb->get_results( "SELECT embedable_name, name FROM {$wpdb->prefix}sm_sr_forms WHERE is_archived <> 1 and activity_id=999 ORDER BY embedable_name" );
		foreach ($generic_forms as $generic_form){
			$sm_sr_generic_forms[] = array('embedable_name' => $generic_form->embedable_name, 'name' => $generic_form->name);
		}

		return $sm_sr_generic_forms;
	}
	/*
	 * get generic pages : activities / categories / interview
	 */
	function sm_get_generic_pages() {

		$slugs = array('categories','activities','interview');
		$generic_pages = array();

		foreach ($slugs as $slug) {
			if ($page = get_page_by_path($slug,ARRAY_A))
				$generic_pages[] = $page;
		}
		return $generic_pages;
	}

?>