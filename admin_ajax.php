<?php
	function sm_ajax_activities(){

		$category_id = intval($_REQUEST['category_id']);

		$api = sm_api_factory();

		$interviews_obj = $api->sr->category->activities->get(array('category'=>$category_id));
		$interviews = $interviews_obj->get_activities();

		//clean for ajax consumption
		$r = array();
		foreach ($interviews as $key => $interview){
			$r[] = array(
				'id' => $interview['id'],
				'label' => $interview['label']
			);
		}

		print json_encode($r);

		exit;
	}

	function sm_ajax_activity_search(){

		$keyword = $_REQUEST['keyword'];
		new sm_wp_log("User searched for \"$keyword\"");
		$api = sm_api_factory();

		$search_obj = $api->sr->activity->search->get(array('keyword'=>$keyword));
		$search_list = $search_obj->get_results();
		//clean for ajax consumption
		$r = array();
		foreach ($search_list as $key => $search){
			$r[] = array(
				'id' => $search['id'],
				'label' => $search['label']
			);
		}

		print json_encode($r);

		exit;
	}

	function sm_ajax_interview(){

		$activity_id = intval($_REQUEST['activity_id']);

		$api = sm_api_factory();

		$interview_obj = $api->sr->activity->interview->get(array("activity"=>$activity_id));
		$questions = $interview_obj->get_questions();

		print json_encode($questions);

		exit;
	}

	function sm_ajax_submit(){
		$required = array("embeddable_id");
		$type = "sp";
		if ($_REQUEST['action'] == "sm_ajax_sr_submit") {
			$required[] = "activity_id";
			$type = "sr";
		}
		foreach ($required as $fld){
			if (!isset($_REQUEST[$fld])){
				throw new Exception("attribute '$fld' is required in admin_ajax form submission");
			}
		}

		new sm_wp_log("Submitting form embeddable_id \"{$_REQUEST['embeddable_id']}\" via ajax");

		$interview_params = array(
			"id" =>$_REQUEST['embeddable_id'],
			"_override_interview_id" => $_REQUEST['activity_id'],
			"_type" => $type
		);

		if (isset($_REQUEST['path'])){
			$interview_params['_path'] = $_REQUEST['path'];
		}

		$interview = sm_make_interview_from_embeddable($interview_params);

		if (empty($interview)){
			throw new sm_exception_general("Could not make interview object", interview_params);
		}

		$return_data = array();

		if (!empty($_REQUEST) AND $interview->submit($_REQUEST)){
			$interview->set_parameter("view", "thanks");
			$interview->set_parameter("ajax", true);
			$return_data['message'] = $interview->render();
			$return_data['track_id'] = $interview->get_submission_result()->get_track_id();
		} else {
			$return_data['errors'] = $interview->get_validator()->get_errors();
		}

		print json_encode($return_data);

		exit();
	}

	function sm_ajax_history_data(){
		global $wpdb;

		$sTable = $wpdb->prefix . "sm_log";
		$sIndexColumn = "id";
		$aColumns = array( 'timest', 'type', 'path', 'message', 'user_name' );

		if (isset($_GET['iSortingCols']) AND $_GET['iSortingCols'] == 0){
			$_GET['iSortingCols'] = "1";
			$_GET['iSortCol_0'] = 0;
			$_GET['sSortDir_0'] = "desc";
		}

		$sLimit = "";

		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".esc_sql( $_GET['iDisplayStart'] ).", ".
				esc_sql( $_GET['iDisplayLength'] );
		}


		/*
		 * Ordering
		 */

		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
						esc_sql( $_GET['sSortDir_'.$i] ) .", ";
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}

		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".esc_sql( $_GET['sSearch'] )."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}

		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".esc_sql($_GET['sSearch_'.$i])."%' ";
			}
		}


		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
			FROM   $sTable
			$sWhere
			$sOrder
			$sLimit
		";

		$rResult = $wpdb->get_results( $sQuery );

		$orig_tzs = date_default_timezone_get();

		$tzs = get_option('timezone_string', '');

		if (empty($tzs)){
			if ($orig_tzs == 'UTC' OR empty($orig_tzs)){
				$tzs = "Europe/Paris";
			} else {
				$tzs = $orig_tzs;
			}
		}

		date_default_timezone_set($tzs);

		foreach($rResult as $key=>$res){
			$rResult[$key]->timest = date('Y-m-d g:i A', $res->timest);
		}

		date_default_timezone_set($orig_tzs);

		/* Data set length after filtering */
		$sQuery = "
			SELECT FOUND_ROWS()
		";

		$iFilteredTotal = $wpdb->get_col($sQuery);

		/* Total data set length */
		$sQuery = "
			SELECT COUNT(`".$sIndexColumn."`)
			FROM   $sTable
		";

		$iTotal = $wpdb->get_col($sQuery);

		/*
		 * Output
		 */
		$output = array(
			"sEcho" => intval(isset($_GET['sEcho']) ? $_GET['sEcho'] : 0),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] == "version" )
				{
					/* Special output formatting for 'version' column */
					$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
				}
				else if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					$row[] = $aRow->$aColumns[$i];
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );

		exit;
	}
	function sm_ajax_api_clear_cache(){
		print sm_clear_api_cache();
		exit();
	}
	/*
	 * Affiliation API WP - add generic pages
	 */
	function sm_ajax_create_genpage() {


		update_option("sm_default_allact_title", 	$_POST['sm_default_allact_title']);
		update_option("sm_default_listcat_title", 	$_POST['sm_default_listcat_title']);
		update_option("sm_default_genform_title", 	$_POST['sm_default_genform_title']);
		update_option("sm_default_form_gen", 		$_POST['sm_default_form_gen']);

		$pages = array();
		$WPpage = array();

		if (isset($_POST['sm_default_allact_title']) && isset($_POST['sm_default_listcat_title']) && isset($_POST['sm_default_genform_title'])){
			$pages['allact']['title']  	= $_POST['sm_default_allact_title'];
			$pages['allact']['contenu']	= '[sm action="home_list" target="/categories/"]';
			$pages['allact']['slug']		= 'activities';
			$pages['listcat']['title'] 	= $_POST['sm_default_listcat_title'];
			$pages['listcat']['contenu']	= '[sm action="category_list" default_cat_id="8" target="/interview/" header="&lt;h2&gt;{{label}}&lt;/h2&gt;"]';
			$pages['listcat']['slug']	= 'categories';
			$pages['genform']['title']  = $_POST['sm_default_genform_title'];
			$pages['genform']['contenu'] = sprintf('[sm action="named_sr_form" default_sr_id="305" form_name="%s"]',$_POST['sm_default_form_gen']);
			$pages['genform']['slug']	= 'interview';
		}

		global $user_ID;
		$pageid = array();

		foreach ($pages as $page) {
			$Existpage = get_page_by_path( $page['slug'] );
			print_r($Existpage);
			if( null == $Existpage ) {
				$WPpage = array();
				$WPpage['comment_status']= 'closed';
				$WPpage['ping_status']	= 'closed';
				$WPpage['post_type']    = 'page';
				$WPpage['post_content'] = $page['contenu'];
				$WPpage['post_parent']  = 0;
				$WPpage['post_author']  = $user_ID;
				$WPpage['post_status']  = 'publish';
				$WPpage['post_name']    = $page['slug'];
				$WPpage['post_title']   = $page['title'];
				print_r($WPpage);
				$pageid[] = wp_insert_post ($WPpage);
				unset($WPpage);
			} else {
				// Arbitrarily use -2 to indicate that the page with the title already exists
				$pageid[] = -2;
				switch ($page['slug']) {
					case 'interview' :
						if ($Existpage->post_content != $page['contenu'] || $Existpage->post_title != $page['title']) {
							wp_update_post (array('ID' => $Existpage->ID, 'post_content' => $page['contenu'],'post_title'  => $page['title']));
						}
						break;
					case 'activities' :
					case 'categories' :
						if ( $Existpage->post_title != $page['title']) {
							wp_update_post (array('ID' => $Existpage->ID, 'post_title'  => $page['title']));
						}
						break;
				}
			}
		}

		print_r($pageid);
		exit;
	}