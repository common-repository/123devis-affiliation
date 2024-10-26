<?php
function sm_activation(){ 

	//first check version
	$php_version = phpversion();
	if (version_compare($php_version, '5.1.0') == -1){
		die("Unfortunately, this plugin is designed to work on php 5.1 or greater. You are currently running $php_version.");
	}

	$admin = & get_role('administrator');
	//WEBSITE-3691 : API Change the publishers' rights
	if (!empty($admin)) {
		$admin->add_cap('sm_api_manage_options');
		$admin->add_cap('sm_api_manage_forms');
	}

	$editor = & get_role('editor');

	if (!empty($editor)) {
		$editor->add_cap('sm_api_manage_forms');
	}

	sm_clear_api_cache();
	sm_individual_activation();

	if (function_exists( 'is_network_admin' ) AND is_network_admin() ) {
		global $wpdb;
		$root_blog = $wpdb->blogid;
		$ms_blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

		foreach ($ms_blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			sm_individual_activation();
		}
		//revert to original blog
		switch_to_blog($root_blog);
	}
}

function sm_individual_activation(){
	global $wpdb;

	$api_sm_db_version = '1';
	$wp_sm_db_version = get_option('sm_db_version');

	if($api_sm_db_version != $wp_sm_db_version) {

		//install some tables
		//first determine if we need to rename sm_sr_forms - if so do it
		$old_sr_table_name = $wpdb->prefix . "sm_forms";
		$new_sr_table_name = $wpdb->prefix . "sm_sr_forms";

		$find_old_table = $wpdb->query("SELECT 1 FROM Information_schema.tables WHERE table_name = '$old_sr_table_name' AND table_schema = '".DB_NAME."'");
		if (!empty($find_old_table)){
			$wpdb->query("ALTER TABLE $old_sr_table_name RENAME $new_sr_table_name;");
		}

		$sr_sql = "CREATE TABLE $new_sr_table_name (
				id int(10) not null auto_increment,
				activity_id int(10) not null,
				activity_title varchar(200) not null,
				embedable_name varchar(50) not null,
				name varchar(50) not null,
				tracking_label varchar(50) not null,
				parameters varchar(2000) not null,
				is_archived smallint(6) not null default '0',
				created datetime,
				altered datetime,
				PRIMARY KEY  (id) )  COLLATE='utf8_general_ci',  ENGINE=InnoDB;
		";

		$sp_table_name = $wpdb->prefix . "sm_sp_forms";
		$sp_sql = "CREATE TABLE $sp_table_name (
				id int(10) not null auto_increment,
				embedable_name varchar(50) not null,
				name varchar(50) not null,
				tracking_label varchar(50) not null,
				parameters varchar(2000) not null,
				is_archived smallint(6) not null default '0',
				created datetime,
				altered datetime,
				PRIMARY KEY  (id) )  COLLATE='utf8_general_ci',  ENGINE=InnoDB;
		";

		$log_table_name = $wpdb->prefix . "sm_log";
		$log_sql = "CREATE TABLE $log_table_name (
				id int(10) not null auto_increment,
				path varchar(200) not null,
				get_json varchar(4000),
				post_json varchar(4000),
				type varchar(50) not null,
				message varchar(200),
				message_more varchar(4000),
				timest int(10) not null,
				user_name varchar(50),
				server_json varchar(4000),
				PRIMARY KEY  (id) )  COLLATE='utf8_general_ci',  ENGINE=InnoDB;
		";

		//check for and remove the kwid field
		foreach(array($new_sr_table_name, $sp_table_name) as $tbl){
			$find_is_archived_field = $wpdb->get_col($wpdb->prepare("SELECT 1 FROM Information_schema.COLUMNS WHERE TABLE_NAME = %s AND COLUMN_NAME = 'kwid' AND TABLE_SCHEMA= %s", $tbl, DB_NAME));
			if (!empty($find_is_archived_field)){
				$wpdb->query("ALTER TABLE {$tbl} DROP COLUMN kwid");
			}
		}

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		foreach(array($sr_sql, $sp_sql, $log_sql) as $tbl) {
			dbDelta($tbl);
		}

		if(false !== $wp_sm_db_version) {
			delete_option('sm_db_version');
		}
		add_option('sm_db_version', $api_sm_db_version, '', 'no');

	}

	//add to settings
	if (!get_option('sm_api_url')){
		add_option( 'sm_api_url', "https://api.servicemagic.eu" );
		add_option( 'sm_api_server', "fr" );
		add_option( 'sm_default_aff_str', "default");
		add_option( 'sm_default_success_more_text', "");
		add_option( 'sm_deactivate_api_during_slow', 0);
		add_option( "sm_accept_spa", 0);
		add_option( 'sm_clear_all_trace_on_deactivation', 1);
		add_option( 'sm_api_timeout_spans', array(array("0300", "0355")));
		add_option( 'sm_api_cache_mechanism', "ETAG");
		add_option( 'sm_set_user_cookie', 1);
		add_option( 'sm_sr_ty_message', "" );
		add_option( 'sm_sp_ty_message', "" );
	}

	new sm_wp_log("Install");
}

function sm_deactivation(){
	sm_individual_deactivation();

	if (function_exists( 'is_network_admin' ) AND is_network_admin() ) {
		global $wpdb;
		$root_blog = $wpdb->blogid;
		$ms_blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($ms_blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			sm_individual_deactivation();
		}

		//revert to original blog
		switch_to_blog($root_blog);
	}
}

function sm_individual_deactivation(){
	global $wpdb;

	if (get_option( 'sm_clear_all_trace_on_deactivation', 0)){
		foreach (array("sm_forms", "sm_sr_forms", "sm_sp_forms") AS $tbl){
			$table_name = $wpdb->prefix . $tbl;
			$sql = "DROP TABLE IF EXISTS $table_name";
			$wpdb->query($sql);
		}

		$table_name = $wpdb->prefix . "sm_log";
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);

		delete_option( "sm_creds" );
		delete_option( "sm_api_url" );
		delete_option( "sm_api_server" );
		delete_option( "sm_accept_spa" );
		delete_option( "sm_default_aff_str" );
		delete_option( "sm_default_success_more_text" );
		delete_option( 'sm_deactivate_api_during_slow' );
		delete_option( "sm_clear_all_trace_on_deactivation" );
		delete_option( 'sm_api_timeout_spans' );
		delete_option( 'sm_api_cache_mechanism' );
		delete_option( "sm_display_defaults" );
		delete_option( 'sm_db_version');
		delete_option( 'sm_set_user_cookie');
		delete_option( 'sm_sr_ty_message', "" );
		delete_option( 'sm_sp_ty_message', "" );
	}
}
