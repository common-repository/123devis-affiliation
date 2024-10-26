<?php
/**
 *
 * @param type $atts
 * @return type
 * @throws Exception
 * @throws sm_exception_general
 *
 * @redmine #11003 	: AFFILIATION | Amélioration design API Wordpress SR FORMS
 */
//[sm] shortcode
function sm_shortcode_func( $atts ){
	$result = '';

	if (!isset($atts['action'])){
		throw new Exception("Action required in SM short tag");
	}

	wp_enqueue_style('sm_css', plugins_url('sm/ui/css/sm.css', __FILE__));
	wp_enqueue_style('sm_d2_css', plugins_url('sm/ui/css/sm_d2.css', __FILE__));

	$action = $atts['action'];
	$interview_params = array();

	$sm_display_defaults = get_option("sm_display_defaults");

	if (!sm_no_api_calls_now()){
		foreach($atts as $ca_n => $ca_v){
			$atts[$ca_n] = str_replace(array("{{", "}}"), array("[","]"), $ca_v);
		}
		switch($action){
			case 'named_sr_form' :
			case 'named_sp_form' :

				if (!get_option( "sm_accept_spa", 0) AND $action == "named_sp_form"){
					new sm_wp_log("Trying to show shortcode for named sp form \"{$atts['form_name']}\" but option sm_accept_spa form not showing");
					break;
				}

				new sm_wp_log("Showing named_form \"{$atts['form_name']}\" via shortcode");

				if (!isset($atts['form_name'])){
					throw new sm_exception_general("attribute 'form_name' is required in SM short tag with action 'named_form'");
				}

				$interview_params = array(
					'embedable_name' => $atts['form_name'],
					'_type' => ($action == 'named_sp_form' ? 'sp' : 'sr')
				);

				if ($action == 'named_sr_form') {
					if (!empty($_REQUEST['sm_int_id']) && is_numeric($_REQUEST['sm_int_id'])){
						$interview_params['_override_interview_id'] = $_REQUEST['sm_int_id'];
					} elseif (isset($atts['default_sr_id']) && is_numeric($atts['default_sr_id'])) {
						$interview_params['_override_interview_id'] = $atts['default_sr_id'];
					}
				}

				$interview_params = apply_filters('smwp_pre_make_interview', $interview_params);

				$interview = sm_make_interview_from_embeddable($interview_params);

				if (empty($interview)){
					new sm_wp_log(array("type"=>"error", "message"=>"Interview \"{$atts['form_name']}\" not found"));
					define('DONOTCACHEPAGE', true);
					$result = __("Interview not available", "sm_translate");
					break;
				}

				if ($interview->has_errors()){
					new sm_wp_log(array("type"=>"error", "message"=>"Interview has errors" . $interview->get_formatted_errors()));
					define('DONOTCACHEPAGE', true);
					$result = __("Interview not available", "sm_translate");
					break;
				}

				sm_enqueue_required_js_for_forms();

				if ($interview->get_parameter('view') == 'multiplude'){//multiplude requires bbq plugin to enable forward and backward functions
					sm_enqueue_required_js_for_forms(array(), array("jquery.bbq","multiplude_back_fix"));
				}

				$result = $interview->render_with_submit();
			break;

			case 'home_list' :
				$api = sm_api_factory();

				$clean_atts = shortcode_atts( array(
					'target' => null
				), $atts );

				if (empty($atts['target'])){
					throw new Exception("target is required in SM short tag with action 'home_list'");
				}

				if (stripos($clean_atts['target'], "?") === false){
					$activity_link = $clean_atts['target'] . "?";
				} else {
					$activity_link = $clean_atts['target'] . "&";
				}

				$activity_link = "<a href=\"" . $activity_link . "sm_cat_id=[id]\">[label]</a>\n";

				new sm_wp_log("Showing home_list via shortcode");

				try {
					$home_list = $api->sr->category->list->get();
				} catch(Exception $e){
					new sm_wp_log(array("type"=>"error", "message"=>"Homelist has errors" . $home_list->get_formatted_errors()));
					define('DONOTCACHEPAGE', true);
					$result = __("Home list not available", "sm_translate");
					break;
				}
				$home_list->set_parameter("sm_display_defaults", $sm_display_defaults);
				$home_list->set_parameter("activity_link", $activity_link);

				$result = $home_list->render();
			break;

			case 'category_list' :
				$api = sm_api_factory();

				$clean_atts = shortcode_atts( array(
					'target' => null,//task_link' => "<a href=\"/sm/interview/?id_activity=[id]\">[label]</a>\n",
					'default_cat_id' => null
				), $atts );

				foreach($clean_atts as $ca_n => $ca_v){
					if (empty($atts[$ca_n])){
						throw new Exception("attribute \"$ca_n\" is required in SM short tag with action 'category_list'");
					}
				}

				if (!empty($_REQUEST['sm_cat_id']) && is_numeric($_REQUEST['sm_cat_id'])){
					$cat_id = $_REQUEST['sm_cat_id'];
				} else {
					$cat_id = $clean_atts['default_cat_id'];
				}

				if (stripos($clean_atts['target'], "?") === false){
					$interview_link = $clean_atts['target'] . "?";
				} else {
					$interview_link = $clean_atts['target'] . "&";
				}

				$interview_link = "<a href=\"" . $interview_link . "sm_int_id=[id]\">[label]</a>\n";

				new sm_wp_log("Showing category \"{$cat_id}\" via shortcode to \"" . htmlentities($interview_link) . "\"");

				//$atts['mode'] = isset($atts['mode']) ? $atts['mode'] : 'basic';//default to basic
				try {
					$category_list = $api->sr->category->activities->get(array('category'=>$cat_id));
				} catch(Exception $e){
					new sm_wp_log(array("type"=>"error", "message"=>"Category List has errors"));
					define('DONOTCACHEPAGE', true);
					$result = __("Category not available", "sm_translate");
				}

				if (! empty($category_list)){
					if ($category_list->has_errors()){
						new sm_wp_log(array("type"=>"error", "message"=>"Category List has errors" . $category_list->get_formatted_errors()));
						define('DONOTCACHEPAGE', true);
						$result = __("Category not available", "sm_translate");
					}

					$category_list->set_parameter("sm_display_defaults", $sm_display_defaults);
					$category_list->set_parameter("interview_link", $interview_link);

					$result .= $category_list->render();
				}
			break;

			case 'search_box' :
				$api = sm_api_factory();

				new sm_wp_log("Showing search_box via shortcode");

				sm_enqueue_required_js_for_forms(array(), array("jquery","jquery.validate"));

				$search_box = $api->sr->activity->search->renderable();
				$search_box->set_parameter("sm_display_defaults", $sm_display_defaults);

				$result = $search_box->render();
			break;

			default :
				new sm_wp_log(array("type"=>"error", "message"=>"Invalid shortcode action \"$action\"!"));
				$result = "<!-- invalid sm shortcode -->";
				//throw new sm_exception_general("Invalid action \"$action\" in SM shortcode");
			break;
		}
	}

	$result = apply_filters('sm_shortcode_func', $result, $interview_params);
	return $result;
}